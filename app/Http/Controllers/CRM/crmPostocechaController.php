<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\Cosecha;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use PHPExcel_Worksheet_MemoryDrawing;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;
use PHPExcel_Style_Alignment;

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

            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
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
            $request_annos = $request->annos;
            sort($request_annos);

            foreach ($request_annos as $a) {
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

            foreach ($request_annos as $a) {
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

    /* ======================== EXCEL ===================== */

    public function exportar_dashboard(Request $request)
    {
        //---------------------- EXCEL --------------------------------------
        $objPHPExcel = new PHPExcel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $currencyFormat = '#,#0.## \€;[Red]-#,#0.## \€';
        $numberFormat = '#,#0.##;[Red]-#,#0.##';

        $objPHPExcel->removeSheetByIndex(0); //Eliminar la hoja inicial por defecto

        $this->excel_hoja($objPHPExcel, $request);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="Dashboard Postcosecha.xlsx"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        ob_start();
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        $opResult = array(
            'status' => 1,
            'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        echo json_encode($opResult);
    }

    public function excel_hoja($objPHPExcel, $request)
    {
        $columnas = [0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I', 9 => 'J', 10 => 'K', 11 => 'L',
            12 => 'M', 13 => 'N', 14 => 'O', 15 => 'P', 16 => 'Q', 17 => 'R', 18 => 'S', 19 => 'T', 20 => 'U', 21 => 'V', 22 => 'W', 23 => 'X',
            24 => 'Y', 25 => 'Z', 26 => 'AA', 27 => 'AB', 28 => 'AC', 29 => 'AD', 30 => 'AE', 31 => 'AF', 32 => 'AG', 33 => 'AH', 34 => 'AI',
            35 => 'AJ', 36 => 'AK', 37 => 'AL', 38 => 'AM', 39 => 'AN', 40 => 'AO', 41 => 'AP', 42 => 'AQ', 43 => 'AR', 44 => 'AS', 45 => 'AT',
            46 => 'AU', 47 => 'AV', 48 => 'AW', 49 => 'AX', 50 => 'AY', 51 => 'AZ', 52 => 'BA', 53 => 'BB', 54 => 'BC', 55 => 'BD', 56 => 'BE',
            57 => 'BF', 58 => 'BG', 59 => 'BH', 60 => 'BI', 61 => 'BJ', 62 => 'BK', 63 => 'BL', 64 => 'BM', 65 => 'BN', 66 => 'BO', 67 => 'BP',
            68 => 'BQ', 69 => 'BR', 70 => 'BS', 71 => 'BT', 72 => 'BU', 73 => 'BV', 74 => 'BW', 75 => 'BX', 76 => 'BY', 77 => 'BZ'];

        $objSheet = new PHPExcel_Worksheet($objPHPExcel, 'Dashboard');
        $objPHPExcel->addSheet($objSheet, 0);

        /* ============== MERGE CELDAS =============*/
        $objSheet->mergeCells('A1:L1');
        /* ============== BACKGROUND COLOR =============*/
        $objSheet->getStyle('A1')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('d2d6de');
        /* ============== ENCABEZADO =============*/
        $objSheet->getCell('A1')->setValue('Últimos 7 días');
        /* ============== BORDE COLOR =============*/
        $objSheet->getStyle('A1:L4')//para todo
        ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)
            ->getColor()
            ->setRGB('000000');

        /* ================================= INDICADORES ================================ */
        /* ============== TEXT COLOR =============*/
        $objSheet->getStyle('A2:L4')// para todos
        ->getFont()
            ->getColor()
            ->setRGB('ffffff');

        /* ============== MERGE CELDAS =============*/
        $objSheet->mergeCells('A2:D3');
        /* ============== MERGE CELDAS =============*/
        $objSheet->mergeCells('A4:D4');
        /* ============== BACKGROUND COLOR =============*/
        $objSheet->getStyle('A2')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('30bbbb');
        $objSheet->getStyle('A4')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('2ba8a8');
        $objSheet->getCell('A2')->setValue(number_format($request->indicador_cajas, 2));
        $objSheet->getCell('A4')->setValue('Cosecha cajas');

        /* ============== MERGE CELDAS =============*/
        $objSheet->mergeCells('E2:H3');
        /* ============== MERGE CELDAS =============*/
        $objSheet->mergeCells('E4:H4');
        /* ============== BACKGROUND COLOR =============*/
        $objSheet->getStyle('E2')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('00c0ef');
        $objSheet->getStyle('E4')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('00acd7');
        $objSheet->getCell('E2')->setValue(number_format($request->indicador_tallos, 2));
        $objSheet->getCell('E4')->setValue('Cosecha tallos');

        /* ============== MERGE CELDAS =============*/
        $objSheet->mergeCells('I2:L3');
        /* ============== MERGE CELDAS =============*/
        $objSheet->mergeCells('I4:L4');
        /* ============== BACKGROUND COLOR =============*/
        $objSheet->getStyle('I2')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('ff851b');
        $objSheet->getStyle('I4')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('e57718');
        $objSheet->getCell('I2')->setValue(number_format($request->indicador_calibre, 2));
        $objSheet->getCell('I4')->setValue('Calibre');

        /* ================================= GRAFICAS ================================ */
        $data = base64_decode(explode(',', $request->src_imagen_chart_cajas)[1]);
        file_put_contents(public_path() . '/images/cajas.png', $data);
        $data = base64_decode(explode(',', $request->src_imagen_chart_tallos)[1]);
        file_put_contents(public_path() . '/images/tallos.png', $data);
        $data = base64_decode(explode(',', $request->src_imagen_chart_calibres)[1]);
        file_put_contents(public_path() . '/images/calibres.png', $data);

        /* ============== MERGE CELDAS =============*/
        $objSheet->mergeCells('A5:P5');
        $objSheet->mergeCells('A6:P19');

        $objSheet->getCell('A5')->setValue('Cajas');

        $img_cajas = imagecreatefrompng(public_path() . '/images/cajas.png');
        $img_tallos = imagecreatefrompng(public_path() . '/images/tallos.png');
        $img_calibres = imagecreatefrompng(public_path() . '/images/calibres.png');

        $background = imagecolorallocate($img_cajas, 0, 0, 0);
        // removing the black from the placeholder
        imagecolortransparent($img_cajas, $background);
        imagecolortransparent($img_tallos, $background);
        imagecolortransparent($img_calibres, $background);

        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('CAJAS');
        $objDrawing->setDescription('CAJAS');
        $objDrawing->setImageResource($img_cajas);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG);
        //$objDrawing->setHeight();
        $objDrawing->setCoordinates('A6');
        $objDrawing->setWorksheet($objSheet);

        $listado = $this->obtener_data_grafica($request);
        if ($listado['view'] == 'acumulado' || $listado['view'] == 'x_variedad') {  // acumulado o por variedad
            /* ============== MERGE CELDAS =============*/
            $objSheet->mergeCells('A23:P36');
            $objSheet->mergeCells('A40:P53');

            /* ============== MERGE CELDAS =============*/
            $objSheet->mergeCells('A22:P22');
            $objSheet->getCell('A22')->setValue('Tallos');
            /* ============== MERGE CELDAS =============*/
            $objSheet->mergeCells('A39:P39');
            $objSheet->getCell('A39')->setValue('Calibre');

            $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
            $objDrawing->setName('TALLOS');
            $objDrawing->setDescription('TALLOS');
            $objDrawing->setImageResource($img_tallos);
            $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
            $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG);
            //$objDrawing->setHeight();
            $objDrawing->setCoordinates('A23');
            $objDrawing->setWorksheet($objSheet);

            $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
            $objDrawing->setName('CALIBRES');
            $objDrawing->setDescription('CALIBRES');
            $objDrawing->setImageResource($img_calibres);
            $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
            $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG);
            //$objDrawing->setHeight();
            $objDrawing->setCoordinates('A40');
            $objDrawing->setWorksheet($objSheet);

            $pos_col = 0;
            foreach ($listado['labels'] as $pos_l => $label) {
                if ($listado['periodo'] == 'diario') {
                    $objSheet->getCell($columnas[$pos_col] . '20')->setValue($label->dia);
                    $objSheet->getCell($columnas[$pos_col] . '37')->setValue($label->dia);
                    $objSheet->getCell($columnas[$pos_col] . '54')->setValue($label->dia);
                } else if ($listado['periodo'] == 'semanal') {
                    $objSheet->getCell($columnas[$pos_col] . '20')->setValue($label->semana);
                    $objSheet->getCell($columnas[$pos_col] . '37')->setValue($label->semana);
                    $objSheet->getCell($columnas[$pos_col] . '54')->setValue($label->semana);
                } else if ($listado['periodo'] == 'anual') {
                    $objSheet->getCell($columnas[$pos_col] . '20')->setValue($label->ano);
                    $objSheet->getCell($columnas[$pos_col] . '37')->setValue($label->ano);
                    $objSheet->getCell($columnas[$pos_col] . '54')->setValue($label->ano);
                } else {
                    $objSheet->getCell($columnas[$pos_col] . '20')
                        ->setValue(getMeses(TP_ABREVIADO)[$label->mes - 1] . ' - ' . $label->ano);
                    $objSheet->getCell($columnas[$pos_col] . '37')
                        ->setValue(getMeses(TP_ABREVIADO)[$label->mes - 1] . ' - ' . $label->ano);
                    $objSheet->getCell($columnas[$pos_col] . '54')
                        ->setValue(getMeses(TP_ABREVIADO)[$label->mes - 1] . ' - ' . $label->ano);
                }
                $objSheet->getCell($columnas[$pos_col] . '21')->setValue($listado['array_cajas'][$pos_l]);
                $objSheet->getCell($columnas[$pos_col] . '38')->setValue($listado['array_tallos'][$pos_l]);
                $objSheet->getCell($columnas[$pos_col] . '55')->setValue($listado['array_calibre'][$pos_l]);
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle('A20:' . $columnas[$pos_col] . '20')
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('d2d6de');
                $objSheet->getStyle('A37:' . $columnas[$pos_col] . '37')
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('d2d6de');
                $objSheet->getStyle('A54:' . $columnas[$pos_col] . '54')
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('d2d6de');
                /* ============== BORDE COLOR =============*/
                $objSheet->getStyle('A20:' . $columnas[$pos_col] . '21')
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)
                    ->getColor()
                    ->setRGB('000000');
                $objSheet->getStyle('A37:' . $columnas[$pos_col] . '38')
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)
                    ->getColor()
                    ->setRGB('000000');
                $objSheet->getStyle('A54:' . $columnas[$pos_col] . '55')
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)
                    ->getColor()
                    ->setRGB('000000');
                $pos_col++;
            }

            $pos_fila = 56;
        } else if ($listado['view'] == 'todas_variedades') {    // todas las variedades
            /* --------------------- cajas ---------------------------- */
            $pos_fila = 20;
            /* ============== BORDE COLOR =============*/
            $objSheet
                ->getStyle('A' . $pos_fila . ':' . $columnas[count($listado['labels'])] . intval($pos_fila + count($listado['arreglo_variedades'])))
                ->getBorders()->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)->getColor()->setRGB('000000');
            foreach ($listado['labels'] as $pos_l => $label) {
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle($columnas[$pos_l] . $pos_fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('d2d6de');
                if ($listado['periodo'] == 'diario') {
                    $objSheet->getCell($columnas[$pos_l] . $pos_fila)->setValue($label->dia);
                } else if ($listado['periodo'] == 'semanal') {
                    $objSheet->getCell($columnas[$pos_l] . $pos_fila)->setValue($label->semana);
                } else if ($listado['periodo'] == 'anual') {
                    $objSheet->getCell($columnas[$pos_l] . $pos_fila)->setValue($label->ano);
                } else {
                    $objSheet->getCell($columnas[$pos_l] . $pos_fila)
                        ->setValue(getMeses(TP_ABREVIADO)[$label->mes - 1] . ' - ' . $label->ano);
                }
            }

            foreach ($listado['arreglo_variedades'] as $variedad) {
                $pos_fila++;
                foreach ($variedad['cajas'] as $pos_v => $valor) {
                    /* ============== BACKGROUND COLOR =============*/
                    $objSheet->getStyle($columnas[$pos_v] . $pos_fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setRGB(str_replace('#', '', $variedad['variedad']->color));
                    $objSheet->getCell($columnas[$pos_v] . $pos_fila)->setValue($valor);
                }
            }

            /* --------------------- tallos ---------------------------- */
            $pos_fila++;
            /* ============== MERGE CELDAS =============*/
            $objSheet->mergeCells('A' . $pos_fila . ':P' . $pos_fila);
            $objSheet->getCell('A' . $pos_fila)->setValue('Tallos');
            $pos_fila++;
            /* ============== MERGE CELDAS =============*/
            $objSheet->mergeCells('A' . $pos_fila . ':P' . intval($pos_fila + 14));

            $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
            $objDrawing->setName('TALLOS');
            $objDrawing->setDescription('TALLOS');
            $objDrawing->setImageResource($img_tallos);
            $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
            $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG);
            //$objDrawing->setHeight();
            $objDrawing->setCoordinates('A' . $pos_fila);
            $objDrawing->setWorksheet($objSheet);

            $pos_fila = intval($pos_fila + 15);
            /* ============== BORDE COLOR =============*/
            $objSheet
                ->getStyle('A' . $pos_fila . ':' . $columnas[count($listado['labels'])] . intval($pos_fila + count($listado['arreglo_variedades'])))
                ->getBorders()->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)->getColor()->setRGB('000000');
            foreach ($listado['labels'] as $pos_l => $label) {
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle($columnas[$pos_l] . $pos_fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('d2d6de');
                if ($listado['periodo'] == 'diario') {
                    $objSheet->getCell($columnas[$pos_l] . $pos_fila)->setValue($label->dia);
                } else if ($listado['periodo'] == 'semanal') {
                    $objSheet->getCell($columnas[$pos_l] . $pos_fila)->setValue($label->semana);
                } else if ($listado['periodo'] == 'anual') {
                    $objSheet->getCell($columnas[$pos_l] . $pos_fila)->setValue($label->ano);
                } else {
                    $objSheet->getCell($columnas[$pos_l] . $pos_fila)
                        ->setValue(getMeses(TP_ABREVIADO)[$label->mes - 1] . ' - ' . $label->ano);
                }
            }

            foreach ($listado['arreglo_variedades'] as $variedad) {
                $pos_fila++;
                foreach ($variedad['tallos'] as $pos_v => $valor) {
                    /* ============== BACKGROUND COLOR =============*/
                    $objSheet->getStyle($columnas[$pos_v] . $pos_fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setRGB(str_replace('#', '', $variedad['variedad']->color));
                    $objSheet->getCell($columnas[$pos_v] . $pos_fila)->setValue($valor);
                }
            }

            /* --------------------- calibres ---------------------------- */
            $pos_fila++;
            /* ============== MERGE CELDAS =============*/
            $objSheet->mergeCells('A' . $pos_fila . ':P' . $pos_fila);
            $objSheet->getCell('A' . $pos_fila)->setValue('Calibre');
            $pos_fila++;
            /* ============== MERGE CELDAS =============*/
            $objSheet->mergeCells('A' . $pos_fila . ':P' . intval($pos_fila + 14));

            $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
            $objDrawing->setName('CALIRBES');
            $objDrawing->setDescription('CALIBRES');
            $objDrawing->setImageResource($img_calibres);
            $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
            $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG);
            //$objDrawing->setHeight();
            $objDrawing->setCoordinates('A' . $pos_fila);
            $objDrawing->setWorksheet($objSheet);

            $pos_fila = intval($pos_fila + 15);
            /* ============== BORDE COLOR =============*/
            $objSheet
                ->getStyle('A' . $pos_fila . ':' . $columnas[count($listado['labels'])] . intval($pos_fila + count($listado['arreglo_variedades'])))
                ->getBorders()->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)->getColor()->setRGB('000000');
            foreach ($listado['labels'] as $pos_l => $label) {
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle($columnas[$pos_l] . $pos_fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('d2d6de');
                if ($listado['periodo'] == 'diario') {
                    $objSheet->getCell($columnas[$pos_l] . $pos_fila)->setValue($label->dia);
                } else if ($listado['periodo'] == 'semanal') {
                    $objSheet->getCell($columnas[$pos_l] . $pos_fila)->setValue($label->semana);
                } else if ($listado['periodo'] == 'anual') {
                    $objSheet->getCell($columnas[$pos_l] . $pos_fila)->setValue($label->ano);
                } else {
                    $objSheet->getCell($columnas[$pos_l] . $pos_fila)
                        ->setValue(getMeses(TP_ABREVIADO)[$label->mes - 1] . ' - ' . $label->ano);
                }
            }

            foreach ($listado['arreglo_variedades'] as $variedad) {
                $pos_fila++;
                foreach ($variedad['calibre'] as $pos_v => $valor) {
                    /* ============== BACKGROUND COLOR =============*/
                    $objSheet->getStyle($columnas[$pos_v] . $pos_fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setRGB(str_replace('#', '', $variedad['variedad']->color));
                    $objSheet->getCell($columnas[$pos_v] . $pos_fila)->setValue($valor);
                }
            }
        } else {    // annos
            /* --------------------- cajas ---------------------------- */
            $pos_fila = 20;
            /* ============== BORDE COLOR =============*/
            $objSheet
                ->getStyle('A' . $pos_fila . ':' . $columnas[count($listado['labels'])] . intval($pos_fila + count($listado['arreglo_variedades'])))
                ->getBorders()->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)->getColor()->setRGB('000000');
            foreach ($listado['labels'] as $pos_l => $label) {
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle($columnas[$pos_l] . $pos_fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('d2d6de');
                $objSheet->getCell($columnas[$pos_l] . $pos_fila)->setValue($label);
            }

            foreach ($listado['annos'] as $pos_a => $anno) {
                $pos_fila++;
                foreach ($anno['cajas'] as $pos_v => $valor) {
                    /* ============== BACKGROUND COLOR =============*/
                    $objSheet->getStyle($columnas[$pos_v] . $pos_fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setRGB(str_replace('#', '', getListColores()[$pos_a]));
                    $objSheet->getCell($columnas[$pos_v] . $pos_fila)->setValue($valor);
                }
            }

            /* --------------------- tallos ---------------------------- */
            $pos_fila++;
            /* ============== MERGE CELDAS =============*/
            $objSheet->mergeCells('A' . $pos_fila . ':P' . $pos_fila);
            $objSheet->getCell('A' . $pos_fila)->setValue('Tallos');
            $pos_fila++;
            /* ============== MERGE CELDAS =============*/
            $objSheet->mergeCells('A' . $pos_fila . ':P' . intval($pos_fila + 14));

            $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
            $objDrawing->setName('TALLOS');
            $objDrawing->setDescription('TALLOS');
            $objDrawing->setImageResource($img_tallos);
            $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
            $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG);
            //$objDrawing->setHeight();
            $objDrawing->setCoordinates('A' . $pos_fila);
            $objDrawing->setWorksheet($objSheet);

            $pos_fila = intval($pos_fila + 15);
            /* ============== BORDE COLOR =============*/
            $objSheet
                ->getStyle('A' . $pos_fila . ':' . $columnas[count($listado['labels'])] . intval($pos_fila + count($listado['arreglo_variedades'])))
                ->getBorders()->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)->getColor()->setRGB('000000');
            foreach ($listado['labels'] as $pos_l => $label) {
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle($columnas[$pos_l] . $pos_fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('d2d6de');
                $objSheet->getCell($columnas[$pos_l] . $pos_fila)->setValue($label);
            }

            foreach ($listado['annos'] as $pos_a => $anno) {
                $pos_fila++;
                foreach ($anno['tallos'] as $pos_v => $valor) {
                    /* ============== BACKGROUND COLOR =============*/
                    $objSheet->getStyle($columnas[$pos_v] . $pos_fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setRGB(str_replace('#', '', getListColores()[$pos_a]));
                    $objSheet->getCell($columnas[$pos_v] . $pos_fila)->setValue($valor);
                }
            }

            /* --------------------- calibres ---------------------------- */
            $pos_fila++;
            /* ============== MERGE CELDAS =============*/
            $objSheet->mergeCells('A' . $pos_fila . ':P' . $pos_fila);
            $objSheet->getCell('A' . $pos_fila)->setValue('Calibre');
            $pos_fila++;
            /* ============== MERGE CELDAS =============*/
            $objSheet->mergeCells('A' . $pos_fila . ':P' . intval($pos_fila + 14));

            $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
            $objDrawing->setName('CALIRBES');
            $objDrawing->setDescription('CALIBRES');
            $objDrawing->setImageResource($img_calibres);
            $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
            $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG);
            //$objDrawing->setHeight();
            $objDrawing->setCoordinates('A' . $pos_fila);
            $objDrawing->setWorksheet($objSheet);

            $pos_fila = intval($pos_fila + 15);
            /* ============== BORDE COLOR =============*/
            $objSheet
                ->getStyle('A' . $pos_fila . ':' . $columnas[count($listado['labels'])] . intval($pos_fila + count($listado['arreglo_variedades'])))
                ->getBorders()->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)->getColor()->setRGB('000000');
            foreach ($listado['labels'] as $pos_l => $label) {
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle($columnas[$pos_l] . $pos_fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('d2d6de');
                $objSheet->getCell($columnas[$pos_l] . $pos_fila)->setValue($label);
            }

            foreach ($listado['annos'] as $pos_a => $anno) {
                $pos_fila++;
                foreach ($anno['calibre'] as $pos_v => $valor) {
                    /* ============== BACKGROUND COLOR =============*/
                    $objSheet->getStyle($columnas[$pos_v] . $pos_fila)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()->setRGB(str_replace('#', '', getListColores()[$pos_a]));
                    $objSheet->getCell($columnas[$pos_v] . $pos_fila)->setValue($valor);
                }
            }
        }

        /* ================================= COSECHA DEL DÍA ================================ */
        $pos_fila += 2;
        /* ============== MERGE CELDAS =============*/
        $objSheet->mergeCells('A' . $pos_fila . ':D' . $pos_fila);
        $objSheet->getCell('A' . $pos_fila)->setValue('Cosecha del día: ' . date('d-m-Y'));
        /* ============== BACKGROUND COLOR =============*/
        $objSheet->getStyle('A' . $pos_fila . ':D' . intval($pos_fila + 1))
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('d2d6de');
        /* ============== BORDE COLOR =============*/
        $objSheet->getStyle('A' . $pos_fila . ':D' . intval($pos_fila + 2 + count($request->array_variedades)))
            ->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)
            ->getColor()->setRGB('000000');
        $pos_fila++;
        $objSheet->getCell('A' . $pos_fila)->setValue('Variedad');
        $objSheet->getCell('B' . $pos_fila)->setValue('Calibre');
        $objSheet->getCell('C' . $pos_fila)->setValue('Clasificados');
        $objSheet->getCell('D' . $pos_fila)->setValue('Cosechados');

        foreach ($request->array_variedades as $variedad) {
            $pos_fila++;
            $objSheet->getCell('A' . $pos_fila)->setValue(getVariedad($variedad['id'])->siglas);
            $objSheet->getCell('B' . $pos_fila)->setValue($variedad['calibre']);
            $objSheet->getCell('C' . $pos_fila)->setValue($variedad['clasificados']);
            $objSheet->getCell('D' . $pos_fila)->setValue($variedad['cosechados']);
        }
        $pos_fila++;
        $objSheet->getCell('A' . $pos_fila)->setValue('Resumen');
        $objSheet->getCell('B' . $pos_fila)->setValue($request->calibre_dia);
        $objSheet->getCell('C' . $pos_fila)->setValue($request->clasificados_dia);
        $objSheet->getCell('D' . $pos_fila)->setValue($request->cosechados_dia);
        /* ============== BACKGROUND COLOR =============*/
        $objSheet->getStyle('A' . $pos_fila . ':D' . $pos_fila)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('d2d6de');


        /* ============== LETRAS NEGRITAS =============*/
        $objSheet->getStyle('A1:L4')->getFont()->setBold(true)->setSize(12);   // para todo
        $objSheet->getStyle('A1:' . $columnas[count($listado['labels'])] . $pos_fila)->getFont()->setBold(true)->setSize(12);   // para todo
        /* ============== CENTRAR =============*/
        $objSheet->getStyle('A1:L4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('A1:' . $columnas[count($listado['labels'])] . $pos_fila)
            ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        unlink(public_path() . '/images/cajas.png');
        unlink(public_path() . '/images/tallos.png');
        unlink(public_path() . '/images/calibres.png');

        foreach ($columnas as $c) {
            $objSheet->getColumnDimension($c)->setAutoSize(true);
        }
    }

    public function obtener_data_grafica(Request $request)
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
            $request_annos = $request->annos;
            sort($request_annos);
            foreach ($request_annos as $a) {
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

            foreach ($request_annos as $a) {
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

        return [
            'view' => $view,
            'target' => $target,
            'labels' => $labels,
            'periodo' => $periodo,
            'annos' => $annos,
            'arreglo_variedades' => $arreglo_variedades,
            'array_cajas' => $array_cajas,
            //'array_ramos' => $array_ramos,
            'array_tallos' => $array_tallos,
            'array_calibre' => $array_calibre,
        ];
    }
}