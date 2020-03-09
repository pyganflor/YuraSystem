<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Regalias;
use yura\Modelos\Semana;
use yura\Modelos\Variedad;

class UpdateRegalias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regalias:update {desde=0} {hasta=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para actualizar las  regalias';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "regalias:update" <<<<< ! >>>>>');

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

            foreach (Variedad::All() as $variedad) {
                foreach ($array_semanas as $sem) {
                    $gastos_anterior = $variedad->getRegaliasLastSemana($sem);
                    $model = $variedad->regaliasBySemana($sem);
                    if ($model == '') {
                        $model = new Regalias();
                        $model->id_variedad = $variedad->id_variedad;
                        $model->codigo_semana = $sem;
                        $model->valor = $gastos_anterior != '' ? $gastos_anterior->valor : 0;
                    } else {
                        if ($model->valor == 0)
                            $model->valor = $gastos_anterior != '' ? $gastos_anterior->valor : 0;
                    }
                    $model->save();
                }
            }
        }
        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "regalias:update" <<<<< * >>>>>');
    }
}
