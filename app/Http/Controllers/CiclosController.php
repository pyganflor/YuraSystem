<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use yura\Jobs\ProyeccionUpdateSemanal;
use yura\Modelos\Ciclo;
use yura\Modelos\Cosecha;
use yura\Modelos\Modulo;
use yura\Modelos\ProyeccionModulo;
use yura\Modelos\Sector;
use Validator;
use yura\Modelos\Semana;

class CiclosController extends Controller
{
    public function listar_ciclos(Request $request)
    {
        $r = [];

        if ($request->tipo == 0) {  // inactivos
            foreach (Modulo::All()->where('estado', 1)->sortBy('nombre') as $m) {
                if ($m->cicloActual() == '') {
                    array_push($r, $m);
                }
            }
        } else {
            foreach (Modulo::All()->where('estado', 1)->sortBy('nombre') as $m) {
                if ($m->cicloActual() != '' && $m->cicloActual()->id_variedad == $request->variedad)
                    array_push($r, $m);
            }
        }

        return view('adminlte.gestion.sectores_modulos.partials.listar_ciclos', [
            'modulos' => $r,
            'tipo' => $request->tipo,
            'variedad' => getVariedad($request->variedad),
        ]);
    }

    public function ver_ciclos(Request $request)
    {
        return view('adminlte.gestion.sectores_modulos.partials.ver_ciclos', [
            'modulo' => getModuloById($request->modulo),
        ]);
    }

    public function ver_cosechas(Request $request)
    {
        $ciclo = Ciclo::find($request->ciclo);
        $cosechas = DB::table('desglose_recepcion as dr')
            ->join('recepcion as r', 'r.id_recepcion', '=', 'dr.id_recepcion')
            ->select('r.id_cosecha as id')->distinct()
            ->where('dr.estado', '=', 1)
            ->where('r.estado', '=', 1)
            ->where('dr.id_modulo', '=', $ciclo->id_modulo)
            ->where('r.fecha_ingreso', '>', opDiasFecha('+', 1, $ciclo->fecha_inicio))
            ->orderBy('r.fecha_ingreso')
            ->get();

        $r = [];
        foreach ($cosechas as $c)
            array_push($r, Cosecha::find($c->id));

        return view('adminlte.gestion.sectores_modulos.partials.ver_cosechas', [
            'modulo' => getModuloById($ciclo->id_modulo),
            'cosechas' => $r,
            'ciclo' => $ciclo,
        ]);
    }

    public function store_ciclo(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'modulo' => 'required',
            'variedad' => 'required',
            'area' => 'required',
            'fecha_inicio' => 'required',
            'poda_siembra' => 'required',
        ], [
            'modulo.required' => 'El módulo es obligatorio',
            'area.required' => 'El área es obligatorio',
            'variedad.required' => 'La variedad es obligatoria',
            'fecha_inicio.required' => 'La fecha de inicio de cilo es obligatoria',
            'poda_siembra.required' => 'El campo poda/siembra es obligatorio',
        ]);
        if (!$valida->fails()) {
            if ($request->fecha_fin != '' && $request->fecha_inicio > $request->fecha_fin) {
                return [
                    'success' => false,
                    'mensaje' => '<div class="alert alert-warning text-center">' .
                        '<p>La fecha de inicio debe ser menor que la fecha fin</p>'
                        . '</div>',
                ];
            }

            $ciclo = new Ciclo();
            $ciclo->id_modulo = $request->modulo;
            $ciclo->id_variedad = $request->variedad;
            $ciclo->area = $request->area;
            $ciclo->fecha_inicio = $request->fecha_inicio;
            $ciclo->poda_siembra = $request->poda_siembra;
            if ($request->fecha_cosecha != '')
                $ciclo->fecha_cosecha = opDiasFecha('+', $request->fecha_cosecha, $request->fecha_inicio);
            $ciclo->fecha_fin = $request->fecha_fin;

            $semana = Semana::All()
                ->where('estado', 1)
                ->where('id_variedad', $ciclo->id_variedad)
                ->where('fecha_inicial', '<=', $ciclo->fecha_inicio)
                ->where('fecha_final', '>=', $ciclo->fecha_inicio)
                ->first();
            $ciclo->desecho = $semana->desecho != '' ? $semana->desecho : 0;
            $ciclo->curva = $semana->curva;
            if ($ciclo->poda_siembra == 'P') {
                $ciclo->semana_poda_siembra = $semana->semana_poda;
                $ciclo->conteo = $semana->tallos_planta_poda;
            } else {
                $ciclo->semana_poda_siembra = $semana->semana_siembra;
                $ciclo->conteo = $semana->tallos_planta_siembra;
            }

            $last_siembra = Ciclo::All()->where('estado', 1)->where('id_modulo', $request->modulo)
                ->where('poda_siembra', 'S')->sortBy('fecha_inicio')->last();

            if ($last_siembra != '')
                $ciclo->plantas_iniciales = $last_siembra->plantas_iniciales;

            if ($ciclo->save()) {
                $ciclo = Ciclo::All()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado un nuevo ciclo satisfactoriamente</p>'
                    . '</div>';
                bitacora('ciclo', $ciclo->id_ciclo, 'I', 'Inserción satisfactoria de un nuevo ciclo');

                /* ===================== QUITAR PROYECCIONES =================== */
                $proyecciones = ProyeccionModulo::All()->where('estado', 1)
                    ->where('id_variedad', $request->variedad)
                    ->where('id_modulo', $request->modulo)
                    ->where('id_semana', $semana->id_semana);
                foreach ($proyecciones as $proy) {
                    $proy->estado = 0;

                    $proy->save();
                    bitacora('proyeccion_modulo', $proy->id_proyeccion_modulo, 'U', 'Actualizacion satisfactoria del estado');
                }

                /* ===================== CREAR SIGUIENTE PROYECCION ==================== */
                $sum_semana = intval($ciclo->semana_poda_siembra) + intval(count(explode('-', $ciclo->curva)));
                $codigo = $semana->codigo;
                $i = 1;
                $next = 1;
                while ($i < $sum_semana) {
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

                /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA ====================== */
                $semana_fin = DB::table('semana')
                    ->select(DB::raw('max(codigo) as max'))
                    ->where('estado', '=', 1)
                    ->where('id_variedad', '=', $request->variedad)
                    ->get()[0]->max;

                ProyeccionUpdateSemanal::dispatch($semana->codigo, $semana_fin, $request->variedad, $request->modulo, 0)
                    ->onQueue('proy_cosecha');
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

    public function update_ciclo(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'ciclo' => 'required',
            'variedad' => 'required',
            'area' => 'required',
            'fecha_inicio' => 'required',
            'poda_siembra' => 'required',
        ], [
            'ciclo.required' => 'El ciclo es obligatorio',
            'area.required' => 'El área es obligatorio',
            'variedad.required' => 'La variedad es obligatoria',
            'fecha_inicio.required' => 'La fecha de inicio de cilo es obligatoria',
            'poda_siembra.required' => 'El campo poda/siembra es obligatorio',
        ]);
        if (!$valida->fails()) {
            $ciclo = Ciclo::find($request->ciclo);

            /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA ====================== */
            $semana_ini = min(getSemanaByDate($ciclo->fecha_inicio)->codigo, getSemanaByDate($request->fecha_inicio)->codigo);
            /* ------------------------ ******************************************* ---------------------- */

            $semana_fin = getLastSemanaByVariedad($request->variedad);

            /* ======================== MOVER PROYECCION_MODULO ====================== */
            $model = $ciclo;
            if (getSemanaByDate($model->fecha_inicio)->codigo != getSemanaByDate($request->fecha_inicio)->codigo) {   // hay que mover las proyecciones
                $sum_semana = $model->semana_poda_siembra + count(explode('-', $model->curva));
                $semana = Semana::All()
                    ->where('estado', 1)
                    ->where('id_variedad', $request->variedad)
                    ->where('fecha_inicial', '<=', $model->fecha_inicio)
                    ->where('fecha_final', '>=', $model->fecha_inicio)
                    ->first();
                $semana_req = Semana::All()
                    ->where('estado', 1)
                    ->where('id_variedad', $request->variedad)
                    ->where('fecha_inicial', '<=', $request->fecha_inicio)
                    ->where('fecha_final', '>=', $request->fecha_inicio)
                    ->first();

                /* ------------------------ OBTENER LAS SEMANAS NEW/OLD ---------------------- */
                $codigo = $semana->codigo;
                $new_codigo = $semana->codigo;
                $i = 1;
                $next = 1;
                while ($i < $sum_semana && $new_codigo <= $semana_fin->codigo) {
                    $new_codigo = $codigo + $next;
                    $semana_new = Semana::All()
                        ->where('estado', '=', 1)
                        ->where('codigo', '=', $new_codigo)
                        ->where('id_variedad', '=', $request->variedad)
                        ->first();

                    if ($semana_new != '') {
                        $i++;
                    }
                    $next++;
                }

                $codigo = $semana_req->codigo;
                $new_codigo = $semana_req->codigo;
                $i = 1;
                $next = 1;
                while ($i < $sum_semana && $new_codigo <= $semana_fin->codigo) {
                    $new_codigo = $codigo + $next;
                    $semana_new_req = Semana::All()
                        ->where('estado', '=', 1)
                        ->where('codigo', '=', $new_codigo)
                        ->where('id_variedad', '=', $request->variedad)
                        ->first();

                    if ($semana_new_req != '') {
                        $i++;
                    }
                    $next++;
                }

                $proy = ProyeccionModulo::where('estado', 1)
                    ->where('id_modulo', $model->id_modulo)
                    ->where('id_variedad', $request->variedad)
                    ->orderBy('fecha_inicio')
                    ->get()->first();

                if ($proy->id_semana == $semana_new->id_semana || $proy->semana->codigo <= $semana_new->codigo || $proy->semana->codigo < $semana_new_req->codigo) {    // hay que mover
                    $proy->id_semana = $semana_new_req->id_semana;
                    $proy->fecha_inicio = $semana_new_req->fecha_final;
                    $proy->desecho = $semana_new_req->desecho > 0 ? $semana_new_req->desecho : 0;
                    $proy->tallos_planta = $semana_new_req->tallos_planta_poda > 0 ? $semana_new_req->tallos_planta_poda : 0;
                    $proy->tallos_ramo = $semana_new_req->tallos_ramo_poda > 0 ? $semana_new_req->tallos_ramo_poda : 0;

                    $proy->save();
                    $proy->restaurar_proyecciones();
                }

            }
            /* ------------------------ ******************************************* ---------------------- */

            foreach ($ciclo->modulo->ciclos->where('estado', 1) as $c) {
                if ($c->id_ciclo != $ciclo->id_ciclo) {
                    if ($request->fecha_inicio >= $c->fecha_inicio && $request->fecha_inicio < $c->fecha_fin)
                        return [
                            'success' => false,
                            'mensaje' => '<div class="alert alert-warning text-center">' .
                                '<p>La fecha de inicio ya se encuentra incluida en un ciclo anterior</p>'
                                . '</div>',
                        ];
                    if ($request->fecha_fin > $c->fecha_inicio && $request->fecha_fin <= $c->fecha_fin)
                        return [
                            'success' => false,
                            'mensaje' => '<div class="alert alert-warning text-center">' .
                                '<p>La fecha fin ya se encuentra incluida en un ciclo anterior: ' . $c->fecha_inicio . ' / ' . $c->fecha_fin . '</p>'
                                . '</div>',
                        ];
                }
            }

            if ($request->fecha_fin != '' && $request->fecha_inicio > $request->fecha_fin) {
                return [
                    'success' => false,
                    'mensaje' => '<div class="alert alert-warning text-center">' .
                        '<p>La fecha de inicio debe ser menor que la fecha fin</p>'
                        . '</div>',
                ];
            }

            $ciclo->id_variedad = $request->variedad;
            $ciclo->area = $request->area;
            $ciclo->fecha_inicio = $request->fecha_inicio;
            $ciclo->poda_siembra = $request->poda_siembra;
            if ($request->fecha_cosecha != '')
                $ciclo->fecha_cosecha = opDiasFecha('+', $request->fecha_cosecha, $request->fecha_inicio);
            else
                $ciclo->fecha_cosecha = null;
            $ciclo->fecha_fin = $request->fecha_fin;
            $ciclo->plantas_iniciales = $request->plantas_iniciales;
            $ciclo->plantas_muertas = $request->plantas_muertas;

            $semana = Semana::All()
                ->where('estado', 1)
                ->where('id_variedad', $ciclo->id_variedad)
                ->where('fecha_inicial', '<=', $ciclo->fecha_inicio)
                ->where('fecha_final', '>=', $ciclo->fecha_inicio)
                ->first();
            $ciclo->desecho = $semana->desecho != '' ? $semana->desecho : 0;
            $ciclo->curva = $semana->curva;
            if ($ciclo->poda_siembra == 'P') {
                $ciclo->semana_poda_siembra = $semana->semana_poda;
                $ciclo->conteo = $request->conteo > 0 ? $request->conteo : $semana->tallos_planta_poda;
            } else {
                $ciclo->semana_poda_siembra = $semana->semana_siembra;
                $ciclo->conteo = $request->conteo > 0 ? $request->conteo : $semana->tallos_planta_siembra;
            }
            if ($ciclo->save()) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha actualizado el ciclo satisfactoriamente</p>'
                    . '</div>';
                bitacora('ciclo', $ciclo->id_ciclo, 'U', 'Actualziacion satisfactoria de un ciclo');

                /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA ====================== */
                ProyeccionUpdateSemanal::dispatch($semana_ini, $semana_fin->codigo, $request->variedad, $ciclo->id_modulo, 0)
                    ->onQueue('proy_cosecha');
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

    public function terminar_ciclo(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'modulo' => 'required',
            'fecha_fin' => 'required',
        ], [
            'modulo.required' => 'El módulo es obligatorio',
            'fecha_fin.required' => 'La fecha final es obligatoria',
        ]);
        if (!$valida->fails()) {
            $modulo = Modulo::find($request->modulo);
            $ciclo = $modulo->cicloActual();
            if ($ciclo->fecha_fin != '') {
                $ciclo->activo = 0;
                $ciclo->fecha_fin = $request->fecha_fin;

                if ($ciclo->save()) {
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha terminado el ciclo satisfactoriamente</p>'
                        . '</div>';
                    bitacora('ciclo', $ciclo->id_ciclo, 'U', 'Actualizacion satisfactoria de un ciclo (terminar ciclo)');

                    /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA ====================== */
                    $semana_ini = getSemanaByDate($ciclo->fecha_inicio)->codigo;
                    $semana_fin = DB::table('semana')
                        ->select(DB::raw('max(codigo) as max'))
                        ->where('estado', '=', 1)
                        ->where('id_variedad', '=', $ciclo->id_variedad)
                        ->get()[0]->max;

                    ProyeccionUpdateSemanal::dispatch($semana_ini, $semana_fin, $ciclo->id_variedad, $ciclo->id_modulo, 0)
                        ->onQueue('proy_cosecha');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p>Faltan las fechas necesarias para terminar el ciclo</p>'
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

    public function abrir_ciclo(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'ciclo' => 'required',
        ], [
            'ciclo.required' => 'El ciclo es obligatorio',
        ]);
        if (!$valida->fails()) {
            $ciclo = Ciclo::find($request->ciclo);
            if ($request->abrir == 'true')
                $ciclo->activo = 1;
            else
                $ciclo->activo = 0;

            if ($ciclo->save()) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha abierto el ciclo satisfactoriamente</p>'
                    . '</div>';
                bitacora('ciclo', $ciclo->id_ciclo, 'U', 'Actualizacion satisfactoria de un ciclo (abrir ciclo)');

                /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA ====================== */
                $semana_ini = getSemanaByDate($ciclo->fecha_inicio)->codigo;
                $semana_fin = DB::table('semana')
                    ->select(DB::raw('max(codigo) as max'))
                    ->where('estado', '=', 1)
                    ->where('id_variedad', '=', $ciclo->id_variedad)
                    ->get()[0]->max;

                ProyeccionUpdateSemanal::dispatch($semana_ini, $semana_fin, $ciclo->id_variedad, $ciclo->id_modulo, 0)
                    ->onQueue('proy_cosecha');
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

    public function eliminar_ciclo(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'ciclo' => 'required',
        ], [
            'ciclo.required' => 'El ciclo es obligatorio',
        ]);
        if (!$valida->fails()) {
            $ciclo = Ciclo::find($request->ciclo);
            $ciclo->activo = 0;
            $ciclo->estado = 0;

            if ($ciclo->save()) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha eliminado el ciclo satisfactoriamente</p>'
                    . '</div>';
                bitacora('ciclo', $ciclo->id_ciclo, 'U', 'Actualizacion satisfactoria de un ciclo');

                /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA ====================== */
                $semana_ini = getSemanaByDate($ciclo->fecha_inicio)->codigo;
                $semana_fin = DB::table('semana')
                    ->select(DB::raw('max(codigo) as max'))
                    ->where('estado', '=', 1)
                    ->where('id_variedad', '=', $ciclo->id_variedad)
                    ->get()[0]->max;

                ProyeccionUpdateSemanal::dispatch($semana_ini, $semana_fin, $ciclo->id_variedad, $ciclo->id_modulo, 0)
                    ->onQueue('proy_cosecha');
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

    public function nuevos_ciclos(Request $request)
    {
        $sem_actual = getSemanaByDate(date('Y-m-d'));
        $query = ProyeccionModulo::where('estado', 1)
            ->where('fecha_inicio', '>=', $sem_actual->fecha_inicial)
            ->where('fecha_inicio', '<=', $sem_actual->fecha_final)
            ->get();

        return view('adminlte.gestion.sectores_modulos.partials.nuevos_ciclos', [
            'nuevos_ciclos' => $query,
            'sem_actual' => $sem_actual,
        ]);
    }

    public function store_nuevo_ciclo(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'id_modulo' => 'required',
            'id_variedad' => 'required',
            'id_semana' => 'required',
            'id_proyeccion_modulo' => 'required',
            'area' => 'required',
            'fecha_inicio' => 'required',
            'poda_siembra' => 'required',
            'plantas_iniciales' => 'required',
            'curva' => 'required',
            'desecho' => 'required',
            'semana_poda_siembra' => 'required',
        ], [
            'id_modulo.required' => 'El módulo es obligatorio',
            'area.required' => 'El área es obligatoria',
            'desecho.required' => 'El desecho es obligatorio',
            'id_variedad.required' => 'La variedad es obligatoria',
            'id_semana.required' => 'La semana es obligatoria',
            'id_proyeccion_modulo.required' => 'La proyección es obligatoria',
            'curva.required' => 'La curva es obligatoria',
            'semana_poda_siembra.required' => 'La semana de inicio de cosecha es obligatoria',
            'plantas_iniciales.required' => 'Las plantas iniciales son obligatorias',
            'fecha_inicio.required' => 'La fecha de inicio de cilo es obligatoria',
            'poda_siembra.required' => 'El campo poda/siembra es obligatorio',
        ]);
        if (!$valida->fails()) {
            /* ------------------------ Cerrar ciclo anterior ------------------- */
            $last_ciclo = Ciclo::All()
                ->where('estado', 1)
                ->where('activo', 1)
                ->where('id_modulo', $request->id_modulo)
                ->first();
            if ($last_ciclo != '') {
                $last_ciclo->activo = 0;
                $last_ciclo->fecha_fin = $request->fecha_fin;
                $last_ciclo->save();
            }

            $ciclo = new Ciclo();
            $ciclo->id_modulo = $request->id_modulo;
            $ciclo->id_variedad = $request->id_variedad;
            $ciclo->area = $request->area;
            $ciclo->fecha_inicio = $request->fecha_inicio;
            $ciclo->fecha_fin = date('Y-m-d');
            $ciclo->poda_siembra = $request->poda_siembra;
            $ciclo->desecho = $request->desecho;
            $ciclo->curva = $request->curva;
            $ciclo->semana_poda_siembra = $request->semana_poda_siembra;
            $ciclo->conteo = $request->conteo;
            $ciclo->plantas_iniciales = $request->plantas_iniciales;

            $semana = Semana::find($request->id_semana);

            if ($ciclo->save()) {
                $ciclo = Ciclo::All()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado un nuevo ciclo satisfactoriamente</p>'
                    . '</div>';
                bitacora('ciclo', $ciclo->id_ciclo, 'I', 'Inserción satisfactoria de un nuevo ciclo');

                /* ===================== QUITAR PROYECCIONES =================== */
                $proyecciones = ProyeccionModulo::find($request->id_proyeccion_modulo);
                $proyecciones->estado = 0;
                $proyecciones->save();
                bitacora('proyeccion_modulo', $proyecciones->id_proyeccion_modulo, 'U', 'Actualizacion satisfactoria del estado');

                /* ===================== CREAR SIGUIENTE PROYECCION ==================== */
                $sum_semana = intval($ciclo->semana_poda_siembra) + intval(count(explode('-', $ciclo->curva)));
                $codigo = $semana->codigo;
                $i = 1;
                $next = 1;
                while ($i < $sum_semana) {
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

                /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA ====================== */
                $semana_fin = DB::table('semana')
                    ->select(DB::raw('max(codigo) as max'))
                    ->where('estado', '=', 1)
                    ->where('id_variedad', '=', $request->id_variedad)
                    ->get()[0]->max;

                ProyeccionUpdateSemanal::dispatch($semana->codigo, $semana_fin, $request->id_variedad, $request->id_modulo, 0)
                    ->onQueue('proy_cosecha');
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
}