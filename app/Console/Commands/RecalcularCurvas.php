<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use yura\Jobs\ProyeccionUpdateSemanal;
use yura\Modelos\Ciclo;
use yura\Jobs\ProyeccionUpdateCiclo;
use yura\Modelos\ProyeccionModulo;
use yura\Modelos\ProyeccionModuloSemana;
use yura\Jobs\ResumenSemanaCosecha;

class RecalcularCurvas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'curva_cosecha:recalcular';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para recalcular las curvas de cosecha';

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
        $semana_pasada = getSemanaByDate(opDiasFecha('-', 7, date('Y-m-d')));
        $ciclos = DB::table('proyeccion_modulo_semana')
            ->select('modelo')->distinct()
            ->where('estado', 1)
            ->where('tabla', 'C')
            ->where('semana', $semana_pasada->codigo)
            ->where('cosechados', '>', 0)
            ->get();
        foreach ($ciclos as $c) {
            $ciclo = Ciclo::find($c->modelo);
            if ($ciclo->id_variedad == 3) {      // quitar
                $sem_ini = $ciclo->semana();
                $num_sem = intval(difFechas($semana_pasada->fecha_inicial, $sem_ini->fecha_inicial)->days / 7) + 1;
                if ($ciclo->activo == 1 && $num_sem >= $ciclo->semana_poda_siembra - 2) {   // esta activo y es una semana minima 2 antes del inicio de cosecha
                    $configuracion = getConfiguracionEmpresa();
                    $modulo = $ciclo->modulo;
                    $getTallosProyectados = $ciclo->getTallosProyectados();
                    if ($num_sem < $ciclo->semana_poda_siembra) {   // se trata de una semana antes del inicio de cosecha
                        $cosechado = DB::table('desglose_recepcion as dr')
                            ->join('recepcion as r', 'r.id_recepcion', '=', 'dr.id_recepcion')
                            ->select(DB::raw('sum(dr.cantidad_mallas * dr.tallos_x_malla) as cant'))
                            ->where('dr.estado', 1)
                            ->where('dr.id_modulo', $modulo->id_modulo)
                            ->where('r.estado', 1)
                            ->where('r.fecha_ingreso', '<=', $semana_pasada->fecha_final)
                            ->where('r.fecha_ingreso', '>=', opDiasFecha('+', 35, $ciclo->fecha_inicio))
                            ->get()[0]->cant;
                        $porc_cosechado = intval(($cosechado * 100) / $getTallosProyectados);
                        if ($porc_cosechado >= $configuracion->proy_minimo_cosecha) {   // hay que mover una semana antes la curva
                            $new_curva = getNuevaCurva($ciclo->curva, $porc_cosechado);
                            $this->update_ciclo($ciclo, $new_curva, $num_sem);
                        }
                    } else {    // se trata de una semana de curva o posterior
                        $pos_sem = $num_sem - $ciclo->semana_poda_siembra;
                        if ($pos_sem == 0) {  // primera semana de la curva
                            $cosechado = DB::table('desglose_recepcion as dr')
                                ->join('recepcion as r', 'r.id_recepcion', '=', 'dr.id_recepcion')
                                ->select(DB::raw('sum(dr.cantidad_mallas * dr.tallos_x_malla) as cant'))
                                ->where('dr.estado', 1)
                                ->where('dr.id_modulo', $modulo->id_modulo)
                                ->where('r.estado', 1)
                                ->where('r.fecha_ingreso', '<=', $semana_pasada->fecha_final)
                                ->where('r.fecha_ingreso', '>=', opDiasFecha('+', 35, $ciclo->fecha_inicio))
                                ->get()[0]->cant;
                            $porc_cosechado = intval(($cosechado * 100) / $getTallosProyectados);
                            if ($porc_cosechado < $configuracion->proy_minimo_cosecha) {    // hay que mover una semana despues
                                $this->update_ciclo($ciclo, $ciclo->curva, ($ciclo->semana_poda_siembra + 1));
                            } else {    // recalcular solamente
                                $new_curva = getNuevaCurva($ciclo->curva, $porc_cosechado);
                                $this->update_ciclo($ciclo, $new_curva, $ciclo->semana_poda_siembra);
                            }
                        } else if ($pos_sem < count(explode('-', $ciclo->curva)) - 1) {   // semana numero "$pos_sem" de la curva
                            $next_curva = explode('-', $ciclo->curva)[$pos_sem];
                            for ($i = $pos_sem; $i < count(explode('-', $ciclo->curva)); $i++) {
                                if ($i > $pos_sem)
                                    $next_curva .= '-' . explode('-', $ciclo->curva)[$i];
                            }
                            $cosechado = DB::table('proyeccion_modulo_semana')
                                ->select('cosechados')
                                ->where('estado', 1)
                                ->where('tabla', 'C')
                                ->where('semana', $semana_pasada->codigo)
                                ->where('modelo', $ciclo->id_ciclo)
                                ->get()[0]->cosechados;
                            $porc_cosechado = intval(($cosechado * 100) / $getTallosProyectados);
                            $new_curva = explode('-', $ciclo->curva)[0];
                            for ($i = 1; $i < count(explode('-', $ciclo->curva)); $i++) {
                                if ($i < $pos_sem)
                                    $new_curva .= '-' . explode('-', $ciclo->curva)[$i];
                            }
                            $new_curva .= '-' . getNuevaCurva($next_curva, $porc_cosechado);
                            $this->update_ciclo($ciclo, $new_curva, $ciclo->semana_poda_siembra);
                        } else {    // ultima semana de la curva
                            //dd('ultima semana de la curva');
                        }
                    }
                }
            }
        }
    }

    function update_ciclo($ciclo, $new_curva, $new_semana_poda_siembra)
    {
        $model = $ciclo;
        $semana_fin = getLastSemanaByVariedad($model->id_variedad);
        $last_semana_new = '';
        /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA ====================== */
        if ($model->semana_poda_siembra != $new_semana_poda_siembra || 1 ||
            $model->curva != $new_curva) { // hubo algun cambio

            $cant_semanas_old = $model->semana_poda_siembra + count(explode('-', $model->curva));   // cantidad de semanas que durará el ciclo old
            $cant_semanas_new = $new_semana_poda_siembra + count(explode('-', $new_curva));   // cantidad de semanas que durará el ciclo new
            $cant_curva_old = count(explode('-', $model->curva));   // cantidad de semanas que durará la cosecha old
            $cant_curva_new = count(explode('-', $new_curva));   // cantidad de semanas que durará la cosecha new

            /* ======================== ACTUALIZAR LAS TABLAS CICLO y PROYECCION_MODULO ====================== */
            ProyeccionUpdateCiclo::dispatch($model->id_ciclo, $new_semana_poda_siembra, $new_curva, $model->poda_siembra, $model->plantas_iniciales, $model->plantas_muertas, $model->desecho, $model->conteo, $model->area)
                ->onQueue('update_ciclo')->onConnection('sync');
            $plantas_actuales = $model->plantas_actuales();

            if ($cant_semanas_old != $cant_semanas_new) {   // hay que mover
                if ($cant_semanas_old < $cant_semanas_new) {    // hay que mover para alante
                    $semana_ini_ciclo = getSemanaByDate($model->fecha_inicio);
                    $next_proy = ProyeccionModuloSemana::where('estado', 1)
                        ->where('tabla', 'P')
                        ->where('tipo', 'Y')
                        ->where('semana', '>', $semana_ini_ciclo->codigo)
                        ->where('id_modulo', $model->id_modulo)
                        ->where('id_variedad', $model->id_variedad)
                        ->orderBy('semana')
                        ->get()->take(1);
                    $next_proy = count($next_proy) > 0 ? $next_proy[0] : '';

                    $proyecciones = ProyeccionModuloSemana::where('estado', 1)
                        ->where('semana', '>=', $semana_ini_ciclo->codigo)
                        ->where('id_modulo', $model->id_modulo)
                        ->where('id_variedad', $model->id_variedad)
                        ->orderBy('semana')
                        ->get();

                    $last_semana = '';
                    $pos_cosecha = 0;
                    $pos_proy_new = '';
                    $last_semana_new = '';
                    foreach ($proyecciones as $pos_proy => $proy) {
                        if ($pos_proy + 1 <= $cant_semanas_new - 1) {   // dentro de las semanas del ciclo
                            $proy->tabla = 'C';
                            $proy->modelo = $model->id_ciclo;

                            $proy->plantas_iniciales = $model->plantas_iniciales;
                            $proy->plantas_actuales = $plantas_actuales;
                            $proy->tallos_planta = $model->conteo;
                            $proy->tallos_ramo = 0;
                            $proy->curva = $new_curva;
                            $proy->poda_siembra = $model->poda_siembra;
                            $proy->semana_poda_siembra = $new_semana_poda_siembra;
                            $proy->desecho = $model->desecho;
                            $proy->area = $model->area;
                            $proy->tipo = 'I';
                            $proy->info = ($pos_proy + 1) . 'º';
                            $proy->proyectados = 0;

                            if ($pos_proy + 1 == 1) {   // primera semana de proyeccion
                                $proy->tipo = $model->poda_siembra;
                                $proy->info = $model->poda_siembra . '-' . $model->modulo->getPodaSiembraByCiclo($model->id_ciclo);
                            }
                            if ($pos_proy + 1 >= $new_semana_poda_siembra) {  // semana de cosecha **
                                $proy->tipo = 'T';
                                $total = $plantas_actuales * $model->conteo;
                                $total = $total * ((100 - $model->desecho) / 100);
                                $proy->proyectados = round($total * (explode('-', $new_curva)[$pos_cosecha] / 100), 2);
                                $pos_cosecha++;
                            }
                        } else if ($next_proy != '') {    // semanas despues de la proyeccion
                            if ($last_semana == '')
                                $last_semana = $proy->semana;
                            if ($last_semana > $next_proy->semana) {    // hay que mover la siguiente proyeccion
                                if ($pos_proy_new == '') {
                                    $pos_proy_new = 0;
                                    $pos_cosecha = 0;
                                }

                                if ($pos_proy_new + 1 <= $next_proy->semana_poda_siembra + count(explode('-', $next_proy->curva)) - 1) {   // esta dentro de las semanas de la proyeccion
                                    $proy->tabla = 'P';
                                    $proy->modelo = $next_proy->modelo;

                                    $proy->plantas_iniciales = $next_proy->plantas_iniciales;
                                    $proy->tallos_planta = $next_proy->tallos_planta;
                                    $proy->tallos_ramo = $next_proy->tallos_ramo;
                                    $proy->curva = $next_proy->curva;
                                    $proy->poda_siembra = $next_proy->poda_siembra;
                                    $proy->semana_poda_siembra = $next_proy->semana_poda_siembra;
                                    $proy->desecho = $next_proy->desecho;
                                    $proy->area = $next_proy->area;
                                    $proy->tipo = 'I';
                                    $proy->info = ($pos_proy_new + 1) . 'º';
                                    $proy->proyectados = 0;

                                    if ($pos_proy_new + 1 == 1) {   // primera semana de proyeccion
                                        $proy->tipo = $next_proy->tipo;
                                        $proy->info = $next_proy->info;
                                    }
                                    if ($pos_proy_new + 1 >= $next_proy->semana_poda_siembra) {  // semana de cosecha
                                        $proy->tipo = 'T';
                                        $total = $next_proy->plantas_iniciales * $next_proy->tallos_planta;
                                        $total = $total * ((100 - $next_proy->desecho) / 100);
                                        $proy->proyectados = round($total * (explode('-', $next_proy->curva)[$pos_cosecha] / 100), 2);
                                        $pos_cosecha++;
                                    }
                                } else {    // semanas despues de la proyeccion
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
                                    $proy->area = null;
                                    $proy->semana_poda_siembra = null;
                                    $proy->tallos_planta = null;
                                    $proy->poda_siembra = null;
                                    $proy->tabla = null;
                                    $proy->modelo = null;
                                }
                                $pos_proy_new++;
                            }
                        }
                        $proy->save();
                    }

                    /* ===================== RECALCULAR el # de PODA_SIEMBRA ===================== */
                    $proyecciones = ProyeccionModuloSemana::whereIn('tipo', ['S', 'P', 'Y'])
                        ->where('id_modulo', $model->id_modulo)
                        ->where('id_variedad', $model->id_variedad)
                        ->where('semana', '>=', $model->semana()->codigo)
                        ->orderBy('semana')
                        ->get();

                    $poda_siembra = $model->modulo->getPodaSiembraByCiclo($model->id_ciclo);
                    foreach ($proyecciones as $proy) {
                        if ($proy->tipo == 'Y') {
                            if ($proy->info == 'P') {
                                $last_proy = ProyeccionModulo::All()
                                    ->where('estado', 1)
                                    ->where('id_modulo', $proy->id_modulo)
                                    ->where('id_variedad', $proy->id_variedad)
                                    ->where('fecha_inicio', '<', $proy->fecha_inicio)
                                    ->sortBy('fecha_inicio')
                                    ->last();
                                if ($last_proy != '') {
                                    $poda_siembra = $last_proy->poda_siembra + 1;
                                } else {
                                    $poda_siembra = intval($poda_siembra + 1);
                                }
                            }
                            $proy->poda_siembra = $poda_siembra;
                        } else {
                            $proy->tipo = $model->poda_siembra;
                            $proy->info = $model->poda_siembra == 'S' ? 'S-0' : $model->poda_siembra . '-' . $poda_siembra;
                        }
                        $proy->save();
                    }

                } else {    // hay que mover para atras
                    $proyecciones = ProyeccionModuloSemana::where('tabla', 'C')
                        ->where('modelo', $model->id_ciclo)
                        ->orderBy('semana')
                        ->get();
                    $pos_cosecha = 0;
                    $last_semana = '';
                    foreach ($proyecciones as $pos_proy => $proy) {
                        if ($pos_proy + 1 <= $cant_semanas_new - 1) {
                            $proy->plantas_iniciales = $model->plantas_iniciales;
                            $proy->plantas_actuales = $plantas_actuales;
                            $proy->tallos_planta = $model->conteo;
                            $proy->curva = $new_curva;
                            $proy->area = $model->area;
                            $proy->poda_siembra = $model->poda_siembra;
                            $proy->semana_poda_siembra = $new_semana_poda_siembra;
                            $proy->desecho = $model->desecho;

                            if ($pos_proy + 1 >= $new_semana_poda_siembra) {   // es una semana a partir de la programacion de cosecha
                                if ($pos_proy + 1 == $new_semana_poda_siembra) {   // nueva primera semana de cosecha
                                    $proy->tipo = 'T';
                                }
                                $total = $plantas_actuales * $model->conteo;
                                $total = $total * ((100 - $model->desecho) / 100);
                                $proy->proyectados = round($total * (explode('-', $new_curva)[$pos_cosecha] / 100), 2);
                                $pos_cosecha++;
                            }
                        } else {
                            $proy->tipo = 'F';
                            $proy->proyectados = 0;
                            $proy->info = '-';
                            $proy->activo = 0;
                            $proy->plantas_iniciales = null;
                            $proy->plantas_actuales = null;
                            $proy->desecho = null;
                            $proy->curva = null;
                            $proy->area = null;
                            $proy->semana_poda_siembra = null;
                            $proy->tallos_planta = null;
                            $proy->poda_siembra = null;
                            $proy->tabla = null;
                            $proy->modelo = null;

                            if ($last_semana == '')
                                $last_semana = $proy->semana;
                        }
                        if (in_array($proy->tipo, ['S', 'P', 'T', 'F'])) {
                            $proy->save();
                        }
                    }

                    /* =========================== MOVER SIGUIENTE PROYECCION ========================== */
                    $new_proyecciones = ProyeccionModuloSemana::where('estado', 1)
                        ->where('id_modulo', $model->id_modulo)
                        ->where('id_variedad', $model->id_variedad)
                        ->where('semana', '>', $proyecciones->last()->semana)
                        ->orderBy('semana')
                        ->get();

                    if (count($new_proyecciones) > 0 && $new_proyecciones[0]->tipo == 'Y') {
                        $prev_proyecciones = ProyeccionModuloSemana::where('estado', 1)
                            ->where('id_modulo', $model->id_modulo)
                            ->where('id_variedad', $model->id_variedad)
                            ->where('semana', '>=', $last_semana)
                            ->where('semana', '<', $new_proyecciones[0]->semana)
                            ->orderBy('semana')
                            ->get();

                        $new_proy = $new_proyecciones[0];
                        $new_proyecciones = $prev_proyecciones->merge($new_proyecciones);
                        $pos_cosecha = 0;
                        $last_semana = '';
                        foreach ($new_proyecciones as $pos_proy => $proy) {
                            //dd($pos_proy + 1, $new_proy->semana_poda_siembra + count(explode('-', $new_proy->curva)) - 1);
                            if ($pos_proy + 1 <= $new_proy->semana_poda_siembra + count(explode('-', $new_proy->curva)) - 1) {   // esta dentro de las semanas de la proyeccion
                                $proy->tabla = 'P';
                                $proy->modelo = $new_proy->modelo;

                                $proy->plantas_iniciales = $new_proy->plantas_iniciales;
                                $proy->tallos_planta = $new_proy->tallos_planta;
                                $proy->tallos_ramo = $new_proy->tallos_ramo;
                                $proy->curva = $new_proy->curva;
                                $proy->poda_siembra = $new_proy->poda_siembra;
                                $proy->semana_poda_siembra = $new_proy->semana_poda_siembra;
                                $proy->desecho = $new_proy->desecho;
                                $proy->area = $new_proy->area;
                                $proy->tipo = 'I';
                                $proy->info = ($pos_proy + 1) . 'º';
                                $proy->proyectados = 0;

                                if ($pos_proy + 1 == 1) {   // primera semana de proyeccion
                                    $proy->tipo = $new_proy->tipo;
                                    $proy->info = $new_proy->info;
                                }
                                if ($pos_proy + 1 >= $new_proy->semana_poda_siembra) {  // semana de cosecha
                                    $proy->tipo = 'T';
                                    $total = $new_proy->plantas_iniciales * $new_proy->tallos_planta;
                                    $total = $total * ((100 - $new_proy->desecho) / 100);
                                    $proy->proyectados = round($total * (explode('-', $new_proy->curva)[$pos_cosecha] / 100), 2);
                                    $pos_cosecha++;
                                }
                            } else {    // semanas despues de la proyeccion
                                if ($last_semana == '') {
                                    $last_semana = $proy->semana;
                                }
                                $proy->tipo = 'F';
                                $proy->proyectados = 0;
                                $proy->info = '-';
                                $proy->activo = 0;
                                $proy->plantas_iniciales = null;
                                $proy->plantas_actuales = null;
                                $proy->desecho = null;
                                $proy->curva = null;
                                $proy->area = null;
                                $proy->semana_poda_siembra = null;
                                $proy->tallos_planta = null;
                                $proy->poda_siembra = null;
                                $proy->tabla = null;
                                $proy->modelo = null;
                            }
                            $proy->save();
                        }
                    }

                    $last_semana_new = $last_semana;

                    /* ===================== RECALCULAR el # de PODA_SIEMBRA ===================== */
                    $proyecciones = ProyeccionModuloSemana::whereIn('tipo', ['S', 'P', 'Y'])
                        ->where('id_modulo', $model->id_modulo)
                        ->where('id_variedad', $model->id_variedad)
                        ->where('semana', '>=', $model->semana()->codigo)
                        ->orderBy('semana')
                        ->get();

                    $poda_siembra = $model->modulo->getPodaSiembraByCiclo($model->id_ciclo);
                    foreach ($proyecciones as $proy) {
                        if ($proy->tipo == 'Y') {
                            if ($proy->info == 'P') {
                                $last_proy = ProyeccionModulo::All()
                                    ->where('estado', 1)
                                    ->where('id_modulo', $proy->id_modulo)
                                    ->where('id_variedad', $proy->id_variedad)
                                    ->where('fecha_inicio', '<', $proy->fecha_inicio)
                                    ->sortBy('fecha_inicio')
                                    ->last();
                                if ($last_proy != '') {
                                    $poda_siembra = $last_proy->poda_siembra + 1;
                                } else {
                                    $poda_siembra = intval($poda_siembra + 1);
                                }
                            }
                            $proy->poda_siembra = $poda_siembra;
                        } else {
                            $proy->tipo = $model->poda_siembra;
                            $proy->info = $model->poda_siembra == 'S' ? 'S-0' : $model->poda_siembra . '-' . $poda_siembra;
                        }
                        $proy->save();
                    }
                }
            } else if ($cant_curva_old != $cant_curva_new) {   // no hay que mover, pero hay que recalcular la curva
                $proyecciones = ProyeccionModuloSemana::whereIn('tipo', ['T'])
                    ->where('id_modulo', $model->id_modulo)
                    ->where('id_variedad', $model->id_variedad)
                    ->where('tabla', 'C')
                    ->where('modelo', $model->id_ciclo)
                    ->orderBy('semana')
                    ->get();
                if ($cant_curva_new < $cant_curva_old) {    // quitar semanas de cosecha
                    $cant_quitar = $cant_curva_old - $cant_curva_new;
                    $pos_cosecha = 0;
                    foreach ($proyecciones as $pos_proy => $proy) {
                        $proy->plantas_iniciales = $model->plantas_iniciales;
                        $proy->plantas_actuales = $plantas_actuales;
                        $proy->tallos_planta = $model->conteo;
                        $proy->curva = $new_curva;
                        $proy->area = $model->area;
                        $proy->poda_siembra = $model->poda_siembra;
                        $proy->semana_poda_siembra = $new_semana_poda_siembra;
                        $proy->desecho = $model->desecho;

                        if (($pos_proy + 1) <= $cant_quitar) {    // convertir a tipo I
                            $proy->tipo = 'I';
                            $proy->proyectados = 0;
                        } else {    // recalcular % de curva
                            $total = $plantas_actuales * $model->conteo;
                            $total = $total * ((100 - $model->desecho) / 100);
                            $proy->proyectados = round($total * (explode('-', $new_curva)[$pos_cosecha] / 100), 2);
                            $pos_cosecha++;
                        }
                        $proy->save();
                    }
                } else {    // aumentar semanas de cosecha
                    $cant_aumentar = $cant_curva_new - $cant_curva_old;
                    $add_proyecciones = ProyeccionModuloSemana::whereIn('tipo', ['I'])
                        ->where('tabla', 'C')
                        ->where('modelo', $model->id_ciclo)
                        ->orderBy('semana', 'desc')
                        ->take($cant_aumentar)
                        ->get();
                    $proyecciones = $add_proyecciones->merge($proyecciones);
                    $pos_cosecha = 0;
                    foreach ($proyecciones as $pos_proy => $proy) {
                        $proy->plantas_iniciales = $model->plantas_iniciales;
                        $proy->plantas_actuales = $plantas_actuales;
                        $proy->tallos_planta = $model->conteo;
                        $proy->curva = $new_curva;
                        $proy->area = $model->area;
                        $proy->poda_siembra = $model->poda_siembra;
                        $proy->semana_poda_siembra = $new_semana_poda_siembra;
                        $proy->desecho = $model->desecho;
                        $proy->tipo = 'T';

                        $total = $plantas_actuales * $model->conteo;
                        $total = $total * ((100 - $model->desecho) / 100);
                        $proy->proyectados = round($total * (explode('-', $new_curva)[$pos_cosecha] / 100), 2);
                        $pos_cosecha++;

                        $proy->save();
                    }
                }

                /* ===================== RECALCULAR el # de PODA_SIEMBRA ===================== */
                $proyecciones = ProyeccionModuloSemana::whereIn('tipo', ['S', 'P', 'Y'])
                    ->where('id_modulo', $model->id_modulo)
                    ->where('id_variedad', $model->id_variedad)
                    ->where('semana', '>=', $model->semana()->codigo)
                    ->orderBy('semana')
                    ->get();

                $poda_siembra = $model->modulo->getPodaSiembraByCiclo($model->id_ciclo);
                foreach ($proyecciones as $proy) {
                    if ($proy->tipo == 'Y') {
                        if ($proy->info == 'P') {
                            $last_proy = ProyeccionModulo::All()
                                ->where('estado', 1)
                                ->where('id_modulo', $proy->id_modulo)
                                ->where('id_variedad', $proy->id_variedad)
                                ->where('fecha_inicio', '<', $proy->fecha_inicio)
                                ->sortBy('fecha_inicio')
                                ->last();
                            if ($last_proy != '') {
                                $poda_siembra = $last_proy->poda_siembra + 1;
                            } else {
                                $poda_siembra = intval($poda_siembra + 1);
                            }
                        }
                        $proy->poda_siembra = $poda_siembra;
                    } else {
                        $proy->tipo = $model->poda_siembra;
                        $proy->info = $model->poda_siembra == 'S' ? 'S-0' : $model->poda_siembra . '-' . $poda_siembra;
                    }
                    $proy->save();
                }

            } else {    // no hay que mover, solo actualizar datos
                $proyecciones = ProyeccionModuloSemana::whereIn('tipo', ['S', 'P', 'T', 'Y'])
                    ->where('id_modulo', $model->id_modulo)
                    ->where('id_variedad', $model->id_variedad)
                    ->where('semana', '>=', $model->semana()->codigo)
                    ->orderBy('semana')
                    ->get();

                $pos_cosecha = 0;
                foreach ($proyecciones as $pos_proy => $proy) {
                    if ($proy->tipo == 'Y') {
                        $poda_siembra = 0;
                        if ($proy->info == 'P') {
                            $poda_siembra = $model->modulo->getPodaSiembraByCiclo($model->id_ciclo);
                            $last_proy = ProyeccionModulo::All()
                                ->where('estado', 1)
                                ->where('id_modulo', $proy->id_modulo)
                                ->where('id_variedad', $proy->id_variedad)
                                ->where('fecha_inicio', '<', $proy->fecha_inicio)
                                ->sortBy('fecha_inicio')
                                ->last();
                            if ($last_proy != '') {
                                $poda_siembra = $last_proy->poda_siembra + 1;
                            } else {
                                $poda_siembra = intval($poda_siembra + 1);
                            }
                        }
                        $proy->poda_siembra = $poda_siembra;
                    } else if ($proy->tabla == 'C') {
                        $proy->plantas_iniciales = $model->plantas_iniciales;
                        $proy->plantas_actuales = $plantas_actuales;
                        $proy->tallos_planta = $model->conteo;
                        $proy->curva = $new_curva;
                        $proy->area = $model->area;
                        $proy->poda_siembra = $model->poda_siembra;
                        $proy->semana_poda_siembra = $new_semana_poda_siembra;
                        $proy->desecho = $model->desecho;

                        if ($proy->tipo == 'T') {
                            $total = $plantas_actuales * $model->conteo;
                            $total = $total * ((100 - $model->desecho) / 100);
                            $proy->proyectados = round($total * (explode('-', $new_curva)[$pos_cosecha] / 100), 2);
                            $pos_cosecha++;
                        } else {    //  se trata de una semana de inicio de ciclo, (Poda o Siembra)
                            $proy->tipo = $model->poda_siembra;
                            $proy->info = $model->poda_siembra == 'S' ? 'S-0' : $model->poda_siembra . '-' . $model->modulo->getPodaSiembraByCiclo($model->id_ciclo);
                        }
                    }

                    $proy->save();
                }
            }
        }

        /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA FINAL ====================== */
        $semana_desde = $last_semana_new;

        if ($semana_desde != '')
            ProyeccionUpdateSemanal::dispatch($semana_desde, $semana_fin->codigo, $model->id_variedad, $model->id_modulo, 0)
                ->onQueue('update_ciclo');

        /* ======================== ACTUALIZAR LA TABLA RESUMEN_COSECHA_SEMANA FINAL ====================== */
        ResumenSemanaCosecha::dispatch($model->semana()->codigo, $semana_fin->codigo, $model->id_variedad)
            ->onQueue('resumen_cosecha_semanal');
    }
}