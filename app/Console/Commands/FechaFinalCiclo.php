<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Ciclo;

class FechaFinalCiclo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ciclo:fecha_fin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar la fecha_fin en los ciclos activos que no tengan fecha_fin';

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
        $ciclos_fin = Ciclo::All()->where('estado', '=', 1)
            ->where('activo', '=', 1)
            ->where('fecha_fin', '!=', date('Y-m-d'));

        if (count($ciclos_fin) > 0) {
<<<<<<< HEAD
            foreach ($ciclos_fin as $c) {
                $c->fecha_fin = date('Y-m-d');
                $c->save();
                Log::info('La fecha_fin del ciclo #' . $c->id_ciclo . ' ha sido actualizada a ' . date('Y-m-d'));
            }
=======
            Log::info('<<<<< ! >>>>> Ejecutando comando "ciclo:fecha_fin" <<<<< ! >>>>>');
            foreach ($ciclos_fin as $c) {
                $c->fecha_fin = date('Y-m-d');
                $c->save();
                Log::info('La fecha_fin del "ciclo" #' . $c->id_ciclo . ' ha sido actualizada a "' . date('Y-m-d') . '"');
            }
            Log::info('<<<<< * >>>>> Fin satisfactorio del comando "ciclo:fecha_fin" <<<<< * >>>>>');
>>>>>>> ded73910bf17211896927d0e5421607f049512c9
        }
        return false;
    }
}