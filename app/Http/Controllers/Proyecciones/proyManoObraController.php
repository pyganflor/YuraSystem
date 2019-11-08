<?php

namespace yura\Http\Controllers\Proyecciones;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Submenu;

class proyManoObraController extends Controller
{
    public function inicio(Request $request)
    {
        $hasta = getSemanaByDate(opDiasFecha('+', 98, date('Y-m-d')));
        return view('adminlte.gestion.proyecciones.mano_obra.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'semana_hasta' => $hasta
        ]);
    }

    public function listar_proyecciones(Request $request)
    {
        return view('adminlte.gestion.proyecciones.mano_obra.partials.listado', [

        ]);
    }
}
