<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use SoapClient;
use yura\Jobs\UpdateSaldosProyVentaSemanal;
use yura\Modelos\Aerolinea;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\ClienteConsignatario;
use yura\Modelos\ClienteDatoExportacion;
use yura\Modelos\ClientePedidoEspecificacion;
use yura\Modelos\Comprobante;
use yura\Modelos\ConfiguracionEmpresa;
use yura\Modelos\DataTallos;
use yura\Modelos\DatosExportacion;
use yura\Modelos\DetalleEnvio;
use yura\Modelos\DetalleEspecificacionEmpaque;
use yura\Modelos\Especificacion;
use yura\Modelos\EspecificacionEmpaque;
use yura\Modelos\Pais;
use yura\Modelos\Pedido;
use yura\Modelos\DetallePedido;
use yura\Modelos\Envio;
use yura\Modelos\DetallePedidoDatoExportacion;
use yura\Modelos\Empaque;
use yura\Jobs\ProyeccionVentaSemanalUpdate;
use Validator;
use DB;
use Barryvdh\DomPDF\Facade as PDF;
use yura\Modelos\ProductoYuraVenture;
use yura\Modelos\Semana;

class PedidoController extends Controller
{
    public function listar_pedidos(Request $request)
    {
        return view('adminlte.gestion.postcocecha.pedidos.inicio',
            [
                'idCliente' => $request->id_cliente,
                'annos' => DB::table('pedido as p')->select(DB::raw('YEAR(p.fecha_pedido) as anno'))->distinct()->get(),
                'especificaciones' => DB::table('pedido as p')
                    ->join('cliente_pedido_especificacion as cpe', 'p.id_cliente', '=', 'cpe.id_cliente')
                    ->join('especificacion as esp', 'cpe.id_especificacion', '=', 'esp.id_especificacion')
                    ->where('p.id_cliente', $request->id_cliente)
                    ->select('esp.id_especificacion', 'esp.nombre', 'cpe.id_cliente_pedido_especificacion')
                    ->distinct()->get()
            ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function ver_pedidos(Request $request)
    {
        // dd($request->all());
        $busquedaAnno = $request->has('busquedaAnno') ? $request->busquedaAnno : '';
        $busquedaEspecificacion = $request->has('id_especificaciones') ? $request->id_especificaciones : '';
        $busquedaDesde = $request->has('desde') ? $request->desde : '';
        $busquedaHasta = $request->has('hasta') ? $request->hasta : '';

        $listado = DB::table('pedido as p')
            ->join('cliente_pedido_especificacion as cpe', 'p.id_cliente', '=', 'cpe.id_cliente')
            ->join('especificacion as esp', 'cpe.id_especificacion', '=', 'esp.id_especificacion')
            ->join('detalle_pedido as dp', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
            ->where('p.id_cliente', $request->id_cliente)
            ->select('p.*')->distinct();

        if ($request->busquedaAnno != '')
            $listado = $listado->where(DB::raw('YEAR(p.fecha_pedido)'), $busquedaAnno);
        if ($request->id_especificaciones != '')
            $listado = $listado->where('dp.id_cliente_especificacion', $busquedaEspecificacion);
        if ($request->desde != '' && $request->hasta != '')
            $listado = $listado->whereBetween('p.fecha_pedido', [$busquedaDesde, $busquedaHasta]);

        $listado = $listado->orderBy('p.fecha_pedido', 'desc')->simplePaginate(20);

        $datos = [
            'listado' => $listado,
            'idCliente' => $request->id_cliente,
        ];
        return view('adminlte.gestion.postcocecha.pedidos.partials.listado', $datos);

    }

    public function add_pedido(Request $request)
    {
        return view('adminlte.gestion.postcocecha.pedidos.forms.add_pedido',
            [
                'idCliente' => $request->id_cliente,
                'pedido_fijo' => $request->pedido_fijo,
                'vista' => $request->vista,
                'clientes' => DB::table('cliente as c')
                    ->join('detalle_cliente as dt', 'c.id_cliente', '=', 'dt.id_cliente')
                    ->where('dt.estado', 1)->orderBy('dt.nombre', 'asc')->get(),
                'id_pedido' => $request->id_pedido,
                'datos_exportacion' => ClienteDatoExportacion::where('id_cliente', $request->id_cliente)->get(),
                'comprobante' => isset($request->id_pedido) ? (isset(getPedido($request->id_pedido)->envios[0]->comprobante) ? getPedido($request->id_pedido)->envios[0]->comprobante : null) : null
            ]);
    }

    public function store_pedidos(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'arrDataPedido' => 'Array',
            'id_cliente' => 'required',
        ]);
        if (!$valida->fails()) {

            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                . '</div>';

            empty($request->arrFechas) ? $request->arrFechas = [$request->fecha_de_entrega] : $request->arrFechas;

            foreach ($request->arrFechas as $key => $fechas) {
                $formatoFecha = '';
                if (isset($request->opcion) && $request->opcion != 3) {
                    $formato = explode("/", $fechas);
                    $formatoFecha = $formato[2] . '-' . $formato[0] . '-' . $formato[1];
                }

                (isset($request->opcion) && $request->opcion != 3) ? $fechaFormateada = $formatoFecha : $fechaFormateada = $fechas;

                if (!empty($request->id_pedido)) { //ACTUALIZAR
                    $dataEnvio = Envio::where('id_pedido', $request->id_pedido)->first();
                    if (isset($dataEnvio->id_envio)) {

                        $dataComprobante = Comprobante::where('id_envio', $dataEnvio->id_envio)
                            ->join('detalle_factura as df', 'comprobante.id_comprobante', 'df.id_comprobante')
                            ->join('impuesto_detalle_factura as idf', 'df.id_detalle_factura', 'idf.id_detalle_factura')
                            ->join('desglose_envio_factura as def', 'comprobante.id_comprobante', 'def.id_comprobante')
                            ->join('impuesto_desglose_envio_factura as idef', 'def.id_desglose_envio_factura', 'idef.id_desglose_envio_factura')
                            ->select('clave_acceso', 'comprobante.id_comprobante', 'comprobante.secuencial')->get();

                        if ($dataComprobante->count() > 0) {

                            $objComprobante = Comprobante::find($dataComprobante[0]->id_comprobante);
                            $objComprobante->habilitado = false;
                            $objComprobante->id_envio = null;
                            $objComprobante->rehusar = true;
                            if ($objComprobante->save()) {
                                $archivo_generado = env('PATH_XML_FIRMADOS') . '/facturas/' . $dataComprobante[0]->clave_acceso . ".xml";
                                $archivo_firmado = env('PATH_XML_GENERADOS') . '/facturas/' . $dataComprobante[0]->clave_acceso . ".xml";

                                if (file_exists($archivo_generado)) unlink($archivo_generado);
                                if (file_exists($archivo_firmado)) unlink($archivo_firmado);

                                $codigo_dae = $dataEnvio->codigo_dae;
                                $dae = $dataEnvio->dae;
                                $guia_madre = $dataEnvio->guia_madre;
                                $guia_hija = $dataEnvio->guia_hija;
                                $email = $dataEnvio->email;
                                $telefono = $dataEnvio->telefono;
                                $direccion = $dataEnvio->direccion;
                                $codigo_pais = $dataEnvio->codigo_pais;
                                $almacen = $dataEnvio->almacen;
                                $aerolinea = getEnvio($dataEnvio->id_envio)->detalles[0]->id_aerolinea;
                                $id_configuracion_empresa = $dataEnvio->pedido->id_configuracion_empresa;
                            }

                        }

                    }

                    /*foreach (getPedido($request->id_pedido)->detalles as $det_ped)
                        if($det_ped->cliente_especificacion->especificacion->tipo === "O")
                            Especificacion::destroy($det_ped->cliente_especificacion->especificacion->id_especificacion);*/
                    //DetallePedidoDatoExportacion::where('id_detalle_pedido',$det_ped->id_detalle_pedido)->delete();
                }

                $objPedido = new Pedido;
                $objPedido->id_cliente = $request->id_cliente;
                $objPedido->descripcion = $request->descripcion;
                $objPedido->fecha_pedido = $fechaFormateada;
                $objPedido->id_configuracion_empresa = isset($id_configuracion_empresa) ? $id_configuracion_empresa : $request->id_configuracion_empresa;
                $objPedido->variedad = substr(implode("|", array_unique($request->variedades)), 0, -1);

                if (isset($dataEnvio->id_envio) && count($dataComprobante) > 0) {
                    $objPedido->clave_acceso_temporal = $dataComprobante[0]->secuencial;  //$dataComprobante[0]->clave_acceso; COMENTANDO PARA QUE LA FACTURACION FUNCIONE CON EL VENTURE
                    $objPedido->id_comprobante_temporal = $dataComprobante[0]->id_comprobante;
                }

                if ($objPedido->save()) {
                    $model = Pedido::all()->last();
                    bitacora('pedido', $model->id_pedido, 'I', 'Inserción satisfactoria de un nuevo pedido');
                    foreach ($request->arrDataDetallesPedido as $key => $item) {
                        $objDetallePedido = new DetallePedido;

                        //SI UN DETALLE PEDIDO (ESPECIFICACION) DEL PEDIDO NO VA EN CAJAS SE CREA UNA NUEVA ESPECIFICACION DE TIPO 'O' Y SE ATA AL CLIENTE
                        if (!isset($item['id_cliente_pedido_especificacion'])) {
                            $asignacion = $this->asignaClienteEspecificacion($item['datos_especificacion'], $request->id_cliente);
                            if (!$asignacion['estado']) {
                                Pedido::destroy($model->id_pedido);
                                $success = false;
                                $msg = '<div class="alert alert-danger text-center">' .
                                    '<p> Hubo un error al guardar la información del pedido en el sistema, intente nuevamente, si el error persiste contacte al área de sistemas</p>'
                                    . '</div>';
                                return [
                                    'mensaje' => $msg,
                                    'success' => $success
                                ];
                            }
                        }

                        $objDetallePedido->id_cliente_especificacion = !isset($item['id_cliente_pedido_especificacion'])
                            ? $asignacion['id_cliente_pedido_especificacion']
                            : $item['id_cliente_pedido_especificacion'];

                        $objDetallePedido->id_pedido = $model->id_pedido;
                        $objDetallePedido->id_agencia_carga = $item['id_agencia_carga'];
                        $objDetallePedido->cantidad = $item['cantidad'];
                        $objDetallePedido->precio = substr($item['precio'], 0, -1);
                        $objDetallePedido->orden = $item['orden'];

                        if ($objDetallePedido->save()) {
                            $modelDetallePedido = DetallePedido::all()->last();
                            if (isset($request->dataTallos) && count($request->dataTallos) > 0) {
                                $storeDataTallos = $this->store_datos_tallos($request->dataTallos[$key], $modelDetallePedido->id_detalle_pedido);
                                if (!$storeDataTallos) {
                                    Pedido::destroy($model->id_pedido);
                                    $success = false;
                                    $msg = '<div class="alert alert-danger text-center">' .
                                        '<p> Hubo un error al guardar la información del pedido en el sistema, intente nuevamente, si el error persiste contacte al área de sistemas</p>'
                                        . '</div>';
                                    return [
                                        'mensaje' => $msg,
                                        'success' => $success
                                    ];
                                }
                            }
                            bitacora('detalle_pedido', $modelDetallePedido->id_detalle_pedido, 'I', 'Inserción satisfactoria de un nuevo detalle pedido');
                            if ($request->arrDatosExportacion != '') {
                                foreach ($request->arrDatosExportacion[$key] as $de) {
                                    if ($de['valor'] != null) {
                                        $objDetallePedidoDatoExportacion = new DetallePedidoDatoExportacion;
                                        $objDetallePedidoDatoExportacion->id_detalle_pedido = $modelDetallePedido->id_detalle_pedido;
                                        $objDetallePedidoDatoExportacion->id_dato_exportacion = $de['id_dato_exportacion'];
                                        $objDetallePedidoDatoExportacion->valor = $de['valor'];
                                        if ($objDetallePedidoDatoExportacion->save()) {
                                            $modelDetallePedidoDatoExportacion = DetallePedidoDatoExportacion::all()->last();
                                            bitacora('detallepedido_datoexportacion', $modelDetallePedidoDatoExportacion->id_detallepedido_datoexportacion, 'I', 'Inserción satisfactoria de un nuevo detallepedido_datoexportacion');
                                        }
                                    }
                                }
                            }

                            //DetalleEnvio::where('id_envio',$dataEnvio->id_envio)->delete();
                            //Envio::where('id_pedido',$request->id_pedido)->delete();
                            Pedido::destroy($request->id_pedido);

                            $success = true;
                            $msg = '<div class="alert alert-success text-center">' .
                                '<p> Se ha guardado el pedido exitosamente</p>'
                                . '</div>';
                        } else {
                            Pedido::destroy($model->id_pedido);
                            $success = false;
                            $msg = '<div class="alert alert-danger text-center">' .
                                '<p> Hubo un error al guardar la información del pedido en el sistema, intente nuevamente, si el error persiste contacte al área de sistemas</p>'
                                . '</div>';
                        }
                    }
                    $objEnvio = new Envio;
                    $objEnvio->fecha_envio = $fechaFormateada;
                    $objEnvio->id_pedido = $model->id_pedido;
                    if (isset($codigo_dae)) {
                        $objEnvio->codigo_dae = $codigo_dae;
                        $objEnvio->dae = $dae;
                        $objEnvio->guia_madre = $guia_madre;
                        $objEnvio->guia_hija = $guia_hija;
                        $objEnvio->email = $email;
                        $objEnvio->telefono = $telefono;
                        $objEnvio->direccion = $direccion;
                        $objEnvio->codigo_pais = $codigo_pais;
                        $objEnvio->almacen = $almacen;
                    }

                    if ($objEnvio->save()) {
                        $modelEnvio = Envio::all()->last();
                        bitacora('envio', $modelEnvio->id_envio, 'I', 'Inserción satisfactoria de un nuevo envío');
                        $dataDetallePedido = DetallePedido::where('id_pedido', $model->id_pedido)
                            ->join('cliente_pedido_especificacion as cpe', 'detalle_pedido.id_cliente_especificacion', 'cpe.id_cliente_pedido_especificacion')
                            ->select('cpe.id_especificacion', 'detalle_pedido.cantidad')->get();
                        foreach ($dataDetallePedido as $detallePeido) {
                            $objDetalleEnvio = new DetalleEnvio;
                            $objDetalleEnvio->id_envio = $modelEnvio->id_envio;
                            $objDetalleEnvio->id_especificacion = $detallePeido->id_especificacion;
                            $objDetalleEnvio->cantidad = $detallePeido->cantidad;
                            isset($aerolinea) ? $objDetalleEnvio->id_aerolinea = $aerolinea : "";
                            if ($objDetalleEnvio->save()) {
                                $modelDetalleEnvio = DetalleEnvio::all()->last();
                                bitacora('detalle_envio', $modelDetalleEnvio->id_detalle_envio, 'I', 'Inserción satisfactoria de un nuevo detalle envío');
                            }
                        }
                    }
                }
            }
            if (isset($request->arrDataPresentacionYuraVenture) && count($request->arrDataPresentacionYuraVenture)) {
                foreach ($request->arrDataPresentacionYuraVenture as $presentacionYuraVenture) {
                    $objProductoYuraVenture = new ProductoYuraVenture;
                    $objProductoYuraVenture->presentacion_yura = $presentacionYuraVenture['codigo_presentacion'];
                    $objProductoYuraVenture->codigo_venture = $presentacionYuraVenture['codigo_venture'];
                    $objProductoYuraVenture->id_configuracion_empresa = isset($id_configuracion_empresa) ? $id_configuracion_empresa : $request->id_configuracion_empresa;
                    $objProductoYuraVenture->tipo = "O";
                    $objProductoYuraVenture->save();
                }
            }
            $semana = getSemanaByDate($fechaFormateada);
            $codigo_semana = $semana != '' ? $semana->codigo : '';
            if ($codigo_semana != '')
                ProyeccionVentaSemanalUpdate::dispatch($codigo_semana, $codigo_semana, 0, $request->id_cliente)
                    ->onQueue('update_venta_semanal_real');
            //UpdateSaldosProyVentaSemanal::dispatch($semana, 0)->onQueue('update_saldos_proy_venta_semanal');
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function inputs_pedidos(Request $request)
    {
        $tipo_especificacion = DetallePedido::where('id_pedido', $request->id_pedido)
            ->join('cliente_pedido_especificacion as cpe', 'detalle_pedido.id_cliente_especificacion', 'cpe.id_cliente_pedido_especificacion')
            ->join('especificacion as esp', 'cpe.id_especificacion', 'esp.id_especificacion')->select('tipo')->first();

        $data_especificaciones = DB::table('cliente_pedido_especificacion as cpe')
            ->join('especificacion as esp', 'cpe.id_especificacion', '=', 'esp.id_especificacion')
            ->where([
                ['cpe.id_cliente', $request->id_cliente],
                ['esp.tipo', isset($tipo_especificacion->tipo) ? $tipo_especificacion->tipo : "N"],
                ['esp.estado', 1]
            ]);

        if (isset($tipo_especificacion->tipo) && $tipo_especificacion->tipo == "O") {
            $data_especificaciones = $data_especificaciones->join('detalle_pedido as dp', 'cpe.id_cliente_pedido_especificacion', 'dp.id_cliente_especificacion')
                ->where('id_pedido', $request->id_pedido);
        }

        $empT = [];
        $empTallos = Empaque::where([
            ['f_empaque', 'T'],
            ['tipo', 'C']
        ])->get();
        foreach ($empTallos as $empRamo)
            $empT[] = $empRamo;

        return view('adminlte.gestion.postcocecha.pedidos.forms.paritals.inputs_dinamicos',
            [
                'especificaciones' => $data_especificaciones->orderBy('id_cliente_pedido_especificacion', 'asc')->get(),
                'agenciasCarga' => DB::table('cliente_agenciacarga as cac')
                    ->join('agencia_carga as ac', 'cac.id_agencia_carga', 'ac.id_agencia_carga')
                    ->where([
                        ['cac.id_cliente', $request->id_cliente],
                        ['cac.estado', 1]
                    ])->get(),
                'datos_exportacion' => DatosExportacion::join('cliente_datoexportacion as cde', 'dato_exportacion.id_dato_exportacion', 'cde.id_dato_exportacion')
                    ->where('id_cliente', $request->id_cliente)->get(),
                'emp_tallos' => $empT
            ]);
    }

    public function inputs_pedidos_edit(Request $request)
    {

        $esp_creadas = [];
        $pedido = getPedido($request->id_pedido);
        foreach ($pedido->detalles as $det_ped)
            $esp_creadas[] = $det_ped->cliente_especificacion->especificacion->id_especificacion;

        $especificaciones_restantes = DB::table('cliente_pedido_especificacion as cpe')
            ->join('especificacion as esp', 'cpe.id_especificacion', 'esp.id_especificacion')
            ->where([
                ['cpe.id_cliente', $request->id_cliente],
                ['esp.tipo', "N"],
                ['esp.estado', 1]
            ])->whereNotIn('esp.id_especificacion', $esp_creadas)
            ->orderBy('id_cliente_pedido_especificacion', 'asc')->get();

        $empT = [];
        $empTallos = Empaque::where([
            ['f_empaque', 'T'],
            ['tipo', 'C']
        ])->get();
        foreach ($empTallos as $empRamo)
            $empT[] = $empRamo;

        return view('adminlte.gestion.postcocecha.pedidos.forms.paritals.inputs_dinamicos_edit',
            [
                'id_pedido' => $request->id_pedido,
                'datos_exportacion' => DatosExportacion::join('cliente_datoexportacion as cde', 'dato_exportacion.id_dato_exportacion', 'cde.id_dato_exportacion')
                    ->where('id_cliente', $request->id_cliente)->get(),
                'agenciasCarga' => DB::table('cliente_agenciacarga as cac')
                    ->join('agencia_carga as ac', 'cac.id_agencia_carga', 'ac.id_agencia_carga')
                    ->where([
                        ['cac.id_cliente', $request->id_cliente],
                        ['cac.estado', 1]
                    ])->get(),
                'especificaciones_restante' => $especificaciones_restantes,
                'emp_tallos' => $empT
            ]);
    }

    public function actualizar_estado_pedido_detalle(Request $request)
    {
        $objDetallePedido = DetallePedido::find($request->id_detalle_pedido);
        $objDetallePedido->estado = $request->estado == 1 ? 0 : 1;

        if ($objDetallePedido->save()) {
            $model = DetallePedido::all()->last();
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha actualizado el estado del detalle del pedido exitosamente</p>'
                . '</div>';
            bitacora('detalle_pedido', $model->id_detalle_pedido, 'U', 'Actualización satisfactoria del estado del detalle del pedido');
        } else {
            $success = false;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Ha ocurrido un error al guardar la información intente nuevamente</p>'
                . '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];

    }

    public function cancelar_pedido(Request $request)
    {
        $pedido = getPedido($request->id_pedido);

        if (isset($pedido->envios[0]->comprobante) && $pedido->envios[0]->comprobante != "") {
            $objComprobante = Comprobante::find($pedido->envios[0]->comprobante->id_comprobante);
            $objComprobante->update([
                'id_envio' => null,
                'rehusar' => true,
                'habilitado'=>false
            ]);
        }
        //$objPedido = Pedido::find($request->id_pedido);
        //$objPedido->estado = $request->estado == 0 ? 1 : 0;

        if (Pedido::destroy($request->id_pedido)) {
            $success = true;
            // $objPedido->estado == 0 ? $palabra = 'cancelado' : $palabra = 'activado';
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha cancelado el pedido exitosamente</p>'
                . '</div>';
            bitacora('pedido', $request->id_pedido, 'D', 'Pedido eliminado con exito');
            $semana = getSemanaByDate($pedido->fecha_pedido)->codigo;
            ProyeccionVentaSemanalUpdate::dispatch($semana, $semana, 0, $pedido->id_cliente)->onQueue('update_venta_semanal_real');
        } else {
            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p> Ha ocurrido un problema al cancelar el pedido, intente nuevamente</p>'
                . '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function opcion_pedido_fijo(Request $request)
    {
        return view('adminlte.gestion.postcocecha.pedidos.forms.paritals.inputs_opciones_pedido_fijo',
            ['opcion' => $request->opcion]);
    }

    public function add_fechas_pedido_fijo_personalizado(Request $request)
    {
        return view('adminlte.gestion.postcocecha.pedidos.forms.paritals.inputs_fechas_pedido_fijo_personalizado',
            ['cant_div' => $request->cant_div]);
    }

    public function crear_packing_list($id_pedido, $vista_despacho = false)
    {
        $pedido = getPedido($id_pedido);
        $empresa = getConfiguracionEmpresa($pedido->id_configuracion_empresa);
        $despacho = isset(getDetalleDespacho($pedido->id_pedido)->despacho) ? getDetalleDespacho($pedido->id_pedido)->despacho : null;
        $facturaTercero = isset($pedido->envios) ? getFacturaClienteTercero($pedido->envios[0]->id_envio) : null;
        if ($facturaTercero !== null) {
            $cliente = [
                'nombre' => $facturaTercero->nombre_cliente_tercero,
                'identificacion' => $facturaTercero->identificacion,
                'tipo_identificacion' => getTipoIdentificacion($facturaTercero->codigo_identificacion)->nombre,
                'pais' => getPais($facturaTercero->codigo_pais)->nombre,
                'provincia' => $facturaTercero->provincia,
                'direccion' => $facturaTercero->direccion,
                'telefono' => $facturaTercero->telefono,
                'dae' => $facturaTercero->dae,
            ];
        } else {
            foreach ($pedido->cliente->detalles as $det_cli)
                if ($det_cli->estado == 1)
                    $cliente = $det_cli;
            $cliente = [
                'nombre' => $cliente->nombre,
                'identificacion' => $cliente->ruc,
                'tipo_identificacion' => getTipoIdentificacion($cliente->codigo_identificacion)->nombre,
                'pais' => getPais($cliente->codigo_pais)->nombre,
                'provincia' => $cliente->provincia,
                'direccion' => $cliente->direccion,
                'telefono' => $cliente->telefono,
                'dae' => $pedido->envios[0]->dae
            ];
        }
        return PDF::loadView('adminlte.gestion.postcocecha.despachos.partials.pdf_packing_list', compact('pedido', 'vista_despacho', 'empresa', 'despacho', 'cliente'))->stream();
    }

    public function facturar_pedido(Request $request)
    {

        if (getPedido($request->id_pedido)->envios->count() === 0) {
            return '<div class="alert alert-danger text-center">' .
                '<p> El pedido no posee envío creado, edite el pedido y deje el Check "Envío autmático" activado para realizar el envío</p>'
                . '</div>';
        }
        $pedido = getPedido($request->id_pedido);
        $listado = Envio::where([
            ['id_envio', $pedido->envios[0]->id_envio],
            ['dc.estado', 1],
            ['envio.estado', 0],
            ['p.estado', 1]
        ])->join('pedido as p', 'envio.id_pedido', '=', 'p.id_pedido')
            ->join('detalle_cliente as dc', 'p.id_cliente', '=', 'dc.id_cliente')
            ->join('impuesto as i', 'dc.codigo_impuesto', 'i.codigo')
            ->join('tipo_impuesto as ti', 'dc.codigo_porcentaje_impuesto', 'ti.codigo')
            ->orderBy('envio.id_envio', 'Desc')
            ->select('p.*', 'envio.*', 'i.nombre as nombre_impuesto', 'ti.porcentaje', 'dc.nombre', 'dc.direccion as direccion_cliente', 'dc.almacen as almacen_cliente', 'dc.provincia', 'dc.codigo_pais as pais_cliente', 'dc.telefono as telefono_cliente', 'dc.correo');

        return view('adminlte.gestion.postcocecha.envios.partials.listado', [
            'envios' => $listado->paginate(10),
            'paises' => Pais::all(),
            'aerolineas' => Aerolinea::where('estado', 1)->orderBy('nombre', 'asc')->get(),
            'vista' => $request->path(),
            'empresas' => ConfiguracionEmpresa::all(),
            'consignatarios' => ClienteConsignatario::where('id_cliente', $pedido->id_cliente)
                ->join('consignatario as c', 'cliente_consignatario.id_consignatario', 'c.id_consignatario')->get()
        ]);
    }

    public function ver_factura_pedido($id_pedido)
    {

        $clave_acceso = getPedido($id_pedido)->envios[0]->comprobante->clave_acceso;
        $tipo_documento = getDetallesClaveAcceso($clave_acceso, 'TIPO_COMPROBANTE');

        if ($tipo_documento == "01")
            $dataComprobante = Comprobante::where('clave_acceso', $clave_acceso)->select('numero_comprobante', 'id_envio')->first();

        if ($tipo_documento == "06")
            $dataComprobante = Comprobante::where('clave_acceso', $clave_acceso)
                ->join('detalle_guia_remision as dgr', 'comprobante.id_comprobante', 'dgr.id_comprobante')->select('id_comprobante_relacionado')->first();

        $cliente = new SoapClient(env('URL_WS_ATURIZACION'));
        $response = $cliente->autorizacionComprobante(["claveAccesoComprobante" => $clave_acceso]);
        $autorizacion = $response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion;

        $data = [
            'autorizacion' => $autorizacion,
            'img_clave_acceso' => generateCodeBarGs1128((String)$autorizacion->numeroAutorizacion),
            'obj_xml' => simplexml_load_string($autorizacion->comprobante),
            'numeroComprobante' => $dataComprobante->numero_comprobante,
            'detalles_envio' => $tipo_documento == "01" ? getEnvio($dataComprobante->id_envio)->detalles : "",
            'pedido' => $tipo_documento == "06" ? getComprobante($dataComprobante->id_comprobante_relacionado)->envio->pedido : ""
        ];
        return PDF::loadView('adminlte.gestion.comprobante.partials.pdf.factura', compact('data'))->stream();
    }

    public function store_especificacion_pedido(Request $request)
    {
        $success = false;
        $msg = '<div class="alert alert-danger text-center">' .
            '<p> Ha ocurrido un problema al guardar la información al sistema, intente nuevamente</p>'
            . '</div>';
        $save = false;

        if ($request->arrDatosExportacion != null) {
            foreach ($request->arrDatosExportacion as $dato_exportacion) {
                foreach ($dato_exportacion as $de) {
                    $objDetallePedido = DetallePedido::find($de['id_detalle_pedido']);
                    if ($objDetallePedido->update(['id_agencia_carga' => $request->id_agencia_carga])) {
                        $objDetallePedidoDatoExportacion = DetallePedidoDatoExportacion::where([
                            ['id_detalle_pedido', $de['id_detalle_pedido']],
                            ['id_dato_exportacion', $de['id_dato_exportacion']]
                        ]);
                        if ($objDetallePedidoDatoExportacion->first() == null) {
                            $detallePedidoDatoExportacion = new DetallePedidoDatoExportacion;
                            $detallePedidoDatoExportacion->id_detalle_pedido = $de['id_detalle_pedido'];
                            $detallePedidoDatoExportacion->id_dato_exportacion = $de['id_dato_exportacion'];
                            $detallePedidoDatoExportacion->valor = $de['valor'];
                            $detallePedidoDatoExportacion->save()
                                ? $save = true
                                : $save = false;
                        } else {
                            $objDetallePedidoDatoExportacion = DetallePedidoDatoExportacion::find($objDetallePedidoDatoExportacion->first()->id_detallepedido_datoexportacion);
                            $objDetallePedidoDatoExportacion->update(['valor' => $de['valor']])
                                ? $save = true
                                : $save = false;
                        }
                        if ($save) {
                            $success = true;
                            $msg = '<div class="alert alert-success text-center">' .
                                '<p> Se ha actualizado la información con éxito</p>'
                                . '</div>';
                        }
                    }
                }
            }
        } else {
            $objDetallePedido = DetallePedido::where('id_pedido', $request->id_pedido);
            if ($objDetallePedido->update(['id_agencia_carga' => $request->id_agencia_carga])) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha actualizado la información con éxito</p>'
                    . '</div>';
            }
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function buscar_codigo_venture(Request $request)
    {
        // dd($request->all());
        $idPlanta = getVariedad($request->id_variedad)->planta->id_planta;
        $presentacion_pedido_caja = $idPlanta . "|" . $request->id_variedad . "|" . $request->id_clasificacion_ramo . "|" . $request->id_u_m_clasificacion_ramo . "|" . $request->tallos_x_ramos . "|" . $request->longitud_ramo . "|" . $request->id_u_m_logitud_ramo;

        $productoVinculados = ProductoYuraVenture::where('id_configuracion_empresa', $request->id_configuracion_empresa)
            ->select('codigo_venture', 'presentacion_yura')->get();
        $arr = [];
        foreach ($productoVinculados as $productoVinculado) {
            $pieza = explode("|", $productoVinculado->presentacion_yura);
            $ids = $pieza[0] . "|" . $pieza[1] . "|" . $pieza[2] . "|" . $pieza[3] . "|" . $pieza[4] . "|" . $pieza[5] . "|" . $pieza[6];
            $arr[] = ["id" => $ids, "codigo_venture" => $productoVinculado->codigo_venture];
        }

        $presentacion_venture = "";
        $codigo_venture = "";
        //$clasificacionRamoEstandar= ClasificacionRamo::where('estandar',1)->first();
        foreach ($arr as $item) {
            if ($item['id'] === $presentacion_pedido_caja) {
                $presentacion_venture = $idPlanta . "|" . $request->id_variedad . "|" . $request->id_clasificacion_ramo . "|" . $request->id_u_m_clasificacion_ramo . "|" . $request->tallos_x_malla . "|" . $request->longitud_ramo . "|" . $request->id_u_m_logitud_ramo;
                $codigo_venture = $item['codigo_venture'];
            }
        }
        //dump($presentacion_venture,$codigo_venture);
        return response()->json([
            'presentacion_venture' => $presentacion_venture,
            'codigo_venture' => $codigo_venture
        ]);
    }

    public function asignaClienteEspecificacion($arr_datos, $id_cliente)
    {
        $estado = false;
        $id_cliente_pedido_especificacion = '';
        $objEspecificacion = new Especificacion;
        $objEspecificacion->estado = 1;
        $objEspecificacion->tipo = 'O';
        if ($objEspecificacion->save()) {
            $modelEspecificacion = Especificacion::all()->last();
            $objEspecificacionEmpaque = new EspecificacionEmpaque;
            $objEspecificacionEmpaque->id_especificacion = $modelEspecificacion->id_especificacion;
            $objEspecificacionEmpaque->id_empaque = Empaque::where([['f_empaque', "T"], ['tipo', 'C']])->first()->id_empaque;
            $objEspecificacionEmpaque->cantidad = 1;
            if ($objEspecificacionEmpaque->save()) {
                $modelEspecificacionEmpaque = EspecificacionEmpaque::all()->last();
                $objDetalleEspecificacionEmpaque = new DetalleEspecificacionEmpaque;
                $objDetalleEspecificacionEmpaque->id_especificacion_empaque = $modelEspecificacionEmpaque->id_especificacion_empaque;
                $objDetalleEspecificacionEmpaque->id_variedad = $arr_datos['variedad'];
                $objDetalleEspecificacionEmpaque->id_clasificacion_ramo = $arr_datos['id_clasificacion_ramo'];//ClasificacionRamo::where('estandar',1)->first()->id_clasificacion_ramo;
                $objDetalleEspecificacionEmpaque->cantidad = $arr_datos['ramos_x_caja'];
                $objDetalleEspecificacionEmpaque->id_empaque_p = Empaque::where([['f_empaque', "T"], ['tipo', 'P']])->first()->id_empaque;;
                $objDetalleEspecificacionEmpaque->tallos_x_ramos = $arr_datos['tallos_x_ramos'];
                $objDetalleEspecificacionEmpaque->longitud_ramo = $arr_datos['longitud_ramo'];
                $objDetalleEspecificacionEmpaque->id_unidad_medida = $arr_datos['unidad_medida'];
                if ($objDetalleEspecificacionEmpaque->save()) {
                    $objClientePedidoEspecificacion = new ClientePedidoEspecificacion;
                    $objClientePedidoEspecificacion->id_cliente = $id_cliente;
                    $objClientePedidoEspecificacion->id_especificacion = $modelEspecificacion->id_especificacion;
                    if ($objClientePedidoEspecificacion->save()) {
                        $modelClientePedidoEspecificacion = ClientePedidoEspecificacion::all()->last();
                        $estado = true;
                        $id_cliente_pedido_especificacion = $modelClientePedidoEspecificacion->id_cliente_pedido_especificacion;
                    }
                } else {
                    Especificacion::destroy($modelEspecificacion->id_especificacion);
                }
            } else {
                Especificacion::destroy($modelEspecificacion->id_especificacion);
            }
        }
        return [
            'estado' => $estado,
            'id_cliente_pedido_especificacion' => $id_cliente_pedido_especificacion
        ];
    }

    public function store_datos_tallos($datos, $id_detalle_pedido)
    {

        $success = false;
        //foreach ($arr_datos as $dataTallo){
        $objDataTallo = new DataTallos;
        $objDataTallo->id_detalle_pedido = $id_detalle_pedido;
        $objDataTallo->mallas = $datos['mallas'];
        $objDataTallo->tallos_x_caja = $datos['tallos_x_caja'];
        $objDataTallo->tallos_x_ramo = $datos['tallos_x_ramo'];
        $objDataTallo->tallos_x_malla = $datos['tallos_x_malla'];
        $objDataTallo->ramos_x_caja = $datos['ramos_x_caja'];
        if ($objDataTallo->save())
            $success = true;
        //}
        return $success;
    }

    public function desglose_pedido(Request $request)
    {

        $pedido = getPedido($request->id_pedido);
        /*$path = explode('/',$request->path())[0];
         if($path === "comprobante"){
             $comprobante = Comprobante::where([
                 ['secuencial',$secuencial],
                 ['tipo_comprobante', '01']
             ])->first();
         }elseif($path=== "pedidos"){
             $comprobante = getPedido($secuencial)->envios[0]->comprobante;
         }*/

        /*if($comprobante == null){
            $data = null;
        }else{
            $img_clave_acceso = null;
            if($comprobante->clave_acceso != null)
                $img_clave_acceso = generateCodeBarGs1128($comprobante->clave_acceso);
            $data = [
                'empresa' => $comprobante->envio->pedido->empresa,
                'secuencial' => $comprobante->secuencial,
                'comprobante' => $comprobante,
                'img_clave_acceso' => $img_clave_acceso
            ];
        }*/

        return view('adminlte.gestion.postcocecha.pedidos.partials.desglose_pedido', [
            'pedido' => $pedido
        ]);
    }
}
