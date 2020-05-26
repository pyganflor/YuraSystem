<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Jobs\ProyeccionVentaSemanalUpdate;
use yura\Modelos\Cliente;
use yura\Modelos\Color;
use yura\Modelos\Coloracion;
use yura\Modelos\DetalleEnvio;
use yura\Modelos\DetalleEspecificacionEmpaqueRamosCaja;
use yura\Modelos\DetallePedido;
use yura\Modelos\DetallePedidoDatoExportacion;
use yura\Modelos\Distribucion;
use yura\Modelos\DistribucionColoracion;
use yura\Modelos\Empaque;
use yura\Modelos\Marca;
use yura\Modelos\Marcacion;
use yura\Modelos\MarcacionColoracion;
use yura\Modelos\Pedido;
use DB;
use yura\Modelos\Submenu;
use yura\Modelos\DetalleCliente;
use yura\Modelos\ClientePedidoEspecificacion;
use yura\Modelos\ClienteAgenciaCarga;
use yura\Modelos\Envio;
use Carbon\Carbon;
use yura\Modelos\Especificacion;
use yura\Modelos\AgenciaCarga;
use yura\Modelos\DatosExportacion;
use Validator;

class PedidoVentaController extends Controller
{
    public function listar_pedidos(Request $request)
    {
        return view('adminlte.gestion.postcocecha.pedidos_ventas.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Pedidos', 'subtitulo' => 'módulo de pedidos'],
                'clientes' => DB::table('cliente as c')
                    ->join('detalle_cliente as dc', 'c.id_cliente', '=', 'dc.id_cliente')
                    ->orderBy('nombre','asc')
                    ->where('dc.estado', 1)->get(),
                'annos' => DB::table('pedido as p')->select(DB::raw('YEAR(p.fecha_pedido) as anno'))
                    ->distinct()->get(),
                'empresas' => getConfiguracionEmpresa(null,true)
            ]);
    }

    public function buscar_pedidos(Request $request)
    {
        $busquedaCliente = $request->has('id_cliente') ? $request->id_cliente : '';
        $busquedaAnno = $request->has('anno') ? $request->anno : '';
        $busquedaDesde = $request->has('desde') ? $request->desde : '';
        $busquedaHasta = $request->has('hasta') ? $request->hasta : '';

        $listado = DB::table('pedido as p')
            ->where('p.estado', $request->estado != '' ? $request->estado : 1)
            ->join('cliente_pedido_especificacion as cpe', 'p.id_cliente', '=', 'cpe.id_cliente')
            ->join('especificacion as esp', 'cpe.id_especificacion', '=', 'esp.id_especificacion')
            ->join('detalle_cliente as dc', 'p.id_cliente', '=', 'dc.id_cliente')
            ->join('detalle_pedido as dp', 'p.id_pedido', 'dp.id_pedido')
            ->select('p.*', 'dp.*', 'dc.nombre', 'p.fecha_pedido', 'p.id_cliente', 'dc.id_cliente')->where('dc.estado', 1);

        if ($request->anno != '')
            $listado = $listado->where(DB::raw('YEAR(p.fecha_pedido)'), $busquedaAnno);

        if ($busquedaDesde != '' && $request->hasta != '') {
            $listado = $listado->whereBetween('p.fecha_pedido', [$busquedaDesde, $busquedaHasta]);
            (Carbon::parse($busquedaHasta)->diffInDays($busquedaDesde) > 0)
                ? $a = true
                : $a = false;
        } else {
            $listado = $listado->where('p.fecha_pedido', Carbon::now()->toDateString());
            $a = false;
        }

        if ($request->id_cliente != '')
            $listado = $listado->where('p.id_cliente', $busquedaCliente);

        $listado = $listado->distinct()->orderBy('p.fecha_pedido', 'desc')->simplePaginate(20);

        $datos = [
            'listado' => $listado,
            'idCliente' => $request->id_cliente,
            'columnaFecha' => $a
        ];

        return view('adminlte.gestion.postcocecha.pedidos_ventas.partials.listado', $datos);
    }

    public function cargar_especificaciones(Request $request)
    {
        return [
            'especificaciones' => ClientePedidoEspecificacion::where('id_cliente', $request->id_cliente)
                ->join('especificacion as e', 'cliente_pedido_especificacion.id_especificacion', 'e.id_especificacion')
                ->select('cliente_pedido_especificacion.id_cliente_pedido_especificacion', 'e.nombre')->get(),
            'agencias_carga' => ClienteAgenciaCarga::where('id_cliente', $request->id_cliente)
                ->join('agencia_carga as ac', 'cliente_agenciacarga.id_agencia_carga', '=', 'ac.id_agencia_carga')
                ->select('ac.id_agencia_carga', 'ac.nombre')->get(),
            'iva_cliente' => getCliente($request->id_cliente)->detalle()->tipo_impuesto()['porcentaje']

        ];
    }

    public function add_orden_semanal(Request $request)
    {
        return view('adminlte.gestion.postcocecha.pedidos_ventas.partials.add_orden_semanal', [
            'clientes' => getClientes(),
            'empaques' => Empaque::All()->where('estado', '=', 1)->where('tipo', '=', 'C'),
            //'envolturas' => Empaque::All()->where('estado', '=', 1)->where('tipo', '=', 'E'),
            'presentaciones' => Empaque::All()->where('estado', '=', 1)->where('tipo', '=', 'P'),
            'colores' => Color::All()->where('estado', '=', 1),
        ]);
    }

    public function editar_pedido(Request $request)
    {
        $pedido = Pedido::where([
            ['pedido.id_pedido', $request->id_pedido],
            ['dc.estado', 1]
        ])->join('detalle_pedido as dp', 'pedido.id_pedido', 'dp.id_pedido')
            ->join('detalle_cliente as dc', 'pedido.id_cliente', 'dc.id_cliente')
            ->join('cliente_pedido_especificacion as cpe', 'dp.id_cliente_especificacion', 'cpe.id_cliente_pedido_especificacion')
            ->select('dp.cantidad as cantidad_especificacion', 'dp.precio', 'dp.id_agencia_carga', 'dc.id_cliente', 'pedido.fecha_pedido', 'pedido.descripcion', 'cpe.id_especificacion')->get();

        return [
            'pedido' => $pedido,
            'iva_cliente' => $pedido[0]->cliente->detalle()->tipo_impuesto()['porcentaje']

        ];
    }

    public function duplicar_especificacion(Request $request)
    {
        $empT = [];
        $empTallos = Empaque::where([
            ['f_empaque','T'],
            ['tipo','C']
        ])->get();
        foreach ($empTallos as $empRamo)
            $empT[] = $empRamo;

        $agenciasCarga = AgenciaCarga::where('c_ac.id_cliente', $request->id_cliente)
            ->join('cliente_agenciacarga as c_ac', 'agencia_carga.id_agencia_carga', 'c_ac.id_agencia_carga')->get();
        return view('adminlte.gestion.postcocecha.pedidos.forms.paritals.duplicar_especificacion', [
            'id_especificacion' => $request->id_especificacion,
            'agenciasCarga' => $agenciasCarga,
            'cant_esp' => $request->cant_esp,
            'id_cliente' => $request->id_cliente,
            'datos_exportacion' => DatosExportacion::join('cliente_datoexportacion as cde', 'dato_exportacion.id_dato_exportacion', 'cde.id_dato_exportacion')
                ->where('id_cliente', $request->id_cliente)->get(),
            'emp_tallos' => $empT,
            'tipo_especificacion' => getEspecificacion($request->id_especificacion)->tipo,
            'empaque' => $request->empaque
        ]);
    }

    public function form_duplicar_pedido(Request $request)
    {
        return view('adminlte.gestion.postcocecha.pedidos_ventas.forms.form_duplicar_pedido', [
            'id_pedido' => $request->id_pedido,
            'datos_exportacion' => DatosExportacion::join('cliente_datoexportacion as cde', 'dato_exportacion.id_dato_exportacion', 'cde.id_dato_exportacion')
                ->where('id_cliente', $request->id_cliente)->get(),
            'agenciasCarga' => AgenciaCarga::where('c_ac.id_cliente', $request->id_cliente)
                ->join('cliente_agenciacarga as c_ac', 'agencia_carga.id_agencia_carga', 'c_ac.id_agencia_carga')->get(),

        ]);
    }

    public function store_duplicar_pedido(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'arrFechas' => 'required|Array',
            'id_pedido' => 'required',
        ]);
        $success = false;
        $msg = '<div class="alert alert-danger text-center">' .
            '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
            . '</div>';

        if (!$valida->fails()) {
            $dataPedido = Pedido::find($request->id_pedido);

            foreach ($request->arrFechas as $fecha) {
                $objPedido = new Pedido;
                $objPedido->id_cliente = $dataPedido->id_cliente;
                $objPedido->fecha_pedido = $fecha['fecha'];
                $objPedido->empaquetado = $dataPedido->empaquetado;
                $objPedido->variedad = $dataPedido->variedad;
                $objPedido->tipo_especificacion = $dataPedido->tipo_especificacion;
                $objPedido->id_configuracion_empresa = $dataPedido->id_configuracion_empresa;
                if ($objPedido->save()) {
                    $modelPedido = Pedido::all()->last();
                    $dataDetallePedido = $dataPedido->detalles;

                    bitacora('pedido', $modelPedido->id_pedido, 'I', 'Inserción satisfactoria de un duplicado de pedido');

                    $objEnvio = new Envio;
                    $objEnvio->fecha_envio = $fecha['fecha'];
                    $objEnvio->id_pedido = $modelPedido->id_pedido;
                    if($objEnvio->save()) $modelEnvio = Envio::all()->last();

                    foreach ($dataDetallePedido as $detallePedido) {

                        $objDetallePedido = new DetallePedido;
                        $objDetallePedido->id_cliente_especificacion = $detallePedido->id_cliente_especificacion;
                        $objDetallePedido->id_pedido = $modelPedido->id_pedido;
                        $objDetallePedido->id_agencia_carga = $detallePedido->id_agencia_carga;
                        $objDetallePedido->cantidad = $detallePedido->cantidad;
                        $objDetallePedido->precio = $detallePedido->precio;
                        $objDetallePedido->orden = $detallePedido->orden;

                        $detalleEnvio = new DetalleEnvio;
                        $detalleEnvio->id_envio = $modelEnvio->id_envio;
                        $objClienteEspecificacion = ClientePedidoEspecificacion::find($detallePedido->id_cliente_especificacion);
                        $detalleEnvio->id_especificacion = $objClienteEspecificacion->id_especificacion;
                        $detalleEnvio->cantidad = $detallePedido->cantidad;
                        $detalleEnvio->save();

                        if ($objDetallePedido->save()) {

                            $model_detalle_pedido = DetallePedido::all()->last();

                            foreach ($detallePedido->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp) {
                                foreach ($esp_emp->detalles as $det_esp_emp) {
                                    $ramos_modificado = getRamosXCajaModificado($detallePedido->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque);
                                    if(isset($ramos_modificado)){
                                        $objDetEspEmpRamosCaja= new DetalleEspecificacionEmpaqueRamosCaja;
                                        $objDetEspEmpRamosCaja->id_detalle_pedido= $model_detalle_pedido->id_detalle_pedido;
                                        $objDetEspEmpRamosCaja->id_detalle_especificacionempaque= $det_esp_emp->id_detalle_especificacionempaque;
                                        $objDetEspEmpRamosCaja->cantidad = $ramos_modificado->cantidad;
                                        $objDetEspEmpRamosCaja->fecha_registro = now()->format('Y-m-d H:i:s.v');
                                        $objDetEspEmpRamosCaja->save();
                                    }

                                }


                            }


                            bitacora('detalle_pedido', $model_detalle_pedido->id_detalle_pedido, 'I', 'Inserción satisfactoria del duplicado de un detalle pedio');

                            //foreach ($dataPedido->detalles as $dataDetPed) {
                                foreach ($detallePedido->detalle_pedido_dato_exportacion as $dePedDatExp){
                                    $objDetallePedidoDatoExportacion = new DetallePedidoDatoExportacion;
                                    $objDetallePedidoDatoExportacion->id_detalle_pedido = $model_detalle_pedido->id_detalle_pedido;
                                    $objDetallePedidoDatoExportacion->id_dato_exportacion = $dePedDatExp->id_dato_exportacion;
                                    $objDetallePedidoDatoExportacion->valor = $dePedDatExp->valor;
                                    $objDetallePedidoDatoExportacion->save();
                                }
                            //}

                            if($modelPedido->tipo_especificacion === "T") {
                                foreach ($detallePedido->cliente_especificacion->especificacion->especificacionesEmpaque as $z => $esp_emp){
                                    $dataColoraciones = Coloracion::where([
                                        ['id_detalle_pedido', $detallePedido->id_detalle_pedido],
                                        ['id_especificacion_empaque',$esp_emp->id_especificacion_empaque]
                                    ])->get();
                                    $dataMarcaciones = Marcacion::where([
                                        ['id_detalle_pedido', $detallePedido->id_detalle_pedido],
                                        ['id_especificacion_empaque',$esp_emp->id_especificacion_empaque]
                                    ])->get();

                                    $arr_colores = [];

                                    foreach ($dataColoraciones as $dC) {
                                        $objColoracion = new Coloracion;
                                        $objColoracion->id_color = $dC->id_color;
                                        $objColoracion->id_especificacion_empaque = $dC->id_especificacion_empaque;
                                        $objColoracion->id_detalle_pedido = $model_detalle_pedido->id_detalle_pedido;
                                        $objColoracion->precio = $dC->precio;
                                        if ($objColoracion->save()) {
                                            $model_coloraciones = Coloracion::all()->last();
                                            $arr_colores[] = $model_coloraciones->id_coloracion;
                                        }
                                    }

                                    foreach ($dataMarcaciones as $x => $dM) {
                                        $arr_marcacion_coloracion = [];
                                        $dataMarcacionColoracion = [];
                                        $objMarcacion = new Marcacion;
                                        $objMarcacion->nombre = $dM->nombre;
                                        $objMarcacion->ramos = $dM->ramos;
                                        $objMarcacion->id_detalle_pedido = $model_detalle_pedido->id_detalle_pedido;
                                        $objMarcacion->id_especificacion_empaque = $dM->id_especificacion_empaque;
                                        $objMarcacion->piezas = $dM->piezas;
                                        if ($objMarcacion->save()) {
                                            $model_marcacion = Marcacion::all()->last();
                                            $dataMarcacionColoracion[] = MarcacionColoracion::where('id_marcacion', $dM->id_marcacion)->get();
                                            foreach ($dataMarcacionColoracion as $dMc) {
                                                foreach ($dMc as $y => $mc) {
                                                    $objMarcacionColoracion = new MarcacionColoracion;
                                                    $objMarcacionColoracion->id_marcacion = $model_marcacion->id_marcacion;
                                                    $objMarcacionColoracion->id_coloracion = $arr_colores[$y];
                                                    $objMarcacionColoracion->id_detalle_especificacionempaque = $mc->id_detalle_especificacionempaque;
                                                    $objMarcacionColoracion->cantidad = $mc->cantidad;
                                                    if($objMarcacionColoracion->save())
                                                        $arr_marcacion_coloracion[] = MarcacionColoracion::all()->last();
                                                }
                                            }

                                            $dataDistribucion = Distribucion::where('id_marcacion',$dM->id_marcacion)->get();

                                            //if(isset($dataDistribucion->id_distribucion)){

                                                if($dataDistribucion->count() > 0){
                                                    foreach ($dataDistribucion as $d){
                                                        $objDistribucion = new Distribucion;
                                                        $objDistribucion->id_marcacion = $model_marcacion->id_marcacion;
                                                        $objDistribucion->ramos = $d->ramos;
                                                        $objDistribucion->piezas = $d->piezas;
                                                        $objDistribucion->pos_pieza = $d->pos_pieza;
                                                        if($objDistribucion->save()){
                                                            /*$dataDistribucionColoracion  = DistribucionColoracion::where('id_distribucion',$d->id_distribucion)->get();
                                                            $model_distribucion = Distribucion::All()->last();
                                                            foreach ($dataDistribucionColoracion as $z => $dC) {
                                                                $objDistribucionColoracion = new DistribucionColoracion;
                                                                $objDistribucionColoracion->id_distribucion = $model_distribucion->id_distribucion;
                                                                $objDistribucionColoracion->id_marcacion_coloracion = $arr_marcacion_coloracion[$z]->id_marcacion_coloracion;
                                                                $objDistribucionColoracion->cantidad = $dC->cantidad;
                                                                $objDistribucionColoracion->save();
                                                            }*/
                                                        }
                                                    }
                                                }
                                           // }
                                        }
                                    }
                                }
                            }
                            $success = true;
                            $msg = '<div class="alert alert-success text-center">' .
                                '<p> Se ha duplicado el pedido exitosamente</p>'
                                . '</div>';
                        }
                    }
                }
            }
            if(count($request->arrFechas) >0 ){
                //$semanaInicio = getSemanaByDate($request->arrFechas[0]['fecha'])->codigo;
                //$semanafin = getSemanaByDate($request->arrFechas[count($request->arrFechas)-1]['fecha'])->codigo;
                //ProyeccionVentaSemanalUpdate::dispatch($semanaInicio,$semanafin,0,$dataPedido->id_cliente)->onQueue('update_venta_semanal_real');
            }

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

    public function empaquetar_pedido(Request $request){

        $pedido = Pedido::find($request->id_pedido);
        $pedido->update([
            'variedad' => '',
            'empaquetado' => true
        ]);
        if ($pedido) {
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha empaquetado el pedido exitosamente</p>'
                . '</div>';
            bitacora('pedido', $request->id_pedido, 'U', 'Pedido empaquetado con éxito');
        } else {
            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p> Ha ocurrido un problema al empaquetar el pedido, intente nuevamente</p>'
                . '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

}
