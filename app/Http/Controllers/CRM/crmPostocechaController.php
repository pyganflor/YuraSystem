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
        $desecho = 0;
        $rendimiento = 0;
        $calibre = 0;
        if ($periodo == 'diario') {
            if ($view == 'acumulado') {
                $cant_verde = 0;
                foreach ($labels as $dia) {
                    $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                    if ($verde != '') {
                        $cajas += round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2);
                        $ramos += $verde->getTotalRamosEstandar();
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
                        $desecho += $verde->desechoByVariedad($target->id_variedad);
                        //$rendimiento += $verde->getRendimiento();
                        $calibre += $verde->calibreByVariedad($target->id_variedad);
                        $cant_verde++;
                    }
                }

                $desecho = $cant_verde > 0 ? round($desecho / $cant_verde, 2) : 0;
                $rendimiento = $cant_verde > 0 ? round($rendimiento / $cant_verde, 2) : 0;
                $calibre = $cant_verde > 0 ? round($calibre / $cant_verde, 2) : 0;
            }
            if ($view == 'todas_variedades') {
                $cajas_array = [];
                $ramos_array = [];
                $desecho_array = [];
                //$rendimiento_array = [];
                $calibre_array = [];
                $cant_verde = 0;
                foreach ($target as $variedad) {
                    foreach ($labels as $dia) {
                        $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
                        if ($verde != '') {
                            $cajas += round($verde->getTotalRamosEstandarByVariedad($target->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                            $ramos += $verde->getTotalRamosEstandarByVariedad($target->id_variedad);
                            $desecho += $verde->desechoByVariedad($target->id_variedad);
                            //$rendimiento += $verde->getRendimiento();
                            $calibre += $verde->calibreByVariedad($target->id_variedad);
                            $cant_verde++;
                        }
                    }
                }

                $desecho = $cant_verde > 0 ? round($desecho / $cant_verde, 2) : 0;
                $rendimiento = $cant_verde > 0 ? round($rendimiento / $cant_verde, 2) : 0;
                $calibre = $cant_verde > 0 ? round($calibre / $cant_verde, 2) : 0;
            }
        }

        return view('adminlte.crm.postcocecha.partials.secciones.indicadores._' . $view, [
            'labels' => $labels,
            'target' => $target,
            'periodo' => $periodo,
            'cajas' => $cajas,
            'ramos' => $ramos,
            'desecho' => $desecho,
            'rendimiento' => $rendimiento,
            'calibre' => $calibre,
        ]);

    }

    public
    function buscar_reporte_cosecha_comparacion(Request $request)
    {
        $desde = '1990-01-01';
        if ($request->desde != '')
            $desde = $request->desde;
        $hasta = date('Y-m-d');
        if ($request->hasta != '')
            $hasta = $request->hasta;


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

        return view('adminlte.crm.postcocecha.partials.secciones.comparacion._inicio', [
            'desde' => $desde,
            'hasta' => $hasta
        ]);

    }

    public
    function buscar_reporte_cosecha_chart(Request $request)
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

        return view('adminlte.crm.postcocecha.partials.secciones.grafica._' . $view, [
            'target' => $target,
            'periodo' => $periodo,
        ]);

    }
}