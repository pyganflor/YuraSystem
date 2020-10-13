<?php

namespace yura\Http\Controllers\Propagacion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $ciclos = DB::table('ciclo_cama as cc')
            ->join('cama as c', 'c.id_cama', 'cc.id_cama')
            ->select('cc.id_ciclo_cama')
            ->where('cc.fecha_inicio', '<=', $request->fecha)
            ->where('cc.fecha_fin', '>=', $request->fecha);

        if ($request->variedad != 'T')
            $ciclos = $ciclos->where('cc.id_variedad', $request->variedad);

        $ciclos = $ciclos->orderBy('cc.fecha_inicio')->orderBy('c.nombre')->get();

        $list = [];
        foreach ($ciclos as $c)
            array_push($list, CicloCama::find($c->id_ciclo_cama));
        return view('adminlte.crm.propagacion.fenograma.partials.filtrar_ciclos', [
            'ciclos' => $list
        ]);
    }
}
