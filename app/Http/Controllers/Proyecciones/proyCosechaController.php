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
        $semana_desde = Semana::All()->where('codigo', $request->desde)->first();
        $semana_hasta = Semana::All()->where('codigo', $request->hasta)->first();
        if ($semana_desde != '' && $semana_hasta != '') {
            $fecha_ini = DB::table('ciclo')
                ->select(DB::raw('min(fecha_inicio) as inicio'))->distinct()
                ->where('estado', '=', 1)
                ->where('id_variedad', '=', $request->variedad)
                ->where('fecha_fin', '>=', $semana_desde->fecha_inicial)
                ->get()[0]->inicio;

            if ($fecha_ini != '') {
                $semana_desde = getSemanaByDate($fecha_ini);

                $array_semanas = [];
                for ($i = $semana_desde->codigo; $i <= $request->hasta; $i++) {
                    $semana = Semana::All()->where('estado', 1)->where('codigo', $i)->first();
                    if ($semana != '')
                        if (!in_array($semana->codigo, $array_semanas))
                            array_push($array_semanas, $semana->codigo);
                }
            } else
                return 'No se han encontrado módulos en el rango establecido.';
        } else
            return 'Revise las semanas, están incorrectas.';

        dd($array_semanas, $request->all());
    }
}