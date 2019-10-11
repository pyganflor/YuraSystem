<?php

namespace yura\Http\Controllers\Proyecciones;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Validator;
use yura\Http\Controllers\Controller;
use yura\Jobs\ProyeccionUpdateCiclo;
use yura\Jobs\ProyeccionUpdateProy;
use yura\Jobs\ProyeccionUpdateSemanal;
use yura\Modelos\Ciclo;
use yura\Modelos\ProyeccionModulo;
use yura\Modelos\ProyeccionModuloSemana;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;

class proyCosechaController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.proyecciones.cosecha.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function listar_proyecciones(Request $request)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        set_time_limit(120);

        $semana_desde_par = Semana::All()->where('codigo', $request->desde)->first();
        $semana_hasta = Semana::All()->where('codigo', $request->hasta)->first();

        if ($semana_desde_par != '' && $semana_hasta != '') {
            $fecha_ini = DB::table('ciclo')
                ->select(DB::raw('min(fecha_inicio) as inicio'))->distinct()
                ->where('estado', '=', 1)
                ->where('id_variedad', '=', $request->variedad)
                ->where('fecha_fin', '>=', $semana_desde_par->fecha_inicial)
                ->get()[0]->inicio;

            if ($fecha_ini != '') {
                $semana_desde = getSemanaByDate($fecha_ini);

                $array_semanas = [];
                $semanas = [];
                for ($i = $semana_desde->codigo; $i <= $request->hasta; $i++) {
                    $semana = Semana::All()
                        ->where('estado', 1)
                        ->where('id_variedad', '=', $request->variedad)
                        ->where('codigo', $i)->first();
                    if ($semana != '')
                        if (!in_array($semana->codigo, $array_semanas)) {
                            array_push($array_semanas, $semana->codigo);
                            array_push($semanas, $semana);
                        }
                }

                $query_modulos = DB::table('ciclo')
                    ->select('id_modulo')->distinct()
                    ->where('estado', '=', 1)
                    ->where('id_variedad', '=', $request->variedad)
                    ->where('fecha_fin', '>=', $semana_desde_par->fecha_inicial)
                    ->orderBy('activo', 'desc')
                    ->orderBy('fecha_inicio', 'asc')
                    ->get();

                $array_modulos = [];
                foreach ($query_modulos as $mod) {
                    $mod = getModuloById($mod->id_modulo);
                    $valores = $mod->getProyeccionesByRango($semana_desde->codigo, $request->hasta, $request->variedad);

                    array_push($array_modulos, [
                        'modulo' => $mod,
                        'valores' => $valores,
                    ]);
                }

                return view('adminlte.gestion.proyecciones.cosecha.partials.listado', [
                    'semanas' => $semanas,
                    'modulos' => $array_modulos,
                    'variedad' => $request->variedad,
                    'semana_desde' => $semana_desde,
                    'opcion' => $request->opcion,
                    'detalle' => $request->detalle,
                    'ramos_x_caja' => getConfiguracionEmpresa()->ramos_x_caja,
                ]);
            } else
                return 'No se han encontrado módulos en el rango establecido.';
        } else
            return 'Revise las semanas, están incorrectas.';
    }

    public function select_celda(Request $request)
    {
        if ($request->tipo == 'F') {    // crear una proyecccion
            return view('adminlte.gestion.proyecciones.cosecha.forms.new_proy', [
                'modulo' => getModuloById($request->modulo),
                'semana' => Semana::All()->where('codigo', $request->semana)->where('id_variedad', $request->variedad)->first(),
                'variedad' => getVariedad($request->variedad),
                'last_ciclo' => Ciclo::All()
                    ->where('estado', 1)
                    ->where('id_variedad', $request->variedad)
                    ->where('id_modulo', $request->modulo)->last(),
            ]);
        }
        if ($request->tipo == 'Y') {    // crear una proyecccion
            $semana = Semana::All()->where('codigo', $request->semana)->where('id_variedad', $request->variedad)->first();
            $variedad = getVariedad($request->variedad);
            return view('adminlte.gestion.proyecciones.cosecha.forms.edit_proy', [
                'modulo' => getModuloById($request->modulo),
                'semana' => $semana,
                'variedad' => $variedad,
                'proyeccion' => ProyeccionModulo::find($request->modelo),
            ]);
        }
        if (in_array($request->tipo, ['P', 'S'])) {    // editar ciclo poda
            $semana = Semana::All()->where('codigo', $request->semana)->where('id_variedad', $request->variedad)->first();
            $variedad = getVariedad($request->variedad);
            return view('adminlte.gestion.proyecciones.cosecha.forms.edit_ciclo', [
                'modulo' => getModuloById($request->modulo),
                'semana' => $semana,
                'variedad' => $variedad,
                'ciclo' => Ciclo::find($request->modelo),
            ]);
        }
        if ($request->tipo == 'T') {    // crear una proyecccion
            if ($request->tabla == 'P') {
                $semana = Semana::All()->where('codigo', $request->semana)->where('id_variedad', $request->variedad)->first();
                $variedad = getVariedad($request->variedad);
                return view('adminlte.gestion.proyecciones.cosecha.forms.edit_proy', [
                    'modulo' => getModuloById($request->modulo),
                    'semana' => $semana,
                    'variedad' => $variedad,
                    'proyeccion' => ProyeccionModulo::find($request->modelo),
                ]);
            } else {
                $semana = Semana::All()->where('codigo', $request->semana)->where('id_variedad', $request->variedad)->first();
                $variedad = getVariedad($request->variedad);
                return view('adminlte.gestion.proyecciones.cosecha.forms.edit_ciclo', [
                    'modulo' => getModuloById($request->modulo),
                    'semana' => $semana,
                    'variedad' => $variedad,
                    'ciclo' => Ciclo::find($request->modelo),
                ]);
            }
        }
    }

    public function store_proyeccion(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'id_modulo' => 'required',
            'id_variedad' => 'required',
            'id_semana' => 'required',
            'tipo' => 'required',
            'curva' => 'required',
            'semana_poda_siembra' => 'required',
            'plantas_iniciales' => 'required',
            'desecho' => 'required',
            'tallos_planta' => 'required',
            'tallos_ramo' => 'required',
        ], [
            'id_modulo.required' => 'El modulo es obligatorio',
            'tipo.required' => 'El tipo es obligatorio',
            'desecho.required' => 'El desecho es obligatorio',
            'plantas_iniciales.required' => 'Las plantas iniciales son obligatorias',
            'tallos_planta.required' => 'Los tallos por planta son obligatorios',
            'tallos_ramo.required' => 'Los tallos por ramo son obligatorios',
            'semana_poda_siembra.required' => 'Las semanas son obligatorias',
            'curva.required' => 'La curva es obligatoria',
            'id_variedad.required' => 'La variedad es obligatoria',
            'id_semana.required' => 'La semana es obligatoria',
        ]);
        if (!$valida->fails()) {
            $model = new ProyeccionModulo();
            $model->id_modulo = $request->id_modulo;
            $model->id_variedad = $request->id_variedad;
            $model->id_semana = $request->id_semana;
            $model->fecha_inicio = Semana::find($request->id_semana)->fecha_inicial;
            $model->tipo = $request->tipo;
            $model->curva = $request->curva;
            $model->semana_poda_siembra = $request->semana_poda_siembra;
            $model->plantas_iniciales = $request->plantas_iniciales;
            $model->desecho = $request->desecho;
            $model->tallos_planta = $request->tallos_planta;
            $model->tallos_ramo = $request->tallos_ramo;
            $model->poda_siembra = 0;

            if ($request->tipo == 'P') {
                $last_ciclo = Ciclo::All()
                    ->where('estado', 1)
                    ->where('id_variedad', $request->id_variedad)
                    ->where('id_modulo', $request->id_modulo)->last();
                if ($last_ciclo != '')
                    $model->poda_siembra = $last_ciclo->poda_siembra == 'S' ? 0 : intval(getModuloById($request->id_modulo)->getPodaSiembraByCiclo($last_ciclo->id_ciclo) + 1);
            }

            if ($model->save()) {
                $model = ProyeccionModulo::All()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado una nueva proyección satisfactoriamente</p>'
                    . '</div>';
                bitacora('proyeccion_modulo', $model->id_proyeccion_modulo, 'I', 'Inserción satisfactoria de una nueva proyección');

                /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA ====================== */
                $semana = Semana::find($request->id_semana);
                $semana_fin = DB::table('semana')
                    ->select(DB::raw('max(codigo) as max'))
                    ->where('estado', '=', 1)
                    ->where('id_variedad', '=', $request->id_variedad)
                    ->get()[0]->max;

                Artisan::call('proyeccion:update_semanal', [
                    'semana_desde' => $semana->codigo,
                    'semana_hasta' => $semana_fin,
                    'variedad' => $request->id_variedad,
                    'modulo' => $request->id_modulo,
                    'restriccion' => 1,
                ]);

            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                    . '</div>';
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function update_proyeccion(Request $request)
    {
        //dd($request->all());
        $valida = Validator::make($request->all(), [
            'id_proyeccion_modulo' => 'required',
            'semana' => 'required',
            'tipo' => 'required',
            'curva' => 'required',
            'semana_poda_siembra' => 'required',
            'plantas_iniciales' => 'required',
            'desecho' => 'required',
            'tallos_planta' => 'required',
            'tallos_ramo' => 'required',
        ], [
            'id_proyeccion_modulo.required' => 'La proyección es obligatoria',
            'tipo.required' => 'El tipo es obligatorio',
            'desecho.required' => 'El desecho es obligatorio',
            'plantas_iniciales.required' => 'Las plantas iniciales son obligatorias',
            'tallos_planta.required' => 'Los tallos por planta son obligatorios',
            'tallos_ramo.required' => 'Los tallos por ramo son obligatorios',
            'semana_poda_siembra.required' => 'Las semanas son obligatorias',
            'curva.required' => 'La curva es obligatoria',
            'semana.required' => 'La semana es obligatoria',
        ]);
        if (!$valida->fails()) {
            $model = ProyeccionModulo::find($request->id_proyeccion_modulo);
            $semana_ini_proy = Semana::All()->where('estado', 1)->where('id_variedad', $model->id_variedad)
                ->where('codigo', $request->semana)->first();
            if ($semana_ini_proy != '') {
                /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA ====================== */
                if ($model->id_semana != $semana_ini_proy->id_semana || $model->tipo != $request->tipo || $model->curva != $request->curva || 1 ||
                    $model->semana_poda_siembra != $request->semana_poda_siembra || $model->plantas_iniciales != $request->plantas_iniciales ||
                    $model->desecho != $request->desecho || $model->tallos_planta != $request->tallos_planta || $model->tallos_ramo != $request->tallos_ramo) { // hubo algun cambio

                    $semana_ini = min($model->semana->codigo, $semana_ini_proy->codigo);

                    $next_proy = ProyeccionModuloSemana::where('estado', 1)
                        ->where('tabla', 'P')
                        ->where('tipo', 'Y')
                        ->where('semana', '>', $semana_ini)
                        ->where('id_modulo', $model->id_modulo)
                        ->where('id_variedad', $model->id_variedad)
                        ->where('modelo', '!=', $model->id_proyeccion_modulo)
                        ->orderBy('semana')
                        ->get()->take(1);
                    $next_proy = count($next_proy) > 0 ? $next_proy[0] : '';

                    $proyecciones = ProyeccionModuloSemana::where('estado', 1)
                        ->where('semana', '>=', $semana_ini)
                        ->where('id_modulo', $model->id_modulo)
                        ->where('id_variedad', $model->id_variedad)
                        ->orderBy('semana')
                        ->get();

                    $cant_semanas_new = $request->semana_poda_siembra + count(explode('-', $request->curva));   // cantidad de semanas que durará la proy new

                    $last_semana = '';
                    $pos_cosecha = 0;
                    $pos_proy = 0;
                    $pos_proy_new = '';
                    $last_semana_new = '';
                    foreach ($proyecciones as $proy) {
                        if ($proy->tabla != 'C') {   // validar que el rango de semanas consultadas estan fuera de los ciclos reales
                            if ($proy->semana < $semana_ini_proy->codigo) { // se movio para adelante la proy, y se trata de una semana anterior
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

                            } else if ($pos_proy + 1 <= $cant_semanas_new - 1) {   // // dentro de las semanas de la proy
                                $proy->tabla = 'P';
                                $proy->modelo = $model->id_proyeccion_modulo;

                                $proy->plantas_iniciales = $request->plantas_iniciales;
                                $proy->tallos_planta = $request->tallos_planta;
                                $proy->tallos_ramo = $request->tallos_planta;
                                $proy->curva = $request->curva;
                                $proy->poda_siembra = 0;
                                $proy->semana_poda_siembra = $request->semana_poda_siembra;
                                $proy->desecho = $request->desecho;
                                $proy->area = $model->modulo->area;
                                $proy->tipo = 'I';
                                $proy->info = ($pos_proy + 1) . 'º';
                                $proy->proyectados = 0;

                                if ($pos_proy + 1 == 1) {   // primera semana de proyeccion
                                    $proy->tipo = 'Y';
                                    $proy->info = $request->tipo;
                                }
                                if ($pos_proy + 1 >= $request->semana_poda_siembra) {  // semana de cosecha **
                                    $proy->tipo = 'T';
                                    $total = $request->plantas_iniciales * $request->tallos_planta;
                                    $total = $total * ((100 - $request->desecho) / 100);
                                    $proy->proyectados = round($total * (explode('-', $request->curva)[$pos_cosecha] / 100), 2);
                                    $pos_cosecha++;
                                }
                                $pos_proy++;
                            } else if ($next_proy != '') {    // semanas despues de la proyeccion, pero en caso de que exista una siguiente proy
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
                                        $proy->semana_poda_siembra = null;
                                        $proy->tallos_planta = null;
                                        $proy->poda_siembra = null;
                                        $proy->tabla = null;
                                        $proy->modelo = null;
                                    }
                                    $pos_proy_new++;
                                }/* else {    // es una semana que queda vacia antes de la siguiente proy
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
                                }*/
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
                        } else {
                            break;
                        }
                        $proy->save();
                    }

                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado la proyección satisfactoriamente</p>'
                        . '</div>';

                    /* ======================== ACTUALIZAR LAS TABLAS CICLO y PROYECCION_MODULO ====================== */
                    ProyeccionUpdateProy::dispatch($request->id_proyeccion_modulo, $request->semana, $request->tipo, $request->curva, $request->semana_poda_siembra, $request->plantas_iniciales, $request->desecho, $request->tallos_planta, $request->tallos_ramo)
                        ->onQueue('proy_cosecha/update_proyeccion');

                    /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA FINAL ====================== */
                    $semana_desde = $last_semana_new;
                    $semana_fin = getLastSemanaByVariedad($model->id_variedad);

                    if ($semana_desde != '')
                        ProyeccionUpdateSemanal::dispatch($semana_desde, $semana_fin->codigo, $model->id_variedad, $model->id_modulo, 0)
                            ->onQueue('proy_cosecha/update_proyeccion');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-info text-center">' .
                        '<p>No se han encontrado cambios</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> La semana de inicio no se encuentra en el sistema</p>'
                    . '</div>';
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function update_ciclo(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'id_ciclo' => 'required',
            'poda_siembra' => 'required',
            'curva' => 'required',
            'semana_poda_siembra' => 'required',
            'plantas_iniciales' => 'required',
            'desecho' => 'required',
            'conteo' => 'required',
        ], [
            'id_ciclo.required' => 'El ciclo es obligatorio',
            'poda_siembra.required' => 'La poda siembra es obligatoria',
            'desecho.required' => 'El desecho es obligatorio',
            'plantas_iniciales.required' => 'Las plantas iniciales son obligatorias',
            'conteo.required' => 'Los tallos por planta son obligatorios',
            'semana_poda_siembra.required' => 'Las semanas son obligatorias',
            'curva.required' => 'La curva es obligatoria',
        ]);
        $success = false;
        if (!$valida->fails()) {
            $model = Ciclo::find($request->id_ciclo);
            $semana_fin = getLastSemanaByVariedad($model->id_variedad);
            $last_semana_new = '';
            /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA ====================== */
            if ($model->semana_poda_siembra != $request->semana_poda_siembra ||
                $model->curva != $request->curva || $model->poda_siembra != $request->poda_siembra ||
                $model->desecho != $request->desecho || $model->conteo != $request->conteo ||
                $model->plantas_iniciales != $request->plantas_iniciales) { // hubo algun cambio

                $cant_semanas_old = $model->semana_poda_siembra + count(explode('-', $model->curva));   // cantidad de semanas que durará el ciclo old
                $cant_semanas_new = $request->semana_poda_siembra + count(explode('-', $request->curva));   // cantidad de semanas que durará el ciclo new
                $cant_curva_old = count(explode('-', $model->curva));   // cantidad de semanas que durará la cosecha old
                $cant_curva_new = count(explode('-', $request->curva));   // cantidad de semanas que durará la cosecha new
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

                                $proy->plantas_iniciales = $request->plantas_iniciales;
                                $proy->tallos_planta = $request->conteo;
                                $proy->tallos_ramo = 0;
                                $proy->curva = $request->curva;
                                $proy->poda_siembra = $request->poda_siembra;
                                $proy->semana_poda_siembra = $request->semana_poda_siembra;
                                $proy->desecho = $request->desecho;
                                $proy->area = $request->area;
                                $proy->tipo = 'I';
                                $proy->info = ($pos_proy + 1) . 'º';
                                $proy->proyectados = 0;

                                if ($pos_proy + 1 == 1) {   // primera semana de proyeccion
                                    $proy->tipo = $request->poda_siembra;
                                    $proy->info = $request->poda_siembra . '-' . $model->modulo->getPodaSiembraByCiclo($model->id_ciclo);
                                }
                                if ($pos_proy + 1 >= $request->semana_poda_siembra) {  // semana de cosecha **
                                    $proy->tipo = 'T';
                                    $total = $request->plantas_iniciales * $request->conteo;
                                    $total = $total * ((100 - $request->desecho) / 100);
                                    $proy->proyectados = round($total * (explode('-', $request->curva)[$pos_cosecha] / 100), 2);
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
                    } else {    // hay que mover para atras
                        $proyecciones = ProyeccionModuloSemana::where('tabla', 'C')
                            ->where('modelo', $request->id_ciclo)
                            ->orderBy('semana')
                            ->get();
                        $pos_cosecha = 0;
                        $last_semana = '';
                        foreach ($proyecciones as $pos_proy => $proy) {
                            if ($pos_proy + 1 <= $cant_semanas_new - 1) {
                                $proy->plantas_iniciales = $request->plantas_iniciales;
                                $proy->tallos_planta = $request->conteo;
                                $proy->curva = $request->curva;
                                $proy->poda_siembra = $request->poda_siembra;
                                $proy->semana_poda_siembra = $request->semana_poda_siembra;
                                $proy->desecho = $request->desecho;

                                if ($pos_proy + 1 >= $request->semana_poda_siembra) {   // es una semana a partir de la programacion de cosecha
                                    if ($pos_proy + 1 == $request->semana_poda_siembra) {   // nueva primera semana de cosecha
                                        $proy->tipo = 'T';
                                    }
                                    $total = $request->plantas_iniciales * $request->conteo;
                                    $total = $total * ((100 - $request->desecho) / 100);
                                    $proy->proyectados = round($total * (explode('-', $request->curva)[$pos_cosecha] / 100), 2);
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
                    }
                } else
                    if ($cant_curva_old != $cant_curva_new) {   // no hay que mover, pero hay que recalcular la curva
                        $proyecciones = ProyeccionModuloSemana::whereIn('tipo', ['T'])
                            ->where('tabla', 'C')
                            ->where('modelo', $model->id_ciclo)
                            ->orderBy('semana')
                            ->get();
                        if ($cant_curva_new < $cant_curva_old) {    // quitar semanas de cosecha
                            $cant_quitar = $cant_curva_old - $cant_curva_new;
                            $pos_cosecha = 0;
                            foreach ($proyecciones as $pos_proy => $proy) {
                                $proy->plantas_iniciales = $request->plantas_iniciales;
                                $proy->tallos_planta = $request->conteo;
                                $proy->curva = $request->curva;
                                $proy->poda_siembra = $request->poda_siembra;
                                $proy->semana_poda_siembra = $request->semana_poda_siembra;
                                $proy->desecho = $request->desecho;

                                if (($pos_proy + 1) <= $cant_quitar) {    // convertir a tipo I
                                    $proy->tipo = 'I';
                                    $proy->proyectados = 0;
                                } else {    // recalcular % de curva
                                    $total = $request->plantas_iniciales * $request->conteo;
                                    $total = $total * ((100 - $request->desecho) / 100);
                                    $proy->proyectados = round($total * (explode('-', $request->curva)[$pos_cosecha] / 100), 2);
                                    $pos_cosecha++;
                                }
                                //dd($pos_proy, 'ok');
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
                                $proy->plantas_iniciales = $request->plantas_iniciales;
                                $proy->tallos_planta = $request->conteo;
                                $proy->curva = $request->curva;
                                $proy->poda_siembra = $request->poda_siembra;
                                $proy->semana_poda_siembra = $request->semana_poda_siembra;
                                $proy->desecho = $request->desecho;
                                $proy->tipo = 'T';

                                $total = $request->plantas_iniciales * $request->conteo;
                                $total = $total * ((100 - $request->desecho) / 100);
                                $proy->proyectados = round($total * (explode('-', $request->curva)[$pos_cosecha] / 100), 2);
                                $pos_cosecha++;

                                $proy->save();
                            }
                        }
                    } else {    // no hay que mover, solo actualizar datos
                        $proyecciones = ProyeccionModuloSemana::whereIn('tipo', ['S', 'P', 'T'])
                            ->where('tabla', 'C')
                            ->where('modelo', $model->id_ciclo)
                            ->orderBy('semana')
                            ->get();

                        $pos_cosecha = 0;
                        foreach ($proyecciones as $pos_proy => $proy) {
                            $proy->plantas_iniciales = $request->plantas_iniciales;
                            $proy->tallos_planta = $request->conteo;
                            $proy->curva = $request->curva;
                            $proy->poda_siembra = $request->poda_siembra;
                            $proy->semana_poda_siembra = $request->semana_poda_siembra;
                            $proy->desecho = $request->desecho;

                            if ($proy->tipo == 'T') {
                                $total = $request->plantas_iniciales * $request->conteo;
                                $total = $total * ((100 - $request->desecho) / 100);
                                $proy->proyectados = round($total * (explode('-', $request->curva)[$pos_cosecha] / 100), 2);
                                $pos_cosecha++;
                            }

                            $proy->save();
                        }
                    }
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p>Se ha guardado la información satisfactoriamente</p>'
                    . '</div>';
            } else {
                $success = false;
                $msg = '<div class="alert alert-info text-center">' .
                    '<p>No se han encontrado cambios</p>'
                    . '</div>';
            }

            /* ======================== ACTUALIZAR LAS TABLAS CICLO y PROYECCION_MODULO ====================== */
            ProyeccionUpdateCiclo::dispatch($request->id_ciclo, $request->semana_poda_siembra, $request->curva, $request->poda_siembra, $request->plantas_iniciales, $request->desecho, $request->conteo)
                ->onQueue('update_ciclo');

            /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA FINAL ====================== */
            $semana_desde = $last_semana_new;

            if ($semana_desde != '')
                ProyeccionUpdateSemanal::dispatch($semana_desde, $semana_fin->codigo, $model->id_variedad, $model->id_modulo, 0)
                    ->onQueue('update_ciclo');
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function restaurar_proyeccion(Request $request)
    {
        Log::info('INICIO DE RESTAURACION de PROYECCION, MODULO: ' . $request->modulo);
        Artisan::call('proyeccion:auto_create', [
            'modulo' => $request->modulo
        ]);
        return [
            'success' => true,
            'modulo' => $request->modulo
        ];
    }

    public function actualizar_proyecciones(Request $request)
    {
        Artisan::call('proyeccion:update_semanal', [
            'semana_desde' => $request->desde,
            'semana_hasta' => $request->hasta,
            'variedad' => $request->variedad,
            'modulo' => $request->modulo,
            'restriccion' => 0,
        ]);
        if (!$request->get_obj)
            return [
                'success' => true,
                'modulo' => $request->modulo
            ];
        else {
            $semana = Semana::All()
                ->where('estado', 1)
                ->where('codigo', $request->desde)
                ->where('id_variedad', $request->variedad)
                ->first();
            return [
                'success' => true,
                'modulo' => $request->modulo,
                'model' => ProyeccionModuloSemana::All()
                    ->where('estado', 1)
                    ->where('id_modulo', $request->modulo)
                    ->where('id_variedad', $request->variedad)
                    ->where('semana', $semana->codigo)
                    ->first(),
                'id_html' => $request->id_html,
            ];
        }
    }

    public function actualizar_semana(Request $request)
    {
        $semana = Semana::find($request->semana);
        foreach ($request->modulos as $mod)
            ProyeccionUpdateSemanal::dispatch($semana->codigo, $semana->codigo, $request->variedad, $mod, 0);
        return [
            'success' => true,
            'semana' => $request->semana
        ];
    }

    public function actualizar_datos(Request $request)
    {
        $modulos = [];
        foreach ($request->modulos as $mod)
            array_push($modulos, getModuloById($mod));

        $semanas = [];
        foreach ($request->semanas as $sem)
            array_push($semanas, Semana::find($sem));

        return view('adminlte.gestion.proyecciones.cosecha.forms.actualizar_datos', [
            'semanas' => $semanas,
            'modulos' => $modulos,
        ]);
    }

    /* ------------------------------------------------------------------- */
    public function actualizar_tipo(Request $request)
    {
        foreach ($request->semanas as $sem) {
            $sem = Semana::find($sem);
            foreach ($request->modulos as $mod) {
                $change = false;
                /* ================ CICLOS ===================== */
                $ciclo = Ciclo::All()
                    ->where('id_variedad', $request->variedad)
                    ->where('id_modulo', $mod)
                    ->where('fecha_inicio', '>=', $sem->fecha_inicial)
                    ->where('fecha_inicio', '<=', $sem->fecha_final)
                    ->where('estado', 1)
                    ->first();
                if ($ciclo != '') {
                    $ciclo->poda_siembra = $request->tipo;
                    $ciclo->save();

                    $change = true;
                }

                /* ================ CICLOS ===================== */
                $proy = ProyeccionModulo::All()
                    ->where('id_variedad', $request->variedad)
                    ->where('id_modulo', $mod)
                    ->where('id_semana', $sem->id_semana)
                    ->where('estado', 1)
                    ->first();
                if ($proy != '') {
                    $proy->tipo = $request->tipo;
                    $proy->save();

                    $change = true;
                }

                /* ================ ACTUALIZAR PROYECCIONES ===================== */
                if ($change)
                    ProyeccionUpdateSemanal::dispatch($sem->codigo, $sem->codigo, $request->variedad, $mod, 0)->onQueue('proy_cosecha/actualizar_tipo');
            }
        }
        return [
            'success' => true,
            'mensaje' => '<div class="alert alert-success text-center">Se ha guardado la información satisfactoriamente</div>',
        ];
    }

    public function actualizar_curva(Request $request)
    {
        foreach ($request->semanas as $sem) {
            $sem = Semana::find($sem);
            foreach ($request->modulos as $mod) {
                $change = false;
                /* ================ CICLOS ===================== */
                $ciclo = Ciclo::All()
                    ->where('id_variedad', $request->variedad)
                    ->where('id_modulo', $mod)
                    ->where('fecha_inicio', '>=', $sem->fecha_inicial)
                    ->where('fecha_inicio', '<=', $sem->fecha_final)
                    ->where('estado', 1)
                    ->first();
                if ($ciclo != '') {
                    $ciclo->curva = $request->curva;
                    $ciclo->save();
                    $change = true;
                }

                /* ================ PROYECCIONES ===================== */
                $proy = ProyeccionModulo::All()
                    ->where('id_variedad', $request->variedad)
                    ->where('id_modulo', $mod)
                    ->where('id_semana', $sem->id_semana)
                    ->where('estado', 1)
                    ->first();
                if ($proy != '') {
                    $proy->curva = $request->curva;
                    $proy->save();
                    $change = true;
                }

                /* ================ SEMANAS ===================== */
                if ($request->check_save_semana == 'true') {
                    $sem->curva = $request->curva;
                    $sem->save();
                }

                /* =================== ACTUALIZAR PROYECCIONES ===================== */
                if ($change) {
                    ProyeccionUpdateSemanal::dispatch($sem->codigo, $request->semana_hasta, $request->variedad, $mod, 0)->onQueue('proy_cosecha/actualizar_curva');
                    $semana_hasta = getLastSemanaByVariedad($request->variedad)->codigo;
                    ProyeccionUpdateSemanal::dispatch($request->semana_hasta, $semana_hasta, $request->variedad, $mod, 0)->onQueue('proy_cosecha');
                }
            }
        }
        return [
            'success' => true,
            'mensaje' => '<div class="alert alert-success text-center">Se ha guardado la información satisfactoriamente</div>',
        ];
    }

    public function actualizar_semana_cosecha(Request $request)
    {
        foreach ($request->semanas as $sem) {
            $sem = Semana::find($sem);
            foreach ($request->modulos as $mod) {
                $change = false;
                /* ================ CICLOS ===================== */
                $ciclo = Ciclo::All()
                    ->where('id_variedad', $request->variedad)
                    ->where('id_modulo', $mod)
                    ->where('fecha_inicio', '>=', $sem->fecha_inicial)
                    ->where('fecha_inicio', '<=', $sem->fecha_final)
                    ->where('estado', 1)
                    ->first();
                if ($ciclo != '') {
                    if ($ciclo->activo == 1) {
                        /* =========================== MOVER PROYECCIONES ========================= */
                        $semana_fin = getLastSemanaByVariedad($ciclo->id_variedad);

                        $sum_semana_new = $request->semana_cosecha + count(explode('-', $ciclo->curva));
                        $sum_semana_old = $ciclo->semana_poda_siembra + count(explode('-', $ciclo->curva));
                        if ($sum_semana_new != $sum_semana_old) {   // hay que mover las proyecciones
                            $semana = Semana::All()
                                ->where('estado', 1)
                                ->where('id_variedad', $ciclo->id_variedad)
                                ->where('fecha_inicial', '<=', $ciclo->fecha_inicio)
                                ->where('fecha_final', '>=', $ciclo->fecha_inicio)
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
                                    ->where('id_variedad', '=', $ciclo->id_variedad)
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
                                        ->where('id_variedad', '=', $ciclo->id_variedad)
                                        ->first();

                                    if ($semana_old != '') {
                                        $i++;
                                    }
                                    $next++;
                                }

                                $proy = ProyeccionModulo::where('estado', 1)
                                    ->where('id_modulo', $ciclo->id_modulo)
                                    ->where('id_variedad', $ciclo->id_variedad)
                                    ->orderBy('fecha_inicio')
                                    ->get()->first();

                                if ($proy != '')
                                    if ($proy->id_semana == $semana_old->id_semana || $proy->semana->codigo < $semana_new->codigo) {    // hay que mover
                                        $proy->id_semana = $semana_new->id_semana;
                                        $proy->fecha_inicio = $semana_new->fecha_final;
                                        $proy->desecho = $semana_new->desecho > 0 ? $semana_new->desecho : 0;
                                        $proy->tallos_planta = $semana_new->tallos_planta_poda > 0 ? $semana_new->tallos_planta_poda : 0;
                                        $proy->tallos_ramo = $semana_new->tallos_ramo_poda > 0 ? $semana_new->tallos_ramo_poda : 0;

                                        $proy->save();
                                        $proy->restaurar_proyecciones();
                                    }
                            } else {    // se pasa de la ultima semana programada
                                /* ======================== QUITAR PROYECCIONES ======================= */
                                $proys = ProyeccionModulo::where('estado', 1)
                                    ->where('id_modulo', $ciclo->id_modulo)
                                    ->where('id_variedad', $ciclo->id_variedad)
                                    ->orderBy('fecha_inicio')
                                    ->get();
                                foreach ($proys as $proy)
                                    $proy->delete();
                            }
                        }

                        $ciclo->semana_poda_siembra = $request->semana_cosecha;
                        $ciclo->save();
                        $change = true;
                    }
                }

                /* ================ PROYECCIONES ===================== */
                $proy = ProyeccionModulo::All()
                    ->where('id_variedad', $request->variedad)
                    ->where('id_modulo', $mod)
                    ->where('id_semana', $sem->id_semana)
                    ->where('estado', 1)
                    ->first();
                if ($proy != '') {
                    $proy->semana_poda_siembra = $request->semana_cosecha;
                    $proy->save();
                    $change = true;
                }

                /* ================ SEMANAS ===================== */
                if ($request->check_save_semana == 'true') {
                    $sem->semana_poda = $request->semana_cosecha;
                    $sem->save();
                }

                /* =================== ACTUALIZAR PROYECCIONES ===================== */
                if ($change) {
                    ProyeccionUpdateSemanal::dispatch($sem->codigo, $request->semana_hasta, $request->variedad, $mod, 0)->onQueue('proy_cosecha/actualizar_semana_cosecha');
                    $semana_hasta = getLastSemanaByVariedad($request->variedad)->codigo;
                    ProyeccionUpdateSemanal::dispatch($request->semana_hasta, $semana_hasta, $request->variedad, $mod, 0)->onQueue('proy_cosecha');
                }
            }
        }
        return [
            'success' => true,
            'mensaje' => '<div class="alert alert-success text-center">Se ha guardado la información satisfactoriamente</div>',
        ];
    }

    /* ------------------------------------------------------------------- */
    public function mover_fechas(Request $request)
    {
        return view('adminlte.gestion.proyecciones.cosecha.forms.mover_fechas', []);
    }
}