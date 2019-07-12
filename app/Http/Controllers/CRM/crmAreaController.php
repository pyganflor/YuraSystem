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
        $tallos = $data_ciclos['tallos_cosechados'];

        $data_cosecha = getCosechaByRango($semana_actual->codigo, $semana_actual->codigo, 'T');
        $calibre = $data_cosecha['calibre'];
        $calibre > 0 ? $ramos = round($tallos / $calibre, 2) : $ramos = 0;

        $ciclo_ano = $ciclo > 0 ? round(365 / $ciclo, 2) : 0;

        $semanal = [
            'ciclo_ano' => $ciclo_ano,
            'area' => $area,
            'ciclo' => $ciclo,
            'tallos' => $area_cerrada > 0 ? round($tallos / $area_cerrada, 2) : 0,
            'ramos' => $area_cerrada > 0 ? round($ramos / $area_cerrada, 2) : 0,
            'ramos_anno' => $area_cerrada > 0 ? round($ciclo_ano * round($ramos / $area_cerrada, 2), 2) : 0,
        ];

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
        $tallos = $data_ciclos['tallos_cosechados'];

        $data_cosecha = getCosechaByRango($fechas[0]->semana, $fechas[3]->semana, 'T');
        $calibre = $data_cosecha['calibre'];
        $calibre > 0 ? $ramos = round($tallos / $calibre, 2) : $ramos = 0;

        $ciclo_ano = $area_cerrada > 0 ? round(365 / $ciclo, 2) : 0;

        $mensual = [
            'ciclo_ano' => $ciclo_ano,
            'area' => round($area / count($fechas), 2),
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

        $data = [];
        if ($request->has('annos') && count($request->annos) > 0) {
            $view = '_annos';

            $periodo = 'semanal';

            foreach ($request->annos as $anno) {
                $array_area = [];
                $array_ciclo = [];
                $array_tallos = [];
                $array_ramos = [];
                $array_ramos_anno = [];

                $fechas = DB::table('semana as s')
                    ->select('s.codigo as semana')->distinct()
                    ->where('estado', '=', 1)
                    ->where('anno', '=', $anno)
                    ->orderBy('codigo')
                    ->get();

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
                    $tallos = $data_ciclos['tallos_cosechados'];

                    $data_cosecha = getCosechaByRango($semana->codigo, $semana->codigo, $request->id_variedad);
                    $calibre = $data_cosecha['calibre'];
                    $calibre > 0 ? $ramos = round($tallos / $calibre, 2) : $ramos = 0;

                    $ciclo_ano = $area_cerrada > 0 ? round(365 / $ciclo, 2) : 0;

                    array_push($array_area, round($area / 10000, 2));
                    array_push($array_ciclo, $ciclo);
                    array_push($array_tallos, $area_cerrada > 0 ? round($tallos / $area_cerrada, 2) : 0);
                    array_push($array_ramos, $area_cerrada > 0 ? round($ramos / $area_cerrada, 2) : 0);
                    array_push($array_ramos_anno, $area_cerrada > 0 ? round($ciclo_ano * round($ramos / $area_cerrada, 2), 2) : 0);
                }

                array_push($data, [
                    'anno' => $anno,
                    'area' => $array_area,
                    'ciclo' => $array_ciclo,
                    'tallos' => $array_tallos,
                    'ramos' => $array_ramos,
                    'ramos_anno' => $array_ramos_anno,
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
                    $tallos = $data_ciclos['tallos_cosechados'];

                    $data_cosecha = getCosechaByRango($semana->codigo, $semana->codigo, 'T');
                    $calibre = $data_cosecha['calibre'];
                    $calibre > 0 ? $ramos = round($tallos / $calibre, 2) : $ramos = 0;

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
                    $tallos = $data_ciclos['tallos_cosechados'];

                    $data_cosecha = getCosechaByRango($semana->codigo, $semana->codigo, $request->id_variedad);
                    $calibre = $data_cosecha['calibre'];
                    $calibre > 0 ? $ramos = round($tallos / $calibre, 2) : $ramos = 0;

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
        } else if ($request->option == 'ramos') {
            $data = [];
            foreach (getVariedades() as $variedad) {
                $array_ramos = [];
                foreach ($semanas as $semana) {
                    /* =========== ciclo ========== */
                    $data_ciclos = getCiclosCerradosByRango($semana->codigo, $semana->codigo, $variedad->id_variedad);
                    $area_cerrada = $data_ciclos['area_cerrada'];

                    $data_cosecha = getCosechaByRango($semana->codigo, $semana->codigo, $variedad->id_variedad);
                    $ramos = $data_cosecha['ramos_estandar'];

                    array_push($array_ramos, $area_cerrada > 0 ? round($ramos / $area_cerrada, 2) : 0);
                }
                array_push($data, [
                    'variedad' => $variedad,
                    'valores' => $array_ramos,
                ]);
            }

            return view('adminlte.crm.crm_area.partials._ramos_desglose', [
                'data' => $data,
                'colores_semana' => ['#ADD8E6', '#FFEF92', '#FFC1A6', '#E9ECEF'],
                'semanas' => $semanas,
            ]);
        } else if ($request->option == 'ramos_anno') {
            $data = [];
            foreach (getVariedades() as $variedad) {
                $array_ramos = [];
                foreach ($semanas as $semana) {
                    /* =========== ciclo ========== */
                    $data_ciclos = getCiclosCerradosByRango($semana->codigo, $semana->codigo, $variedad->id_variedad);
                    $ciclo = $data_ciclos['ciclo'];
                    $area_cerrada = $data_ciclos['area_cerrada'];

                    $data_cosecha = getCosechaByRango($semana->codigo, $semana->codigo, $variedad->id_variedad);
                    $ramos = $data_cosecha['ramos_estandar'];

                    $ciclo_ano = $area_cerrada > 0 ? round(365 / $ciclo, 2) : 0;

                    array_push($array_ramos, $area_cerrada > 0 ? round($ciclo_ano * round($ramos / $area_cerrada, 2), 2) : 0);
                }
                array_push($data, [
                    'variedad' => $variedad,
                    'valores' => $array_ramos,
                ]);
            }

            return view('adminlte.crm.crm_area.partials._ramos_desglose', [
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