<?php

namespace yura\Http\Controllers\Proyecciones;

use Carbon\Carbon;
use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Jobs\ProyeccionVentaSemanalUpdate;
use yura\Modelos\HistoricoVentas;
use yura\Modelos\ResumenSaldoProyeccionVentaSemanal;
use yura\Modelos\ResumenSemanaCosecha;
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
            'clientes' => Cliente::where('estado',1)->get(),
            'hasta' => getSemanaByDate(opDiasFecha('+', 70, date('Y-m-d')))
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
            ->groupBy('id_cliente')->select('id_cliente',
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
                    //'cajas_fisicas_anno_anterior'=>$proyeccionVentaSemanalReal->cajas_fisicas_anno_anterior
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
               'clientes' => $clientes,
               'cantProyeccionVentaSemanalReal' => count($data),
               'semanaActual'=> getSemanaByDate(now()->toDateString())->codigo,
               'ramosxCajaEmpresa'=>getConfiguracionEmpresa()->ramos_x_caja
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
       // dd($request->all());
        try{
            if(isset($request->semanas) && count($request->semanas)>0){
                if(isset($request->clientes)){
                    foreach($request->clientes as $cliente){
                        $valor = substr($cliente['valor'],1,20);
                        $objProyeccionVentaSemanalReal = ProyeccionVentaSemanalReal::where([
                            ['id_cliente',$cliente['id_cliente']],
                            ['id_variedad',$request->id_variedad],
                            ['codigo_semana',$cliente['semana']]
                        ]);
                        $objProyeccionVentaSemanalReal->update([
                            'cajas_fisicas' => $cliente['cajas_fisicas'],
                            'cajas_equivalentes' => $cliente['cajas_equivalentes'],
                            'valor' => round($valor,2)
                        ]);
                    }
                }

                if(isset($request->desecho)) {
                    foreach ($request->desecho as $desecho) {
                        $objResumenCosecha = ResumenSemanaCosecha::where([
                            ['id_variedad', $request->id_variedad],
                            ['codigo_semana', $desecho['semana']]
                        ]);
                        $objResumenCosecha->update(['desecho' => $desecho['cantidad']]);
                    }
                }

                if(isset($request->saldos)) {
                    foreach ($request->saldos as $saldo) {
                        $objResumenSaldos = ResumenSaldoProyeccionVentaSemanal::where([
                            ['id_variedad', $request->id_variedad],
                            ['codigo_semana', $saldo['semana']]
                        ]);
                        $objResumenSaldos->update([
                            'saldo_inicial' => $saldo['inicial'],
                            'saldo_final' => $saldo['final']
                        ]);
                    }
                }
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado la proyección con éxito </p>'
                    .'</div>';

                $ultimaSemana = Semana::orderBy('codigo','desc')->select('codigo')->first();
                //ProyeccionVentaSemanalUpdate::dispatch($request->semanas[(count($request->semanas)-1)]['semana'],$ultimaSemana->codigo,$request->id_variedad,0)->onQueue('update_venta_semanal_real');

            }else{
                $success = false;
                $msg = '<div class="alert alert-danger text-center">' .
                    '<p>  Debe elegir al menos una semana para programar </p>'
                    .'</div>';
            }
            /*else{
                foreach($request->clientes as $cliente){
                    $valor = substr($cliente['valor'],1,20);
                    $objProyeccionVentaSemanalReal = ProyeccionVentaSemanalReal::where([
                        ['id_cliente',$cliente['id_cliente']],
                        ['id_variedad',$request->id_variedad],
                        ['codigo_semana',$cliente['semana']]
                    ]);
                    $objProyeccionVentaSemanalReal->update([
                        'cajas_fisicas' => $cliente['cajas_fisicas'],
                        'cajas_equivalentes' => $cliente['cajas_equivalentes'],
                        'valor' => round($valor,2)
                    ]);
                }
            }*/

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

        $objProyeccionVentaSemanalReal = ResumenSemanaCosecha::where([
            ['id_variedad',$request->id_variedad],
            ['codigo_semana',$request->semana]
        ]);

        try{
            $objProyeccionVentaSemanalReal->update(['desecho' => isset($request->desecho) ? $request->desecho : 0]);
            //UpdateSaldosProyVentaSemanal::dispatch($request->semana, $request->id_variedad)->onQueue('update_saldos_proy_venta_semanal');
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


