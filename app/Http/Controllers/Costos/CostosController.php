<?php

namespace yura\Http\Controllers\Costos;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Area;
use yura\Modelos\Submenu;

class CostosController extends Controller
{
    public function gestion(Request $request)
    {
        return view('adminlte.gestion.costos.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'areas' => Area::All()
        ]);
    }
}