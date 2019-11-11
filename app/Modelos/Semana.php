<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use DB;

class Semana extends Model
{
    protected $table = 'semana';
    protected $primaryKey = 'id_semana';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'anno',
        'codigo',
        'fecha_inicial',
        'fecha_final',
        'curva',
        'desecho',
        'semana_poda',
        'semana_siembra',
        'fecha_registro',
        'estado',
        'id_variedad',
        'tallos_planta_siembra',
        'tallos_planta_poda',
        'tallos_ramo_siembra',
        'tallos_ramo_poda',
    ];

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function getTotalesProyeccionVentaSemanal($idsCliente,$idVariedad){

        $primeraSemana = ProyeccionVentaSemanalReal::where('id_variedad', $idVariedad)->select(DB::raw('MIN(codigo_semana) as codigo'))->first();
        $existeSemana =ProyeccionVentaSemanalReal::where([
            ['id_variedad', $idVariedad],
            ['codigo_semana',$this->codigo]
        ])->first();

        if(!$existeSemana)
            $this->codigo = $primeraSemana->codigo;

        $proyeccion = ProyeccionVentaSemanalReal::where([
            ['id_variedad',$idVariedad],
            ['codigo_semana',$this->codigo]
        ]);

        if($idsCliente)
            $proyeccion->whereNotIn('id_cliente',$idsCliente);

        return $proyeccion->select(
                DB::raw('sum(valor) as total_valor'),
                DB::raw('sum(cajas_fisicas) as total_cajas_fisicas'),
                DB::raw('sum(cajas_equivalentes) as total_cajas_equivalentes')
            )->groupBy('codigo_semana')->first();
    }

    public function getSaldoInicial($idVariedad){
        $cajasProyectadas = $this->getCajasProyectadas($idVariedad);
        $cajasVendidas = $this->getTotalesProyeccionVentaSemanal(null,$idVariedad)->total_cajas_equivalentes;
        $desecho = $cajasProyectadas*($this->desecho($idVariedad)/100);
        //dump($this->codigo.": ".$cajasVendidas);
        return $cajasProyectadas-$cajasVendidas-$desecho;
    }

    public function getSaldoFinal($idVariedad){
        $cajasProyectadas = $this->getCajasProyectadas($idVariedad);
        $cajasVendidas = $this->getTotalesProyeccionVentaSemanal(null,$idVariedad)->total_cajas_equivalentes;
        $desecho = $cajasProyectadas*($this->desecho($idVariedad)/100);
        $saldoInicialSemanaAnterior = getObjSemana($this->codigo)->getSaldoInicial($idVariedad);
        dump($this->codigo.": ".$cajasProyectadas.",".$cajasVendidas.", ". $saldoInicialSemanaAnterior);
        if($cajasProyectadas ==0 ){
            $saldoInicialSemanaAnterior =0;
            $cajasProyectadas =0;
            $cajasVendidas=0;
        }
        return $saldoInicialSemanaAnterior+$cajasProyectadas-$cajasVendidas-$desecho;
    }

    public function getCajasProyectadas($idVariedad){

        $objResumenSemanaCosecha = ResumenSemanaCosecha::where([
            ['id_variedad',$idVariedad],
            ['codigo_semana',$this->codigo-1]
        ])->first();

        $cajasCosechadas =  isset($objResumenSemanaCosecha->cajas)
                                ? ($objResumenSemanaCosecha->cajas == 0 ? $objResumenSemanaCosecha->cajas_proyectadas
                                : $objResumenSemanaCosecha->cajas) : 0;

        return $cajasCosechadas;

    }

    public function desecho($idVariedad){
        $objResumenSemanaCosecha =  ResumenSemanaCosecha::where([
            ['codigo_semana',$this->codigo],
            ['id_variedad',$idVariedad]
        ])->first();

        return isset($objResumenSemanaCosecha) ? $objResumenSemanaCosecha->desecho : 0;
    }
}
