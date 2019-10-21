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
        $top = isset($request->top) ? $request->top : 10;

        if (isset($semana_desde) && isset($semana_hasta)) {

            $objProyeccionVentaSemanalReal = ProyeccionVentaSemanalReal::whereBetween('codigo_semana',[$semana_desde->codigo,$semana_hasta->codigo]);

            if(isset($request->id_cliente))
                $objProyeccionVentaSemanalReal->where('cliente.id_cliente',$request->id_cliente);

            if(isset($request->id_variedad))
                $objProyeccionVentaSemanalReal->where('id_variedad',$request->id_variedad);

            $objProyeccionVentaSemanalReal = $objProyeccionVentaSemanalReal->get();

            $arrProyeccionVentaSemanalReal =[];
            foreach($objProyeccionVentaSemanalReal as $proyeccionVentaSemanalReal){
                $arrProyeccionVentaSemanalReal[$proyeccionVentaSemanalReal->id_cliente][$proyeccionVentaSemanalReal->codigo_semana] =[
                    'cajas_fisicas' => $proyeccionVentaSemanalReal->cajas_fisicas,
                    'cajas_equivalentes' => $proyeccionVentaSemanalReal->cajas_equivalentes,
                    'valor' => $proyeccionVentaSemanalReal->valor,

                ];
            }

            $totales_x_cliente=[];
            foreach($arrProyeccionVentaSemanalReal as $idCliente => $cliente){
                $cf=0;
                $ce=0;
                $v =0;
                foreach ($cliente as $semana => $item) {
                    $totales_x_cliente[$idCliente] = [
                        'cajas_fisicas_totales'=>$cf+=$item['cajas_fisicas'],
                        'cajas_equivalentes_totales'=>$ce+=$item['cajas_equivalentes'],
                        'valor_total' => $v+=$item['valor']
                    ];
                }
            }

            $data = [];
            foreach($arrProyeccionVentaSemanalReal as $idCliente => $cliente){
                $data[$idCliente] = [
                    'semanas'=>$cliente,
                    'cajas_fisicas_totales'=>$totales_x_cliente[$idCliente]['cajas_fisicas_totales'],
                    'cajas_equivalentes_totales'=>$totales_x_cliente[$idCliente]['cajas_equivalentes_totales'],
                    'valor_total'=>$totales_x_cliente[$idCliente]['valor_total'],
                ];

            }
            $data = collect($data);

            switch ($request->criterio){
                case 'CF':
                    $data = $data->sortByDesc('cajas_fisicas_totales');
                    break;
                case 'CE':
                    $data = $data->sortByDesc('cajas_equivalentes_totales');
                    break;
                default:
                    $data = $data->sortByDesc('valor_total'); ;
                    break;
            }

            return view('adminlte.gestion.proyecciones.venta.partials.listado',[
                'proyeccionVentaSemanalReal' => $data->take($top),
                'idVariedad' => $request->id_variedad,
                'semanas'=>$data->values()[0]['semanas']
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
