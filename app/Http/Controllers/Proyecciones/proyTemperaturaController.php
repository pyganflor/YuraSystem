<?php

namespace yura\Http\Controllers\Proyecciones;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Ciclo;
use yura\Modelos\GrupoMenu;
use yura\Modelos\Sector;
use yura\Modelos\Submenu;

class proyTemperaturaController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.proyecciones.temperaturas.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'grupos_menu' => GrupoMenu::All(),
            'sectores' => Sector::All()->where('estado', 1)->where('interno', 1)
        ]);
    }

    public function listar_ciclos(Request $request)
    {
        $query = Ciclo::where('estado', 1)
            ->where('activo', 1)
            ->where('id_variedad', $request->variedad)
            ->orderBy('fecha_inicio', 'desc')
            ->where('poda_siembra', $request->poda_siembra)
            ->get();    // ciclos activos
        dd($query);
        return view('adminlte.gestion.proyecciones.temperaturas.partials.listado', [
            'ciclos' => $query,
            'sector' => $request->sector,
        ]);
    }
}
