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
        $pedidos_today = Pedido::All()->where('estado', 1)->where('fecha_pedido', date('Y-m-d'));
        $cajas = 0;
        $valor = 0;
        foreach ($pedidos_today as $i) {
            $cajas += $i->getCajas();
            $valor += $i->getPrecio();
        }
        $today = [
            'cajas' => $cajas,
            'valor' => $valor
        ];
        return view('adminlte.crm.ventas.inicio', [
            'today' => $today
        ]);
    }
}