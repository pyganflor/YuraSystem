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
                $semanas = [];
                for ($i = $semana_desde->codigo; $i <= $request->hasta; $i++) {
                    $semana = Semana::All()
                        ->where('estado', 1)
                        ->where('id_variedad', '=', $request->variedad)
                        ->where('codigo', $i)->first();
                    if ($semana != '')
                        if (!in_array($semana->codigo, $array_semanas)) {
                            array_push($array_semanas, $semana->codigo);
                            array_push($semanas, $semana);
                        }
                }

                $query_modulos = DB::table('ciclo')
                    ->select('id_modulo')->distinct()
                    ->where('estado', '=', 1)
                    ->where('id_variedad', '=', $request->variedad)
                    ->where('fecha_fin', '>=', $semana_desde->fecha_inicial)
                    ->get();

                $array_modulos = [];
                foreach ($query_modulos as $mod) {
                    $mod = getModuloById($mod->id_modulo);
                    $array_valores = [];
                    foreach ($semanas as $sem) {
                        if ($sem->codigo < getSemanaByDate(date('Y-m-d'))->codigo) {    // semana pasada
                            $data = $mod->getDataBySemana(-1, $sem, $request->variedad);
                            $valor = [
                                'tiempo' => -1,
                                'data' => $data,
                            ];
                        } else if ($sem->codigo == getSemanaByDate(date('Y-m-d'))->codigo) {    // semana actual
                            $data = $mod->getDataBySemana(0, $sem, $request->variedad);
                            $valor = [
                                'tiempo' => 0,
                                'data' => $data,
                            ];
                        } else {    // semana posterior
                            $data = $mod->getDataBySemana(1, $sem, $request->variedad);
                            $valor = [
                                'tiempo' => 1,
                                'data' => $data,
                            ];
                        }
                        array_push($array_valores, $valor);
                    }
                    array_push($array_modulos, [
                        'modulo' => $mod,
                        'valores' => $array_valores
                    ]);
                }

                return view('adminlte.gestion.proyecciones.cosecha.partials.listado', [
                    'semanas' => $semanas,
                    'modulos' => $array_modulos,
                ]);
            } else
                return 'No se han encontrado módulos en el rango establecido.';
        } else
            return 'Revise las semanas, están incorrectas.';
    }
}