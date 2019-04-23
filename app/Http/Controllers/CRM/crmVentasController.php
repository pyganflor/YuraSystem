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

        return view('adminlte.crm.ventas.inicio', [
            'today' => $today,
            'semanal' => $semanal,
        ]);
    }
}