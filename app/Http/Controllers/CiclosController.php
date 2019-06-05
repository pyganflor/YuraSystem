<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Modelos\Ciclo;
use yura\Modelos\Cosecha;
use yura\Modelos\Modulo;
use yura\Modelos\Sector;
use Validator;

class CiclosController extends Controller
{
    public function listar_ciclos(Request $request)
    {
        $r = [];

        if ($request->tipo == 0) {  // inactivos
            foreach (Modulo::All()->where('estado', 1)->sortBy('nombre') as $m) {
                if ($m->cicloActual() == '')
                    array_push($r, Modulo::find($m->id_modulo));
            }
        } else {
            foreach (Modulo::All()->where('estado', 1)->sortBy('nombre') as $m) {
                if ($m->cicloActual() != '' && $m->cicloActual()->id_variedad == $request->variedad)
                    array_push($r, Modulo::find($m->id_modulo));
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

            if ($ciclo->save()) {
                $ciclo = Ciclo::All()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado un nuevo ciclo satisfactoriamente</p>'
                    . '</div>';
                bitacora('ciclo', $ciclo->id_ciclo, 'I', 'Inserción satisfactoria de un nuevo ciclo');
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
            foreach ($ciclo->modulo->ciclos as $c) {
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
                                '<p>La fecha fin ya se encuentra incluida en un ciclo anterior</p>'
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
            $ciclo->fecha_fin = $request->fecha_fin;

            if ($ciclo->save()) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha actualizado el ciclo satisfactoriamente</p>'
                    . '</div>';
                bitacora('ciclo', $ciclo->id_ciclo, 'U', 'Actualziacion satisfactoria de un ciclo');
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
        ], [
            'modulo.required' => 'El módulo es obligatorio',
        ]);
        if (!$valida->fails()) {
            $modulo = Modulo::find($request->modulo);
            $ciclo = $modulo->cicloActual();
            if ($ciclo->fecha_cosecha != '' && $ciclo->fecha_fin != '') {
                $ciclo->activo = 0;

                if ($ciclo->save()) {
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha terminado el ciclo satisfactoriamente</p>'
                        . '</div>';
                    bitacora('ciclo', $ciclo->id_ciclo, 'U', 'Actualizacion satisfactoria de un ciclo (terminar ciclo)');
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
                    '<p> Se ha terminado el ciclo satisfactoriamente</p>'
                    . '</div>';
                bitacora('ciclo', $ciclo->id_ciclo, 'U', 'Actualizacion satisfactoria de un ciclo (abrir ciclo)');
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