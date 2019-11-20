<?php

namespace yura\Http\Controllers\Indicadores;

use Carbon\Carbon;
use yura\Http\Controllers\Controller;
use yura\Modelos\ResumenSemanaCosecha;
use yura\Modelos\Indicador;
use DB;

class ProyeccionesVenta extends Controller
{
    public function sumCajasFuturas4Semanas(){

        $intervalos = self::intervalosTiempo();

        $dato = ResumenSemanaCosecha::whereBetween('codigo_semana',[$intervalos['primeraSemanaFutura'],$intervalos['cuartSemanaFutura']])
            ->select(DB::Raw('sum(cajas_proyectadas) as cajas'))->first();

        $objInidicardor = Indicador::where('nombre','DP1');
        $objInidicardor->valor = $dato->caja;
        $objInidicardor->save();
    }

    public static function intervalosTiempo(){
        $fechaActual =now()->toDateString();
        return [
            'primeraSemanaFutura' =>getSemanaByDate(Carbon::Parse($fechaActual)->addDays(7))->codigo,
            'cuartSemanaFutura' =>getSemanaByDate(opDiasFecha('+', 28,  $fechaActual))->codigo
        ];
    }
}
