<?php

namespace yura\Http\Controllers\Proyecciones;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\Ciclo;
use yura\Modelos\Modulo;
use yura\Modelos\ProyeccionModulo;
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

        $semana_desde = Semana::All()->where('codigo', $request->desde)->first();
        $semana_hasta = Semana::All()->where('codigo', $request->hasta)->first();
        if ($semana_desde != '' && $semana_hasta != '') {
            $fecha_ini = DB::table('ciclo')
                ->select(DB::raw('min(fecha_inicio) as inicio'))->distinct()
                ->where('estado', '=', 1)
                ->where('id_variedad', '=', $request->variedad)
                ->where('fecha_fin', '>=', $semana_desde->fecha_inicial)
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
                    ->where('fecha_fin', '>=', $semana_desde->fecha_inicial)
                    ->orderBy('activo', 'desc')
                    ->orderBy('fecha_inicio', 'asc')
                    ->get();

                //dd($semanas, $query_modulos);

                $array_modulos = [];
                foreach ($query_modulos as $pos_mod => $mod) {
                    if ($pos_mod <= 10) {
                        $mod = getModuloById($mod->id_modulo);
                        $array_valores = [];
                        foreach ($semanas as $sem) {
                                $data = $mod->getDataBySemana($sem, $request->variedad, $semana_desde->fecha_inicial, $request->opcion, $request->detalle);
                                $valor = [
                                    'tiempo' => -1,
                                    'data' => $data,
                                ];
                            array_push($array_valores, $valor);
                        }
                        array_push($array_modulos, [
                            'modulo' => $mod,
                            'valores' => $array_valores
                        ]);
                    }
                }


                return view('adminlte.gestion.proyecciones.cosecha.partials.listado', [
                    'semanas' => $semanas,
                    'modulos' => $array_modulos,
                    'variedad' => $request->variedad,
                    'semana_desde' => $semana_desde,
                    'opcion' => $request->opcion,
                    'detalle' => $request->detalle,
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
                'semana' => Semana::find($request->semana),
                'variedad' => getVariedad($request->variedad),
                'last_ciclo' => Ciclo::All()
                    ->where('estado', 1)
                    ->where('id_variedad', $request->variedad)
                    ->where('id_modulo', $request->modulo)->last(),
            ]);
        }
        if ($request->tipo == 'Y') {    // crear una proyecccion
            return view('adminlte.gestion.proyecciones.cosecha.forms.edit_proy', [
                'modulo' => getModuloById($request->modulo),
                'semana' => Semana::find($request->semana),
                'variedad' => getVariedad($request->variedad),
                'proyeccion' => ProyeccionModulo::find($request->model),
            ]);
        }
        if ($request->tipo == 'T') {    // crear una proyecccion
            if ($request->tabla == 'P') {
                return view('adminlte.gestion.proyecciones.cosecha.forms.edit_proy', [
                    'modulo' => getModuloById($request->modulo),
                    'semana' => Semana::find($request->semana),
                    'variedad' => getVariedad($request->variedad),
                    'proyeccion' => ProyeccionModulo::find($request->model),
                ]);
            } else {
                return view('adminlte.gestion.proyecciones.cosecha.forms.edit_ciclo', [
                    'modulo' => getModuloById($request->modulo),
                    'semana' => Semana::find($request->semana),
                    'variedad' => getVariedad($request->variedad),
                    'ciclo' => Ciclo::find($request->model),
                ]);
            }
        }
        if (in_array($request->tipo, ['P', 'S'])) {    // editar ciclo poda
            return view('adminlte.gestion.proyecciones.cosecha.forms.edit_ciclo', [
                'modulo' => getModuloById($request->modulo),
                'semana' => Semana::find($request->semana),
                'variedad' => getVariedad($request->variedad),
                'ciclo' => Ciclo::find($request->model),
            ]);
        }
    }

    public function load_celda(Request $request)
    {
        $title = 'OK';
        return view('adminlte.gestion.proyecciones.cosecha.partials._celda', [
            'title' => $title
        ]);
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
            $model->id_semana = $semana->id_semana;
            $model->fecha_inicio = $semana->fecha_inicial;

            $model->poda_siembra = 0;       // borrar campo

            if ($model->save()) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado la proyección satisfactoriamente</p>'
                    . '</div>';
                bitacora('proyeccion_modulo', $model->id_proyeccion_modulo, 'U', 'Actualización satisfactoria de la proyección');
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