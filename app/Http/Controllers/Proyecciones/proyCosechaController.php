<?php

namespace yura\Http\Controllers\Proyecciones;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;

class proyCosechaController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.proyecciones.cosecha.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function listar_proyecciones(Request $request)
    {
        //dd($request->all());

        $array_semanas = [];
        for ($i = $request->desde; $i <= $request->hasta; $i++) {
            $semana = Semana::All()->where('estado', 1)->where('codigo', $i)->first();
            if ($semana != '')
                if (!in_array($semana->codigo, $array_semanas))
                    array_push($array_semanas, $semana->codigo);
        }

        dd($array_semanas);
    }
}