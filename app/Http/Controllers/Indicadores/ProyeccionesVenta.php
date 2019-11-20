<?php

namespace yura\Http\Controllers\Indicadores;

use Carbon\Carbon;
use yura\Http\Controllers\Controller;
use yura\Modelos\ProyeccionVentaSemanalReal;
use yura\Modelos\ResumenSemanaCosecha;
use yura\Modelos\Indicador;
use DB;

class ProyeccionesVenta extends Controller
{
    public static function sumCajasFuturas4Semanas(){
        $intervalos = self::intervalosTiempo();
        $dato = ResumenSemanaCosecha::whereBetween('codigo_semana',[$intervalos['primeraSemanaFutura'],$intervalos['cuartSemanaFutura']])
            ->select(DB::Raw('sum(cajas_proyectadas) as cajas'))->first();

        $objInidicardor = Indicador::where('nombre','DP1');
        $objInidicardor->update(['valor'=>number_format($dato->cajas,2,".","")]);
    }

    public static function sumTallosFuturos4Semanas(){
        $intervalos = self::intervalosTiempo();
        $dato = ResumenSemanaCosecha::whereBetween('codigo_semana',[$intervalos['primeraSemanaFutura'],$intervalos['cuartSemanaFutura']])
            ->select(DB::Raw('sum(tallos_proyectados) as tallos'))->first();
        $objInidicardor = Indicador::where('nombre','DP2');
        $objInidicardor->update(['valor'=>number_format($dato->tallos,2,".","")]);
    }

    public static function sumCajasVendidas(){
        $intervalos = self::intervalosTiempo();
        $dato = ProyeccionVentaSemanalReal::whereBetween('codigo_semana',[$intervalos['primeraSemanaFutura'],$intervalos['cuartSemanaFutura']])
            ->select(DB::Raw('sum(cajas_equivalentes) as cajas'))->first();
        $objInidicardor = Indicador::where('nombre','DP3');
        $objInidicardor->update(['valor'=>number_format($dato->cajas,2,".","")]);
    }


    public static function sumDineroGeneradoVentas(){
        $intervalos = self::intervalosTiempo();
        $dato = ProyeccionVentaSemanalReal::whereBetween('codigo_semana',[$intervalos['primeraSemanaFutura'],$intervalos['cuartSemanaFutura']])
            ->select(DB::Raw('sum(valor) as valor'))->first();
        $objInidicardor = Indicador::where('nombre','DP3');
        $objInidicardor->update(['valor'=>number_format($dato->valor,2,".","")]);
    }


    public static function intervalosTiempo(){
        $fechaActual =now()->toDateString();
        return [
            'primeraSemanaFutura' =>getSemanaByDate(Carbon::Parse($fechaActual)->addDays(7))->codigo,
            'cuartSemanaFutura' =>getSemanaByDate(opDiasFecha('+', 28,  $fechaActual))->codigo
        ];
    }
}
