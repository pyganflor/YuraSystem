<?php

namespace yura\Http\Controllers\Proyecciones;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\Ciclo;
use yura\Modelos\Modulo;
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

        $modulos = DB::table('ciclo as c')
            ->select('c.id_modulo')->distinct()
            ->where('c.estado', '=', 1)
            ->where('c.id_variedad', '=', $request->variedad)
            ->where('c.fecha_inicio', '>=', getSemanaByDate($request->desde)->fecha_inicial)
            ->get();

        dd($array_semanas);
    }
}