<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;
use yura\Modelos\Variedad;
use Validator;

class SemanaController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.semanas.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function get_accion(Request $request)
    {
        if ($request->accion == 1) {    // filtrar
            return view('adminlte.gestion.semanas.partials.accion_filtrar', [
                'variedades' => Variedad::All()->where('estado', '=', 1),
                'annos' => DB::table('semana as s')
                    ->select('s.anno')->distinct()
                    ->where('s.estado', '=', 1)->orderBy('s.anno')->get()
            ]);
        } else if ($request->accion == 2) { // procesar semanas
            return view('adminlte.gestion.semanas.partials.accion_procesar', [
                'variedades' => Variedad::All()->where('estado', '=', 1)
            ]);
        } else if ($request->accion == 3) { // copiar semanas
            return view('adminlte.gestion.semanas.partials.accion_copiar', [
                'variedades' => Variedad::All()->where('estado', '=', 1),
                'annos' => DB::table('semana as s')
                    ->select('s.anno')->distinct()
                    ->where('s.estado', '=', 1)->orderBy('s.anno', 'desc')->get()
            ]);
        }
    }

    public function procesar(Request $request)
    {
        $msg = '';
        $success = true;
        if (count(Semana::All()->where('anno', '=', $request->anno)
                ->where('id_variedad', '=', $request->id_variedad)) == 0) {
            if ($request->fecha_inicial < $request->fecha_final) {
                /* =========================== OBTENER LAS SEMANAS =======================*/
                $arreglo = [];
                $inicio = $request->fecha_inicial;
                $fin = strtotime('+6 day', strtotime($inicio));
                $fin = date('Y-m-d', $fin);

                array_push($arreglo, [
                    'inicio' => $inicio,
                    'fin' => $fin
                ]);

                $inicio = strtotime('+1 day', strtotime($fin));
                $inicio = date('Y-m-j', $inicio);

                while ($inicio < $request->fecha_final) {
                    if (existInSemana($inicio, $request->id_variedad, $request->anno) && existInSemana($fin, $request->id_variedad, $request->anno)) {
                        $fin = strtotime('+6 day', strtotime($inicio));
                        $fin = date('Y-m-d', $fin);

                        array_push($arreglo, [
                            'inicio' => $inicio,
                            'fin' => $fin
                        ]);

                        $inicio = strtotime('+1 day', strtotime($fin));
                        $inicio = date('Y-m-d', $inicio);
                    } else {
                        $success = false;
                        $msg = '<div class="text-center alert alert-danger">El rango indicado incluye al menos una fecha que ya está registrada</div>';
                        break;
                    }
                }
                /* =========================== VERIFICAR LA CANTIDAD DE SEMANAS EN UN AÑO =======================*/
                if (count($arreglo) >= 52 && count($arreglo) <= 53) {
                    /* =========================== GRABAR EN LA BASE LAS SEMANAS =======================*/
                    for ($i = 0; $i < count($arreglo); $i++) {
                        $model = new Semana();
                        $model->id_variedad = $request->id_variedad;
                        $model->anno = $request->anno;
                        $pref = ($i + 1) < 10 ? '0' : '';
                        $model->codigo = substr($request->anno, 2) . $pref . ($i + 1);
                        $model->fecha_inicial = $arreglo[$i]['inicio'];
                        $model->fecha_final = $arreglo[$i]['fin'];
                        if ($model->save()) {
                            $model = Semana::All()->last();
                            bitacora('semana', $model->id_semana, 'I', 'Inserción satisfactoria de una semana');
                        } else {
                            $success = false;
                            $msg .= '<div class="text-center alert alert-danger">' .
                                'Ha ocurrido un problema al guardar la información de la semana ' . $model->codigo .
                                '</div>';
                        }
                    }
                } else {
                    $success = false;
                    $msg = '<div class="text-center alert alert-danger">No se ha cumplido el rango de 52-53 semanas de un año en el rango indicado</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="text-center alert alert-danger">La fecha inicial debe ser menor que la final</div>';
            }
        } else {
            $success = false;
            $msg = '<div class="text-center alert alert-danger">Ya existe una programación para esta variedad en el año ' . $request->anno . '</div>';
        }
        if ($success)
            $msg = '<div class="text-center alert alert-success">Sa han procesado correctamente las semanas</div>';
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function listar_semanas(Request $request)
    {
        $r = Semana::All()->where('anno', '=', $request->anno)
            ->where('id_variedad', '=', $request->id_variedad);
        return view('adminlte.gestion.semanas.partials.listado', [
            'semanas' => $r,
            'variedad' => getVariedad($request->id_variedad),
            'getMeses' => getMeses(),
        ]);
    }

    public function update_semana(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'curva' => 'required',
            'desecho' => 'required|max:2|',
            'semana_poda' => 'required|max:2|',
            'semana_siembra' => 'required|max:2|',
            'id_semana' => 'required|',
            'tallos_planta_siembra' => 'required|',
            'tallos_planta_poda' => 'required|',
            'tallos_ramo_siembra' => 'required|',
            'tallos_ramo_poda' => 'required|',
            'mes' => 'required|',
        ], [
            'id_semana.required' => 'La semana es obligatoria',
            'semana_siembra.required' => 'La semana de inicio de siembra es obligatoria',
            'semana_siembra.max' => 'La semana de inicio de siembra es muy grande',
            'semana_poda.required' => 'La semana de inicio de poda es obligatoria',
            'semana_poda.max' => 'La semana de inicio de poda es muy grande',
            'desecho.required' => 'El porcentaje de desecho es obligatorio',
            'desecho.max' => 'El porcentaje de desecho es muy grande',
            'curva.required' => 'La curva es obligatoria',
            'mes.required' => 'El mes es obligatorio',
        ]);
        if (!$valida->fails()) {
            $model = Semana::find($request->id_semana);
            $model->curva = str_limit(strtoupper(espacios($request->curva)), 11);
            $model->desecho = $request->desecho;
            $model->semana_poda = $request->semana_poda;
            $model->semana_siembra = $request->semana_siembra;
            $model->tallos_planta_siembra = $request->tallos_planta_siembra;
            $model->tallos_planta_poda = $request->tallos_planta_poda;
            $model->tallos_ramo_siembra = $request->tallos_ramo_siembra;
            $model->tallos_ramo_poda = $request->tallos_ramo_poda;
            $model->tallos_ramo_poda = $request->tallos_ramo_poda;

            $objSemana = Semana::where('codigo', $model->codigo);
            $objSemana->update(['mes' => $request->mes]);

            if ($model->save()) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha actualizado la semana satisfactoriamente</p>'
                    . '</div>';
                bitacora('semana', $model->id_semana, 'U', 'Actualización satisfactoria de una semana');
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

    public function igualar_datos(Request $request)
    {
        return view('adminlte.gestion.semanas.forms.igualar_datos', [
            'curva' => $request->curva,
            'desecho' => $request->desecho,
            'semana_poda' => $request->semana_poda,
            'semana_siembra' => $request->semana_siembra,
            'tallos_planta_siembra' => $request->tallos_planta_siembra,
            'tallos_planta_poda' => $request->tallos_planta_poda,
            'tallos_ramo_siembra' => $request->tallos_ramo_siembra,
            'tallos_ramo_poda' => $request->tallos_ramo_poda,
            'selection' => $request->selection,
        ]);
    }

    public function store_igualar_datos(Request $request)
    {
        $success = true;
        $msg = '';
        $valida = Validator::make($request->all(), [
            'ids' => 'required|',
        ], [
            'ids.required' => 'Al menos seleccione una semana',
        ]);
        if (!$valida->fails()) {
            foreach ($request->ids as $id) {
                $model = Semana::find($id);
                if ($request->curva != null) {
                    $model->curva = str_limit(strtoupper(espacios($request->curva)), 11);
                }
                if ($request->desecho != null)
                    $model->desecho = str_limit(strtoupper(espacios($request->desecho)), 2);
                if ($request->semana_poda != null)
                    $model->semana_poda = str_limit(strtoupper(espacios($request->semana_poda)), 2);
                if ($request->semana_siembra != null)
                    $model->semana_siembra = str_limit(strtoupper(espacios($request->semana_siembra)), 2);
                if ($request->tallos_planta_siembra != null)
                    $model->tallos_planta_siembra = $request->tallos_planta_siembra;
                if ($request->tallos_planta_poda != null)
                    $model->tallos_planta_poda = $request->tallos_planta_poda;
                if ($request->tallos_ramo_siembra != null)
                    $model->tallos_ramo_siembra = $request->tallos_ramo_siembra;
                if ($request->tallos_ramo_poda != null)
                    $model->tallos_ramo_poda = $request->tallos_ramo_poda;

                if ($model->save()) {
                    $msg .= '<div class="alert alert-success text-center">' .
                        '<p> Se ha actualizado la semana ' . $model->codigo . ' satisfactoriamente</p>'
                        . '</div>';
                    bitacora('semana', $model->id_semana, 'U', 'Actualización satisfactoria de una semana');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
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

    public function copiar_semanas(Request $request)
    {
        $semanas = Semana::where('estado', 1)
            ->where('id_variedad', $request->variedad)
            ->where('anno', $request->anno)
            ->get();
        if (count($semanas) > 0) {
            foreach (getVariedades() as $var) {
                $sem_var = Semana::where('estado', 1)
                    ->where('id_variedad', $var->id_variedad)
                    ->where('anno', $request->anno)
                    ->get();
                if (count($sem_var) == 0)
                    foreach ($semanas as $sem) {
                        $new = new Semana();
                        $new->id_variedad = $var->id_variedad;
                        $new->anno = $sem->anno;
                        $new->codigo = $sem->codigo;
                        $new->fecha_inicial = $sem->fecha_inicial;
                        $new->fecha_final = $sem->fecha_final;
                        $new->curva = $sem->curva;
                        $new->desecho = $sem->desecho;
                        $new->semana_poda = $sem->semana_poda;
                        $new->semana_siembra = $sem->semana_siembra;
                        $new->tallos_planta_siembra = $sem->tallos_planta_siembra;
                        $new->tallos_planta_poda = $sem->tallos_planta_poda;
                        $new->tallos_ramo_siembra = $sem->tallos_ramo_siembra;
                        $new->tallos_ramo_poda = $sem->tallos_ramo_poda;
                        $new->mes = $sem->mes;

                        $new->save();
                    }
            }
            $success = true;
            $msg = '<div class="alert alert-success text-center">Se han copiado las semanas satisfactoriamente</div>';
        } else {
            $success = false;
            $msg = '<div class="alert alert-danger text-center">La variedad no tiene semanas ingresadas para el año indicado</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
        dd($semanas, $request->all());
    }
}
