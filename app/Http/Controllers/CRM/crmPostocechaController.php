<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\Cosecha;

class crmPostocechaController extends Controller
{
    public function inicio(Request $request)
    {
        $cosecha = Cosecha::All()->where('fecha_ingreso', '=', date('Y-m-d'))->first();
        $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', date('Y-m-d'))->first();

        return view('adminlte.crm.postcocecha.inicio', [
            'cosecha' => $cosecha,
            'verde' => $verde,
        ]);
    }

    public function cargar_cosecha(Request $request)
    {
        $cosecha = Cosecha::All()->where('fecha_ingreso', '=', date('Y-m-d'))->first();
        $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', date('Y-m-d'))->first();
        $listado_variedades = [];
        if ($verde != '') {
            $listado_variedades = $verde->variedades();
        }
        return view('adminlte.crm.postcocecha.partials.cosecha', [
            'cosecha' => $cosecha,
            'verde' => $verde,
            'listado_variedades' => $listado_variedades,
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
                ->get();
        }

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
        $desecho = 0;
        $rendimiento = 0;
        $calibre = 0;
        $arreglo_variedades = [];
        if ($periodo == 'diario') {
            if ($view == 'acumulado') {
                $cant_verde = 0;
                foreach ($labels as $dia) {
                    $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                    if ($verde != '') {
                        $cajas += round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2);
                        $ramos += $verde->getTotalRamosEstandar();
                        $tallos += $verde->total_tallos();
                        $desecho += $verde->desecho();
                        $rendimiento += $verde->getRendimiento();
                        $calibre += round($verde->total_tallos() / $verde->getTotalRamosEstandar(), 2);
                        $cant_verde++;
                    }
                }

                $desecho = $cant_verde > 0 ? round($desecho / $cant_verde, 2) : 0;
                $rendimiento = $cant_verde > 0 ? round($rendimiento / $cant_verde, 2) : 0;
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
                        $desecho += $verde->desechoByVariedad($target->id_variedad);
                        $rendimiento += $verde->getRendimientoByVariedad($target->id_variedad);
                        $calibre += $verde->calibreByVariedad($target->id_variedad);
                        $cant_verde++;
                    }
                }

                $desecho = $cant_verde > 0 ? round($desecho / $cant_verde, 2) : 0;
                $rendimiento = $cant_verde > 0 ? round($rendimiento / $cant_verde, 2) : 0;
                $calibre = $cant_verde > 0 ? round($calibre / $cant_verde, 2) : 0;
            }
            if ($view == 'todas_variedades') {
                foreach ($target as $variedad) {
                    $cant_verde = 0;
                    $cajas = 0;
                    $ramos = 0;
                    $tallos = 0;
                    $cosecha = 0;
                    $desecho = 0;
                    $rendimiento = 0;
                    $calibre = 0;
                    foreach ($labels as $dia) {
                        $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                        if ($verde != '') {
                            $cajas += round($verde->getTotalRamosEstandarByVariedad($variedad->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                            $ramos += $verde->getTotalRamosEstandarByVariedad($variedad->id_variedad);
                            $tallos += $verde->tallos_x_variedad($variedad->id_variedad);
                            $cosecha += $verde->total_tallos_recepcionByVariedad($variedad->id_variedad);
                            //$desecho += $verde->desechoByVariedad($variedad->id_variedad);
                            $rendimiento += $verde->getRendimientoByVariedad($variedad->id_variedad);
                            $calibre += $verde->calibreByVariedad($variedad->id_variedad);
                            $cant_verde++;
                        }
                    }

                    //$desecho = $cant_verde > 0 ? round($desecho / $cant_verde, 2) : 0;
                    $desecho = $cosecha > 0 ? 100 - round((($tallos * 100) / $cosecha), 2) : 0;
                    $rendimiento = $cant_verde > 0 ? round($rendimiento / $cant_verde, 2) : 0;
                    $calibre = $cant_verde > 0 ? round($calibre / $cant_verde, 2) : 0;

                    array_push($arreglo_variedades, [
                        'variedad' => $variedad,
                        'cajas' => $cajas,
                        'ramos' => $ramos,
                        'tallos' => $tallos,
                        'cosecha' => $cosecha,
                        'desecho' => $desecho,
                        'rendimiento' => $rendimiento,
                        'calibre' => $calibre,
                    ]);
                }
            }
        }
        if ($periodo == 'semanal') {
            if ($view == 'acumulado') {
                $cant_verde = 0;
                foreach ($labels as $dia) {
                    $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                    if ($verde != '') {
                        $cajas += round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2);
                        $ramos += $verde->getTotalRamosEstandar();
                        $tallos += $verde->total_tallos();
                        $desecho += $verde->desecho();
                        $rendimiento += $verde->getRendimiento();
                        $calibre += round($verde->total_tallos() / $verde->getTotalRamosEstandar(), 2);
                        $cant_verde++;
                    }
                }

                $desecho = $cant_verde > 0 ? round($desecho / $cant_verde, 2) : 0;
                $rendimiento = $cant_verde > 0 ? round($rendimiento / $cant_verde, 2) : 0;
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
                        $desecho += $verde->desechoByVariedad($target->id_variedad);
                        $rendimiento += $verde->getRendimientoByVariedad($target->id_variedad);
                        $calibre += $verde->calibreByVariedad($target->id_variedad);
                        $cant_verde++;
                    }
                }

                $desecho = $cant_verde > 0 ? round($desecho / $cant_verde, 2) : 0;
                $rendimiento = $cant_verde > 0 ? round($rendimiento / $cant_verde, 2) : 0;
                $calibre = $cant_verde > 0 ? round($calibre / $cant_verde, 2) : 0;
            }
            if ($view == 'todas_variedades') {
                foreach ($target as $variedad) {
                    $cant_verde = 0;
                    $cajas = 0;
                    $ramos = 0;
                    $tallos = 0;
                    $cosecha = 0;
                    $desecho = 0;
                    $rendimiento = 0;
                    $calibre = 0;
                    foreach ($labels as $dia) {
                        $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                        if ($verde != '') {
                            $cajas += round($verde->getTotalRamosEstandarByVariedad($variedad->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                            $ramos += $verde->getTotalRamosEstandarByVariedad($variedad->id_variedad);
                            $tallos += $verde->tallos_x_variedad($variedad->id_variedad);
                            $cosecha += $verde->total_tallos_recepcionByVariedad($variedad->id_variedad);
                            //$desecho += $verde->desechoByVariedad($variedad->id_variedad);
                            $rendimiento += $verde->getRendimientoByVariedad($variedad->id_variedad);
                            $calibre += $verde->calibreByVariedad($variedad->id_variedad);
                            $cant_verde++;
                        }
                    }

                    //$desecho = $cant_verde > 0 ? round($desecho / $cant_verde, 2) : 0;
                    $desecho = $cosecha > 0 ? 100 - round((($tallos * 100) / $cosecha), 2) : 0;
                    $rendimiento = $cant_verde > 0 ? round($rendimiento / $cant_verde, 2) : 0;
                    $calibre = $cant_verde > 0 ? round($calibre / $cant_verde, 2) : 0;

                    array_push($arreglo_variedades, [
                        'variedad' => $variedad,
                        'cajas' => $cajas,
                        'ramos' => $ramos,
                        'tallos' => $tallos,
                        'cosecha' => $cosecha,
                        'desecho' => $desecho,
                        'rendimiento' => $rendimiento,
                        'calibre' => $calibre,
                    ]);
                }
            }
        }

        return view('adminlte.crm.postcocecha.partials.secciones.indicadores._' . $view, [
            'labels' => $labels,
            'target' => $target,
            'periodo' => $periodo,
            'cajas' => $cajas,
            'ramos' => $ramos,
            'tallos' => $tallos,
            'desecho' => $desecho,
            'rendimiento' => $rendimiento,
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
            $labels = DB::table('clasificacion_verde as v')
                ->select('v.id_clasificacion_verde')
                ->where('v.fecha_ingreso', '>=', $desde)
                ->where('v.fecha_ingreso', '<=', $hasta)
                ->get();

            $cosecha = 0;
            $clasificacion = 0;
            foreach ($labels as $item) {
                $verde = getClasificacionVerde($item->id_clasificacion_verde);
                $cosecha += $verde->total_tallos_recepcionByVariedad($variedad->id_variedad);
                $clasificacion += $verde->tallos_x_variedad($variedad->id_variedad);    // posible optimizacion relacionada con la fecha de trabajo en vez de la hora de la clasificacion verde
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
                ->get();

        }

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
        $arreglo_variedades = [];

        $array_cajas = [];
        $array_ramos = [];
        $array_tallos = [];
        $array_desecho = [];
        $array_rendimiento = [];
        $array_calibre = [];
        if ($periodo == 'diario') {
            if ($view == 'acumulado') {
                foreach ($labels as $dia) {
                    $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                    if ($verde != '') {
                        array_push($array_cajas, round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2));
                        array_push($array_ramos, $verde->getTotalRamosEstandar());
                        array_push($array_tallos, $verde->total_tallos());
                        array_push($array_desecho, $verde->desecho());
                        array_push($array_rendimiento, $verde->getRendimiento());
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
                        array_push($array_desecho, $verde->desechoByVariedad($target->id_variedad));
                        array_push($array_rendimiento, $verde->getRendimientoByVariedad($target->id_variedad));
                        array_push($array_calibre, $verde->calibreByVariedad($target->id_variedad));
                    }
                }
            }
            if ($view == 'todas_variedades') {
                foreach ($target as $variedad) {
                    $array_cajas = [];
                    $array_ramos = [];
                    $array_tallos = [];
                    $array_desecho = [];
                    $array_rendimiento = [];
                    $array_calibre = [];
                    foreach ($labels as $dia) {
                        $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                        if ($verde != '') {
                            array_push($array_cajas, round($verde->getTotalRamosEstandarByVariedad($variedad->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2));
                            array_push($array_ramos, $verde->getTotalRamosEstandarByVariedad($variedad->id_variedad));
                            array_push($array_tallos, $verde->tallos_x_variedad($variedad->id_variedad));
                            array_push($array_desecho, $verde->desechoByVariedad($variedad->id_variedad));
                            array_push($array_rendimiento, $verde->getRendimientoByVariedad($variedad->id_variedad));
                            array_push($array_calibre, $verde->calibreByVariedad($variedad->id_variedad));
                        }
                    }

                    array_push($arreglo_variedades, [
                        'variedad' => $variedad,
                        'cajas' => $array_cajas,
                        'ramos' => $array_ramos,
                        'tallos' => $array_tallos,
                        'desecho' => $array_desecho,
                        'rendimiento' => $array_rendimiento,
                        'calibre' => $array_calibre,
                    ]);
                }
            }
        }

        //dd($arreglo_variedades);

        return view('adminlte.crm.postcocecha.partials.secciones.grafica._' . $view, [
            'target' => $target,
            'labels' => $labels,
            'periodo' => $periodo,
            'arreglo_variedades' => $arreglo_variedades,
            'array_cajas' => $array_cajas,
            'array_ramos' => $array_ramos,
            'array_tallos' => $array_tallos,
            'array_desecho' => $array_desecho,
            'array_rendimiento' => $array_rendimiento,
            'array_calibre' => $array_calibre,
        ]);

    }
}