<?php

namespace yura\Http\Controllers\Indicadores;

use Carbon\Carbon;
use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\ResumenSemanaCosecha;
use yura\Modelos\Indicador;
use DB;

class ProyeccionesVenta extends Controller
{
    public function __construct()
    {
        $fechaActual =now()->toDateString();
        $this->primeraSemanaFutura = getSemanaByDate(Carbon::Parse($fechaActual)->addDays(7))->codigo;
        $this->cuartSemanaFutura = getSemanaByDate(opDiasFecha('+', 28,  $this->fechaActual))->codigo;
    }

    public function sumCajasFuturas4Semanas(){

        $dato = ResumenSemanaCosecha::whereBetween('codigo_semana',[$this->primeraSemanaFutura,$this->cuartSemanaFutura ])
            ->select(DB::Raw('sum(cajas_proyectadas) as cajas'))->first();

        $objInidicardor = Indicador::where('nombre','DP1');
        $objInidicardor->valor = $dato->caja;
        $objInidicardor->save();
    }
}
