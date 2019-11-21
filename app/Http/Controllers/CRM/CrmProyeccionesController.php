<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Indicador;
use yura\Modelos\Rol;
use yura\Modelos\Submenu;

class CrmProyeccionesController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.crm.proyecciones.inicio',[
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'text' => ['titulo'=>'Dashboard','subtitulo'=>'Proyecciones'],
            'indicador' => Indicador::whereIn('nombre',['DP1','DP2','DP3','DP4','DP5','DP6','DP7','DP8','DP9'])->select('valor')->get()
        ]);
    }

    public function desgloseIndicador(Request $request){
        return view('adminlte.crm.proyecciones.partials.modal_cosechado',[
            //$data =
        ]);
    }
}
