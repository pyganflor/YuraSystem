<?php

namespace yura\Http\Controllers\Proyecciones;

use Carbon\Carbon;
use Couchbase\Exception;
use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\HistoricoVentas;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;
use yura\Modelos\Cliente;
use yura\Modelos\ProyeccionVentaSemanalReal;
use yura\Modelos\PrecioVariedadCliente;
use DB;

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

            $objHistoricoVentas = HistoricoVentas::where('id_variedad',$request->id_variedad);

            if(isset($request->id_cliente))
                $objHistoricoVentas->where('id_cliente',$request->id_cliente);

            $fechaActual = now();
            $mesActual = Carbon::parse($fechaActual)->format('m');
            $annoActual = Carbon::parse($fechaActual)->format('Y');

            $fechaAnnoAnterior = Carbon::parse($fechaActual)->subYear(1);
            $mesAnterior = Carbon::parse($fechaAnnoAnterior)->format('m');
            $annoAnterior = Carbon::parse($fechaAnnoAnterior)->format('Y');

            $objHistoricoVentas = $objHistoricoVentas->where([
                ['mes','>=',$mesAnterior],
                ['mes','<=',12],
                ['anno',$annoAnterior],
            ])->orWhere([
                ['mes','>=','01'],
                ['mes','<=',$mesActual],
                ['anno',$annoActual],
            ])->where('id_variedad',$request->id_variedad)
            ->groupBy('id_cliente')->select('id_cliente','desecho',
                    DB::raw('SUM(cajas_fisicas) as cajas_fisicas_totales'),
                    DB::raw('SUM(cajas_equivalentes) as cajas_equivalentes_totales'),
                    DB::raw('SUM(valor) as valor_total')
            )->take($top);

            if(isset($request->id_cliente))
                $objHistoricoVentas->where('id_cliente',$request->id_cliente);

            switch ($request->criterio) {
                case 'CF':
                    $data = $objHistoricoVentas->orderBy('cajas_fisicas_totales','desc');
                    break;
                case 'CE':
                    $data = $objHistoricoVentas->orderBy('cajas_equivalentes_totales','desc');
                    break;
                default:
                    $data = $objHistoricoVentas->orderBy('valor_total','desc');
                    break;
            }

            $objHistoricoVentas = $data->get();

            $idsClientes =[];
            foreach ($objHistoricoVentas as $cliente) {
                $idsClientes[]= $cliente->id_cliente;
            }

            $proyeccionVentaSemanalRealCliente=[];
            foreach ($idsClientes as $idCliente) {
                $objProyeccionVentaSemanalReal = ProyeccionVentaSemanalReal::whereBetween('codigo_semana',[$semana_desde->codigo,$semana_hasta->codigo])
                ->where([
                    ['id_cliente',$idCliente],
                    ['id_variedad',$request->id_variedad]
                ])->orderBy('codigo_semana','asc')->get();
                foreach ($objProyeccionVentaSemanalReal as $item) {
                    $proyeccionVentaSemanalRealCliente[]=$item;
                }
            }

            $arrProyeccionVentaSemanalReal =[];
           foreach($proyeccionVentaSemanalRealCliente as $proyeccionVentaSemanalReal){
                $arrProyeccionVentaSemanalReal[$proyeccionVentaSemanalReal->id_cliente][$proyeccionVentaSemanalReal->codigo_semana] =[
                    'cajas_fisicas' => $proyeccionVentaSemanalReal->cajas_fisicas,
                    'cajas_equivalentes' => $proyeccionVentaSemanalReal->cajas_equivalentes,
                    'valor' => $proyeccionVentaSemanalReal->valor,
                    'cajas_fisicas_anno_anterior'=>$proyeccionVentaSemanalReal->cajas_fisicas_anno_anterior
                ];
            }

           $data = [];
           foreach($arrProyeccionVentaSemanalReal as $idCliente => $cliente){
                $data[$idCliente] = [
                    'semanas'=>$cliente,
                    'cajas_fisicas_totales'=>$arrProyeccionVentaSemanalReal[$idCliente],
                    'cajas_equivalentes_totales'=>$arrProyeccionVentaSemanalReal[$idCliente],
                    'valor_total'=>$arrProyeccionVentaSemanalReal[$idCliente],
                ];
            }

           $clientes= Cliente::where('estado',1)->count();

           return view('adminlte.gestion.proyecciones.venta.partials.listado',[
               'proyeccionVentaSemanalReal' => $data,
               'idVariedad' => $request->id_variedad,
               'semanas'=>isset(array_values($data)[0]['semanas']) ? array_values($data)[0]['semanas'] : [],
               'otros' => $top >= $clientes ? false : true,
               'clientes' => $clientes
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
        //dd($request->all());
        $objProyeccionVentaSemanalReal = ProyeccionVentaSemanalReal::where([
            ['id_cliente',$request->id_cliente],
            ['id_variedad',$request->id_variedad],
            ['codigo_semana',$request->semana]
        ]);

        try{
            $objProyeccionVentaSemanalReal->update([
                'cajas_fisicas' => $request->cajas_fisicas,
                'cajas_equivalentes' => $request->cajas_equivalentes,
                'valor' => substr($request->valor,1,20)
            ]);
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha guardado la proyección con éxito </p>'
                .'</div>';
        }catch (\Exception $e){
            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p>  Ha ocurrido el siguiente error al intentar guardar la información <br />"'.$e->getMessage().'"<br /> Comuníquelo al área de sistemas</p>'
                .'</div>';
        }

        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function storeProyeccionDesecho(Request $request){

        $objProyeccionVentaSemanalReal = ProyeccionVentaSemanalReal::where([
            ['id_variedad',$request->id_variedad],
            ['codigo_semana',$request->semana]
        ]);

        try{
            $objProyeccionVentaSemanalReal->update(['desecho' => $request->desecho]);
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha guardado el desecho con éxito </p>'
                .'</div>';
        }catch (\Exception $e){
            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p>  Ha ocurrido el siguiente error al intentar guardar la información <br />"'.$e->getMessage().'"<br /> Comuníquelo al área de sistemas</p>'
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
