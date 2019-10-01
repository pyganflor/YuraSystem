<?php

namespace yura\Http\Controllers\Proyecciones;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;
use yura\Modelos\Cliente;
use yura\Modelos\Pedido;

class ProyVentaController extends Controller
{
    public  function inicio(Request $request){
        return view('adminlte.gestion.proyecciones.venta.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'text' => ['titulo' => 'Proyecciones', 'subtitulo' => 'ventas por cliente'],
            'clientes' => Cliente::where('estado',1)->get()
        ]);
    }

    public function listarProyecionVentaSemanal(Request $request){
        $semana_desde = Semana::where('codigo', $request->desde)->first();
        $semana_hasta = Semana::where('codigo', $request->hasta)->first();

        if ($semana_desde != '' && $semana_hasta != '') {

            /*$pedidos = Pedido::where('estado',1)
                ->join('detalle_pedido as dp','pedido.id_pedido','dp.id_pedido')
                ->join('cliente');*/

        }

    }
}
