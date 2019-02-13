<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\ClientePedidoEspecificacion;
use yura\Modelos\DetalleEspecificacionEmpaque;
use yura\Modelos\DetallePedido;
use yura\Modelos\Especificacion;
use yura\Modelos\Cliente;
use DB;
use yura\Modelos\Submenu;

class EspecificacionController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.gestion.postcocecha.especificacion.incio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Especificaciones', 'subtitulo' => 'módulo de especificaciones'],
                'clientes' => Cliente::join('detalle_cliente as dc','cliente.id_cliente','dc.id_cliente')->where('dc.estado',1)->get()
            ]);
    }

    public function listado_especificaciones(Request $request)
    {
        $busqueda   = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $id_cliente = $request->has('id_cliente') ? $request->id_cliente : '';
        $estado     = $request->has('estado') ? $request->estado : '';
        $tipo       = $request->has('tipo') ? $request->tipo : '';
        $id_cliente = str_replace(' ', '%%', $id_cliente);

        $listado = DB::table('especificacion as e')
            ->join('especificacion_empaque as espemp','e.id_especificacion','espemp.id_especificacion');

        if($busqueda != '')
            $listado->where('e.nombre', 'like', '%' . $busqueda . '%');
        if($id_cliente != '')
            $listado->join('cliente_pedido_especificacion as cpe','e.id_especificacion','cpe.id_especificacion')->join('detalle_cliente as dc','cpe.id_cliente','dc.id_cliente')->where('cpe.id_cliente',$id_cliente)->where('dc.estado',1);

        $listado = $listado->where([
            ['e.tipo',$tipo != '' ? $tipo : 'N'],
            ['e.estado',$estado != '' ? $estado : 1]
        ])->orderBy('e.id_especificacion', 'desc')
            ->select('e.nombre as nombre_especificacicon','e.id_especificacion','e.descripcion','e.tipo','e.estado')
            ->distinct()->paginate(20);
        $datos = [
            'listado' => $listado
        ];
        return view('adminlte.gestion.postcocecha.especificacion.partials.listado', $datos);
    }

    public function form_asignacion_especificacion(Request $request){
        return view('adminlte.gestion.postcocecha.especificacion.form.form_asignar_especificacion',[
            'listado' => Cliente::join('detalle_cliente as dc','cliente.id_cliente','dc.id_cliente')->where('dc.estado',1)->get(),
            'id_especificacion' => $request->id_especificacion,
            'asginacion'  => ClientePedidoEspecificacion::where('id_especificacion',$request->id_especificacion)->select('id_cliente')->get()
        ]);
    }

    public function sotre_asignacion_especificacion(Request $request){
        $msg= '';
        foreach ($request->arrClientes as $cliente){

            $clientePedidoEspecificacion =  DB::table('cliente_pedido_especificacion')
                ->where([
                    ['id_especificacion', $cliente[0]],
                    ['id_cliente',$cliente[1]]
                ])->select('id_cliente_pedido_especificacion')->first();

            /*$existDetallePedido = DetallePedido::where('id_cliente_especificacion',$clientePedidoEspecificacion->id_cliente_pedido_especificacion)->count();
            if($existDetallePedido > 0){
                $detalle_cliente = Cliente::where('cliente.id_cliente',$cliente[1])->join('detalle_cliente as dc', 'cliente.id_cliente','dc.id_cliente')
                    ->where('dc.estado',1)->select('nombre')->first();
                $msg .= '<div class="alert alert-danger text-center">' .
                            '<p> No se le puede quitar esta especificación al cliente '.$detalle_cliente->nombre.', debido a que ya posee pedidos realizados con esta especificación</p>'
                     . '</div>';
            }*/
            if($clientePedidoEspecificacion == null){
                /*$existClienteEspecificacion =  DB::table('cliente_pedido_especificacion')
                    ->where([
                        ['id_especificacion', $cliente[0]],
                        ['id_cliente',$cliente[1]]
                    ])->delete();*/
                $detalle_cliente = Cliente::where('cliente.id_cliente',$cliente[1])->join('detalle_cliente as dc', 'cliente.id_cliente','dc.id_cliente')
                    ->where('dc.estado',1)->select('nombre')->first();
                $objClientePedidoEspecificacion = new ClientePedidoEspecificacion;
                $objClientePedidoEspecificacion->id_cliente        = $cliente[1];
                $objClientePedidoEspecificacion->id_especificacion = $cliente[0];

                if($objClientePedidoEspecificacion->save()){
                    $msg .= '<div class="alert alert-success text-center">' .
                        '<p> Se ha agregado exitosamente la especificación al cliente '.$detalle_cliente->nombre.'</p>'
                        . '</div>';
                }else{
                    $msg .= '<div class="alert alert-danger text-center">' .
                        '<p> hubo un error asignando la especificación al cliente '.$detalle_cliente->nombre.', intente nuevamente</p>'
                        . '</div>';
                }
            }

        }
        return $msg;
    }
}
