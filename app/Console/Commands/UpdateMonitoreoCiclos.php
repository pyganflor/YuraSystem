<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Ciclo;
use yura\Modelos\Monitoreo;

class UpdateMonitoreoCiclos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ciclo:update_monitoreo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para crear automaticamente el monitoreo semanal correspondiente';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "ciclo:update_monitoreo" <<<<< ! >>>>>');

        $ciclos = Ciclo::where('estado', 1)
            ->where('activo', 1)
            ->get();
        foreach ($ciclos as $c) {
            $new_sem = difFechas(date('Y-m-d'), $c->fecha_inicio)->days;
            $new_sem = intval($new_sem / 7);
            for ($i = 1; $i <= $new_sem; $i++) {
                $model = Monitoreo::All()
                    ->where('estado', 1)
                    ->where('id_ciclo', $c->id_ciclo)
                    ->where('num_sem', $i)
                    ->first();
                if ($model == '') {
                    $model = new Monitoreo();
                    $model->id_ciclo = $c->id_ciclo;
                    $model->num_sem = $i;
                    $model->altura = 0;

                    $model->save();
                }
            }
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "ciclo:update_monitoreo" <<<<< * >>>>>');
    }
}
