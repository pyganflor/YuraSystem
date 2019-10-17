<?php

namespace yura\Http\Controllers\Proyecciones;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;
use yura\Modelos\Cliente;
use yura\Modelos\ProyeccionVentaSemanalReal;
use yura\Modelos\PrecioVariedadCliente;

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
                                'proyeccion' => $proyeccionVentaSemanalReal,
                            ];
                        }
                    }
                }
            }

            return view('adminlte.gestion.proyecciones.venta.partials.listado',[
                'proyeccionVentaSemanalReal' => $data,
                'idVariedad' => $request->id_variedad
            ]);

        }else{ // LA semana no esta programada
            $a ="La semana no esta programada";
        }

    }

    public function storeFactorCliente(Request $request){
        $success = false;
        $msg = '<div class="alert alert-danger text-center">' .
                    '<p> Ha ocurrido un error al guardar el factor de conversión del cliente</p>'
             . '</div>';

        $objCliente = Cliente::find($request->id_cliente);
        $objCliente->factor = $request->factor;

        if($objCliente->save()){
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado el factor de conversión del cliente con éxito </p>'
                  .'</div>';
        }

        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function storeProyeccionVenta(Request $request){
        $success = false;
        $msg = '<div class="alert alert-danger text-center">' .
            '<p> Ha ocurrido un error al guardar la proyección, intente nuevamente</p>'
            . '</div>';

        $objProyeccionVentaSemanalReal = ProyeccionVentaSemanalReal::where([
            ['id_cliente',$request->id_cliente],
            ['id_variedad',$request->id_variedad],
            ['codigo_semana',$request->semana]
        ]);
        
        if($objProyeccionVentaSemanalReal->update([
            'cajas_fisicas' => $request->cajas_fisicas,
            'cajas_equivalentes' => $request->cajas_equivalentes,
            'valor' => substr($request->valor,1,20)
        ])){
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha guardado la proyección con éxito </p>'
                .'</div>';
        }

        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function storePrecioPromedio(Request $request){
        $success = false;
        $msg = '<div class="alert alert-danger text-center">' .
            '<p> Ha ocurrido un error al guardar el precio promedio, intente nuevamente</p>'
            . '</div>';

        $objprecioVariedadCliente = PrecioVariedadCliente::where([
            ['id_cliente',$request->id_cliente],
            ['id_variedad',$request->id_variedad]
        ]);
        $data = $objprecioVariedadCliente->first();
        if(isset($data)){
            $objprecioVariedadCliente->update([
                'precio' => $request->precio_promedio
            ]);
        }else{
            $objprecioVariedadCliente = new PrecioVariedadCliente;
            $objprecioVariedadCliente->id_cliente = $request->id_cliente;
            $objprecioVariedadCliente->id_variedad = $request->id_variedad;
            $objprecioVariedadCliente->precio = $request->precio_promedio;
            $objprecioVariedadCliente->save();
        }

        if($objprecioVariedadCliente){
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha guardado el precio con éxito </p>'
                .'</div>';
        }

        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }


}
