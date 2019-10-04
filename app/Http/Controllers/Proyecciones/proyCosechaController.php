<?php

namespace yura\Http\Controllers\Proyecciones;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Http\Controllers\Controller;
use yura\Jobs\ProyeccionUpdateSemanal;
use yura\Modelos\Ciclo;
use yura\Modelos\Modulo;
use yura\Modelos\ProyeccionModulo;
use yura\Modelos\ProyeccionModuloSemana;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;
use Validator;

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
            $model->tipo = $request->tipo;
            $model->curva = $request->curva;
            $model->semana_poda_siembra = $request->semana_poda_siembra;
            $model->plantas_iniciales = $request->plantas_iniciales;
            $model->desecho = $request->desecho;
            $model->tallos_planta = $request->tallos_planta;
            $model->tallos_ramo = $request->tallos_ramo;

            $semana = Semana::All()->where('estado', 1)->where('id_variedad', $model->id_variedad)
                ->where('codigo', $request->semana)->first();
            if ($semana != '') {
                $model->id_semana = $semana->id_semana;
                $model->fecha_inicio = $semana->fecha_inicial;

                $model->poda_siembra = 0;       // borrar campo

                if ($model->save()) {
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado la proyección satisfactoriamente</p>'
                        . '</div>';
                    bitacora('proyeccion_modulo', $model->id_proyeccion_modulo, 'U', 'Actualización satisfactoria de la proyección');

                    /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA ====================== */
                    $semana_desde = min($request->semana, $request->semana_actual, $semana->codigo);
                    $semana_fin = DB::table('semana')
                        ->select(DB::raw('max(codigo) as max'))
                        ->where('estado', '=', 1)
                        ->where('id_variedad', '=', $model->id_variedad)
                        ->get()[0]->max;

                    Artisan::call('proyeccion:update_semanal', [
                        'semana_desde' => $semana_desde,
                        'semana_hasta' => $semana_fin,
                        'variedad' => $model->id_variedad,
                        'modulo' => $model->id_modulo,
                    ]);
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
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
        if (!$valida->fails()) {
            $model = Ciclo::find($request->id_ciclo);

            $semana_fin = getLastSemanaByVariedad($model->id_variedad);

            $sum_semana_new = $request->semana_poda_siembra + count(explode('-', $request->curva));
            $sum_semana_old = $model->semana_poda_siembra + count(explode('-', $model->curva));
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
                        ->where('id_modulo', $model->id_modulo)
                        ->where('id_variedad', $model->id_variedad)
                        ->orderBy('fecha_inicio')
                        ->get();
                    foreach ($proys as $proy)
                        $proy->delete();
                }
            }

            $model->poda_siembra = $request->poda_siembra;
            $model->curva = $request->curva;
            $model->semana_poda_siembra = $request->semana_poda_siembra;
            $model->plantas_iniciales = $request->plantas_iniciales;
            $model->desecho = $request->desecho;
            $model->conteo = $request->conteo;

            if ($model->save()) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado la información satisfactoriamente</p>'
                    . '</div>';
                bitacora('ciclo', $model->id_ciclo, 'U', 'Actualización satisfactoria de un ciclo');

                /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA ====================== */
                $semana_desde = getSemanaByDate($model->fecha_inicio)->codigo;

                Artisan::call('proyeccion:update_semanal', [
                    'semana_desde' => $semana_desde,
                    'semana_hasta' => $semana_fin->codigo,
                    'variedad' => $model->id_variedad,
                    'modulo' => $model->id_modulo,
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
            'restriccion' => 1,
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
        return view('adminlte.gestion.proyecciones.cosecha.forms.actualizar_datos', []);
    }
}