<?php

namespace yura\Http\Controllers\Proyecciones;

use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\ProyeccionVentaSemanalReal;
use yura\Modelos\ResumenSemanaCosecha;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;

class proyResumenTotalController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.gestion.proyecciones.resumen_total.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'text' => ['titulo' => 'Proyecciones', 'subtitulo' => 'resumen total'],
            'hasta' => getSemanaByDate(opDiasFecha('+', 98, date('Y-m-d')))
        ]);
    }

    public function listarProyecionResumenTotal(Request $request){

        $desde = isset($request->desde) ? $request->desde : now()->toDateString();
        $hasta = isset($request->hasta) ? $request->hasta : now()->toDateString();
        $semana_desde = Semana::where('codigo', $desde)->first();
        $semana_hasta = Semana::where('codigo', $hasta)->first();


        if (isset($semana_desde) && isset($semana_hasta)) {

            //$semanaActual = getSemanaByDate(now()->toDateString());
            $semanas=[];
            /*$existSemanaAnterior = Semana::where('codigo',($semana_desde->codigo-1))->select('codigo')->first();
            if(isset($existSemanaAnterior)){
                $semanaAnterior = $semana_desde->codigo-1;
            }else{
                for($x=($semana_desde->codigo-1);$x>0001;$x--){
                    $existSemanaAnterior = Semana::where('codigo',$x)->select('codigo')->first();
                    if(isset($existSemanaAnterior)){
                        $semanaAnterior = $existSemanaAnterior->codigo;
                        break;
                    }
                }
            }
            $semanas[]=$semanaAnterior;*/
            for ($i = $semana_desde->codigo; $i <= $semana_hasta->codigo; $i++) {
                $existSemana = Semana::where('codigo', $i)->select('codigo')->first();
                if (isset($existSemana->codigo))
                    $semanas[] = $existSemana->codigo;
            }

           // $dataCosecha = [];
            $semResumenSemanaCosecha = ResumenSemanaCosecha::whereIn('codigo_semana',$semanas)
                ->select('codigo_semana',
                    //DB::raw('SUM(cajas) as cajas'),
                    //DB::raw('SUM(tallos) as tallos'),
                    DB::raw('SUM(cajas_proyectadas) as cajas_proyectadas'),
                    DB::raw('SUM(tallos_proyectados) as tallos_proyectados'))->groupBy('codigo_semana')->get();
            /*for($y=1;$y<count($semanas); $y++){
                $d= $semResumenSemanaCosecha[$y-1];
                if($semanas[$y] > $semanaActual->codigo){
                    $data=[
                        'cajas'=> round($d->cajas_proyectadas,2),
                        'tallos'=>round($d->tallos_proyectados,2)
                    ];
                }else{
                    $data=[
                        'cajas'=> round($d->cajas,2),
                        'tallos'=>round($d->tallos,2)
                    ];
                }

                $dataCosecha[$semanas[$y]]=$data;
            }
            unset($semanas[0]);*/
            $dataVentas = ProyeccionVentaSemanalReal::whereIn('codigo_semana',$semanas)
                ->select('codigo_semana',
                    DB::raw('SUM(valor) as valor'),
                    DB::raw('SUM(cajas_equivalentes) as cajas_equivalentes'))->groupBy('codigo_semana')->get();

            $success = true;


        }else{ // LA semana no esta programada
            $success = false;
        }


        return view('adminlte.gestion.proyecciones.resumen_total.partials.listado',[
            'success' => $success,
            'semanas'=>$semanas,
            'dataCosecha'=>$semResumenSemanaCosecha,
            'success'=>$success,
            'dataVentas'=>$dataVentas
        ]);



    }
}
