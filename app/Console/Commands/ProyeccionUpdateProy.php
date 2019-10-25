<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Ciclo;
use yura\Modelos\ProyeccionModulo;
use yura\Modelos\Semana;

class ProyeccionUpdateProy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proyeccion:update_proy {id_proyeccion_modulo} {semana} {tipo} {curva} {semana_poda_siembra} {plantas_iniciales} {desecho} {tallos_planta} {tallos_ramo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commando para actualizar la tabla proyeccion_modulo';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "proyeccion:update_proy" <<<<< ! >>>>>');
        $par_id_proyeccion_modulo = $this->argument('id_proyeccion_modulo');
        $par_semana = $this->argument('semana');
        $par_tipo = $this->argument('tipo');
        $par_curva = $this->argument('curva');
        $par_semana_poda_siembra = $this->argument('semana_poda_siembra');
        $par_plantas_iniciales = $this->argument('plantas_iniciales');
        $par_desecho = $this->argument('desecho');
        $par_tallos_planta = $this->argument('tallos_planta');
        $par_tallos_ramo = $this->argument('tallos_ramo');

        $model = ProyeccionModulo::find($par_id_proyeccion_modulo);
        $semana_ini = Semana::All()->where('estado', 1)->where('id_variedad', $model->id_variedad)
            ->where('codigo', $par_semana)->first();
        $poda_siembra = 0;

        if ($par_tipo == 'C') {    // borrar las siguientes proyecciones
            $next_proyecciones = ProyeccionModulo::All()
                ->where('fecha_inicio', '>=', $model->fecha_inicio)
                ->where('id_modulo', $model->id_modulo)
                ->where('id_variedad', $model->id_variedad);

            foreach ($next_proyecciones as $proy) {
                $proy->delete();
            }
        } else {
            $next_proy = ProyeccionModulo::where('estado', 1)
                ->where('id_modulo', $model->id_modulo)
                ->where('id_variedad', $model->id_variedad)
                ->where('fecha_inicio', '>', $model->fecha_inicio)
                ->get();

            $model->tipo = $par_tipo;
            $model->curva = $par_curva;
            $model->semana_poda_siembra = $par_semana_poda_siembra;
            $model->plantas_iniciales = $par_plantas_iniciales;
            $model->desecho = $par_desecho;
            $model->tallos_planta = $par_tallos_planta;
            $model->tallos_ramo = $par_tallos_ramo;
            $model->id_semana = $semana_ini->id_semana;
            $model->poda_siembra = $poda_siembra;

            if ($model->tipo == 'P') {
                $last_ciclo = Ciclo::All()
                    ->where('estado', 1)
                    ->where('id_variedad', $model->id_variedad)
                    ->where('id_modulo', $model->id_modulo)
                    ->sortBy('fecha_inicio')
                    ->last();
                if ($last_ciclo != '') {
                    $last_proy = ProyeccionModulo::All()
                        ->where('estado', 1)
                        ->where('id_modulo', $model->id_modulo)
                        ->where('id_variedad', $model->id_variedad)
                        ->where('fecha_inicio', '<', $model->fecha_inicio)
                        ->sortBy('fecha_inicio')
                        ->last();
                    if ($last_proy != '') {
                        $poda_siembra = $last_proy->poda_siembra + 1;
                    } else {
                        $poda_siembra = intval($last_ciclo->modulo->getPodaSiembraByCiclo($last_ciclo->id_ciclo) + 1);
                    }
                    $model->poda_siembra = $poda_siembra;
                }
            }
            $model->fecha_inicio = $semana_ini->fecha_final;

            /* ========================================= ¿ MOVER SIGUIETNE PROYECCION ? ================================= */
            if (count($next_proy) > 0) {
                $next_proy = $next_proy[0];

                $sum_semanas = $model->semana_poda_siembra + count(explode('-', $model->curva));
                $semana_fin = getLastSemanaByVariedad($model->id_variedad);
                $codigo = $semana_ini->codigo;
                $new_codigo = $semana_ini->codigo;
                $i = 1;
                $next = 1;
                while ($i < $sum_semanas && $new_codigo <= $semana_fin->codigo) {
                    $new_codigo = $codigo + $next;
                    $semana_new = Semana::All()
                        ->where('estado', '=', 1)
                        ->where('codigo', '=', $new_codigo)
                        ->where('id_variedad', '=', $model->id_variedad)
                        ->first();

                    if ($semana_new != '') {
                        $i++;
                    }
                    $next++;
                }
                if ($semana_new->fecha_inicial > $next_proy->fecha_inicio) {    // hay que mover
                    $del_proyecciones = ProyeccionModulo::where('estado', 1)
                        ->where('id_modulo', $model->id_modulo)
                        ->where('id_variedad', $model->id_variedad)
                        ->where('fecha_inicio', '>', $next_proy->fecha_inicio)
                        ->get();
                    foreach ($del_proyecciones as $proy)    // borrar las proyecciones a partir de la tercera proyeccion en adelante
                        $proy->delete();

                    $next_proy->fecha_inicio = $semana_new->fecha_inicial;
                    $next_proy->id_semana = $semana_new->id_semana;
                }

                $next_proy->poda_siembra = $next_proy->tipo == 'S' ? 0 : $poda_siembra + 1;

                $next_proy->save();
            }
            $model->save();
            bitacora('proyeccion_modulo', $model->id_proyeccion_modulo, 'U', 'Actualización satisfactoria de la proyección');
        }
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "proyeccion:update_proy" <<<<< * >>>>>');
    }
}