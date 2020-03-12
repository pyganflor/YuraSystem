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
            ->where('poda_siembra', $request->poda_siembra)
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
            'min_semanas' => $request->min_semanas,
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

    public function store_nuevos_ingresos(Request $request)
    {
        foreach ($request->data as $pos => $item) {
            $ciclo = Ciclo::find($item['ciclo']);
            if ($ciclo != '') {
                $last_monitoreo = Monitoreo::where('estado', 1)
                    ->where('id_ciclo', $ciclo->id_ciclo)
                    ->orderBy('num_sem', 'desc')
                    ->first();
                if ($last_monitoreo == '') {
                    $last_monitoreo = new Monitoreo();
                    $last_monitoreo->id_ciclo = $ciclo->id_ciclo;
                    $last_monitoreo->num_sem = 1;
                }
                $last_monitoreo->altura = $item['valor'];

                if (!$last_monitoreo->save()) {
                    return [
                        'success' => false,
                        'mensaje' => '<div class="alert alert-danger text-center">Ha ocurrido un problema con el módulo "' . $ciclo->modulo->nombre . '</div>',
                    ];
                }
            }
        }
        return [
            'success' => true,
            'mensaje' => '<div class="alert alert-success text-center">Se ha guardado satisfactoramente la información</div>',
        ];
    }
}