<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use yura\Modelos\Ciclo;
use yura\Modelos\Semana;

class CurvaCiclo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ciclo:curva';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asignar las curvas de la semana de inicio a los ciclos sin curva';

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
        $ciclos = Ciclo::All()->where('estado', 1)->where('curva', '');

        foreach ($ciclos as $c) {
            $semana = Semana::All()
                ->where('estado', 1)
                ->where('id_variedad', $c->id_variedad)
                ->where('fecha_inicial', '<=', $c->fecha_inicio)
                ->where('fecha_final', '>=', $c->fecha_inicio)
                ->first();

            if ($semana == '') {
                dd('No se encontró la semana para los parametros siguientes:', $c->id_variedad, $c->fecha_inicio);
            } else {
                $c->curva = $semana->curva;

                if ($semana != '') {
                    $c->curva = $semana->curva;
                    if ($c->poda_siembra == 'P')
                        $c->semana_poda_siembra = $semana->semana_poda;
                    else
                        $c->semana_poda_siembra = $semana->semana_siembra;

                    $c->save();
                }
            }
        }
    }
}