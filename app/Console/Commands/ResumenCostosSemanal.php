<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Semana;
use yura\Modelos\ResumenCostosSemanal as ModelResumen;

class ResumenCostosSemanal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'costos:update_semanal {desde=0} {hasta=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para resumir los costos por semana';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "costos:update_semanal" <<<<< ! >>>>>');

        $semana_actual = getSemanaByDate(date('Y-m-d'));

        $desde_par = $this->argument('desde') != 0 ? $this->argument('desde') : $semana_actual->codigo;
        $hasta_par = $this->argument('hasta') != 0 ? $this->argument('hasta') : $semana_actual->codigo;

        for ($s = $desde_par; $s <= $hasta_par; $s++) {
            $semana = Semana::All()
                ->where('estado', 1)
                ->where('codigo', $s)
                ->first();
            if ($semana != '') {
                $mano_obra = DB::table('costos_semana_mano_obra')
                    ->select(DB::raw('sum(valor) as cant'))
                    ->where('codigo_semana', $semana->codigo)
                    ->get()[0]->cant;
                $insumos = DB::table('costos_semana')
                    ->select(DB::raw('sum(valor) as cant'))
                    ->where('codigo_semana', $semana->codigo)
                    ->get()[0]->cant;
                $fijos = DB::table('otros_gastos')
                    ->select(DB::raw('sum(gip + ga) as cant'))
                    ->where('codigo_semana', $semana->codigo)
                    ->get()[0]->cant;
                $regalias = 0;
                $cant_regalias = 0;
                foreach (getVariedades() as $var) {
                    $r = $var->regaliasBySemana($semana->codigo)->valor;
                    $regalias += $r;
                    $r > 0 ? $cant_regalias++ : false;
                }
                $regalias = $cant_regalias > 0 ? ($regalias / $cant_regalias) : 0;
                $area = DB::table('resumen_area_semanal')
                    ->select(DB::raw('sum(area) as cant'))
                    ->where('estado', 1)
                    ->where('codigo_semana', $semana->codigo)
                    ->get()[0]->cant;
                $regalias = round(($regalias / 52) * ($area / 10000), 2);

                $resumen = ModelResumen::All()
                    ->where('codigo_semana', $semana->codigo)
                    ->first();
                if ($resumen == '') {   // es nuevo
                    $resumen = new ModelResumen();
                    $resumen->codigo_semana = $semana->codigo;
                }
                $resumen->mano_obra = $mano_obra;
                $resumen->insumos = $insumos;
                $resumen->fijos = $fijos;
                $resumen->regalias = $regalias;

                $resumen->save();
            }
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "costos:update_semanal" <<<<< * >>>>>');
    }
}
