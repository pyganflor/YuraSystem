<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Modelos\ActividadManoObra;
use yura\Modelos\ActividadProducto;
use yura\Modelos\CostosSemana;
use yura\Modelos\CostosSemanaManoObra;
use yura\Modelos\Semana;
use yura\Modelos\ResumenCostosSemanal as ModelResumen;

class ResumenCostosSemanal7 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'costos:update_semanal_7';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para resumir los costos 7 semanas hacia atras';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "costos:update_semanal_7" <<<<< ! >>>>>');

        $semana_actual = getSemanaByDate(date('Y-m-d'));
        $semana_desde = getSemanaByDate(opDiasFecha('-', 42, date('Y-m-d')));

        $desde_par = $semana_desde->codigo;
        $hasta_par = $semana_actual->codigo;

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
                $resumen->mano_obra = $mano_obra != '' ? $mano_obra : 0;
                $resumen->insumos = $insumos != '' ? $insumos : 0;
                $resumen->fijos = $fijos != '' ? $fijos : 0;
                $resumen->regalias = $regalias != '' ? $regalias : 0;
                $resumen->save();

                /* ======================== Guardar en 0 automaticamente los costos que no existan ======================== */
                //  mano de obra
                foreach (ActividadManoObra::All() as $act_mo) {
                    $model = CostosSemanaManoObra::All()
                        ->where('id_actividad_mano_obra', $act_mo->id_actividad_mano_obra)
                        ->where('codigo_semana', $semana->codigo)
                        ->first();
                    if ($model == '') {
                        $model = new CostosSemanaManoObra();
                        $model->id_actividad_mano_obra = $act_mo->id_actividad_mano_obra;
                        $model->codigo_semana = $semana->codigo;
                        $model->valor = 0;
                        $model->cantidad = 0;
                        $model->save();
                    }
                }
                //  insumos
                foreach (ActividadProducto::All() as $act_p) {
                    $model = CostosSemana::All()
                        ->where('id_actividad_producto', $act_p->id_actividad_producto)
                        ->where('codigo_semana', $semana->codigo)
                        ->first();
                    if ($model == '') {
                        $model = new CostosSemana();
                        $model->id_actividad_producto = $act_p->id_actividad_producto;
                        $model->codigo_semana = $semana->codigo;
                        $model->valor = 0;
                        $model->cantidad = 0;
                        $model->save();
                    }
                }
            }
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "costos:update_semanal_7" <<<<< * >>>>>');
    }
}
