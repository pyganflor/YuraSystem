<?php

namespace yura\Http\Controllers\Indicadores;

use Carbon\Carbon;
use yura\Http\Controllers\Controller;
use yura\Modelos\Pedido;
use yura\Modelos\ProyeccionVentaSemanalReal;
use yura\Modelos\ResumenSemanaCosecha;
use yura\Modelos\Indicador;
use DB;

class Proyecciones extends Controller
{
    public static function sumCajasFuturas4Semanas(){
        $intervalos = self::intervalosTiempo();
        $dato = ResumenSemanaCosecha::whereBetween('codigo_semana',[$intervalos['primeraSemanaFutura'],$intervalos['cuartaSemanaFutura']])
            ->select(DB::Raw('sum(cajas_proyectadas) as cajas'))->first();

        $objInidicardor = Indicador::where('nombre','DP1');
        $objInidicardor->update(['valor'=>number_format($dato->cajas,2,".","")]);
    }

    public static function sumTallosFuturos4Semanas(){
        $intervalos = self::intervalosTiempo();
        $dato = ResumenSemanaCosecha::whereBetween('codigo_semana',[$intervalos['primeraSemanaFutura'],$intervalos['cuartaSemanaFutura']])
            ->select(DB::Raw('sum(tallos_proyectados) as tallos'))->first();
        $objInidicardor = Indicador::where('nombre','DP2');
        $objInidicardor->update(['valor'=>number_format($dato->tallos,2,".","")]);
    }

    public static function sumCajasVendidas(){
        $intervalos = self::intervalosTiempo();
        $dato = ProyeccionVentaSemanalReal::whereBetween('codigo_semana',[$intervalos['primeraSemanaFutura'],$intervalos['cuartaSemanaFutura']])
            ->select(DB::Raw('sum(cajas_equivalentes) as cajas'))->first();
        $objInidicardor = Indicador::where('nombre','DP3');
        $objInidicardor->update(['valor'=>number_format($dato->cajas,2,".","")]);
    }

    public static function sumDineroGeneradoVentas(){
        $intervalos = self::intervalosTiempo();
        $dato = ProyeccionVentaSemanalReal::whereBetween('codigo_semana',[$intervalos['primeraSemanaFutura'],$intervalos['cuartaSemanaFutura']])
            ->select(DB::Raw('sum(valor) as valor'))->first();
        $objInidicardor = Indicador::where('nombre','DP4');
        $objInidicardor->update(['valor'=>number_format($dato->valor,2,".","")]);
    }

    public static function sumTallosCosechadosFuturo1Semana(){
        $intervalos = self::intervalosTiempo();
        $dato = ResumenSemanaCosecha::where('codigo_semana',$intervalos['primeraSemanaFutura'])
            ->select(DB::Raw('sum(tallos_proyectados) as tallos'))->first();
        $objInidicardor = Indicador::where('nombre','DP6');
        $objInidicardor->update(['valor'=>number_format($dato->tallos,2,".","")]);
    }

    public static function sumCajasVendidasFuturas1Semana(){
        $intervalos = self::intervalosTiempo();
        $dato = ProyeccionVentaSemanalReal::where('codigo_semana',$intervalos['primeraSemanaFutura'])
            ->select(DB::Raw('sum(cajas_equivalentes) as cajas'))->first();

        $objInidicardor = Indicador::where('nombre','DP7');
        $objInidicardor->update(['valor'=>number_format($dato->cajas,2,".","")]);
    }

    public static function sumCajasCosechadasFuturas1Semana(){
        $intervalos = self::intervalosTiempo();
        $dato = ResumenSemanaCosecha::where('codigo_semana',$intervalos['primeraSemanaFutura'])
            ->select(DB::Raw('sum(cajas_proyectadas) as cajas'))->first();

        $objInidicardor = Indicador::where('nombre','DP8');
        $objInidicardor->update(['valor'=>number_format($dato->cajas,2,".","")]);
    }

    public static function sumDineroGeneradoFuturo1Semana(){
        $intervalos = self::intervalosTiempo();
        $dato = ProyeccionVentaSemanalReal::where('codigo_semana',$intervalos['primeraSemanaFutura'])
            ->select(DB::Raw('sum(valor) as valor'))->first();

        $objInidicardor = Indicador::where('nombre','DP9');
        $objInidicardor->update(['valor'=>number_format($dato->valor,2,".","")]);
    }

    public static function proyeccionVentaFutura3Meses(){

        $primerMesSiguiente = Carbon::parse(now())->addMonth()->toDateString();
        $SegundoMesSiguiente = Carbon::parse($primerMesSiguiente)->addMonth()->toDateString();
        $tercerMesSiguiente = Carbon::parse($SegundoMesSiguiente)->addMonth()->toDateString();
        $data=[];

        //-------------PRIMER MES SIGUIENTE--------------//
        $inicio = Carbon::parse($primerMesSiguiente)->startOfMonth()->toDateString();
        $fin = Carbon::parse($primerMesSiguiente)->endOfMonth()->toDateString();
        $valor=0;
        $pedidos = Pedido::where('estado',1)->whereBetween('fecha_pedido',[$inicio,$fin])->get();
        foreach($pedidos as $pedido)
            $valor+= $pedido->getPrecioByPedido();

        $data['primer_mes']=$valor;

        //-------------SEGUNDO MES SIGUIENTE--------------//
        $inicio =Carbon::parse($SegundoMesSiguiente)->startOfMonth()->toDateString();
        $fin =Carbon::parse($SegundoMesSiguiente)->endOfMonth()->toDateString();
        $valor=0;
        $pedidos = Pedido::where('estado',1)->whereBetween('fecha_pedido',[$inicio,$fin])->get();
        foreach($pedidos as $pedido)
            $valor+= $pedido->getPrecioByPedido();

        $data['segundo_mes']=$valor;

        //-------------TERCER MES SIGUIENTE--------------//
        $inicio =Carbon::parse($tercerMesSiguiente)->startOfMonth()->toDateString();
        $fin =Carbon::parse($tercerMesSiguiente)->endOfMonth()->toDateString();
        $valor=0;
        $pedidos = Pedido::where('estado',1)->whereBetween('fecha_pedido',[$inicio,$fin])->get();
        foreach($pedidos as $pedido)
            $valor+= $pedido->getPrecioByPedido();

        $data['tercer_mes']=$valor;

        $objInidicardor = Indicador::where('nombre','DP5');
        $objInidicardor->update(['valor'=>$data['primer_mes']['valor']."|".$data['segundo_mes']['valor']."|".$data['tercer_mes']['valor']]);

    }

    public static function intervalosTiempo(){
        $fechaActual =now()->toDateString();
        return [
            'primeraSemanaFutura' =>getSemanaByDate(Carbon::Parse($fechaActual)->addDays(7))->codigo,
            'cuartaSemanaFutura' =>getSemanaByDate(opDiasFecha('+', 28,  $fechaActual))->codigo
        ];
    }
}
