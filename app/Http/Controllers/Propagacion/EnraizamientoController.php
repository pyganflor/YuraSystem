<?php

namespace yura\Http\Controllers\Propagacion;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Submenu;

class EnraizamientoController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.propagacion.enraizamiento.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }
}
