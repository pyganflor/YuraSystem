<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Comprobante;
use yura\Modelos\Submenu;

class EtiquetaController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.gestion.postcocecha.etiquetas.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Etiquetas', 'subtitulo' => 'módulo de postcocecha']
            ]);
    }

    public function listado(Request $request){
        return view('adminlte.gestion.postcocecha.etiquetas.partials.listado',[
            'facturas'=> Comprobante::where([
                ['estado',5],
                ['tipo_comprobante','01'],
                ['habilitado',1]
            ])->select('id_comprobante')->get()
        ]);
    }
}
