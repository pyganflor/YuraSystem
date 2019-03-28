<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\ClienteDatoExportacion;
use yura\Modelos\DatosExportacion;
use yura\Modelos\DetalleEnvio;
use yura\Modelos\Pedido;
use yura\Modelos\DetallePedido;
use yura\Modelos\Envio;
use yura\Modelos\DetallePedidoDatoExportacion;
use Validator;
use DB;

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
                    ->where('dt.estado', 1)->orderBy('dt.nombre','asc')->get(),
                'id_pedido' => $request->id_pedido,
                'datos_exportacion' => ClienteDatoExportacion::where('id_cliente',$request->id_cliente)->get()

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

                if(!empty($request->id_pedido)){
                    $dataEnvio = Envio::where('id_pedido',$request->id_pedido)->select('id_envio')->first();
                    if($dataEnvio !== null){
                        DetalleEnvio::where('id_envio',$dataEnvio->id_envio)->delete();
                        Envio::where('id_pedido',$request->id_pedido)->delete();
                    }
                    foreach (getPedido($request->id_pedido)->detalles as $det_ped){
                        DetallePedidoDatoExportacion::where('id_detalle_pedido',$det_ped->id_detalle_pedido)->delete();
                    }
                    DetallePedido::where('id_pedido',$request->id_pedido)->delete();
                    Pedido::destroy($request->id_pedido);
                }

                $objPedido = new Pedido;
                $objPedido->id_cliente = $request->id_cliente;
                $objPedido->descripcion = $request->descripcion;
                $objPedido->fecha_pedido = $fechaFormateada;
                $objPedido->variedad = substr(implode("|",array_unique($request->variedades)), 0, -1);

                if ($objPedido->save()) {
                    $model = Pedido::all()->last();
                    foreach ($request->arrDataDetallesPedido as $key => $item) {
                        $objDetallePedido = new DetallePedido;
                        $objDetallePedido->id_cliente_especificacion = $item['id_cliente_pedido_especificacion'];
                        $objDetallePedido->id_pedido = $model->id_pedido;
                        $objDetallePedido->id_agencia_carga = $item['id_agencia_carga'];
                        $objDetallePedido->cantidad = $item['cantidad'];
                        $objDetallePedido->precio = substr($item['precio'], 0, -1);
                        if ($objDetallePedido->save()) {
                            $modelDetallePedido = DetallePedido::all()->last();
                            if($request->arrDatosExportacion!=''){
                                foreach ($request->arrDatosExportacion[$key] as $de){
                                    if( $de['valor'] != null){
                                        $objDetallePedidoDatoExportacion = new DetallePedidoDatoExportacion;
                                        $objDetallePedidoDatoExportacion->id_detalle_pedido = $modelDetallePedido->id_detalle_pedido;
                                        $objDetallePedidoDatoExportacion->id_dato_exportacion = $de['id_dato_exportacion'];
                                        $objDetallePedidoDatoExportacion->valor = $de['valor'];
                                        $objDetallePedidoDatoExportacion->save();
                                    }
                                }
                            }
                            $success = true;
                            $request->crear_envio == 'true'
                                ? $text =  "y se ha creado el envío"
                                : $text = "";

                            $msg = '<div class="alert alert-success text-center">' .
                                '<p> Se ha guardado el pedido '.$text.' exitosamente</p>'
                                . '</div>';
                            bitacora('pedido|detalle_pedido', $model->id_pedido, 'I', 'Inserción satisfactoria de un nuevo pedido');
                        } else {
                            Pedido::destroy($model->id_pedido);
                            $success = false;
                            $msg = '<div class="alert alert-danger text-center">' .
                                '<p> Hubo un error al guardar la información en el sistema</p>'
                                . '</div>';
                        }
                    }
                }
            }
            if($success && $request->crear_envio == "true"){
                $objEnvio = new Envio;
                $objEnvio->fecha_envio = $request->fecha_envio;
                $objEnvio->id_pedido = $model->id_pedido;
                if($objEnvio->save()){
                    $modelEnvio = Envio::all()->last();
                    $dataDetallePedido = DetallePedido::where('id_pedido',$model->id_pedido)
                        ->join('cliente_pedido_especificacion as cpe','detalle_pedido.id_cliente_especificacion','cpe.id_cliente_pedido_especificacion')
                        ->select('cpe.id_especificacion','detalle_pedido.cantidad')->get();
                    foreach ($dataDetallePedido as $detallePeido){
                        $objDetalleEnvio = new DetalleEnvio;
                        $objDetalleEnvio->id_envio = $modelEnvio->id_envio;
                        $objDetalleEnvio->id_especificacion = $detallePeido->id_especificacion;
                        $objDetalleEnvio->cantidad = $detallePeido->cantidad;
                        $objDetalleEnvio->save();
                    }
                }
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

    public function inputs_pedidos(Request $request)
    {
        $tipo_especificacion = DetallePedido::where('id_pedido',$request->id_pedido)
            ->join('cliente_pedido_especificacion as cpe','detalle_pedido.id_cliente_especificacion','cpe.id_cliente_pedido_especificacion')
            ->join('especificacion as esp','cpe.id_especificacion','esp.id_especificacion')->select('tipo')->first();

        $data_especificaciones = DB::table('cliente_pedido_especificacion as cpe')
            ->join('especificacion as esp', 'cpe.id_especificacion', '=', 'esp.id_especificacion')
            ->where([
                ['cpe.id_cliente', $request->id_cliente],
                ['esp.tipo',isset($tipo_especificacion->tipo) ? $tipo_especificacion->tipo : "N"],
                ['esp.estado',1]
            ]);

        if(isset($tipo_especificacion->tipo) && $tipo_especificacion->tipo == "O"){
            $data_especificaciones = $data_especificaciones->join('detalle_pedido as dp','cpe.id_cliente_pedido_especificacion','dp.id_cliente_especificacion')
                ->where('id_pedido',$request->id_pedido);
        }

        return view('adminlte.gestion.postcocecha.pedidos.forms.paritals.inputs_dinamicos',
            [
                'especificaciones' => $data_especificaciones->orderBy('id_cliente_pedido_especificacion','asc')->get(),
                'agenciasCarga' => DB::table('cliente_agenciacarga as cac')
                    ->join('agencia_carga as ac', 'cac.id_agencia_carga', 'ac.id_agencia_carga')
                    ->where([
                        ['cac.id_cliente', $request->id_cliente],
                        ['cac.estado', 1]
                    ])->get(),
                'datos_exportacion' => DatosExportacion::join('cliente_datoexportacion as cde','dato_exportacion.id_dato_exportacion','cde.id_dato_exportacion')
                                    ->where('id_cliente',$request->id_cliente)->get(),
            ]);
    }

    public function inputs_pedidos_edit(Request $request){
        $tipo_especificacion = DetallePedido::where('id_pedido',$request->id_pedido)
            ->join('cliente_pedido_especificacion as cpe','detalle_pedido.id_cliente_especificacion','cpe.id_cliente_pedido_especificacion')
            ->join('especificacion as esp','cpe.id_especificacion','esp.id_especificacion')->select('tipo')->first();

        $esp_creadas =[];
        foreach(getPedido($request->id_pedido)->detalles as $det_ped){
           $esp_creadas[] = $det_ped->cliente_especificacion->especificacion->id_especificacion;
        }

        $data_especificaciones = DB::table('cliente_pedido_especificacion as cpe')
            ->join('especificacion as esp', 'cpe.id_especificacion', '=', 'esp.id_especificacion')
            ->where([
                ['cpe.id_cliente', $request->id_cliente],
                ['esp.tipo',isset($tipo_especificacion->tipo) ? $tipo_especificacion->tipo : "N"],
                ['esp.estado',1]
            ])->whereNotIn('esp.id_especificacion',$esp_creadas);

        if(isset($tipo_especificacion->tipo) && $tipo_especificacion->tipo == "O"){
            $data_especificaciones = $data_especificaciones->join('detalle_pedido as dp','cpe.id_cliente_pedido_especificacion','dp.id_cliente_especificacion')
                ->where('id_pedido',$request->id_pedido);
        }
        return view('adminlte.gestion.postcocecha.pedidos.forms.paritals.inputs_dinamicos_edit',
            [
                'id_pedido' => $request->id_pedido,
                'datos_exportacion' => DatosExportacion::join('cliente_datoexportacion as cde','dato_exportacion.id_dato_exportacion','cde.id_dato_exportacion')
                    ->where('id_cliente',$request->id_cliente)->get(),
                'agenciasCarga' => DB::table('cliente_agenciacarga as cac')
                    ->join('agencia_carga as ac', 'cac.id_agencia_carga', 'ac.id_agencia_carga')
                    ->where([
                        ['cac.id_cliente', $request->id_cliente],
                        ['cac.estado', 1]
                    ])->get(),
                'especificaciones' => $data_especificaciones->orderBy('id_cliente_pedido_especificacion','asc')->get(),

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
        $objPedido = Pedido::find($request->id_pedido);
        $objPedido->estado = $request->estado == 0 ? 1 : 0;

        if ($objPedido->save()) {
            $model = Pedido::all()->last();
            $success = true;
            $objPedido->estado == 0 ? $palabra = 'cancelado' : $palabra = 'activado';
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha ' . $palabra . ' el pedido exitosamente</p>'
                . '</div>';
            bitacora('pedido', $model->id_pedido, 'U', 'Actualizacion satisfactoria del estado de un pedido');
        } else {
            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
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
}
