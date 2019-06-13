<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\Ciclo;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\Cosecha;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;

class crmAreaController extends Controller
{
    public function inicio(Request $request)
    {
        /* =========== MENSUAL ============= */
        $desde = opDiasFecha('-', 28, date('Y-m-d'));
        $hasta = opDiasFecha('-', 7, date('Y-m-d'));

        $fechas = DB::table('semana as s')
            ->select('s.codigo as semana')->distinct()
            ->Where(function ($q) use ($desde, $hasta) {
                $q->where('s.fecha_inicial', '>=', $desde)
                    ->where('s.fecha_inicial', '<=', $hasta);
            })
            ->orWhere(function ($q) use ($desde, $hasta) {
                $q->where('s.fecha_final', '>=', $desde)
                    ->Where('s.fecha_final', '<=', $hasta);
            })
            ->orderBy('codigo')
            ->get();

        $area = 0;
        $data_4semanas = getAreaCiclosByRango($fechas[0]->semana, $fechas[3]->semana, 'T');

        foreach ($data_4semanas['variedades'] as $var) {
            foreach ($var['ciclos'] as $c) {
                foreach ($c['areas'] as $a) {
                    $area += $a;
                }
            }
        }

        $data_ciclos = getCiclosCerradosByRango($fechas[0]->semana, $fechas[3]->semana, 'T');
        $ciclo = $data_ciclos['ciclo'];
        $area_cerrada = $data_ciclos['area_cerrada'];

        $data_cosecha = getCosechaByRango($fechas[0]->semana, $fechas[3]->semana, 'T');
        $tallos = $data_cosecha['tallos_cosechados'];
        $ramos = $data_cosecha['ramos_estandar'];

        $ciclo_ano = $area_cerrada > 0 ? round(365 / $ciclo, 2) : 0;

        $mensual = [
            'ciclo_ano' => $ciclo_ano,
            'area' => round($area / count($fechas), 2),
            'ciclo' => $ciclo,
            'tallos' => $area_cerrada > 0 ? round($tallos / $area_cerrada, 2) : 0,
            'ramos' => $area_cerrada > 0 ? round($ramos / $area_cerrada, 2) : 0,
            'ramos_anno' => $area_cerrada > 0 ? round($ciclo_ano * round($ramos / $area_cerrada, 2), 2) : 0,
        ];

        /* =========== SEMANAL ============= */
        $semana_actual = getSemanaByDate(opDiasFecha('-', 7, date('Y-m-d')));

        /* ========== area ========== */
        $area = 0;
        $data_semana_acutal = getAreaCiclosByRango($semana_actual->codigo, $semana_actual->codigo, 'T');

        foreach ($data_semana_acutal['variedades'] as $var) {
            foreach ($var['ciclos'] as $c) {
                foreach ($c['areas'] as $a) {
                    $area += $a;
                }
            }
        }

        /* ========== ciclo ========== */
        $data_ciclos = getCiclosCerradosByRango($semana_actual->codigo, $semana_actual->codigo, 'T');
        $ciclo = $data_ciclos['ciclo'];
        $area_cerrada = $data_ciclos['area_cerrada'];

        $data_cosecha = getCosechaByRango($semana_actual->codigo, $semana_actual->codigo, 'T');
        $tallos = $data_cosecha['tallos_cosechados'];
        $ramos = $data_cosecha['ramos_estandar'];

        $ciclo_ano = $ciclo > 0 ? round(365 / $ciclo, 2) : 0;

        $semanal = [
            'ciclo_ano' => $ciclo_ano,
            'area' => $area,
            'ciclo' => $ciclo,
            'tallos' => $area_cerrada > 0 ? round($tallos / $area_cerrada, 2) : 0,
            'ramos' => $area_cerrada > 0 ? round($ramos / $area_cerrada, 2) : 0,
            'ramos_anno' => $area_cerrada > 0 ? round($ciclo_ano * round($ramos / $area_cerrada, 2), 2) : 0,
        ];

        /* ======= AÑOS ======= */
        $annos = DB::table('ciclo')
            ->select(DB::raw('year(fecha_inicio) as anno'))->distinct()
            ->orderBy(DB::raw('year(fecha_inicio)'))
            ->get();

        return view('adminlte.crm.crm_area.inicio', [
            'mensual' => $mensual,
            'semanal' => $semanal,
            'annos' => $annos,
            'semana_actual' => $semana_actual,

            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function filtrar_graficas(Request $request)
    {
        $desde = $request->desde;
        $hasta = $request->hasta;

        $fechas = DB::table('semana as s')
            ->select('s.codigo as semana')->distinct()
            ->Where(function ($q) use ($desde, $hasta) {
                $q->where('s.fecha_inicial', '>=', $desde)
                    ->where('s.fecha_inicial', '<=', $hasta);
            })
            ->orWhere(function ($q) use ($desde, $hasta) {
                $q->where('s.fecha_final', '>=', $desde)
                    ->Where('s.fecha_final', '<=', $hasta);
            })
            ->orderBy('codigo')
            ->get();

        $arreglo_annos = [];
        if ($request->has('annos') && count($request->annos) > 0) {
            $view = '_annos';

            $fechas = [];

            $data = [];
            $periodo = 'mensual';

            foreach ($request->annos as $anno) {
                $arreglo_valores = [];
                $arreglo_fisicas = [];
                $arreglo_cajas = [];
                $arreglo_precios = [];

                foreach (getMeses(TP_NUMERO) as $mes) {
                    $query = DB::table('historico_ventas')
                        ->select(DB::raw('sum(valor) as valor'), DB::raw('sum(cajas_fisicas) as cajas_fisicas'),
                            DB::raw('sum(cajas_equivalentes) as cajas_equivalentes'),
                            DB::raw('sum(precio_x_ramo) as precio_x_ramo'))
                        ->where('anno', '=', $anno)
                        ->where('mes', '=', $mes);
                    $count_query = DB::table('historico_ventas')
                        ->select(DB::raw('count(*) as count'))
                        ->where('anno', '=', $anno)
                        ->where('mes', '=', $mes);

                    if ($request->id_variedad != '') {
                        $query = $query->where('id_variedad', '=', $request->id_variedad);
                        $count_query = $count_query->where('id_variedad', '=', $request->id_variedad);
                    }
                    if ($request->x_modulo == 'true' && $request->id_modulo != '') {
                        $query = $query->where('id_modulo', '=', $request->id_modulo);
                        $count_query = $count_query->where('id_modulo', '=', $request->id_modulo);
                    }
                    $query = $query->get();
                    $count_query = $count_query->get();


                    array_push($arreglo_valores, count($query) > 0 ? round($query[0]->valor, 2) : 0);
                    array_push($arreglo_fisicas, count($query) > 0 ? round($query[0]->cajas_fisicas, 2) : 0);
                    array_push($arreglo_cajas, count($query) > 0 ? round($query[0]->cajas_equivalentes, 2) : 0);
                    array_push($arreglo_precios, (count($query) > 0 && $count_query[0]->count > 0) ? round($query[0]->precio_x_ramo / $count_query[0]->count, 2) : 0);
                }
                array_push($arreglo_annos, [
                    'anno' => $anno,
                    'valores' => $arreglo_valores,
                    'fisicas' => $arreglo_fisicas,
                    'equivalentes' => $arreglo_cajas,
                    'precios' => $arreglo_precios,
                ]);
            }
        } else {
            $view = '_graficas';

            $periodo = 'semanal';

            $array_area = [];
            $array_ciclo = [];
            $array_tallos = [];
            $array_ramos = [];
            $array_ramos_anno = [];
            if ($request->id_variedad == 'T') {
                foreach ($fechas as $semana) {
                    $semana = Semana::All()->where('codigo', $semana->semana)->first();
                    /* =========== area ========== */
                    $area = 0;
                    $data_area = getAreaCiclosByRango($semana->codigo, $semana->codigo, 'T');

                    foreach ($data_area['variedades'] as $var) {
                        foreach ($var['ciclos'] as $c) {
                            foreach ($c['areas'] as $a) {
                                $area += $a;
                            }
                        }
                    }

                    /* =========== ciclo ========== */
                    $data_ciclos = getCiclosCerradosByRango($semana->codigo, $semana->codigo, 'T');
                    $ciclo = $data_ciclos['ciclo'];
                    $area_cerrada = $data_ciclos['area_cerrada'];

                    $data_cosecha = getCosechaByRango($semana->codigo, $semana->codigo, 'T');
                    $tallos = $data_cosecha['tallos_cosechados'];
                    $ramos = $data_cosecha['ramos_estandar'];

                    $ciclo_ano = $area_cerrada > 0 ? round(365 / $ciclo, 2) : 0;

                    array_push($array_area, round($area / 10000, 2));
                    array_push($array_ciclo, $ciclo);
                    array_push($array_tallos, $area_cerrada > 0 ? round($tallos / $area_cerrada, 2) : 0);
                    array_push($array_ramos, $area_cerrada > 0 ? round($ramos / $area_cerrada, 2) : 0);
                    array_push($array_ramos_anno, $area_cerrada > 0 ? round($ciclo_ano * round($ramos / $area_cerrada, 2), 2) : 0);
                }
            } else {
                foreach ($fechas as $semana) {
                    $semana = Semana::All()->where('codigo', $semana->semana)->first();
                    /* =========== area ========== */
                    $area = 0;
                    $data_area = getAreaCiclosByRango($semana->codigo, $semana->codigo, $request->id_variedad);

                    foreach ($data_area['variedades'] as $var) {
                        foreach ($var['ciclos'] as $c) {
                            foreach ($c['areas'] as $a) {
                                $area += $a;
                            }
                        }
                    }

                    /* =========== ciclo ========== */
                    $data_ciclos = getCiclosCerradosByRango($semana->codigo, $semana->codigo, $request->id_variedad);
                    $ciclo = $data_ciclos['ciclo'];
                    $area_cerrada = $data_ciclos['area_cerrada'];

                    $data_cosecha = getCosechaByRango($semana->codigo, $semana->codigo, $request->id_variedad);
                    $tallos = $data_cosecha['tallos_cosechados'];
                    $ramos = $data_cosecha['ramos_estandar'];

                    $ciclo_ano = $area_cerrada > 0 ? round(365 / $ciclo, 2) : 0;

                    array_push($array_area, round($area / 10000, 2));
                    array_push($array_ciclo, $ciclo);
                    array_push($array_tallos, $area_cerrada > 0 ? round($tallos / $area_cerrada, 2) : 0);
                    array_push($array_ramos, $area_cerrada > 0 ? round($ramos / $area_cerrada, 2) : 0);
                    array_push($array_ramos_anno, $area_cerrada > 0 ? round($ciclo_ano * round($ramos / $area_cerrada, 2), 2) : 0);
                }
            }

            $data = [
                'area' => $array_area,
                'ciclo' => $array_ciclo,
                'tallos' => $array_tallos,
                'ramos' => $array_ramos,
                'ramos_anno' => $array_ramos_anno,
            ];
        }

        return view('adminlte.crm.crm_area.partials.' . $view, [
            'labels' => $fechas,
            'arreglo_annos' => $arreglo_annos,
            'data' => $data,
            'periodo' => $periodo,
        ]);
    }

    public function desglose_indicador(Request $request)
    {
        $semana_ini = getSemanaByDate(opDiasFecha('-', 28, date('Y-m-d')));
        $semana_fin = getSemanaByDate(opDiasFecha('-', 7, date('Y-m-d')));

        $query = DB::table('semana as s')
            ->select('s.codigo')->distinct()
            ->Where(function ($q) use ($semana_ini, $semana_fin) {
                $q->where('s.fecha_inicial', '>=', $semana_ini->fecha_inicial)
                    ->where('s.fecha_inicial', '<=', $semana_fin->fecha_final);
            })
            ->orWhere(function ($q) use ($semana_ini, $semana_fin) {
                $q->where('s.fecha_final', '>=', $semana_ini->fecha_inicial)
                    ->Where('s.fecha_final', '<=', $semana_fin->fecha_final);
            })
            ->orderBy('codigo')
            ->get();
        $semanas = [];
        foreach ($query as $s)
            array_push($semanas, Semana::All()->where('codigo', $s->codigo)->first());

        if ($request->option == 'area') {
            $data = getAreaCiclosByRango($semana_ini->codigo, $semana_fin->codigo, 'T');
            return view('adminlte.crm.regalias_semanas.partials.listado', [
                'variedades' => $data['variedades'],
                'semanas' => $data['semanas']
            ]);
        } else if ($request->option == 'ciclo') {
            $data = getCiclosCerradosByRangoVariedades($semana_ini->codigo, $semana_fin->codigo);
            return view('adminlte.crm.crm_area.partials._ciclo_desglose', [
                'data' => $data,
                'colores_semana' => ['#ADD8E6', '#FFEF92', '#FFC1A6', '#E9ECEF'],
                'semanas' => $semanas,
            ]);
        } else if ($request->option == 'tallos') {
            $data = [];
            foreach (getVariedades() as $variedad) {
                $array_tallos = [];
                $array_ciclos = [];
                foreach ($semanas as $semana) {
                    /* =========== ciclo ========== */
                    $data_ciclos = getCiclosCerradosByRango($semana->codigo, $semana->codigo, $variedad->id_variedad);
                    $ciclos = $data_ciclos['ciclos'];
                    $ciclo = $data_ciclos['ciclo'];
                    $area_cerrada = $data_ciclos['area_cerrada'];

                    $tallos = 0;

                    array_push($array_tallos, $area_cerrada > 0 ? round($tallos / $area_cerrada, 2) : 0);
                    array_push($array_ciclos, $ciclos);
                }
                array_push($data, [
                    'variedad' => $variedad,
                    'tallos' => $array_tallos,
                    'ciclos' => $array_ciclos,
                ]);
            }

            return view('adminlte.crm.crm_area.partials._tallos_desglose', [
                'data' => $data,
                'colores_semana' => ['#ADD8E6', '#FFEF92', '#FFC1A6', '#E9ECEF'],
                'semanas' => $semanas,
            ]);
        }
    }

    /* ===================== REGALIAS SEMANAS ===================== */
    public function regalias_semanas(Request $request)
    {
        return view('adminlte.crm.regalias_semanas.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function buscar_listado(Request $request)
    {
        $data = getAreaCiclosByRango($request->desde, $request->hasta, $request->variedad);
        return view('adminlte.crm.regalias_semanas.partials.listado', [
            'variedades' => $data['variedades'],
            'semanas' => $data['semanas']
        ]);
    }
}