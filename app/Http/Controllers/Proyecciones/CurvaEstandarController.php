<?php

namespace yura\Http\Controllers\Proyecciones;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Ciclo;
use yura\Modelos\GrupoMenu;
use yura\Modelos\Submenu;

class CurvaEstandarController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.proyecciones.curva_estandar.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'grupos_menu' => GrupoMenu::All(),
        ]);
    }

    public function listar_ciclos(Request $request)
    {
        $sem_desde = getSemanaByDate(opDiasFecha('-', 42, date('Y-m-d')));
        $sem_pasada = getSemanaByDate(opDiasFecha('-', 7, date('Y-m-d')));
        $query = Ciclo::All()
            ->where('estado', 1)
            ->where('id_variedad', $request->variedad)
            //->where('poda_siembra', $request->poda_siembra)
            ->where('fecha_inicio', '<=', $sem_desde->fecha_inicial)
            ->where('fecha_fin', '>=', $sem_desde->fecha_inicial)
            ->sortByDesc('fecha_inicio');
        $ciclos = [];
        foreach ($query as $item) {
            $sem_curva = getSemanaByDate(opDiasFecha('+', ($item->semana_poda_siembra * 7), $item->fecha_inicio));
            if ($item->modulo == '90A')
                dd($sem_curva->codigo);
            if ($sem_curva->codigo >= $sem_desde->codigo && $sem_curva->codigo <= $sem_pasada->codigo) {
                array_push($ciclos, $item);
            }
        }
        return view('adminlte.gestion.proyecciones.curva_estandar.partials.listado', [
            'ciclos' => $ciclos
        ]);
    }
}
