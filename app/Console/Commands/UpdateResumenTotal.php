<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Area;
use yura\Modelos\Semana;
use yura\Modelos\ResumenSemanalTotal;

class UpdateResumenTotal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resumen_total:update_semanal {desde=0} {hasta=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para actualizar la tabla resumen_semanal_total para los costos';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "resumen_total:update_semanal" <<<<< ! >>>>>');

        $desde_par = $this->argument('desde');
        $hasta_par = $this->argument('hasta');

        if ($desde_par <= $hasta_par) {
            if ($desde_par != 0)
                $semana_desde = Semana::All()->where('estado', 1)->where('codigo', $desde_par)->first();
            else
                $semana_desde = getSemanaByDate(date('Y-m-d'));
            if ($hasta_par != 0)
                $semana_hasta = Semana::All()->where('estado', 1)->where('codigo', $hasta_par)->first();
            else
                $semana_hasta = getSemanaByDate(date('Y-m-d'));

            Log::info('SEMANA PARAMETRO DESDE: ' . $desde_par . ' => ' . $semana_desde->codigo);
            Log::info('SEMANA PARAMETRO HASTA: ' . $hasta_par . ' => ' . $semana_hasta->codigo);

            $array_semanas = [];
            for ($i = $semana_desde->codigo; $i <= $semana_hasta->codigo; $i++) {
                $semana = Semana::All()
                    ->where('estado', 1)
                    ->where('codigo', $i)->first();
                if ($semana != '')
                    if (!in_array($semana->codigo, $array_semanas)) {
                        array_push($array_semanas, $semana->codigo);
                    }
            }

            foreach ($array_semanas as $sem) {
                $model = ResumenSemanalTotal::All()
                    ->where('codigo_semana', $sem)
                    ->first();
                if ($model == '') {
                    $model = new ResumenSemanalTotal();
                    $model->codigo_semana = $sem;
                }

                /* ----------------------------- campo ------------------------- */
                $area = Area::All()->where('estado', 1)->where('nombre', 'CAMPO')->first();
                $campo_mp = DB::table('costos_semana as c')
                    ->select(DB::raw('sum(c.valor) as cant'))
                    ->join('actividad_producto as ac', 'ac.id_actividad_producto', '=', 'c.id_actividad_producto')
                    ->join('actividad as a', 'a.id_actividad', '=', 'ac.id_actividad')
                    ->where('a.id_area', '=', $area->id_area)
                    ->where('c.codigo_semana', $sem)
                    ->get()[0]->cant;
                $model->campo_mp = $campo_mp;
                $campo_mo = DB::table('costos_semana_mano_obra as c')
                    ->select(DB::raw('sum(c.valor) as cant'))
                    ->join('actividad_mano_obra as am', 'am.id_actividad_mano_obra', '=', 'c.id_actividad_mano_obra')
                    ->join('actividad as a', 'a.id_actividad', '=', 'am.id_actividad')
                    ->where('a.id_area', '=', $area->id_area)
                    ->where('c.codigo_semana', $sem)
                    ->get()[0]->cant;
                $model->campo_mo = $campo_mo;
                $campo_gip = DB::table('otros_gastos as o')
                    ->select(DB::raw('sum(o.gip) as cant'))
                    ->where('o.id_area', '=', $area->id_area)
                    ->where('o.codigo_semana', $sem)
                    ->get()[0]->cant;
                $model->campo_gip = $campo_gip;
                $campo_ga = DB::table('otros_gastos as o')
                    ->select(DB::raw('sum(o.ga) as cant'))
                    ->where('o.id_area', '=', $area->id_area)
                    ->where('o.codigo_semana', $sem)
                    ->get()[0]->cant;
                $model->campo_ga = $campo_ga;

                $model->save();
            }
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "resumen_total:update_semanal" <<<<< * >>>>>');
    }
}
