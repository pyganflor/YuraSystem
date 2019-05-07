<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\Pedido;
use yura\Modelos\Semana;

class crmVentasController extends Controller
{
    public function inicio(Request $request)
    {
        /* =========== TODAY ============= */
        $pedidos_today = Pedido::All()->where('estado', 1)->where('fecha_pedido', date('Y-m-d'));
        $cajas = 0;
        $valor = 0;
        foreach ($pedidos_today as $p) {
            $cajas += $p->getCajas();
            $valor += $p->getPrecio();
        }
        $today = [
            'cajas' => $cajas,
            'valor' => $valor
        ];

        /* =========== SEMANAL ============= */
        $pedidos_semanal = Pedido::All()->where('estado', 1)
            ->where('fecha_pedido', '>=', opDiasFecha('-', 7, date('Y-m-d')))
            ->where('fecha_pedido', '<=', opDiasFecha('-', 1, date('Y-m-d')));
        $valor = 0;
        $cajas = 0;
        $tallos = 0;
        foreach ($pedidos_semanal as $p) {
            $valor += $p->getPrecio();
            $cajas += $p->getCajas();
            $tallos += $p->getTallos();
        }
        $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
        $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
        $precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

        $semanal = [
            'valor' => $valor,
            'cajas' => $cajas,
            'precio_x_ramo' => $precio_x_ramo,
            'precio_x_tallo' => $precio_x_tallo,
        ];

        /* ======= AÑOS ======= */
        $annos = DB::table('historico_ventas')
            ->select('anno')->distinct()
            ->get();

        return view('adminlte.crm.ventas.inicio', [
            'today' => $today,
            'semanal' => $semanal,
            'annos' => $annos,
        ]);
    }

    public function filtrar_graficas(Request $request)
    {
        $desde = $request->desde;
        $hasta = $request->hasta;

        $arreglo_annos = [];
        if ($request->has('annos')) {
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

                    if ($request->x_cliente == 'true' && $request->id_cliente != '') {
                        $query = $query->where('id_cliente', '=', $request->id_cliente)
                            ->get();
                        $count_query = $count_query->where('id_cliente', '=', $request->id_cliente)
                            ->get();
                    } else {
                        $query = $query->get();
                        $count_query = $count_query->get();
                    }

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
            $view = 'graficas';

            if ($request->diario == 'true') {
                $periodo = 'diario';

                $array_valor = [];
                $array_cajas = [];
                $array_precios = [];
                if ($request->total == 'true') {
                    $fechas = DB::table('pedido as p')
                        ->select('p.fecha_pedido as dia')->distinct()
                        ->where('p.estado', '=', 1)
                        ->where('p.fecha_pedido', '>=', $request->desde)
                        ->where('p.fecha_pedido', '<=', $request->hasta)
                        ->orderBy('p.fecha_pedido')
                        ->get();

                    foreach ($fechas as $f) {
                        $pedidos_semanal = Pedido::All()->where('estado', 1)
                            ->where('fecha_pedido', '=', $f->dia);
                        $valor = 0;
                        $cajas = 0;
                        $tallos = 0;
                        foreach ($pedidos_semanal as $p) {
                            $valor += $p->getPrecio();
                            $cajas += $p->getCajas();
                            $tallos += $p->getTallos();
                        }
                        $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
                        $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
                        $precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

                        array_push($array_valor, $valor);
                        array_push($array_cajas, $cajas);
                        array_push($array_precios, $precio_x_ramo);
                    }
                } else if ($request->x_cliente == 'true' && $request->id_cliente != '') {
                    $fechas = DB::table('pedido as p')
                        ->select('p.fecha_pedido as dia')->distinct()
                        ->where('p.estado', '=', 1)
                        ->where('p.fecha_pedido', '>=', $request->desde)
                        ->where('p.fecha_pedido', '<=', $request->hasta)
                        ->where('p.id_cliente', '=', $request->id_cliente)
                        ->orderBy('p.fecha_pedido')
                        ->get();

                    foreach ($fechas as $f) {
                        $pedidos_semanal = Pedido::All()->where('estado', 1)
                            ->where('fecha_pedido', '=', $f->dia)
                            ->where('id_cliente', '=', $request->id_cliente);
                        $valor = 0;
                        $cajas = 0;
                        $tallos = 0;
                        foreach ($pedidos_semanal as $p) {
                            $valor += $p->getPrecio();
                            $cajas += $p->getCajas();
                            $tallos += $p->getTallos();
                        }
                        $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
                        $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
                        $precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

                        array_push($array_valor, $valor);
                        array_push($array_cajas, $cajas);
                        array_push($array_precios, $precio_x_ramo);
                    }
                }
                $data = [
                    'valores' => $array_valor,
                    'cajas' => $array_cajas,
                    'precios' => $array_precios,
                ];
            } else if ($request->semanal == 'true') {
                $periodo = 'semanal';

                $array_valor = [];
                $array_cajas = [];
                $array_precios = [];
                if ($request->total == 'true') {
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
                        $pedidos = Pedido::All()->where('estado', 1)
                            ->where('fecha_pedido', '>=', $semana->fecha_inicial)
                            ->where('fecha_pedido', '<=', $semana->fecha_final);
                        $valor = 0;
                        $cajas = 0;
                        $tallos = 0;

                        foreach ($pedidos as $p) {
                            $valor += $p->getPrecio();
                            $cajas += $p->getCajas();
                            $tallos += $p->getTallos();
                        }
                        $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
                        $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
                        $precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

                        array_push($array_valor, $valor);
                        array_push($array_cajas, $cajas);
                        array_push($array_precios, $precio_x_ramo);
                    }
                } else if ($request->x_cliente == 'true' && $request->id_cliente != '') {
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
                        $pedidos = Pedido::All()->where('estado', 1)
                            ->where('id_cliente', '>=', $request->id_cliente)
                            ->where('fecha_pedido', '>=', $semana->fecha_inicial)
                            ->where('fecha_pedido', '<=', $semana->fecha_final);
                        $valor = 0;
                        $cajas = 0;
                        $tallos = 0;

                        foreach ($pedidos as $p) {
                            $valor += $p->getPrecio();
                            $cajas += $p->getCajas();
                            $tallos += $p->getTallos();
                        }
                        $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
                        $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
                        $precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

                        array_push($array_valor, $valor);
                        array_push($array_cajas, $cajas);
                        array_push($array_precios, $precio_x_ramo);
                    }
                }

                $data = [
                    'valores' => $array_valor,
                    'cajas' => $array_cajas,
                    'precios' => $array_precios,
                ];
            }
        }

        return view('adminlte.crm.ventas.partials.' . $view, [
            'labels' => $fechas,
            'arreglo_annos' => $arreglo_annos,
            'data' => $data,
            'periodo' => $periodo,
        ]);
    }

    public function desglose_indicador(Request $request)
    {
        $fechas = DB::table('pedido')
            ->select('fecha_pedido as dia')->distinct()
            ->where('estado', 1)
            ->where('fecha_pedido', '>=', opDiasFecha('-', 7, date('Y-m-d')))
            ->where('fecha_pedido', '<=', opDiasFecha('-', 1, date('Y-m-d')))
            ->orderBy('fecha_pedido')
            ->get();

        $arreglo_variedades = [];
        foreach (getVariedades() as $v) {
            $array_valores = [];
            $array_cajas = [];
            $array_precios = [];
            $array_tallos = [];
            foreach ($fechas as $dia) {
                $pedidos_semanal = Pedido::All()->where('estado', 1)
                    ->where('fecha_pedido', '=', $dia->dia);
                $valor = 0;
                $cajas = 0;
                $tallos = 0;
                foreach ($pedidos_semanal as $p) {
                    $valor += $p->getPrecioByVariedad($v->id_variedad);
                    $cajas += $p->getCajasByVariedad($v->id_variedad);
                    $tallos += $p->getTallosByVariedad($v->id_variedad);
                }
                $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
                $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
                $precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

                array_push($array_valores, $valor);
                array_push($array_cajas, $cajas);
                array_push($array_precios, $precio_x_ramo);
                array_push($array_tallos, $precio_x_tallo);
            }
            array_push($arreglo_variedades, [
                'variedad' => $v,
                'valores' => $array_valores,
                'cajas' => $array_cajas,
                'precios' => $array_precios,
                'tallos' => $array_tallos,
            ]);
        }

        //dd($arreglo_variedades, $fechas, $request->option);

        return view('adminlte.crm.ventas.partials._desglose_indicador', [
            'labels' => $fechas,
            'arreglo_variedades' => $arreglo_variedades,
            'option' => $request->option,
        ]);
    }
}