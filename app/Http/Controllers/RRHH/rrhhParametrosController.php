<?php

namespace yura\Http\Controllers\RRHH;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Banco;
use yura\Modelos\Submenu;

class rrhhParametrosController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.rrhh.parametros.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function listar_parametro(Request $request)
    {
        if ($request->tipo == 'banco') {
            return view('adminlte.gestion.rrhh.parametros.partials.listado_bancos', [
                'listado' => Banco::All()->sortBy('nombre')
            ]);
        }
        if ($request->tipo == 'cargos') {
            return 'listado de cargos';
        }
        if ($request->tipo == 'profesion') {
            return 'listado de profesiones';
        }
        if ($request->tipo == 'tipo_rol') {
            return 'listado de tipos de rol';
        }
        return '';
    }

    public function store_banco(Request $request)
    {
        $model = new Banco();
        $model->nombre = $request->nombre;
        if ($model->save()) {
            $msg = '<div class="alert alert-success text-center">Se ha guardado el banco satisfactoriamente</div>';
            $success = true;
        } else {
            $msg = '<div class="alert alert-danger text-center">Ha ocurrido un error al guardar la informacion</div>';
            $success = false;
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }
}