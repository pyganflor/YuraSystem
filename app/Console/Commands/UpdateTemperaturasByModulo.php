<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Ciclo;
use yura\Modelos\CicloTemperatura;

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
            $semana_fen = intval(difFechas($c->fecha_inicio, date('Y-m-d'))->days / 7) + 1;
            for ($i = 1; $i <= $semana_fen; $i++) {
                $fecha_hasta = opDiasFecha('+', ($i * 7), $c->fecha_inicio);
                $acumulado = DB::table('temperatura')
                    ->select(DB::raw('sum(((minima + maxima) / 2) - 8) as cant'))
                    ->where('estado', 1)
                    ->where('fecha', '>=', $c->fecha_inicio)
                    ->where('fecha', '<=', $fecha_hasta)
                    ->get()[0]->cant;

                $ct = CicloTemperatura::All()
                    ->where('estado', 1)
                    ->where('id_ciclo', $c->id_ciclo)
                    ->where('num_semana', $i)
                    ->first();
                if ($ct == '') {
                    $ct = new CicloTemperatura();
                    $ct->id_ciclo = $c->id_ciclo;
                    $ct->num_semana = $i;
                }
                $ct->acumulado = $acumulado > 0 ? $acumulado : 0;
                $ct->save();
            }
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "ciclo:update_temperaturas" <<<<< * >>>>>');
    }
}
