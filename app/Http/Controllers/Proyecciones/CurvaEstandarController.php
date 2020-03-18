<?php

namespace yura\Http\Controllers\Proyecciones;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\GrupoMenu;
use yura\Modelos\Submenu;

class CurvaEstandarController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.proyecciones.curva_estandar.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'grupos_menu' => GrupoMenu::All(),
        ]);
    }

    public function listar_ciclos(Request $request)
    {
        dd($request->all());
        return view('adminlte.gestion.proyecciones.curva_estandar.partials.listado', [
        ]);
    }
}
