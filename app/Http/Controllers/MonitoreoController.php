<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Modelos\Ciclo;
use yura\Modelos\GrupoMenu;
use yura\Modelos\Submenu;

class MonitoreoController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.proyecciones.monitoreo.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'grupos_menu' => GrupoMenu::All()
        ]);
    }

    public function listar_ciclos(Request $request)
    {
        $ciclos = DB::table('ciclo as c')
            //->join('modulo as mod', 'mod.id_modulo', '=', 'c.id_modulo')
            ->join('monitoreo as m', 'm.id_ciclo', '=', 'c.id_ciclo')
            //->select('mod.nombre as nombre', 'c.fecha_inicio', 'm.*')
            ->where('c.estado', 1)
            ->where('mod.estado', 1)
            ->where('m.estado', 1)
            ->where('c.activo', 1)
            ->where('c.id_variedad', $request->variedad)
            ->orderBy('c.fecha_inicio', 'desc')
            ->get();

        dd($ciclos);
        return view('adminlte.gestion.proyecciones.monitoreo.partials.listado', [
            'ciclos' => $ciclos,
            'num_semanas' => $request->num_semanas,
        ]);
    }
}