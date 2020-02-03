<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Modelos\IndicadorSemana;
use yura\Modelos\Semana;

class IndicadorSemanal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'indicador_semana:update {desde=0} {hasta=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para actualizar los indicadores por semana';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "indicador_semana:update" <<<<< ! >>>>>');

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

            /* ========================== C9 Costos/m2 (-16 semanas) =========================== */
            $indicador = getIndicadorByName('C9');  // Costos/m2 (-16 semanas)
            if ($indicador != '') {
                foreach ($array_semanas as $sem) {
                    $model = $indicador->getSemana($sem);
                    if ($model == '') {
                        $model = new IndicadorSemana();
                        $model->id_indicador = $indicador->id_indicador;
                        $model->codigo_semana = $sem;
                    }
                    $costos = DB::table('resumen_costos_semanal')
                        ->select(DB::raw('sum(mano_obra + insumos + fijos + regalias) as cant'))
                        ->where('codigo_semana', $sem)
                        ->get()[0]->cant;

                    $data = getAreaCiclosByRango($sem, $sem, 'T');
                    $area = getAreaActivaFromData($data['variedades'], $data['semanas']) * 10000;

                    $valor = $area > 0 ? round($costos / $area, 2) : 0;

                    $model->valor = $valor;
                    $model->save();
                }
            }
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "indicador_semana:update" <<<<< * >>>>>');
    }
}