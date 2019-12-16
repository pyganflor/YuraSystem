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

            $semanaActual = getSemanaByDate(now()->toDateString());
            $semanas=[];
            for ($i = $semana_desde; $i <= $semana_hasta; $i++) {
                $existSemana = Semana::where('codigo', $i)->first();
                if (isset($existSemana->codigo)) {
                    $semanas[] = $existSemana;
                }
            }
            $dataGeneral = [];
            $semResumenSemanaCosecha = ResumenSemanaCosecha::whereIn('codigo_semana',$semanas)
                ->select('codigo_semana',
                    DB::raw('SUM(cajas) as cajas'),
                    DB::raw('SUM(cajas_proyectadas) as cajas_proyectadas'),
                    DB::raw('SUM(tallos) as tallos'),
                    DB::raw('SUM(tallos_proyectados) as tallos_proyectados'))->groupBy('codigo_semana')->get();

            dd($semResumenSemanaCosecha);
           /* $objResumenSemanaCosecha = ResumenSemanaCosecha::where([
                ['codigo_semana',$this->codigo-1]
            ])->select('cajas_proyectadas','cajas')->first();

            if(isset($objResumenSemanaCosecha)){
                if($this->codigo > $semanaActual->codigo){
                    $cajasProyectadas = $objResumenSemanaCosecha->cajas_proyectadas;
                }else{
                    $cajasProyectadas = $objResumenSemanaCosecha->cajas;
                }
            }else{
                for($x=$this->codigo;$x>0001;$x--){
                    $objResumenSemanaCosecha = ResumenSemanaCosecha::where([
                        ['id_variedad',$idVariedad],
                        ['codigo_semana',$x-1]
                    ])->select('cajas_proyectadas','codigo_semana')->first();
                    if(isset($objResumenSemanaCosecha)){
                        $cajasProyectadas = $objResumenSemanaCosecha->cajas_proyectadas;
                        break;
                    }else{
                        $cajasProyectadas=0;
                    }
                }
            }

            return $cajasProyectadas;*/


            $success = true;


        }else{ // LA semana no esta programada
            $success = false;
        }


        return view('adminlte.gestion.proyecciones.resmen_total.partials.listado',[
           'success' => $success
        ]);



    }
}
