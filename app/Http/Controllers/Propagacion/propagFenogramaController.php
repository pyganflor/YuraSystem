<?php

namespace yura\Http\Controllers\Propagacion;

use Illuminate\Http\Request;
use yura\Modelos\Submenu;
use yura\Http\Controllers\Controller;

class propagFenogramaController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.crm.propagacion.fenograma.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function filtrar_ciclos(Request $request)
    {
        dd($request->all());
    }
}
