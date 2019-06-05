<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use yura\Modelos\Color;

class ColorController extends Controller
{
    public function inicio(Request $request)
    {
        $colores = getColores();
        return view('layouts.adminlte.partials.admin_colores', [
            'colores' => $colores
        ]);
    }

    public function store_color(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:250|unique:color',
            'fondo' => 'required|unique:color',
            'texto' => 'required',
        ], [
            'nombre.unique' => 'El nombre ya existe',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
            'fondo.unique' => 'El fondo ya existe',
            'fondo.required' => 'El fondo es obligatorio',
            'texto.required' => 'El texto es obligatorio',
        ]);
        if (!$valida->fails()) {
            $model = new Color();
            $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 250);
            $model->fondo = $request->fondo;
            $model->texto = $request->texto;

            if ($model->save()) {
                $model = Color::All()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado un nuevo color satisfactoriamente</p>'
                    . '</div>';
                bitacora('color', $model->id_color, 'I', 'Inserción satisfactoria de un nuevo color');
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

    public function update_color(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:250|',
            'fondo' => 'required|',
            'texto' => 'required',
            'id_color' => 'required',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
            'fondo.required' => 'El fondo es obligatorio',
            'texto.required' => 'El texto es obligatorio',
            'id_color.required' => 'El id_color es obligatorio',
        ]);
        if (!$valida->fails()) {
            if (count(Color::All()->where('nombre', str_limit(mb_strtoupper(espacios($request->nombre)), 250))
                    ->where('id_color', '!=', $request->id_color)) > 0) {
                $success = false;
                $msg = '<div class="alert alert-danger text-center">El nombre ya existe</div>';
            } elseif (count(Color::All()->where('fondo', $request->fondo)
                    ->where('id_color', '!=', $request->id_color)) > 0) {
                $success = false;
                $msg = '<div class="alert alert-danger text-center">El fondo ya existe</div>';
            } else {
                $model = Color::find($request->id_color);
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 250);
                $model->fondo = $request->fondo;
                $model->texto = $request->texto;

                if ($model->save()) {
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha actualizado el color satisfactoriamente</p>'
                        . '</div>';
                    bitacora('color', $model->id_color, 'U', 'Actualizacion satisfactoria de un color');
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
}
