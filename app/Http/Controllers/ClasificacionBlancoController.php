<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Modelos\Submenu;
use yura\Modelos\Variedad;

class ClasificacionBlancoController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.postcocecha.clasificacion_blanco.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'annos' => DB::table('semana as s')
                ->select('s.anno')->distinct()
                ->where('s.estado', '=', 1)->orderBy('s.anno')->get(),
            'variedades' => Variedad::All()->where('estado', '=', 1),
            'unitarias' => getUnitarias(),
        ]);
    }

    public function buscar_stock(Request $request)
    {
        $listado = [];
        if ($request->variedad != '') {
            $listado = DB::table('stock_apertura as a')
                ->join('clasificacion_unitaria as u', 'u.id_clasificacion_unitaria', '=', 'a.id_clasificacion_unitaria')
                ->select('a.*')->distinct()
                ->where('a.disponibilidad', '=', 1);

            if ($request->fecha_desde != '')
                $listado = $listado->where('a.fecha_inicio', '>=', $request->fecha_desde);
            if ($request->fecha_hasta != '')
                $listado = $listado->where('a.fecha_inicio', '<=', $request->fecha_hasta);
            if ($request->variedad != '')
                $listado = $listado->where('a.id_variedad', '=', $request->variedad);
            if ($request->unitaria != '')
                $listado = $listado->where('a.id_clasificacion_unitaria', '=', $request->unitaria);

            $listado = $listado->orderBy('a.fecha_inicio', 'asc')->orderBy('u.nombre', 'asc')
                ->get();
        }

        $datos = [
            'listado' => $listado,
        ];

        return view('adminlte.gestion.postcocecha.aperturas.partials.listado', $datos);
    }
}