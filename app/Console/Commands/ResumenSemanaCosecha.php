<?php

namespace yura\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\ProyeccionModuloSemana;
use yura\Modelos\Semana;
use yura\Modelos\Variedad;
use yura\Modelos\ResumenSemanaCosecha as ResumenCosecha;

class ResumenSemanaCosecha extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resumen:semana_cosecha {semana_desde=0} {semana_hasta=0} {variedad=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resume todos los datos por variedad y semana de la cosecha';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $inicio =  $tiempo_inicial = microtime(true);
        $desde = $this->argument('semana_desde');
        $hasta = $this->argument('semana_hasta');
        $variedad = $this->argument('variedad');
        Info("Inicia el comando resumen:semana_cosecha");
        Info("Variables recibidas, desde: ".$desde. "hasta: ".$hasta. " variedad: ".$variedad );

        $variedades = Variedad::where('estado', 1);
        if($variedad != 0)
            $variedades->where('id_variedad',$variedad);

        $variedades = $variedades->get();
        $fechaClasificacionVerde = ClasificacionVerde::all()->last();

        if($desde != 0){
            $semana_desde = Semana::where('estado', 1)->where('codigo', $desde)->first();
            if(!isset($semana_desde->codigo)){
                Info("No existe la semana de inicio ".$desde);
                return false;
            }
        }else {
            $semana_desde = getSemanaByDate(Carbon::parse($fechaClasificacionVerde->fecha_ingreso)->subDays(7)->toDateString());
        }

        if($hasta != 0){
            $semana_hasta = Semana::where('estado', 1)->where('codigo', $hasta)->first();
            if(!isset($semana_hasta->codigo)){
                Info("No existe la semana de fin ". $hasta);
                return false;
            }
        }else {
            $semana_hasta = getSemanaByDate($fechaClasificacionVerde->fecha_ingreso);
        }

        Info('SEMANA DESDE: ' . $semana_desde->codigo);
        Info('SEMANA HASTA: ' . $semana_hasta->codigo );

        if($desde<=$hasta) {
            $semanas=[];
            for($i=$desde; $i<=$hasta; $i++){
                $existSemana= Semana::where('codigo', $i)->first();
                if(isset($existSemana->codigo)){
                    $semanas[] = $existSemana;
                }
            }
            $calibreActual=0;
            $z=0;
            foreach ($semanas as $x => $semana){
                foreach($variedades as $y=> $variedad){
                    $resumenSemanaCosecha = ResumenCosecha::where([
                        ['id_variedad',$variedad->id_variedad],
                        ['codigo_semana',$semana->codigo]
                    ])->first();

                    if(!isset($resumenSemanaCosecha)){
                        $objResumenSemanaCosecha = new ResumenCosecha;
                        $objResumenSemanaCosecha->id_variedad = $variedad->id_variedad;
                        $objResumenSemanaCosecha->codigo_semana = $semana->codigo;
                    }else{
                        $objResumenSemanaCosecha = ResumenCosecha::find($resumenSemanaCosecha->id_resumen_semana_cosecha);
                    }
                    $proyeccionModuloSemana = ProyeccionModuloSemana::where([
                            ['semana',$semana->codigo],
                            ['id_variedad',]
                        ])->sum('proyectados');
                    $objResumenSemanaCosecha->cajas = getCajasByRangoVariedad($semana->fecha_inicial, $semana->fecha_final, $variedad->id_variedad);

                    //Info($semana->fecha_inicial." | ".$semana->fecha_final." | ".$variedad->id_variedad);
                    $semanaActual = getSemanaByDate(now()->toDateString())->codigo;

                    if($semana->codigo > $semanaActual){
                        if($z>0 && $z<5){
                            $calibre= $calibreActual;
                        }else{
                            $calibre = Semana::where('codigo',$semana->codigo)->first()->tallos_ramo_poda;
                        }
                    }else{

                        $calibre = getCalibreByRangoVariedad($semana->fecha_inicial, $semana->fecha_final, $variedad->id_variedad);
                        $calibreActual = $calibre;
                        $z=1;
                    }

                    $tallos = getTallosCosechadosByModSemVar(null, $semana->codigo, $variedad->id_variedad);
                    $objResumenSemanaCosecha->tallos = isset($tallos) ? $tallos : 0;
                    $objResumenSemanaCosecha->calibre = isset($calibre) ? $calibre : 0;
                    $objResumenSemanaCosecha->tallos_proyectados = $proyeccionModuloSemana;
                    $objResumenSemanaCosecha->cajas_proyectadas= $objResumenSemanaCosecha->calibre > 0 ? number_format(($proyeccionModuloSemana / $objResumenSemanaCosecha->calibre / getConfiguracionEmpresa(null,false)->ramos_x_caja),2,".",",") : 0;
                    $objResumenSemanaCosecha->save();
                }
                $z++;
            }

        }else{
                Info('La semana hasta no puede ser menor a la semana desde en el comando ResumenSemanaCosecha');
        }
        $fin = microtime(true);
        Info("El script se completo en : ".(number_format(($fin-$inicio),2,".","")). " segundos");
    }
}
