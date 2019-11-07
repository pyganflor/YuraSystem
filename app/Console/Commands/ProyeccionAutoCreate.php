<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Jobs\ProyeccionUpdateSemanal;
use yura\Modelos\Ciclo;
use yura\Modelos\ProyeccionModulo;
use yura\Modelos\ProyeccionModuloSemana;
use yura\Modelos\Semana;

class ProyeccionAutoCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proyeccion:auto_create {modulo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commando para crear automaticamente proyecciones a un modulo con ciclo activo';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "proyeccion:auto_create" <<<<< ! >>>>>');
        $modulo = $this->argument('modulo');

        $ciclo = Ciclo::All()
            ->where('estado', 1)
            ->where('activo', 1)
            ->where('id_modulo', $modulo)
            ->first();

        if ($ciclo != '') {
            $semana = Semana::All()
                ->where('estado', 1)
                ->where('id_variedad', $ciclo->id_variedad)
                ->where('fecha_inicial', '<=', $ciclo->fecha_inicio)
                ->where('fecha_final', '>=', $ciclo->fecha_inicio)
                ->first();

            $sum_semana = intval($ciclo->semana_poda_siembra) + intval(count(explode('-', $ciclo->curva)));
            $codigo = $semana->codigo;
            $new_codigo = $semana->codigo;
            $i = 1;
            $next = 1;
            while ($i < $sum_semana && $new_codigo <= getLastSemanaByVariedad($ciclo->id_variedad)->codigo) {
                $new_codigo = $codigo + $next;
                $query = Semana::All()
                    ->where('estado', '=', 1)
                    ->where('codigo', '=', $new_codigo)
                    ->where('id_variedad', '=', $ciclo->id_variedad)
                    ->first();

                if ($query != '') {
                    $i++;
                }
                $next++;
            }

            /* ===================== QUITAR PROYECCIONES =================== */
            $proyecciones = ProyeccionModulo::All()
                ->where('estado', 1)
                ->where('id_variedad', $ciclo->id_variedad)
                ->where('id_modulo', $modulo);
            foreach ($proyecciones as $proy) {
                $proy->delete();
            }

            if ($query != '') {
                $proy = new ProyeccionModulo();
                $proy->id_modulo = $ciclo->id_modulo;
                $proy->id_semana = $query->id_semana;
                $proy->id_variedad = $ciclo->id_variedad;
                $proy->tipo = 'P';
                $proy->curva = $ciclo->curva;
                $proy->semana_poda_siembra = $ciclo->semana_poda_siembra;
                $proy->poda_siembra = $ciclo->modulo->getPodaSiembraByCiclo($ciclo->id_ciclo) + 1;
                $proy->plantas_iniciales = $ciclo->plantas_iniciales != '' ? $ciclo->plantas_iniciales : 0;
                $proy->desecho = $ciclo->desecho;
                $proy->tallos_planta = $ciclo->conteo != '' ? $ciclo->conteo : 0;
                $proy->tallos_ramo = $query->tallos_ramo_poda != '' ? $query->tallos_ramo_poda : 0;
                $proy->fecha_inicio = $query->fecha_final;

                $proy->save();
                $model = ProyeccionModulo::All()->where('estado', 1)->where('id_modulo', $modulo)->last();

                /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA ====================== */
                $cant_semanas_new = $model->semana_poda_siembra + count(explode('-', $model->curva));   // cantidad de semanas que durará la proy new

                $proyecciones = ProyeccionModuloSemana::where('estado', 1)
                    ->where('id_modulo', $model->id_modulo)
                    ->where('id_variedad', $model->id_variedad)
                    ->where('semana', '>=', $model->semana->codigo)
                    ->orderBy('semana')
                    ->get();

                $last_semana_new = '';
                $pos_cosecha = 0;
                foreach ($proyecciones as $pos_proy => $proy) {
                    if ($pos_proy + 1 <= $cant_semanas_new - 1) {   // // dentro de las semanas de la proy
                        $proy->tabla = 'P';
                        $proy->modelo = $model->id_proyeccion_modulo;

                        $proy->plantas_iniciales = $model->plantas_iniciales;
                        $proy->tallos_planta = $model->tallos_planta;
                        $proy->tallos_ramo = $model->tallos_planta;
                        $proy->curva = $model->curva;
                        $proy->poda_siembra = $model->poda_siembra;
                        $proy->semana_poda_siembra = $model->semana_poda_siembra;
                        $proy->desecho = $model->desecho;
                        $proy->area = $model->modulo->area;
                        $proy->tipo = 'I';
                        $proy->info = ($pos_proy + 1) . 'º';
                        $proy->proyectados = 0;

                        if ($pos_proy + 1 == 1) {   // primera semana de proyeccion
                            $proy->tipo = 'Y';
                            $proy->info = $model->tipo;
                        }
                        if ($pos_proy + 1 >= $model->semana_poda_siembra) {  // semana de cosecha **
                            $proy->tipo = 'T';
                            $total = $model->plantas_iniciales * $model->tallos_planta;
                            $total = $total * ((100 - $model->desecho) / 100);
                            $proy->proyectados = round($total * (explode('-', $model->curva)[$pos_cosecha] / 100), 2);
                            $pos_cosecha++;
                        }
                    } else {    // fuera de las semanas de la proy
                        if ($last_semana_new == '') {
                            $last_semana_new = $proy->semana;
                        }
                        $proy->tipo = 'F';
                        $proy->proyectados = 0;
                        $proy->info = '-';
                        $proy->activo = 0;
                        $proy->plantas_iniciales = null;
                        $proy->plantas_actuales = null;
                        $proy->desecho = null;
                        $proy->curva = null;
                        $proy->semana_poda_siembra = null;
                        $proy->tallos_planta = null;
                        $proy->poda_siembra = null;
                        $proy->tabla = null;
                        $proy->modelo = null;
                    }
                    $proy->save();
                }

                /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA FINAL ====================== */
                $semana_desde = $last_semana_new;
                $semana_fin = getLastSemanaByVariedad($model->id_variedad);

                if ($semana_desde != '')
                    ProyeccionUpdateSemanal::dispatch($semana_desde, $semana_fin->codigo, $model->id_variedad, $model->id_modulo, 0)
                        ->onQueue('proy_cosecha/store_proyeccion');
            } else {
                Log::info('<*> La semana se encuentra fuera de lo programado  <*>');
            }
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> MODULO: ' . getModuloById($modulo)->nombre . '  <*>');


        Log::info('<*> DURACION: ' . $time_duration . '  <*>');

        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "proyeccion:auto_create" <<<<< * >>>>>');
    }
}