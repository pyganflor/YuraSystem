<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Ciclo;

class CicloPrimeraFlor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ciclo:primera_flor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizar el campo primera flor (fecha_cosecha) de los ciclos activos';

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
        $ciclos = Ciclo::All()->where('estado', 1)->where('activo', 1);

        if (count($ciclos) > 0) {
            Log::info('<<<<< ! >>>>> Ejecutando comando "ciclo:primera_flor" <<<<< ! >>>>>');
            foreach ($ciclos as $c) {
                $fecha_min = DB::table('desglose_recepcion as dr')
                    ->join('recepcion as r', 'dr.id_recepcion', '=', 'r.id_recepcion')
                    ->select(DB::raw('min(r.fecha_ingreso) as fecha'))
                    ->where('dr.estado', '=', 1)
                    ->where('r.estado', '=', 1)
                    ->where('dr.id_modulo', '=', $c->id_modulo)
                    ->where('dr.id_variedad', '=', $c->id_variedad)
                    ->where('r.fecha_ingreso', '>', opDiasFecha('+', 1, $c->fecha_inicio))
                    ->where('r.fecha_ingreso', '<=', $c->fecha_fin . ' 23:59:59')
                    ->get()[0]->fecha;

                $dias = '';
                if ($fecha_min != '') {
                    $fecha_min = substr($fecha_min, 0, 10);
                    $dias = difFechas($fecha_min, $c->fecha_inicio)->days;
                } else
                    $fecha_min = null;

                $c->fecha_cosecha = $fecha_min;
                $c->save();
                Log::info('MODULO: ' . $c->modulo->nombre . ' => ' . $fecha_min . ' => ' . $dias);
            }
            Log::info('<<<<< * >>>>> Fin satisfactorio del comando "ciclo:primera_flor" <<<<< * >>>>>');
        }
    }
}