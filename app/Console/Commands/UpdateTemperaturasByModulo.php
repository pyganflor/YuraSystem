<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Ciclo;

class UpdateTemperaturasByModulo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ciclo:update_temperaturas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "ciclo:update_temperaturas" <<<<< ! >>>>>');

        $ciclos = Ciclo::where('estado', 1)
            ->where('activo', 1)
            ->get();
        foreach ($ciclos as $c) {
            for ($i = 1; $i <= (intval(difFechas($c->fecha_inicio, date('Y-m-d'))->days / 7) + 1); $i++) {

            }
            dd($c->modulo->nombre);
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "ciclo:update_temperaturas" <<<<< * >>>>>');
    }
}
