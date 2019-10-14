<?php

namespace yura\Http\Controllers\Proyecciones;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;
use yura\Modelos\Cliente;
use yura\Modelos\ProyeccionVentaSemanalReal;

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

        $desde = isset($request->desde) ? $request->desde : now()->toDateString();
        $hasta = isset($request->hasta) ? $request->hasta : now()->toDateString();

        $semana_desde = Semana::where('codigo', $desde)->first();
        $semana_hasta = Semana::where('codigo', $hasta)->first();


        if (isset($semana_desde) && isset($semana_hasta)) {

            $objProyeccionVentaSemanalReal = ProyeccionVentaSemanalReal::whereBetween('codigo_semana',[$semana_desde->codigo,$semana_hasta->codigo]);
            $objClientes = Cliente::where([
                ['cliente.estado',1],
                ['dc.estado',1]
           ])->join('detalle_cliente as dc','cliente.id_cliente','dc.id_cliente');

            if(isset($request->id_cliente))
                $objClientes->where('cliente.id_cliente',$request->id_cliente);

            if(isset($request->id_variedad))
                $objProyeccionVentaSemanalReal->where('id_variedad',$request->id_variedad);

            $objProyeccionVentaSemanalReal = $objProyeccionVentaSemanalReal->get();
            $objClientes = $objClientes->get();

            $data =[];
            /*foreach($objProyeccionVentaSemanalReal as $proyeccionVentaSemanalReal) {
                $data[$proyeccionVentaSemanalReal->id_cliente][$proyeccionVentaSemanalReal->codigo_semana][] = $proyeccionVentaSemanalReal;
            }*/
            if(isset($request->id_variedad)){
                foreach($objClientes as $x => $cliente){
                    foreach($objProyeccionVentaSemanalReal as $proyeccionVentaSemanalReal){
                        if($cliente->id_cliente === $proyeccionVentaSemanalReal->id_cliente){
                            $data[$cliente->nombre][$proyeccionVentaSemanalReal->codigo_semana] =[
                                'proyeccion' => $proyeccionVentaSemanalReal
                            ];
                        }
                    }
                }
            }

            return view('adminlte.gestion.proyecciones.venta.partials.listado',[
                'proyeccionVentaSemanalReal' => $data
            ]);

        }else{ // LA semana no esta programada
            $a ="La semana no esta programada";
        }

    }
}
