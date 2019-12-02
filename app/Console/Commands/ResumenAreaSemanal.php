<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Semana;
use yura\Modelos\ResumenAreaSemanal as ResumenArea;
use yura\Modelos\Variedad;

class ResumenAreaSemanal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'area:update_semanal {semana_desde=0} {semana_hasta=0} {variedad=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para actualizar semanalmente la info sobre el área';

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

        $ini = date('Y-m-d H:i:s');
        Log::info('<<<<< ! >>>>> Ejecutando comando "area:update_semanal" <<<<< ! >>>>>');

        $semana_actual = getSemanaByDate(date('Y-m-d'));

        $desde_par = $this->argument('semana_desde') != 0 ? $this->argument('semana_desde') : $semana_actual->codigo;
        $hasta_par = $this->argument('semana_hasta') != 0 ? $this->argument('semana_hasta') : $semana_actual->codigo;
        $variedad_par = $this->argument('variedad');

        $variedades = $variedad_par == 0 ? Variedad::All() : [getVariedad($variedad_par)];

        for ($s = $desde_par; $s <= $hasta_par; $s++) {
            $semana = Semana::All()
                ->where('estado', 1)
                ->where('codigo', $s)
                ->first();
            if ($semana != '') {
                foreach ($variedades as $var) {
                    $model = ResumenArea::All()
                        ->where('estado', 1)
                        ->where('id_variedad', $var->id_variedad)
                        ->where('codigo_semana', $semana->codigo)
                        ->first();
                    if ($model == '') {
                        $model = new ResumenArea();
                        $model->id_variedad = $var->id_variedad;
                        $model->codigo_semana = $semana->codigo;
                    }

                    $area = 0;
                    $data = getAreaCiclosByRango($semana->codigo, $semana->codigo, $var->id_variedad);
                    foreach ($data['variedades'] as $v) {
                        foreach ($v['ciclos'] as $c) {
                            foreach ($c['areas'] as $a) {
                                $area += $a;
                            }
                        }
                    }

                    $data_ciclos = getCiclosCerradosByRango($semana->codigo, $semana->codigo, $var->id_variedad);
                    $ciclo = $data_ciclos['ciclo'];
                    $tallos_m2 = $data_ciclos['area_cerrada'] > 0 ? round($data_ciclos['tallos_cosechados'] / $data_ciclos['area_cerrada'], 2) : 0;
                    $area_cerrada = $data_ciclos['area_cerrada'];
                    $tallos = $data_ciclos['tallos_cosechados'];
                    $data_cosecha = getCosechaByRango($semana->codigo, $semana->codigo, $var->id_variedad);
                    $calibre = $data_cosecha['calibre'];
                    $ramos = $calibre > 0 ? round($tallos / $calibre, 2) : 0;
                    $ramos_m2 = $area_cerrada > 0 ? round($ramos / $area_cerrada, 2) : 0;

                    $ciclo_ano = $area_cerrada > 0 ? round(365 / $ciclo, 2) : 0;
                    $ramos_m2_anno = $area_cerrada > 0 ? round($ciclo_ano * round($ramos / $area_cerrada, 2), 2) : 0;


                    $model->area = $area;
                    $model->ciclo = $ciclo;
                    $model->tallos_m2 = $tallos_m2;
                    $model->ramos_m2 = $ramos_m2;
                    $model->ramos_m2_anno = $ramos_m2_anno;
                    $model->save();
                }
            }
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "area:update_semanal" <<<<< * >>>>>');
    }
}