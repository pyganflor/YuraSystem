<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\Cosecha;
use yura\Modelos\Semana;

class crmPostocechaController extends Controller
{
    public function inicio(Request $request)
    {
        $labels = DB::table('clasificacion_verde as v')
            ->select('v.fecha_ingreso as dia')->distinct()
            ->where('v.fecha_ingreso', '>=', opDiasFecha('-', 7, date('Y-m-d')))
            ->where('v.fecha_ingreso', '<=', opDiasFecha('-', 1, date('Y-m-d')))
            ->get();

        $cajas = 0;
        $ramos = 0;
        $tallos = 0;
        $calibre = 0;

        $cant_verde = 0;
        foreach ($labels as $dia) {
            $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
            if ($verde != '') {
                $cajas += round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2);
                $ramos += $verde->getTotalRamosEstandar();
                $tallos += $verde->total_tallos();
                $calibre += round($verde->total_tallos() / $verde->getTotalRamosEstandar(), 2);
                $cant_verde++;
            }
        }

        $calibre = $cant_verde > 0 ? round($calibre / $cant_verde, 2) : 0;

        $indicadores = [
            'cajas' => $cajas,
            'ramos' => $ramos,
            'tallos' => $tallos,
            'calibre' => $calibre,
        ];

        $cosecha = Cosecha::All()->where('fecha_ingreso', '=', date('Y-m-d'))->first();
        $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', date('Y-m-d'))->first();

        return view('adminlte.crm.postcocecha.inicio', [
            'desde' => opDiasFecha('-', 7, date('Y-m-d')),
            'hasta' => opDiasFecha('-', 1, date('Y-m-d')),
            'cosecha' => $cosecha,
            'verde' => $verde,
            'indicadores' => $indicadores,
            'cant_verde' => $cant_verde,
        ]);
    }

    public function cargar_cosecha(Request $request)
    {
        $labels = DB::table('clasificacion_verde as v')
            ->select('v.fecha_ingreso as dia')->distinct()
            ->where('v.fecha_ingreso', '>=', opDiasFecha('-', 30, date('Y-m-d')))
            ->where('v.fecha_ingreso', '<=', date('Y-m-d'))
            ->get();

        $array_cajas = [];
        $array_ramos = [];
        $array_tallos = [];
        $array_calibre = [];
        foreach ($labels as $dia) {
            $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
            if ($verde != '') {
                array_push($array_cajas, round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2));
                array_push($array_ramos, $verde->getTotalRamosEstandar());
                array_push($array_tallos, $verde->total_tallos());
                array_push($array_calibre, round($verde->total_tallos() / $verde->getTotalRamosEstandar(), 2));
            }
        }

        $cosecha = Cosecha::All()->where('fecha_ingreso', '=', date('Y-m-d'))->first();
        $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', date('Y-m-d'))->first();
        $listado_variedades = [];
        if ($cosecha != '') {
            $listado_variedades = $cosecha->getVariedades();
        }

        $dias_atras = 1;
        $last_verde = ClasificacionVerde::All()
            ->where('fecha_ingreso', '=', opDiasFecha('-', $dias_atras, date('Y-m-d')))->first();
        while ($last_verde == '' && $dias_atras <= 7) {
            $dias_atras++;
            $last_verde = ClasificacionVerde::All()
                ->where('fecha_ingreso', '=', opDiasFecha('-', $dias_atras, date('Y-m-d')))->first();
        }
        $porcentaje = 0;
        if ($last_verde != '' && $verde != '')
            $porcentaje = 100 - porcentaje($verde->getCalibre(), $last_verde->getCalibre(), 1);

        $annos = DB::table('clasificacion_verde as v')
            ->select(DB::raw('year(v.fecha_ingreso) as anno'))->distinct()
            ->orderBy(DB::raw('year(v.fecha_ingreso)'))
            ->get();

        return view('adminlte.crm.postcocecha.partials.cosecha', [
            'cosecha' => $cosecha,
            'verde' => $verde,
            'porcent' => $porcentaje,
            'last_verde' => $last_verde,
            'listado_variedades' => $listado_variedades,
            'periodo' => 'diario',
            'labels' => $labels,
            'array_cajas' => $array_cajas,
            'array_ramos' => $array_ramos,
            'array_tallos' => $array_tallos,
            'array_calibre' => $array_calibre,
            'annos' => $annos,
        ]);
    }

    public function buscar_reporte_cosecha_indicadores(Request $request)
    {
        $desde = '1990-01-01';
        if ($request->desde != '')
            $desde = $request->desde;
        $hasta = date('Y-m-d');
        if ($request->hasta != '')
            $hasta = $request->hasta;

        $target = '';
        $view = '';

        $select = 'v.fecha_ingreso as dia';

        $labels = DB::table('clasificacion_verde as v')
            ->select($select)->distinct()
            ->where('v.fecha_ingreso', '>=', $desde)
            ->where('v.fecha_ingreso', '<=', $hasta)
            ->get();

        if ($request->x_variedad == 'true') {
            if ($request->id_variedad != '') {
                $target = getVariedad($request->id_variedad);
                $view = 'x_variedad';
            } else {
                return '<div class="alert alert-warning text-center">Indique la variedad por la que desea filtrar</div>';
            }
        }
        if ($request->total == 'true') {
            $target = getVariedades();
            $view = 'todas_variedades';
        }
        if ($request->total == 'false' && $request->x_variedad == 'false') {
            $view = 'acumulado';
        }

        /* ================ OBTENER RESULTADOS =============*/
        $cajas = 0;
        $ramos = 0;
        $tallos = 0;
        $calibre = 0;
        $arreglo_variedades = [];
        if ($view == 'acumulado') {
            $cant_verde = 0;
            foreach ($labels as $dia) {
                $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                if ($verde != '') {
                    $cajas += round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2);
                    $ramos += $verde->getTotalRamosEstandar();
                    $tallos += $verde->total_tallos();
                    $calibre += round($verde->total_tallos() / $verde->getTotalRamosEstandar(), 2);
                    $cant_verde++;
                }
            }

            $calibre = $cant_verde > 0 ? round($calibre / $cant_verde, 2) : 0;
        }
        if ($view == 'x_variedad') {
            $cant_verde = 0;
            foreach ($labels as $dia) {
                $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                if ($verde != '') {
                    $cajas += round($verde->getTotalRamosEstandarByVariedad($target->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                    $ramos += $verde->getTotalRamosEstandarByVariedad($target->id_variedad);
                    $tallos += $verde->tallos_x_variedad($target->id_variedad);
                    $calibre += $verde->calibreByVariedad($target->id_variedad);
                    $cant_verde++;
                }
            }

            $calibre = $cant_verde > 0 ? round($calibre / $cant_verde, 2) : 0;
        }
        if ($view == 'todas_variedades') {
            foreach ($target as $variedad) {
                $cant_verde = 0;
                $cajas = 0;
                $ramos = 0;
                $tallos = 0;
                $cosecha = 0;
                $calibre = 0;
                foreach ($labels as $dia) {
                    $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                    if ($verde != '') {
                        $cajas += round($verde->getTotalRamosEstandarByVariedad($variedad->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                        $ramos += $verde->getTotalRamosEstandarByVariedad($variedad->id_variedad);
                        $tallos += $verde->tallos_x_variedad($variedad->id_variedad);
                        $cosecha += $verde->total_tallos_recepcionByVariedad($variedad->id_variedad);
                        $calibre += $verde->calibreByVariedad($variedad->id_variedad);
                        $cant_verde++;
                    }
                }

                $calibre = $cant_verde > 0 ? round($calibre / $cant_verde, 2) : 0;

                array_push($arreglo_variedades, [
                    'variedad' => $variedad,
                    'cajas' => $cajas,
                    'ramos' => $ramos,
                    'tallos' => $tallos,
                    'cosecha' => $cosecha,
                    'calibre' => $calibre,
                ]);
            }
        }

        return view('adminlte.crm.postcocecha.partials.secciones.indicadores._' . $view, [
            'desde' => $desde,
            'hasta' => $hasta,
            'labels' => $labels,
            'target' => $target,
            'cajas' => $cajas,
            'ramos' => $ramos,
            'tallos' => $tallos,
            'calibre' => $calibre,
            'arreglo_variedades' => $arreglo_variedades,
        ]);

    }

    public function buscar_reporte_cosecha_comparacion(Request $request)
    {
        $desde = '1990-01-01';
        if ($request->desde != '')
            $desde = $request->desde;
        $hasta = date('Y-m-d');
        if ($request->hasta != '')
            $hasta = $request->hasta;

        $listado_variedades = [];
        foreach (getVariedades() as $variedad) {
            $labels = DB::table('cosecha as c')
                ->select('c.id_cosecha', 'c.fecha_ingreso')
                ->where('c.fecha_ingreso', '>=', $desde)
                ->where('c.fecha_ingreso', '<=', $hasta)
                ->get();

            $cosecha = 0;
            $clasificacion = 0;
            foreach ($labels as $item) {
                $verde = ClasificacionVerde::where('fecha_ingreso', $item->fecha_ingreso)->first();
                $cosecha += Cosecha::find($item->id_cosecha)->getTotalTallosByVariedad($variedad->id_variedad);
                $clasificacion += $verde != '' ? $verde->tallos_x_variedad($variedad->id_variedad) : 0;    // posible optimizacion relacionada con la fecha de trabajo en vez de la hora de la clasificacion verde
            }
            array_push($listado_variedades, [
                'variedad' => $variedad,
                'cosecha' => $cosecha,
                'clasificacion' => $clasificacion,
            ]);
        }

        return view('adminlte.crm.postcocecha.partials.secciones.comparacion._inicio', [
            'desde' => $desde,
            'hasta' => $hasta,
            'listado_variedades' => $listado_variedades,
        ]);
    }

    public function buscar_reporte_cosecha_chart(Request $request)
    {
        $desde = '1990-01-01';
        if ($request->desde != '')
            $desde = $request->desde;
        $hasta = date('Y-m-d');
        if ($request->hasta != '')
            $hasta = $request->hasta;

        $target = '';
        $view = '';

        if ($request->semanal == 'true') {  // semanal
            $labels = DB::table('semana as s')
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
            $periodo = 'semanal';
        } else {
            if ($request->anual == 'true') {    // anual
                $select = DB::raw('Year(v.fecha_ingreso) as ano');
                $periodo = 'anual';
            } else if ($request->mensual == 'true') {   // mensual
                $select = [DB::raw('Year(v.fecha_ingreso) as ano'), DB::raw('Month(v.fecha_ingreso) as mes')];
                $periodo = 'mensual';
            } else if ($request->diario == 'true') {    // diario
                $select = 'v.fecha_ingreso as dia';
                $periodo = 'diario';
            }

            $labels = DB::table('clasificacion_verde as v')
                ->select($select)->distinct()
                ->where('v.fecha_ingreso', '>=', $desde)
                ->where('v.fecha_ingreso', '<=', $hasta)
                ->orderBy('fecha_ingreso')
                ->get();
        }
        $annos = [];

        $array_cajas = [];
        $array_ramos = [];
        $array_tallos = [];
        $array_calibre = [];

        if ($request->x_variedad == 'true') {
            if ($request->id_variedad != '') {
                $target = getVariedad($request->id_variedad);
                $view = 'x_variedad';
            } else {
                return '<div class="alert alert-warning text-center">Indique la variedad por la que desea filtrar</div>';
            }
        }
        if ($request->total == 'true') {
            $target = getVariedades();
            $view = 'todas_variedades';
        }
        if ($request->total == 'false' && $request->x_variedad == 'false') {
            $view = 'acumulado';
        }


        if ($request->has('annos')) {
            $view = 'annos';
            $labels = [];
            foreach ($request->annos as $a) {
                $fechas = DB::table('clasificacion_verde as v')
                    ->select('v.fecha_ingreso as dia')->distinct()
                    ->where('v.fecha_ingreso', '>=', $a . '-01-01')
                    ->where('v.fecha_ingreso', '<=', $a + 1 . '-12-31')
                    ->orderBy('fecha_ingreso')
                    ->get();
                foreach ($fechas as $l)
                    if (!in_array(substr(getSemanaByDate($l->dia)->codigo, 2), $labels))
                        array_push($labels, substr(getSemanaByDate($l->dia)->codigo, 2));
            }

            foreach ($request->annos as $a) {
                $cajas = [];
                $tallos = [];
                $calibre = [];
                foreach ($labels as $l) {
                    $semana = Semana::All()->where('codigo', '=', substr($a, 2) . $l)->first();
                    $list_verdes = ClasificacionVerde::All()
                        ->where('fecha_ingreso', '>=', $semana->fecha_inicial)
                        ->where('fecha_ingreso', '<=', $semana->fecha_final);
                    $cajas_c = 0;
                    $tallos_c = 0;
                    $calibre_c = 0;

                    foreach ($list_verdes as $verde) {
                        $cajas_c += round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2);
                        $tallos_c += $verde->total_tallos();
                        $calibre_c += $verde->getTotalRamosEstandar() > 0 ? round($verde->total_tallos() / $verde->getTotalRamosEstandar(), 2) : 0;
                    }
                    $calibre_c = count($list_verdes) > 0 ? round($calibre_c / count($list_verdes), 2) : 0;

                    array_push($cajas, $cajas_c);
                    array_push($tallos, $tallos_c);
                    array_push($calibre, $calibre_c);
                }

                array_push($annos, [
                    'anno' => $a,
                    'cajas' => $cajas,
                    'tallos' => $tallos,
                    'calibre' => $calibre,
                ]);
            }
        }


        /* ================ OBTENER RESULTADOS =============*/
        $arreglo_variedades = [];
        if ($periodo == 'diario') {
            if ($view == 'acumulado') {
                foreach ($labels as $dia) {
                    $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                    if ($verde != '') {
                        array_push($array_cajas, round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2));
                        array_push($array_ramos, $verde->getTotalRamosEstandar());
                        array_push($array_tallos, $verde->total_tallos());
                        array_push($array_calibre, round($verde->total_tallos() / $verde->getTotalRamosEstandar(), 2));
                    }
                }
            }
            if ($view == 'x_variedad') {
                foreach ($labels as $dia) {
                    $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                    if ($verde != '') {
                        array_push($array_cajas, round($verde->getTotalRamosEstandarByVariedad($target->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2));
                        array_push($array_ramos, $verde->getTotalRamosEstandarByVariedad($target->id_variedad));
                        array_push($array_tallos, $verde->tallos_x_variedad($target->id_variedad));
                        array_push($array_calibre, $verde->calibreByVariedad($target->id_variedad));
                    }
                }
            }
            if ($view == 'todas_variedades') {
                foreach ($target as $variedad) {
                    $array_cajas = [];
                    $array_ramos = [];
                    $array_tallos = [];
                    $array_calibre = [];
                    foreach ($labels as $dia) {
                        $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                        if ($verde != '') {
                            array_push($array_cajas, round($verde->getTotalRamosEstandarByVariedad($variedad->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2));
                            array_push($array_ramos, $verde->getTotalRamosEstandarByVariedad($variedad->id_variedad));
                            array_push($array_tallos, $verde->tallos_x_variedad($variedad->id_variedad));
                            array_push($array_calibre, $verde->calibreByVariedad($variedad->id_variedad));
                        }
                    }

                    array_push($arreglo_variedades, [
                        'variedad' => $variedad,
                        'cajas' => $array_cajas,
                        'ramos' => $array_ramos,
                        'tallos' => $array_tallos,
                        'calibre' => $array_calibre,
                    ]);
                }
            }
        }
        if ($periodo == 'semanal') {
            if ($view == 'acumulado') {
                foreach ($labels as $codigo) {
                    $semana = Semana::All()->where('codigo', '=', $codigo->semana)->first();
                    $list_verdes = ClasificacionVerde::All()
                        ->where('fecha_ingreso', '>=', $semana->fecha_inicial)
                        ->where('fecha_ingreso', '<=', $semana->fecha_final);
                    $cajas = 0;
                    $ramos = 0;
                    $tallos = 0;
                    $calibre = 0;
                    foreach ($list_verdes as $verde) {
                        $cajas += round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2);
                        $ramos += $verde->getTotalRamosEstandar();
                        $tallos += $verde->total_tallos();
                        $calibre += round($verde->total_tallos() / $verde->getTotalRamosEstandar(), 2);
                    }
                    $calibre = count($list_verdes) > 0 ? round($calibre / count($list_verdes), 2) : 0;

                    array_push($array_cajas, $cajas);
                    array_push($array_ramos, $ramos);
                    array_push($array_tallos, $tallos);
                    array_push($array_calibre, $calibre);
                }
            }
            if ($view == 'x_variedad') {
                foreach ($labels as $codigo) {
                    $semana = Semana::All()->where('codigo', '=', $codigo->semana)->first();
                    $list_verdes = ClasificacionVerde::All()
                        ->where('fecha_ingreso', '>=', $semana->fecha_inicial)
                        ->where('fecha_ingreso', '<=', $semana->fecha_final);
                    $cajas = 0;
                    $ramos = 0;
                    $tallos = 0;
                    $calibre = 0;
                    foreach ($list_verdes as $verde) {
                        $cajas += round($verde->getTotalRamosEstandarByVariedad($target->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                        $ramos += $verde->getTotalRamosEstandarByVariedad($target->id_variedad);
                        $tallos += $verde->tallos_x_variedad($target->id_variedad);
                        $calibre += $verde->calibreByVariedad($target->id_variedad);
                    }
                    $calibre = count($list_verdes) > 0 ? round($calibre / count($list_verdes), 2) : 0;

                    array_push($array_cajas, $cajas);
                    array_push($array_ramos, $ramos);
                    array_push($array_tallos, $tallos);
                    array_push($array_calibre, $calibre);
                }
            }
            if ($view == 'todas_variedades') {
                foreach ($target as $variedad) {
                    $array_cajas = [];
                    $array_ramos = [];
                    $array_tallos = [];
                    $array_calibre = [];

                    foreach ($labels as $codigo) {
                        $semana = Semana::All()->where('codigo', '=', $codigo->semana)->first();
                        $list_verdes = ClasificacionVerde::All()
                            ->where('fecha_ingreso', '>=', $semana->fecha_inicial)
                            ->where('fecha_ingreso', '<=', $semana->fecha_final);
                        $cajas = 0;
                        $ramos = 0;
                        $tallos = 0;
                        $desecho = 0;
                        $rendimiento = 0;
                        $calibre = 0;
                        foreach ($list_verdes as $verde) {
                            $cajas += round($verde->getTotalRamosEstandarByVariedad($variedad->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                            $ramos += $verde->getTotalRamosEstandarByVariedad($variedad->id_variedad);
                            $tallos += $verde->tallos_x_variedad($variedad->id_variedad);
                            $calibre += $verde->calibreByVariedad($variedad->id_variedad);
                        }
                        $calibre = count($list_verdes) > 0 ? round($calibre / count($list_verdes), 2) : 0;

                        array_push($array_cajas, $cajas);
                        array_push($array_ramos, $ramos);
                        array_push($array_tallos, $tallos);
                        array_push($array_calibre, $calibre);
                    }

                    array_push($arreglo_variedades, [
                        'variedad' => $variedad,
                        'cajas' => $array_cajas,
                        'ramos' => $array_ramos,
                        'tallos' => $array_tallos,
                        'calibre' => $array_calibre,
                    ]);
                }
            }
        }
        if ($periodo == 'mensual') {
            if ($view == 'acumulado') {
                foreach ($labels as $mes) {
                    $list_verdes = DB::table('clasificacion_verde as v')
                        ->where(DB::raw('Month(fecha_ingreso)'), '=', $mes->mes)
                        ->where(DB::raw('Year(fecha_ingreso)'), '=', $mes->ano)
                        ->get();
                    $cajas = 0;
                    $ramos = 0;
                    $tallos = 0;
                    $calibre = 0;
                    foreach ($list_verdes as $item) {
                        $verde = ClasificacionVerde::find($item->id_clasificacion_verde);
                        $cajas += round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2);
                        $ramos += $verde->getTotalRamosEstandar();
                        $tallos += $verde->total_tallos();
                        $calibre += round($verde->total_tallos() / $verde->getTotalRamosEstandar(), 2);
                    }
                    $calibre = count($list_verdes) > 0 ? round($calibre / count($list_verdes), 2) : 0;

                    array_push($array_cajas, $cajas);
                    array_push($array_ramos, $ramos);
                    array_push($array_tallos, $tallos);
                    array_push($array_calibre, $calibre);
                }
            }
            if ($view == 'x_variedad') {
                foreach ($labels as $mes) {
                    $list_verdes = DB::table('clasificacion_verde as v')
                        ->where(DB::raw('Month(fecha_ingreso)'), '=', $mes->mes)
                        ->where(DB::raw('Year(fecha_ingreso)'), '=', $mes->ano)
                        ->get();
                    $cajas = 0;
                    $ramos = 0;
                    $tallos = 0;
                    $calibre = 0;
                    foreach ($list_verdes as $item) {
                        $verde = ClasificacionVerde::find($item->id_clasificacion_verde);
                        $cajas += round($verde->getTotalRamosEstandarByVariedad($target->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                        $ramos += $verde->getTotalRamosEstandarByVariedad($target->id_variedad);
                        $tallos += $verde->tallos_x_variedad($target->id_variedad);
                        $calibre += $verde->calibreByVariedad($target->id_variedad);
                    }
                    $calibre = count($list_verdes) > 0 ? round($calibre / count($list_verdes), 2) : 0;

                    array_push($array_cajas, $cajas);
                    array_push($array_ramos, $ramos);
                    array_push($array_tallos, $tallos);
                    array_push($array_calibre, $calibre);
                }
            }
            if ($view == 'todas_variedades') {
                foreach ($target as $variedad) {
                    $array_cajas = [];
                    $array_ramos = [];
                    $array_tallos = [];
                    $array_calibre = [];

                    foreach ($labels as $mes) {
                        $list_verdes = DB::table('clasificacion_verde as v')
                            ->where(DB::raw('Month(fecha_ingreso)'), '=', $mes->mes)
                            ->where(DB::raw('Year(fecha_ingreso)'), '=', $mes->ano)
                            ->get();
                        $cajas = 0;
                        $ramos = 0;
                        $tallos = 0;
                        $calibre = 0;
                        foreach ($list_verdes as $item) {
                            $verde = ClasificacionVerde::find($item->id_clasificacion_verde);
                            $cajas += round($verde->getTotalRamosEstandarByVariedad($variedad->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                            $ramos += $verde->getTotalRamosEstandarByVariedad($variedad->id_variedad);
                            $tallos += $verde->tallos_x_variedad($variedad->id_variedad);
                            $calibre += $verde->calibreByVariedad($variedad->id_variedad);
                        }
                        $calibre = count($list_verdes) > 0 ? round($calibre / count($list_verdes), 2) : 0;

                        array_push($array_cajas, $cajas);
                        array_push($array_ramos, $ramos);
                        array_push($array_tallos, $tallos);
                        array_push($array_calibre, $calibre);
                    }

                    array_push($arreglo_variedades, [
                        'variedad' => $variedad,
                        'cajas' => $array_cajas,
                        'ramos' => $array_ramos,
                        'tallos' => $array_tallos,
                        'calibre' => $array_calibre,
                    ]);
                }
            }
        }
        if ($periodo == 'anual') {
            if ($view == 'acumulado') {
                foreach ($labels as $mes) {
                    $list_verdes = DB::table('clasificacion_verde as v')
                        ->where(DB::raw('Year(fecha_ingreso)'), '=', $mes->ano)
                        ->get();
                    $cajas = 0;
                    $ramos = 0;
                    $tallos = 0;
                    $calibre = 0;
                    foreach ($list_verdes as $item) {
                        $verde = ClasificacionVerde::find($item->id_clasificacion_verde);
                        $cajas += round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2);
                        $ramos += $verde->getTotalRamosEstandar();
                        $tallos += $verde->total_tallos();
                        $calibre += round($verde->total_tallos() / $verde->getTotalRamosEstandar(), 2);
                    }
                    $calibre = count($list_verdes) > 0 ? round($calibre / count($list_verdes), 2) : 0;

                    array_push($array_cajas, $cajas);
                    array_push($array_ramos, $ramos);
                    array_push($array_tallos, $tallos);
                    array_push($array_calibre, $calibre);
                }
            }
            if ($view == 'x_variedad') {
                foreach ($labels as $mes) {
                    $list_verdes = DB::table('clasificacion_verde as v')
                        ->where(DB::raw('Year(fecha_ingreso)'), '=', $mes->ano)
                        ->get();
                    $cajas = 0;
                    $ramos = 0;
                    $tallos = 0;
                    $calibre = 0;
                    foreach ($list_verdes as $item) {
                        $verde = ClasificacionVerde::find($item->id_clasificacion_verde);
                        $cajas += round($verde->getTotalRamosEstandarByVariedad($target->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                        $ramos += $verde->getTotalRamosEstandarByVariedad($target->id_variedad);
                        $tallos += $verde->tallos_x_variedad($target->id_variedad);
                        $calibre += $verde->calibreByVariedad($target->id_variedad);
                    }
                    $calibre = count($list_verdes) > 0 ? round($calibre / count($list_verdes), 2) : 0;

                    array_push($array_cajas, $cajas);
                    array_push($array_ramos, $ramos);
                    array_push($array_tallos, $tallos);
                    array_push($array_calibre, $calibre);
                }
            }
            if ($view == 'todas_variedades') {
                foreach ($target as $variedad) {
                    $array_cajas = [];
                    $array_ramos = [];
                    $array_tallos = [];
                    $array_calibre = [];

                    foreach ($labels as $mes) {
                        $list_verdes = DB::table('clasificacion_verde as v')
                            ->where(DB::raw('Year(fecha_ingreso)'), '=', $mes->ano)
                            ->get();
                        $cajas = 0;
                        $ramos = 0;
                        $tallos = 0;
                        $calibre = 0;
                        foreach ($list_verdes as $item) {
                            $verde = ClasificacionVerde::find($item->id_clasificacion_verde);
                            $cajas += round($verde->getTotalRamosEstandarByVariedad($variedad->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                            $ramos += $verde->getTotalRamosEstandarByVariedad($variedad->id_variedad);
                            $tallos += $verde->tallos_x_variedad($variedad->id_variedad);
                            $calibre += $verde->calibreByVariedad($variedad->id_variedad);
                        }
                        $calibre = count($list_verdes) > 0 ? round($calibre / count($list_verdes), 2) : 0;

                        array_push($array_cajas, $cajas);
                        array_push($array_ramos, $ramos);
                        array_push($array_tallos, $tallos);
                        array_push($array_calibre, $calibre);
                    }

                    array_push($arreglo_variedades, [
                        'variedad' => $variedad,
                        'cajas' => $array_cajas,
                        'ramos' => $array_ramos,
                        'tallos' => $array_tallos,
                        'calibre' => $array_calibre,
                    ]);
                }
            }
        }

        return view('adminlte.crm.postcocecha.partials.secciones.grafica._' . $view, [
            'target' => $target,
            'labels' => $labels,
            'periodo' => $periodo,
            'annos' => $annos,
            'arreglo_variedades' => $arreglo_variedades,
            'array_cajas' => $array_cajas,
            'array_ramos' => $array_ramos,
            'array_tallos' => $array_tallos,
            'array_calibre' => $array_calibre,
        ]);

    }

    public function show_data_cajas(Request $request)
    {
        $labels = DB::table('clasificacion_verde as v')
            ->select('v.fecha_ingreso as dia')->distinct()
            ->where('v.fecha_ingreso', '>=', $request->desde)
            ->where('v.fecha_ingreso', '<=', $request->hasta)
            ->get();
        $target = getVariedades();

        /* ================ OBTENER RESULTADOS =============*/
        $arreglo_variedades = [];

        foreach ($target as $variedad) {
            $array_cajas = [];
            foreach ($labels as $dia) {
                $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                if ($verde != '') {
                    array_push($array_cajas, round($verde->getTotalRamosEstandarByVariedad($variedad->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2));
                }
            }

            array_push($arreglo_variedades, [
                'variedad' => $variedad,
                'cajas' => $array_cajas,
            ]);
        }

        return view('adminlte.crm.postcocecha.partials.secciones.indicadores.modals.data_cajas', [
            'target' => $target,
            'labels' => $labels,
            'arreglo_variedades' => $arreglo_variedades
        ]);
    }

    public function show_data_tallos(Request $request)
    {
        $labels = DB::table('clasificacion_verde as v')
            ->select('v.fecha_ingreso as dia')->distinct()
            ->where('v.fecha_ingreso', '>=', $request->desde)
            ->where('v.fecha_ingreso', '<=', $request->hasta)
            ->get();
        $target = getVariedades();

        /* ================ OBTENER RESULTADOS =============*/
        $arreglo_variedades = [];

        foreach ($target as $variedad) {
            $array_tallos = [];
            foreach ($labels as $dia) {
                $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                if ($verde != '') {
                    array_push($array_tallos, $verde->tallos_x_variedad($variedad->id_variedad));
                }
            }

            array_push($arreglo_variedades, [
                'variedad' => $variedad,
                'tallos' => $array_tallos,
            ]);
        }
        return view('adminlte.crm.postcocecha.partials.secciones.indicadores.modals.data_tallos', [
            'target' => $target,
            'labels' => $labels,
            'arreglo_variedades' => $arreglo_variedades
        ]);
    }

    public function show_data_calibres(Request $request)
    {
        $labels = DB::table('clasificacion_verde as v')
            ->select('v.fecha_ingreso as dia')->distinct()
            ->where('v.fecha_ingreso', '>=', $request->desde)
            ->where('v.fecha_ingreso', '<=', $request->hasta)
            ->get();
        $target = getVariedades();

        /* ================ OBTENER RESULTADOS =============*/
        $arreglo_variedades = [];

        foreach ($target as $variedad) {
            $array_calibre = [];
            foreach ($labels as $dia) {
                $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                if ($verde != '') {
                    array_push($array_calibre, $verde->calibreByVariedad($variedad->id_variedad));
                }
            }

            array_push($arreglo_variedades, [
                'variedad' => $variedad,
                'calibre' => $array_calibre,
            ]);
        }
        return view('adminlte.crm.postcocecha.partials.secciones.indicadores.modals.data_calibres', [
            'target' => $target,
            'labels' => $labels,
            'arreglo_variedades' => $arreglo_variedades
        ]);
    }

    public function actualizar_cosecha_x_variedad(Request $request)
    {
        $cosecha = Cosecha::All()->where('fecha_ingreso', '=', date('Y-m-d'))->first();
        $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', date('Y-m-d'))->first();
        $listado_variedades = [];
        if ($cosecha != '') {
            $listado_variedades = $cosecha->getVariedades();
        }
        $dias_atras = 1;
        $last_verde = ClasificacionVerde::All()
            ->where('fecha_ingreso', '=', opDiasFecha('-', $dias_atras, date('Y-m-d')))->first();
        while ($last_verde == '' && $dias_atras <= 7) {
            $dias_atras++;
            $last_verde = ClasificacionVerde::All()
                ->where('fecha_ingreso', '=', opDiasFecha('-', $dias_atras, date('Y-m-d')))->first();
        }
        $porcentaje = 0;
        if ($last_verde != '' && $verde != '')
            $porcentaje = 100 - porcentaje($verde->getCalibre(), $last_verde->getCalibre(), 1);
        return view('adminlte.crm.postcocecha.partials.secciones.cosecha_x_variedad', [
            'cosecha' => $cosecha,
            'verde' => $verde,
            'porcent' => $porcentaje,
            'last_verde' => $last_verde,
            'listado_variedades' => $listado_variedades,
        ]);
    }
}