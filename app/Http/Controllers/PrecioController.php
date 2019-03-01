<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Submenu;

class PrecioController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.gestion.postcocecha.precio.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Precios', 'subtitulo' => 'módulo de postcocecha']
            ]);
    }

    public function buscar(Request $request){
        return view('adminlte.gestion.postcocecha.precio.partials.listado');
    }

}
