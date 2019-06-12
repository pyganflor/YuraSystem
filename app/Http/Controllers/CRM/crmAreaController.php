<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\Ciclo;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\Cosecha;
use yura\Modelos\Semana;

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

        $data_semana_acutal = getAreaCiclosByRango($fechas[0]->semana, $fechas[3]->semana, 'T');

        foreach ($data_semana_acutal['variedades'] as $var) {
            foreach ($var['ciclos'] as $c) {
                foreach ($c['areas'] as $a) {
                    $area += $a;
                }
            }
        }


        $data_ciclos = getCiclosCerradosByRango($fechas[0]->semana, $fechas[3]->semana, 'T');
        $ciclo = $data_ciclos['ciclo'];
        $area_cerrada = $data_ciclos['area_cerrada'];
        $tallos = 0;
        $ramos = 0;
        foreach ($fechas as $codigo) {
            $semana = Semana::All()->where('codigo', '=', $codigo->semana)->first();

            $labels = DB::table('clasificacion_verde as v')
                ->select('v.fecha_ingreso as dia')->distinct()
                ->where('v.fecha_ingreso', '>=', $semana->fecha_inicial)
                ->where('v.fecha_ingreso', '<=', $semana->fecha_final)
                ->get();

            foreach ($labels as $dia) {
                $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                $cosecha = Cosecha::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                if ($verde != '') {
                    $ramos += $verde->getTotalRamosEstandar();
                    $tallos += $cosecha->getTotalTallos();
                }
            }
        }
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
        $ciclos_fin = Ciclo::All()
            ->where('estado', 1)
            ->where('activo', 0)
            ->where('fecha_fin', '>=', $semana_actual->fecha_inicial)
            ->where('fecha_fin', '<=', $semana_actual->fecha_final);

        $ciclo = 0;
        $area_cerrada = 0;
        foreach ($ciclos_fin as $c) {
            $area_cerrada += $c->area;
            $fin = date('Y-m-d');
            if ($c->fecha_fin != '')
                $fin = $c->fecha_fin;
            $ciclo += difFechas($fin, $c->fecha_inicio)->days;
        }
        $ciclo = count($ciclos_fin) > 0 ? round($ciclo / count($ciclos_fin), 2) : 0;

        $labels = DB::table('clasificacion_verde as v')
            ->select('v.fecha_ingreso as dia')->distinct()
            ->where('v.fecha_ingreso', '>=', opDiasFecha('-', 14, date('Y-m-d')))
            ->where('v.fecha_ingreso', '<=', opDiasFecha('-', 7, date('Y-m-d')))
            ->get();
        $tallos = 0;
        $ramos = 0;
        foreach ($labels as $dia) {
            $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
            $cosecha = Cosecha::All()->where('fecha_ingreso', '=', $dia->dia)->first();
            if ($verde != '') {
                $ramos += $verde->getTotalRamosEstandar();
                $tallos += $cosecha->getTotalTallos();
            }
        }

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
        ]);
    }

    public function filtrar_graficas(Request $request)
    {
        $desde = $request->desde;
        $hasta = $request->hasta;

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
            if ($request->total == 'true' && $request->id_modulo == '' && $request->id_variedad == '') {
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

                foreach ($fechas as $codigo) {
                    $semana = Semana::All()->where('codigo', '=', $codigo->semana)->first();

                    /* ========== area ========== */
                    $area = 0;

                    $ciclos_area = DB::table('ciclo')
                        ->select('id_ciclo as id')->distinct()
                        ->where('estado', '=', 1)
                        ->Where(function ($q) use ($semana) {
                            $q->where('fecha_fin', '>=', $semana->fecha_inicial)
                                ->where('fecha_fin', '<=', $semana->fecha_final);
                        })
                        ->orWhere(function ($q) use ($semana) {
                            $q->where('fecha_inicio', '>=', $semana->fecha_inicial)
                                ->where('fecha_inicio', '<=', $semana->fecha_final);
                        })
                        ->orWhere(function ($q) use ($semana) {
                            $q->where('fecha_inicio', '<', $semana->fecha_inicial)
                                ->where('fecha_fin', '>', $semana->fecha_final);
                        })
                        ->get();

                    foreach ($ciclos_area as $item) {
                        $item = Ciclo::find($item->id);
                        $area += $item->area;
                    }

                    /* ========== ciclo ========== */
                    $ciclos_fin = Ciclo::All()
                        ->where('estado', 1)
                        ->where('activo', 0)
                        ->where('fecha_fin', '>=', $semana->fecha_inicial)
                        ->where('fecha_fin', '<=', $semana->fecha_final);

                    $ciclo = 0;
                    $area_cerrada = 0;
                    foreach ($ciclos_fin as $c) {
                        $area_cerrada += $c->area;
                        $fin = date('Y-m-d');
                        if ($c->fecha_fin != '')
                            $fin = $c->fecha_fin;
                        $ciclo += difFechas($fin, $c->fecha_inicio)->days;
                    }
                    $ciclo = count($ciclos_fin) > 0 ? round($ciclo / count($ciclos_fin), 2) : 0;

                    $labels = DB::table('clasificacion_verde as v')
                        ->select('v.fecha_ingreso as dia')->distinct()
                        ->where('v.fecha_ingreso', '>=', opDiasFecha('-', 14, date('Y-m-d')))
                        ->where('v.fecha_ingreso', '<=', opDiasFecha('-', 7, date('Y-m-d')))
                        ->get();
                    $tallos = 0;
                    $ramos = 0;
                    foreach ($labels as $dia) {
                        $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                        $cosecha = Cosecha::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                        if ($verde != '') {
                            $ramos += $verde->getTotalRamosEstandar();
                            $tallos += $cosecha->getTotalTallos();
                        }
                    }

                    $ciclo_ano = $ciclo > 0 ? round(365 / $ciclo, 2) : 0;

                    array_push($array_area, $area);
                    array_push($array_ciclo, $ciclo);
                    array_push($array_tallos, $area_cerrada > 0 ? round($tallos / $area_cerrada, 2) : 0);
                    array_push($array_ramos, $area_cerrada > 0 ? round($ramos / $area_cerrada, 2) : 0);
                    array_push($array_ramos_anno, $area_cerrada > 0 ? round($ciclo_ano * round($ramos / $area_cerrada, 2), 2) : 0);
                }
            } else {
                $semanas_ciclo = DB::table('semana as s')
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

                $fechas = [];

                foreach ($semanas_ciclo as $codigo) {
                    $semana = Semana::All()->where('codigo', '=', $codigo->semana)->first();

                    /* ========== area ========== */
                    $area = 0;

                    $ciclos_area = DB::table('ciclo')
                        ->select('id_ciclo as id')->distinct()
                        ->where('estado', '=', 1)
                        ->Where(function ($q) use ($semana) {
                            $q->where('fecha_fin', '>=', $semana->fecha_inicial)
                                ->where('fecha_fin', '<=', $semana->fecha_final);
                        })
                        ->orWhere(function ($q) use ($semana) {
                            $q->where('fecha_inicio', '>=', $semana->fecha_inicial)
                                ->where('fecha_inicio', '<=', $semana->fecha_final);
                        })
                        ->orWhere(function ($q) use ($semana) {
                            $q->where('fecha_inicio', '<', $semana->fecha_inicial)
                                ->where('fecha_fin', '>', $semana->fecha_final);
                        })
                        ->get();

                    foreach ($ciclos_area as $item) {
                        $item = Ciclo::find($item->id);
                        $area += $item->area;
                    }

                    /* ========== ciclo ========== */
                    $ciclos_fin = Ciclo::All()
                        ->where('estado', 1)
                        ->where('activo', 0)
                        ->where('fecha_fin', '>=', $semana->fecha_inicial)
                        ->where('fecha_fin', '<=', $semana->fecha_final);
                    if ($request->id_modulo != '')
                        $ciclos_fin = $ciclos_fin->where('id_modulo', $request->id_modulo);
                    if ($request->id_variedad != '')
                        $ciclos_fin = $ciclos_fin->where('id_variedad', $request->id_variedad);

                    if (count($ciclos_fin) > 0) {
                        array_push($fechas, $codigo);

                        $ciclo = 0;
                        $area_cerrada = 0;
                        foreach ($ciclos_fin as $pos => $c) {
                            $area_cerrada += $c->area;
                            $fin = date('Y-m-d');
                            if ($c->fecha_fin != '')
                                $fin = $c->fecha_fin;
                            $ciclo += difFechas($fin, $c->fecha_inicio)->days;
                        }
                        $ciclo = count($ciclos_fin) > 0 ? round($ciclo / count($ciclos_fin), 2) : 0;

                        $labels = DB::table('clasificacion_verde as v')
                            ->select('v.fecha_ingreso as dia')->distinct()
                            ->where('v.fecha_ingreso', '>=', opDiasFecha('-', 14, date('Y-m-d')))
                            ->where('v.fecha_ingreso', '<=', opDiasFecha('-', 7, date('Y-m-d')))
                            ->get();
                        $tallos = 0;
                        $ramos = 0;
                        foreach ($labels as $dia) {
                            $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                            $cosecha = Cosecha::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                            if ($verde != '') {
                                if ($request->id_variedad != '') {
                                    $ramos += $verde->getTotalRamosEstandarByVariedad($request->id_variedad);
                                    if ($request->id_modulo != '')
                                        $tallos += $cosecha->getTotalTallosByModuloVariedad($request->id_modulo, $request->id_variedad);
                                    else
                                        $tallos += $cosecha->getTotalTallosByVariedad($request->id_variedad);
                                } else if ($request->id_modulo != '') {
                                    $ramos += $verde->getTotalRamosEstandar();
                                    $tallos += $cosecha->getTotalTallosByModulo($request->id_modulo);
                                } else {
                                    $ramos += $verde->getTotalRamosEstandar();
                                    $tallos += $cosecha->getTotalTallos();
                                }
                            }
                        }

                        $ciclo_ano = $ciclo > 0 ? round(365 / $ciclo, 2) : 0;

                        array_push($array_area, $area);
                        array_push($array_ciclo, $ciclo);
                        array_push($array_tallos, $area_cerrada > 0 ? round($tallos / $area_cerrada, 2) : 0);
                        array_push($array_ramos, $area_cerrada > 0 ? round($ramos / $area_cerrada, 2) : 0);
                        array_push($array_ramos_anno, $area_cerrada > 0 ? round($ciclo_ano * round($ramos / $area_cerrada, 2), 2) : 0);
                    }
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

    /* ===================== REGALIAS SEMANAS ===================== */
    public function regalias_semanas(Request $request)
    {
        return view('adminlte.crm.regalias_semanas.inicio');
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
