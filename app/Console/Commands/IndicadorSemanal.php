<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Modelos\IndicadorSemana;
use yura\Modelos\IndicadorVariedad;
use yura\Modelos\Semana;
use yura\Modelos\Variedad;

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

                    $semana = Semana::All()
                        ->where('codigo', $sem)
                        ->first();
                    $sem_desde = getSemanaByDate(opDiasFecha('-', 112, $semana->fecha_inicial));   // 16 semana atras
                    $sem_hasta = $semana;

                    $costos = DB::table('resumen_costos_semanal')
                        ->select(DB::raw('sum(mano_obra + insumos + fijos + regalias) as cant'))
                        ->where('codigo_semana', '>=', $sem_desde->codigo)
                        ->where('codigo_semana', '<=', $sem_hasta->codigo)
                        ->get()[0]->cant;
                    $area = DB::table('resumen_area_semanal')
                        ->select(DB::raw('sum(area) as cant'))
                        ->where('codigo_semana', '>=', $sem_desde->codigo)
                        ->where('codigo_semana', '<=', $sem_hasta->codigo)
                        ->get()[0]->cant;

                    $valor = $area > 0 ? round(($costos / ($area / 16)) * 3, 2) : 0;

                    $model->valor = $valor;
                    $model->save();
                }
            }

            /* ========================== D9 Venta $/m2/año (-4 meses) =========================== */
            $indicador = getIndicadorByName('D9');  // Venta $/m2/año (-4 meses)
            if ($indicador != '') {
                foreach ($array_semanas as $sem) {
                    $model = $indicador->getSemana($sem);
                    if ($model == '') {
                        $model = new IndicadorSemana();
                        $model->id_indicador = $indicador->id_indicador;
                        $model->codigo_semana = $sem;
                    }
                    $semana = Semana::All()
                        ->where('codigo', $sem)
                        ->first();

                    $hasta_sem = $semana;
                    $desde_sem = getSemanaByDate(opDiasFecha('-', 105, $hasta_sem->fecha_inicial));

                    $venta_mensual = DB::table('resumen_semanal_total')
                        ->select(DB::raw('sum(valor) as cant'))
                        //->where('estado', 1)
                        ->where('codigo_semana', '>=', $desde_sem->codigo)
                        ->where('codigo_semana', '<=', $hasta_sem->codigo)
                        ->get()[0]->cant;

                    $semana_desde = getSemanaByDate(opDiasFecha('-', 112, $desde_sem->fecha_inicial));   // 16 semanas atras
                    $semana_hasta = $desde_sem;

                    $data = getAreaCiclosByRango($semana_desde->codigo, $semana_hasta->codigo, 'T');
                    $area_anual = getAreaActivaFromData($data['variedades'], $data['semanas']) * 10000;

                    /*if ($sem == 2005)
                        dd($desde_sem->codigo, $hasta_sem->codigo, $venta_mensual, $semana_desde->codigo, $semana_hasta->codigo, $area_anual);*/

                    $model->valor = $area_anual > 0 ? round(($venta_mensual / $area_anual) * 3, 2) : 0;
                    $model->save();

                    /* ============================== INDICADOR x VARIEDAD ================================= */
                    foreach (Variedad::All() as $var) {
                        $ind_var = IndicadorVariedad::All()
                            ->where('id_indicador', $model->id_indicador)
                            ->where('id_variedad', $var->id_variedad)
                            ->first();
                        if ($ind_var != '') {

                        }
                    }
                }
            }

            /* ========================== R1 Rentabilidad (-4 meses) =========================== */
            $indicador = getIndicadorByName('R1');  // Rentabilidad (-4 meses)
            if ($indicador != '') {
                foreach ($array_semanas as $sem) {
                    $model = $indicador->getSemana($sem);
                    if ($model == '') {
                        $model = new IndicadorSemana();
                        $model->id_indicador = $indicador->id_indicador;
                        $model->codigo_semana = $sem;
                    }

                    $valor = getIndicadorByName('D9')->getSemana($sem)->valor - getIndicadorByName('C9')->getSemana($sem)->valor;
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