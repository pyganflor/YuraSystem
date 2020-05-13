<?php

namespace yura\Http\Controllers\Proyecciones;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $sem_desde = getSemanaByDate(opDiasFecha('-', 70, date('Y-m-d')));
        $sem_pasada = getSemanaByDate(opDiasFecha('-', 7, date('Y-m-d')));
        $query = Ciclo::where('estado', 1)
            ->where('activo', 0)
            ->where('id_variedad', $request->variedad)
            ->where('poda_siembra', $request->poda_siembra)
            ->where('fecha_fin', '>=', $sem_desde->fecha_inicial)
            ->where('fecha_fin', '<=', $sem_pasada->fecha_final)
            ->orderBy('semana_poda_siembra')
            ->get();
        $ciclos = [];
        $max_dia = 0;
        $min_temp = count($query) > 0 ? $query[0]->getTemperaturaByFecha($query[0]->fecha_cosecha) : 0;     // **
        $max_temp = 0;
        $temp_prom = 0;
        foreach ($query as $item) {
            $sem_curva = getSemanaByDate(opDiasFecha('+', ($item->semana_poda_siembra * 7), $item->fecha_inicio));
            if ($sem_curva->codigo >= $sem_desde->codigo && $sem_curva->codigo <= $sem_pasada->codigo) {
                $cosechas = DB::table('proyeccion_modulo_semana')
                    ->where('estado', 1)
                    ->where('tabla', 'C')
                    ->where('modelo', $item->id_ciclo)
                    ->where('cosechados', '>', 0)
                    ->where('semana', '>=', getSemanaByDate(opDiasFecha('-', 21, $sem_curva->fecha_inicial))->codigo)
                    ->where('tipo', 'T')
                    ->get();
                if (count(explode('-', $item->curva)) == count($cosechas)) {
                    $total_cosechado = DB::table('proyeccion_modulo_semana')
                        ->select(DB::raw('sum(cosechados) as cant'))
                        ->where('estado', 1)
                        ->where('tabla', 'C')
                        ->where('modelo', $item->id_ciclo)
                        ->where('cosechados', '>', 0)
                        ->where('semana', '>=', getSemanaByDate(opDiasFecha('-', 21, $sem_curva->fecha_inicial))->codigo)
                        ->where('tipo', 'T')
                        ->get()[0]->cant;
                    $acumulado = $item->fecha_cosecha != '' ? $item->getTemperaturaByFecha($item->fecha_cosecha) : 0;
                    array_push($ciclos, [
                        'ciclo' => $item,
                        'cosechas' => $cosechas,
                        'total_cosechado' => $total_cosechado,
                        'acumulado' => $acumulado,
                    ]);
                    if (count(explode('-', $item->curva)) > $max_dia)
                        $max_dia = count(explode('-', $item->curva));
                    if ($min_temp > $acumulado)
                        $min_temp = $acumulado;
                    if ($max_temp < $acumulado)
                        $max_temp = $acumulado;
                    $temp_prom += $acumulado;
                }
            }
        }
        $temp_prom = count($ciclos) > 0 ? round($temp_prom / count($ciclos), 2) : 0;

        return view('adminlte.gestion.proyecciones.curva_estandar.partials.listado', [
            'ciclos' => $ciclos,
            'max_dia' => $max_dia,
            'min_temp' => $min_temp,
            'max_temp' => $max_temp,
            'temp_prom' => $temp_prom,
        ]);
    }
}