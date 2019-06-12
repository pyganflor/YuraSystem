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
}
