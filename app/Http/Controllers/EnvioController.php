<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\DetallePedido;
use yura\Modelos\AgenciaTransporte;

class EnvioController extends Controller
{
    public function add_envio(Request $request){
        $dataDetallePedido = DetallePedido::where('detalle_pedido.id_pedido',$request->id_pedido);
        return view('adminlte.gestion.postcocecha.envios.forms.form_envio',
            [
                'cantForms'           => $dataDetallePedido->count(),
                'dataDetallesPedidos' => $dataDetallePedido->join('cliente_pedido_especificacion as cpe','detalle_pedido.id_cliente_especificacion','=','cpe.id_cliente_pedido_especificacion')
                                                           ->join('especificacion as e','cpe.id_especificacion','=','e.id_especificacion')
                                                           ->select('detalle_pedido.cantidad','detalle_pedido.id_detalle_pedido','e.nombre')->get()
                ]);
    }

    public function add_form_envio(Request $request){

        return view('adminlte.gestion.postcocecha.envios.forms.inputs_detalles_envio',
            [
                'rows'=>$request->rows,
                'agencia_transporte' => AgenciaTransporte::all(),
                'cantidad'           => $request->cant_pedidos,
                'form'               => $request->id_form
            ]);
    }
}
