<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Semana;
use yura\Modelos\ResumenAreaSemanal as ResumenArea;

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

        $variedades = $variedad_par == 0 ? getVariedades() : [getVariedad($variedad_par)];

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

                    $model->save();
                }
            }
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "area:update_semanal" <<<<< * >>>>>');
    }
}
