<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use yura\Http\Controllers\Indicadores\Calibre;
use yura\Http\Controllers\Indicadores\Verde;

class UpdateIndicador extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'indicador:update {indicador=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commando para actualizar los indicadores de los reportes del sistema';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "indicador:update" <<<<< ! >>>>>');

        $indicador_par = $this->argument('indicador');

        if ($indicador_par === '0' || $indicador_par === 'D1') {  // Calibre (7 días)
            Calibre::dias_atras_7();
            Log::info('INDICADOR: "Calibre (7 dias)"');
        }
        if ($indicador_par === '0' || $indicador_par === 'D2') {  // Tallos clasificados (7 días)
            Verde::tallos_clasificados_7_atras();
            Log::info('INDICADOR: "Tallos clasificados (7 dias)"');
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "proyeccion:update_semanal" <<<<< * >>>>>' . $indicador_par);
    }
}