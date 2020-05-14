<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Ciclo;
use yura\Modelos\ProyeccionModulo;
use yura\Modelos\Semana;

class ProyeccionUpdateCiclo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proyeccion:update_ciclo {id_ciclo} {semana_poda_siembra} {curva} {poda_siembra} {plantas_iniciales} {plantas_muertas} {desecho} {conteo} {area} {no_recalcular_curva}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commando para actualizar los datos en las tablas Ciclo y Proyeccion_Modulo';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "proyeccion:update_ciclo" <<<<< ! >>>>>');
        $par_id_ciclo = $this->argument('id_ciclo');
        $par_semana_poda_siembra = $this->argument('semana_poda_siembra');
        $par_curva = $this->argument('curva');
        $par_poda_siembra = $this->argument('poda_siembra');
        $par_plantas_iniciales = $this->argument('plantas_iniciales');
        $par_plantas_muertas = $this->argument('plantas_muertas');
        $par_desecho = $this->argument('desecho');
        $par_conteo = $this->argument('conteo');
        $par_area = $this->argument('area');
        $par_no_recalcular_curva = $this->argument('no_recalcular_curva');

        $model = Ciclo::find($par_id_ciclo);
        $semana_fin = getLastSemanaByVariedad($model->id_variedad);

        $sum_semana_new = $par_semana_poda_siembra + count(explode('-', $par_curva));
        $sum_semana_old = $model->semana_poda_siembra + count(explode('-', $model->curva));

        $model->poda_siembra = $par_poda_siembra;
        $model->curva = $par_curva;
        $model->semana_poda_siembra = $par_semana_poda_siembra;
        $model->plantas_iniciales = $par_plantas_iniciales;
        $model->plantas_muertas = $par_plantas_muertas;
        $model->desecho = $par_desecho;
        $model->conteo = $par_conteo;
        $model->area = $par_area;
        $model->no_recalcular_curva = $par_no_recalcular_curva;

        $model->save();
        bitacora('ciclo', $model->id_ciclo, 'U', 'Actualización satisfactoria de un ciclo');

        $poda_siembra = $model->modulo->getPodaSiembraByCiclo($model->id_ciclo);

        if ($sum_semana_new != $sum_semana_old) {   // hay que mover las proyecciones
            $semana = Semana::All()
                ->where('estado', 1)
                ->where('id_variedad', $model->id_variedad)
                ->where('fecha_inicial', '<=', $model->fecha_inicio)
                ->where('fecha_final', '>=', $model->fecha_inicio)
                ->first();

            /* ------------------------ OBTENER LAS SEMANAS NEW/OLD ---------------------- */
            $codigo = $semana->codigo;
            $new_codigo = $semana->codigo;
            $i = 1;
            $next = 1;
            while ($i < $sum_semana_new && $new_codigo <= $semana_fin->codigo) {
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

            if ($new_codigo <= $semana_fin->codigo) {   // aun es una semana programada
                $new_codigo = $semana->codigo;
                $i = 1;
                $next = 1;
                while ($i < $sum_semana_old && $new_codigo <= $semana_fin->codigo) {
                    $new_codigo = $codigo + $next;
                    $semana_old = Semana::All()
                        ->where('estado', '=', 1)
                        ->where('codigo', '=', $new_codigo)
                        ->where('id_variedad', '=', $model->id_variedad)
                        ->first();

                    if ($semana_old != '') {
                        $i++;
                    }
                    $next++;
                }

                $proy = ProyeccionModulo::where('estado', 1)
                    ->where('id_modulo', $model->id_modulo)
                    ->where('id_variedad', $model->id_variedad)
                    ->orderBy('fecha_inicio')
                    ->get()->first();
                if ($proy != '')
                    if ($proy->id_semana == $semana_old->id_semana || $proy->semana->codigo < $semana_new->codigo) {    // hay que mover
                        /*$del_proyecciones = ProyeccionModulo::where('estado', 1)
                            ->where('id_modulo', $model->id_modulo)
                            ->where('id_variedad', $model->id_variedad)
                            ->where('fecha_inicio', '>', $proy->fecha_inicio)
                            ->get();
                        foreach ($del_proyecciones as $next_proy)    // borrar las proyecciones a partir de esta proyeccion en adelante
                            $next_proy->delete();*/

                        $proy->id_semana = $semana_new->id_semana;
                        $proy->fecha_inicio = $semana_new->fecha_final;
                        $proy->desecho = $semana_new->desecho > 0 ? $semana_new->desecho : 0;
                        $proy->tallos_planta = $semana_new->tallos_planta_poda > 0 ? $semana_new->tallos_planta_poda : 0;
                        $proy->tallos_ramo = $semana_new->tallos_ramo_poda > 0 ? $semana_new->tallos_ramo_poda : 0;

                        $proy->poda_siembra = $proy->tipo != 'S' ? $poda_siembra + 1 : 0;

                        $proy->save();
                        $proy->restaurar_proyecciones();
                    }
            } else {    // se pasa de la ultima semana programada
                /* ======================== QUITAR PROYECCIONES ======================= */
                $proys = ProyeccionModulo::where('estado', 1)
                    ->where('id_modulo', $model->id_modulo)
                    ->where('id_variedad', $model->id_variedad)
                    ->orderBy('fecha_inicio')
                    ->get();
                foreach ($proys as $proy)
                    $proy->delete();
            }
        } else {    // no hay que mover pero hay que recalcular el campo poda_siembra en las proyecciones
            $proyecciones = ProyeccionModulo::where('estado', 1)
                ->where('id_modulo', $model->id_modulo)
                ->where('id_variedad', $model->id_variedad)
                ->orderBy('fecha_inicio')
                ->get();

            foreach ($proyecciones as $pos_proy => $proy) {
                if ($proy->tipo == 'S') {
                    $proy->poda_siembra = 0;
                    $poda_siembra = 0;
                } else {
                    $proy->poda_siembra = $poda_siembra + 1;
                    $poda_siembra++;
                }
                $proy->save();
            }
        }
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "proyeccion:update_ciclo" <<<<< * >>>>>');
    }
}
