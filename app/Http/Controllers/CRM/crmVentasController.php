<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\Pedido;

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
        $annos = DB::table('pedido as p')
            ->select(DB::raw('year(p.fecha_pedido) as anno'))->distinct()
            ->where('p.estado', '=', 1)
            ->get();

        return view('adminlte.crm.ventas.inicio', [
            'today' => $today,
            'semanal' => $semanal,
            'annos' => $annos,
        ]);
    }

    public function filtrar_graficas(Request $request)
    {

        if ($request->has('annos')) {
            $view = '_annos';
        } else {
            $view = 'graficas';

            if ($request->diario == 'true') {
                $fechas = DB::table('pedido as p')
                    ->select('p.fecha_pedido as dia')->distinct()
                    ->where('p.estado', '=', 1)
                    ->where('p.fecha_pedido', '>=', $request->desde)
                    ->where('p.fecha_pedido', '<=', $request->hasta)
                    ->orderBy('p.fecha_pedido')
                    ->get();

                $array_valor = [];
                $array_cajas = [];
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
                }
                $arreglos = [
                    'valores' => $array_valor,
                    'cajas' => $array_cajas,
                ];
                dd($fechas, $arreglos);
            }
        }
        dd($request->all());

        return view('adminlte.crm.ventas.partials.' . $view, [
            'labels' => $fechas,
            'arreglos' => $arreglos,
        ]);
    }
}