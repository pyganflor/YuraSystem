<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Submenu;

class VentasM2Controller extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.crm.ventas_m2.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function chart_m2(Request $request)
    {
        return view('adminlte.crm.ventas_m2.partials.chart_m2', [
        ]);
    }

    public function chart_m2_anno(Request $request)
    {
        return view('adminlte.crm.ventas_m2.partials.chart_m2_anno', [
        ]);
    }
}