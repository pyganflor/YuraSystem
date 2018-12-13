<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Pedido;
use yura\Modelos\Especificacion;
use yura\Modelos\ClientePedidoEspecificacion;
use yura\Modelos\DetallePedido;
use yura\Modelos\AgenciaCarga;
use Validator;
use DB;

class PedidoController extends Controller
{

    public function listar_pedidos(Request $request){
    return view('adminlte.gestion.postcocecha.pedidos.inicio',
        [
            'idCliente'        => $request->id_cliente,
            'annos'            => DB::table('pedido as p')->select(DB::raw('YEAR(p.fecha_pedido) as anno'))->distinct()->get(),
            'especificaciones' => DB::table('pedido as p')
                ->join('cliente_pedido_especificacion as cpe', 'p.id_cliente','=','cpe.id_cliente')
                ->join('especificacion as esp','cpe.id_especificacion','=','esp.id_especificacion')
                ->where('p.id_cliente',$request->id_cliente)
                ->select('esp.id_especificacion','esp.nombre','cpe.id_cliente_pedido_especificacion')
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
        $busquedaAnno           = $request->has('busquedaAnno') ? $request->busquedaAnno : '';
        $busquedaEspecificacion = $request->has('id_especificaciones') ? $request->id_especificaciones : '';
        $busquedaDesde          = $request->has('desde') ? $request->desde : '';
        $busquedaHasta          = $request->has('hasta') ? $request->hasta : '';

        $listado = DB::table('pedido as p')
            ->join('cliente_pedido_especificacion as cpe', 'p.id_cliente','=','cpe.id_cliente')
            ->join('especificacion as esp','cpe.id_especificacion','=','esp.id_especificacion')
            ->join('detalle_pedido as dp','cpe.id_cliente_pedido_especificacion','=','dp.id_cliente_especificacion' )
            ->where('p.id_cliente',$request->id_cliente)
            ->select('p.*')->distinct();

        if ($request->busquedaAnno != '')
            $listado = $listado->where(DB::raw('YEAR(p.fecha_pedido)'), $busquedaAnno );
        if ($request->id_especificaciones != '')
            $listado = $listado->where('dp.id_cliente_especificacion',$busquedaEspecificacion);

        if ($request->desde != '' && $request->hasta != '')
            $listado = $listado->whereBetween('p.fecha_pedido', [$busquedaDesde,$busquedaHasta]);

        $listado = $listado->orderBy('p.fecha_pedido', 'desc')->simplePaginate(6);
       //dd($listado);
        $datos = [
            'listado' => $listado,
            'idCliente' => $request->id_cliente,
        ];
        return view('adminlte.gestion.postcocecha.pedidos.partials.listado',$datos);

    }

    public function add_pedido(Request $request){
       return view('adminlte.gestion.postcocecha.pedidos.forms.add_pedido',
           [
               'idCliente'   => $request->id_cliente,
               'pedido_fijo' => $request->pedido_fijo,
               'vista'      => $request->vista,
               'clientes' => DB::table('cliente as c')
                   ->join('detalle_cliente as dt','c.id_cliente','=','dt.id_cliente')
                   ->where('dt.estado',1) ->get(),
           ]);
    }

    public function store_pedidos(Request $request){

       //dd($request->all());
        $valida = Validator::make($request->all(), [
            'arrDataPedido'    => 'Array',
            'id_cliente'    => 'required',
        ]);

        if (!$valida->fails()) {
            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                . '</div>';

            empty($request->arrFechas) ? $request->arrFechas = [$request->fecha_de_entrega] : $request->arrFechas;

            foreach($request->arrFechas as $key => $fechas){

                $formatoFecha='';
                if(isset($request->opcion) && $request->opcion != 3){
                    $formato      = explode("/",$fechas);
                    $formatoFecha = $formato[2].'-'.$formato[0].'-'.$formato[1];
                }

                (isset($request->opcion) && $request->opcion != 3)  ? $fechaFormateada = $formatoFecha  : $fechaFormateada = $fechas;

                $objPedido = new Pedido;
                $objPedido->id_cliente   = $request->id_cliente;
                $objPedido->descripcion  = $request->descripcion;
                $objPedido->fecha_pedido = $fechaFormateada;

                if($objPedido->save()) {
                    $model = Pedido::all()->last();
                    foreach ($request->arrDataDetallesPedido as $key => $item) {
                        $objDetallePedido = new DetallePedido;
                        $objDetallePedido->id_cliente_especificacion = $item[1];
                        $objDetallePedido->id_pedido                 = $model->id_pedido;
                        $objDetallePedido->id_agencia_carga          = $item[2];
                        $objDetallePedido->cantidad                  = $item[0];

                        if($objDetallePedido->save()){
                            $success = true;
                            $msg = '<div class="alert alert-success text-center">' .
                                '<p> Se ha guardado el pedido exitosamente</p>'
                                . '</div>';
                            bitacora('pedido|detalle_pedido', $model->id_pedido, 'I', 'Inserción satisfactoria de un nuevo pedido');
                        }else{
                            $deletePedido = Pedido::find($model->id_pedido);
                            $deletePedido->destroy();
                        }
                    }
                }
            }
        }else {
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

    public  function inputs_pedidos(Request $request){

        return view('adminlte.gestion.postcocecha.pedidos.forms.paritals.inputs_dinamicos',
            [
                'especificaciones' => DB::table('cliente_pedido_especificacion as cpe')
                ->join('especificacion as esp', 'cpe.id_especificacion','=','esp.id_especificacion')
                ->where('cpe.id_cliente',$request->id_cliente)->get(),
                'agenciasCarga'    => DB::table('cliente_agenciacarga as cac')
                    ->join('agencia_carga as ac','cac.id_agencia_carga','ac.id_agencia_carga')
                    ->where([
                        ['cac.id_cliente',$request->id_cliente],
                        ['cac.estado',1]
                    ])->get(),
                'cantTr' => $request->cant_tr + 1
            ]);
    }

    public function actualizar_estado_pedido_detalle(Request $request){

        $objDetallePedido = DetallePedido::find($request->id_detalle_pedido);
        $objDetallePedido->estado = $request->estado == 1 ? 0 : 1;

        if($objDetallePedido->save()){
            $model = DetallePedido::all()->last();
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha actualizado el estado del detalle del pedido exitosamente</p>'
                . '</div>';
            bitacora('detalle_pedido', $model->id_detalle_pedido, 'U','Actualización satisfactoria del estado del detalle del pedido');
        }else{
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

    public function cancelar_pedido(Request $request){

        $objPedido = Pedido::find($request->id_pedido);
        $objPedido->estado = $request->estado == 0 ? 1 : 0;

        if($objPedido->save()){
            $model = Pedido::all()->last();
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha cancelado el pedido exitosamente</p>'
                . '</div>';
            bitacora('pedido', $model->id_pedido, 'U', 'Actualizacion satisfactoria del estado de un pedido');
        }else{
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

    public function opcion_pedido_fijo(Request $request){
        return view('adminlte.gestion.postcocecha.pedidos.forms.paritals.inputs_opciones_pedido_fijo',
            ['opcion'=> $request->opcion]);
    }

    public function add_fechas_pedido_fijo_personalizado(Request $request){
        return view('adminlte.gestion.postcocecha.pedidos.forms.paritals.inputs_fechas_pedido_fijo_personalizado',
            ['cant_div' => $request->cant_div]);
    }
}
