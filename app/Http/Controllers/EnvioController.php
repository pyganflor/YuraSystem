<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\DetallePedido;
use yura\Modelos\AgenciaTransporte;
<<<<<<< HEAD
use yura\Modelos\Envio;
use yura\Modelos\DetalleEnvioio;
=======
>>>>>>> f7d939a64537592b1e24eedf8cf21d3e9742e791

class EnvioController extends Controller
{
    public function add_envio(Request $request){
        $dataDetallePedido = DetallePedido::where('detalle_pedido.id_pedido',$request->id_pedido);
        return view('adminlte.gestion.postcocecha.envios.forms.form_envio',
            [
                'cantForms'           => $dataDetallePedido->count(),
                'dataDetallesPedidos' => $dataDetallePedido->join('cliente_pedido_especificacion as cpe','detalle_pedido.id_cliente_especificacion','=','cpe.id_cliente_pedido_especificacion')
                                                           ->join('especificacion as e','cpe.id_especificacion','=','e.id_especificacion')
<<<<<<< HEAD
                                                           ->select('detalle_pedido.cantidad','detalle_pedido.id_detalle_pedido','e.nombre','cpe.id_especificacion','cpe.id_cliente')->get()
=======
                                                           ->select('detalle_pedido.cantidad','detalle_pedido.id_detalle_pedido','e.nombre')->get()
>>>>>>> f7d939a64537592b1e24eedf8cf21d3e9742e791
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
<<<<<<< HEAD

    public function store_envio(Request $request){
        dd($request->all());
    }
=======
>>>>>>> f7d939a64537592b1e24eedf8cf21d3e9742e791
}
