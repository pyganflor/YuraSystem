<?php

namespace yura\Http\Controllers\Proyecciones;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Ciclo;
use yura\Modelos\GrupoMenu;
use yura\Modelos\Sector;
use yura\Modelos\Submenu;
use yura\Modelos\Temperatura;
use Validator;

class proyTemperaturaController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.proyecciones.temperaturas.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'grupos_menu' => GrupoMenu::All(),
            'sectores' => Sector::All()->where('estado', 1)->where('interno', 1)->sortBy('nombre')
        ]);
    }

    public function listar_ciclos(Request $request)
    {
        $query = Ciclo::where('estado', 1)
            ->where('activo', 1)
            ->where('id_variedad', $request->variedad)
            ->orderBy('fecha_inicio', 'desc')
            ->where('poda_siembra', $request->poda_siembra)
            ->get();    // ciclos activos
        return view('adminlte.gestion.proyecciones.temperaturas.partials.listado', [
            'ciclos' => $query,
            'sector' => $request->sector,
        ]);
    }

    public function add_temperatura(Request $request)
    {
        $temperatura = Temperatura::All()
            ->where('estado', 1)
            ->where('fecha', date('Y-m-d'))
            ->first();
        return view('adminlte.gestion.proyecciones.temperaturas.forms.add_temperatura', [
            'temperatura' => $temperatura,
        ]);
    }

    public function store_temperatura(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'fecha' => 'required',
            'maxima' => 'required',
            'minima' => 'required',
            'lluvia' => 'required',
        ], [
            'fecha.required' => 'La fecha es obligatoria',
            'maxima.required' => 'La máxima es obligatoria',
            'minima.required' => 'La mínima es obligatoria',
            'lluvia.required' => 'La lluvia es obligatoria',
        ]);
        if (!$valida->fails()) {
            $model = Temperatura::All()
                ->where('estado', 1)
                ->where('fecha', $request->fecha)
                ->first();
            if ($model == '') {
                $model = new Temperatura();
                $model->fecha = $request->fecha;
                $id_model = '';
            } else
                $id_model = $model->id_temperatura;
            $model->maxima = $request->maxima;
            $model->minima = $request->minima;
            $model->lluvia = $request->lluvia;

            if ($model->save()) {
                $model = $id_model != '' ? Temperatura::find($id_model) : Temperatura::All()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado una nueva temperatura satisfactoriamente</p>'
                    . '</div>';
                bitacora('temperatura', $model->id_temperatura, $id_model != '' ? 'U' : 'I', 'Inserción satisfactoria de una nueva temperatura');
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

    public function buscar_temperatura(Request $request)
    {
        $temperatura = Temperatura::All()
            ->where('estado', 1)
            ->where('fecha', $request->fecha)
            ->first();
        return [
            'temperatura' => $temperatura
        ];
    }

    public function store_all_temperatura(Request $request)
    {
        $success = true;
        $msg = '';
        foreach ($request->data as $data) {
            if ($data['fecha'] != '') {
                $model = Temperatura::All()
                    ->where('estado', 1)
                    ->where('fecha', $data['fecha'])
                    ->first();
                if ($model == '') {
                    $model = new Temperatura();
                    $model->fecha = $data['fecha'];
                    $id_model = '';
                } else
                    $id_model = $model->id_temperatura;
                $model->maxima = $data['maxima'];
                $model->minima = $data['minima'];
                $model->lluvia = $data['lluvia'];

                if ($model->save()) {
                    $model = $id_model != '' ? Temperatura::find($id_model) : Temperatura::All()->last();
                    bitacora('temperatura', $model->id_temperatura, $id_model != '' ? 'U' : 'I', 'Inserción satisfactoria de una temperatura');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información del día "' . $data['fecha'] . '"</p>'
                        . '</div>';
                    break;
                }
            }
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }
}