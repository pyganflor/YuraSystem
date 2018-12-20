<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Grosor;
use Validator;

class GrosorRamoController extends Controller
{
    public function admin_grosor_ramo(Request $request)
    {
        return view('adminlte.gestion.configuracion_empresa.partials.admin_grosor_ramo', [
            'listado' => Grosor::All()
        ]);
    }

    public function store_grosor_ramo(Request $request)
    {
        $msg = '';
        $success = true;
        if ($request->arreglo != '' && count($request->arreglo) > 0) {
            foreach ($request->arreglo as $item) {
                $valida = Validator::make($item, [
                    'nombre' => 'required|max:250|unique:grosor_ramo',
                ], [
                    'nombre.unique' => 'El nombre ya existe',
                    'nombre.required' => 'El nombre es obligatorio',
                    'nombre.max' => 'El nombre es muy grande',
                ]);
                if (!$valida->fails()) {
                    $model = new Grosor();
                    $model->nombre = str_limit(espacios(mb_strtoupper($item['nombre'])), 250);

                    if ($model->save()) {
                        $model = Grosor::All()->last();
                        bitacora('grosor_ramo', $model->id_grosor_ramo, 'I', 'Insercion satisfactoria de un nuevo grosor de ramo');
                    } else {
                        $success = false;
                        $msg = '<div class="alert alert-warning text-center">' .
                            'Hubo un problema al guardar el grosor ' . $item['nombre'] .
                            '</div>';
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
                    $msg .= '<div class="alert alert-warning">' .
                        '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                        '<ul>' .
                        $errores .
                        '</ul>' .
                        '</div>';
                }
            }
        } else {
            $success = false;
            $msg = '<div class="alert alert-warning text-center">' .
                'Al menos ingrese un nombre de un grosor nuevo' .
                '</div>';
        }
        if ($success) {
            $msg = '<div class="alert alert-success text-center">' .
                'Se ha guardado toda la información satisfactoriamente' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function update_grosor_ramo(Request $request)
    {
        $msg = '';
        $success = true;
        $valida = Validator::make($request->all(), [
            'id_grosor_ramo' => 'required',
            'nombre' => 'required|max:250',
        ], [
            'id_grosor_ramo.required' => 'El grosor es obligatorio',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            if (count(Grosor::All()->where('estado', '=', 1)->where('id_grosor_ramo', '!=', $request->id_grosor_ramo)
                    ->where('nombre', '=', $request->nombre)) == 0) {
                $model = Grosor::find($request->id_grosor_ramo);
                $model->nombre = str_limit(espacios(mb_strtoupper($request->nombre)), 250);

                if ($model->save()) {
                    bitacora('grosor_ramo', $model->id_grosor_ramo, 'U', 'Actualizacion satisfactoria de un grosor de ramo');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        'Hubo un problema al guardar el grosor' .
                        '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    'El nombre ya existe' .
                    '</div>';
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
            $msg = '<div class="alert alert-warning">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        if ($success) {
            $msg = '<div class="alert alert-success text-center">' .
                'Se ha guardado toda la información satisfactoriamente' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function delete_grosor_ramo(Request $request)
    {
        $success = false;
        $msg = '';
        if ($request->has('id_grosor_ramo')) {
            $g = Grosor::find($request->id_grosor_ramo);
            if ($g != '') {
                $g->estado = $request->estado;
                if ($g->save()) {
                    $texto = $request->estado == 1 ? 'Se ha activado satisfactoriamente' : 'Se ha desactivado satisfactoriamente';
                    $msg = '<div class="alert alert-success text-center">' . $texto . '</div>';
                    $success = true;

                    bitacora('grosor_ramo', $g->id_grosor_ramo, 'U', 'Cambio de estado de un grosor de ramo');
                } else {
                    $msg = '<div class="alert alert-warning text-center">No se ha podido guardar la información en el sistema</div>';
                    $success = false;
                }
            } else {
                $msg = '<div class="alert alert-warning text-center">No se ha encontrado el grupo de menú en el sistema</div>';
                $success = false;
            }
        } else {
            $msg = '<div class="alert alert-warning text-center">No se ha seleccionado un grupo de menú</div>';
            $success = false;
        }
        return [
            'success' => $success,
            'mensaje' => $msg
        ];
    }
}