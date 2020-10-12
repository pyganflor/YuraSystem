<?php

namespace yura\Http\Controllers\Propagacion;

use Illuminate\Http\Request;
use yura\Modelos\CicloCama;
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
        $ciclos = CicloCama::where('fecha_inicio', '<=', $request->fecha)
            ->where('fecha_fin', '>=', $request->fecha);

        if ($request->variedad != 'T')
            $ciclos = $ciclos->where('id_variedad', $request->variedad);

        $ciclos = $ciclos->orderBy('fecha_inicio')->get();

        return view('adminlte.crm.propagacion.fenograma.partials.filtrar_ciclos', [
            'ciclos' => $ciclos
        ]);
    }
}
