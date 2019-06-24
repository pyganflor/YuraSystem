<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Ciclo;
use yura\Modelos\Submenu;

class FenogramaEjecucionController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.crm.fenograma_ejecucion.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function filtrar_ciclos(Request $request)
    {
        $ciclos = Ciclo::All()
            ->where('estado', 1)
            ->where('fecha_inicio', '<=', $request->fecha)
            ->where('fecha_fin', '>=', $request->fecha);

        if ($request->variedad != 'T')
            $ciclos = $ciclos->where('id_variedad', $request->variedad);

        return view('adminlte.crm.fenograma_ejecucion.partials.filtrar_ciclos', [
            'ciclos' => $ciclos
        ]);
    }
}