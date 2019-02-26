<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\ClasificacionUnitaria;

class ClasificacionesController extends Controller
{
    public function admin_clasificacion_unitaria(Request $request)
    {
        return view('adminlte.gestion.configuracion_empresa.partials.admin_clasificacion_unitaria', [
            'unitarias' => getUnitarias()
        ]);
    }

    public function admin_clasificacion_ramo(Request $request)
    {
        return view('adminlte.gestion.configuracion_empresa.partials.admin_clasificacion_ramo', [
            'ramos' => getCalibresRamo(),
            'unidades' => getUnidadesMedida()
        ]);
    }

    public function update_unitaria(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:25',
            'id_unidad_medida' => 'required|',
            'id_clasificacion_unitaria' => 'required|',
            'id_clasificacion_ramo_estandar' => 'required|',
            'id_clasificacion_ramo_real' => 'required|',
            'color' => 'required|',
            'ramos_x_balde' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'id_clasificacion_ramo_estandar.required' => 'El ramo estandar es obligatorio',
            'id_clasificacion_ramo_real.required' => 'El ramo real es obligatorio',
            'id_clasificacion_unitaria.required' => 'La clasificación es obligatoria',
            'id_unidad_medida.required' => 'La unidad de medida es obligatoria',
            'color.required' => 'El color es obligatorio',
            'ramos_x_balde.required' => 'Los ramos por balde son obligatorios',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            $unitaria = ClasificacionUnitaria::find($request->id_clasificacion_unitaria);
            $unitaria->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 25);
            $unitaria->id_unidad_medida = $request->id_unidad_medida;
            $unitaria->id_clasificacion_ramo_real = $request->id_clasificacion_ramo_real;
            $unitaria->id_clasificacion_ramo_estandar = $request->id_clasificacion_ramo_estandar;
            $unitaria->tallos_x_ramo = $request->tallos_x_ramo;
            $unitaria->ramos_x_balde = $request->ramos_x_balde;
            $unitaria->color = $request->color . '|' . $request->color_txt;
            if ($unitaria->save()) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha actualizado la clasificación unitaria satisfactoriamente</p>'
                    . '</div>';
                bitacora('clasificacion_unitaria', $unitaria->id_clasificacion_unitaria, 'U', 'Actualización satisfactoria de una clasificación unitaria');
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

    public function update_ramo(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:25',
            'id_clasificacion_ramo' => 'required|',
            'id_unidad_medida' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'id_unidad_medida.required' => 'La unidad de medida es obligatoria',
            'id_clasificacion_ramo.required' => 'La clasificación es obligatoria',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            $ramo = ClasificacionRamo::find($request->id_clasificacion_ramo);
            $ramo->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 25);
            $ramo->id_unidad_medida = $request->id_unidad_medida;
            if ($ramo->save()) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha actualizado la clasificación del ramo satisfactoriamente</p>'
                    . '</div>';
                bitacora('clasificacion_ramo', $ramo->id_clasificacion_ramo, 'U', 'Actualización satisfactoria de una clasificación de ramo');
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

    public function seleccionar_unidad_medida(Request $request)
    {
        $r = ClasificacionRamo::All()->where('id_unidad_medida', '=', $request->id_unidad_medida)
            ->where('estado', '=', 1);
        return view('adminlte.gestion.configuracion_empresa.partials.select_clasificacion_ramo', [
            'ramos' => $r,
            'campo' => $request->campo,
            'unitaria' => ClasificacionUnitaria::find($request->id_clasificacion_unitaria)
        ]);
    }

}