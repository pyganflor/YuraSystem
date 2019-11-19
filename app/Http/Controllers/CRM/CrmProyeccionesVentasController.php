<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Rol;
use yura\Modelos\Submenu;

class CrmProyeccionesVentasController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.crm.proyecciones_venta.inicio',[
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'text' => ['titulo'=>'Dashboard','subtitulo'=>'Proyección de ventas']
        ]);
    }
}
