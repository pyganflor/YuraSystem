<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Modelos\Ciclo;
use yura\Modelos\GrupoMenu;
use yura\Modelos\Monitoreo;
use yura\Modelos\Submenu;
use Validator;

class MonitoreoController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.proyecciones.monitoreo.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'grupos_menu' => GrupoMenu::All()
        ]);
    }

    public function listar_ciclos(Request $request)
    {
        $query = Ciclo::where('estado', 1)
            ->where('activo', 1)
            ->where('id_variedad', $request->variedad)
            ->orderBy('fecha_inicio', 'desc')
            ->get();
        $ciclos = [];
        foreach ($query as $item) {
            $monitoreos = Monitoreo::where('estado', 1)
                ->where('id_ciclo', $item->id_ciclo)
                ->where('num_sem', '<=', $request->num_semanas)
                ->orderBy('num_sem')
                ->get();
            array_push($ciclos, [
                'ciclo' => $item,
                'monitoreos' => $monitoreos,
            ]);
        }

        return view('adminlte.gestion.proyecciones.monitoreo.partials.listado', [
            'ciclos' => $ciclos,
            'num_semanas' => $request->num_semanas,
        ]);
    }

    public function guardar_monitoreo(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'ciclo' => 'required',
            'cant_mon' => 'required',
            'valor' => 'required',
        ], [
            'ciclo.required' => 'El nombre es obligatorio',
            'cant_mon.required' => 'El número de la semana es obligatorio',
            'valor.required' => 'El valor a guardar es obligatorio',
        ]);
        if (!$valida->fails()) {
            $model = Monitoreo::All()
                ->where('estado', 1)
                ->where('id_ciclo', $request->ciclo)
                ->where('num_sem', $request->cant_mon)
                ->first();
            if ($model == '') {
                $model = new Monitoreo();
                $model->id_ciclo = $request->ciclo;
                $model->num_sem = $request->cant_mon;
            }
            $model->altura = $request->valor;

            if ($model->save()) {
                $model = Monitoreo::All()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado el monitoreo satisfactoriamente</p>'
                    . '</div>';
                bitacora('monitoreo', $model->id_monitoreo, 'I', 'Modificacion del monitoreo');
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