<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Comprobante;
use yura\Modelos\Submenu;

class EtiquetaFacturaController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.gestion.postcocecha.etiquetas_facturas.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Etiquetas por factura', 'subtitulo' => 'módulo de postcocecha']
            ]);
    }

    public function listado(Request $request){
        return view('adminlte.gestion.postcocecha.etiquetas.partials.listado',[
            'facturas' => Comprobante::where([
                ['tipo_comprobante',01],
                ['habilitado',1],
                ['fecha_emision',$request->desde]
            ])->whereIn('estado',[1,5])->select('id_comprobante')->get(),
            'vista' => 'etiqueta_factura'
        ]);
    }

    public function form_etiqueta(Request $request){
        return view('adminlte.gestion.postcocecha.etiquetas_facturas.partials.form_etiquetas',[
            'comprobante'=> getComprobante($request->id_comprobante)
        ]);
    }

    public function campos_etiqueta(Request $request){
        return view('adminlte.gestion.postcocecha.etiquetas_facturas.partials.campos_etiquetas',[
            'filas' => $request->filas,
            'empaque' => Empaque::where('tipo','C')->get()
        ]);
    }
}
