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

    public function getSaldo($idVariedad){

        $cajasProyectadas = $this->getCajasProyectadas($idVariedad);
        $cajasVendidas =  $this->getTotalesProyeccionVentaSemanal(null,$idVariedad)->total_cajas_equivalentes;

        return  $cajasProyectadas-$cajasVendidas-$this->desecho($idVariedad);
    }

    public function getCajasProyectadas($idVariedad){
        $semanaActual = getSemanaByDate(now()->toDateString());

        $objResumenSemanaCosecha = ResumenSemanaCosecha::where([
            ['id_variedad',$idVariedad],
            ['codigo_semana',$this->codigo-1]
        ])->first();

        if(isset($objResumenSemanaCosecha)){
            if($this->codigo >= $semanaActual->codigo){
                $cajasProyectadas = $objResumenSemanaCosecha->cajas_proyectadas;
                //dump($objResumenSemanaCosecha->id_resumen_semana_cosecha,$objResumenSemanaCosecha->cajas_proyectadas);
            }else{
                $cajasProyectadas =  $objResumenSemanaCosecha->cajas;
            }
        }else{
            $cajasProyectadas = 0;
        }
        return $cajasProyectadas;
    }

    public function desecho($idVariedad){
        $objResumenSemanaCosecha =  ResumenSemanaCosecha::where([
            ['codigo_semana',$this->codigo],
            ['id_variedad',$idVariedad]
        ])->first();

        return isset($objResumenSemanaCosecha) ? $objResumenSemanaCosecha->desecho : 0;
    }

    public function getLastSaldoInicial($idVariedad,$desde){
        $firstSemana = $this->firstSemanaResumenSemanaCosechaByVariedad($idVariedad);
        if($firstSemana<=$desde){
            $z=0;
            $saldoInicial =0;
            for ($x=$firstSemana;$x<$desde;$x++){
                $semana = Semana::where([['codigo',$x],['id_variedad',$idVariedad]])->first();
                if(isset($semana)){
                    if($z ==0)
                        $saldoInicial = $this->firstSaldoInicialByVariedad($idVariedad);

                    $saldoFinal = getObjSemana($semana->codigo)->getSaldo($idVariedad)+$saldoInicial;
                    if($x>0)
                        $saldoInicial =$saldoFinal;
                    $z++;
                }
            }
            return $saldoInicial;
        }else{
            return 0;
        }
    }

    public function getLastSaldoFinal($idVariedad,$desde){
        $firstSemana = $this->firstSemanaResumenSemanaCosechaByVariedad($idVariedad);
        if($firstSemana<=$desde){
            $z=0;
            $saldoInicial =0;
            for ($x=$firstSemana;$x<=$desde;$x++){
                $semana = Semana::where([['codigo',$x],['id_variedad',$idVariedad]])->first();
                if(isset($semana)){
                    if($z ==0)
                        $saldoInicial = $this->firstSaldoInicialByVariedad($idVariedad);

                    $saldoFinal = getObjSemana($semana->codigo)->getSaldo($idVariedad)+$saldoInicial;
                    if($x>0)
                        $saldoInicial =$saldoFinal;
                    $z++;
                }
            }
            return $saldoInicial;
        }else{
            return 0;
        }
    }

    public function firstSemanaResumenSemanaCosechaByVariedad($idVariedad){
        return ResumenSemanaCosecha::where('id_variedad',$idVariedad)
            ->select(DB::raw('MIN(codigo_semana) as codigo'))->first()->codigo;
    }

    public function firstSaldoInicialByVariedad($idVariedad){
        return Variedad::find($idVariedad)->saldo_inicial;
    }

    public function cuartaSemanaFutura($idVariedad){
         $semanas = Semana::where([['codigo','>',$this->codigo],['id_variedad',$idVariedad]])
             ->limit(4)->orderBy('codigo','asc');

         if($semanas->count()>0){
             return $semanas->get()->last()->codigo;
         }else{
             return 0;
         }

    }

    public function diasFuturos($dias){
        $x =1;
        while($x>=1 && $x<=$dias){
            $semana = Semana::where('codigo',$this->codigo)->first();
            if(isset($semanas)){
                $semana = $semana->codigo;
                $x++;
            }
            $this->codigo++;
        }
        return $semana;
    }

}
