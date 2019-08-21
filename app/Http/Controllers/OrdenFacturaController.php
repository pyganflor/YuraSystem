<?php

namespace yura\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use yura\Modelos\Cliente;
use yura\Modelos\Comprobante;
use yura\Modelos\Submenu;

class OrdenFacturaController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.gestion.postcocecha.orden_facturas.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Ordenar facturas', 'subtitulo' => 'módulo de facturación'],
                'empresas' => getConfiguracionEmpresa(null,true)
            ]);
    }

    public function buscar_pedido_facturada_generada(Request $request){
        return view('adminlte.gestion.postcocecha.orden_facturas.partials.listado_pedido_factura_generada',[
            'comprobantes' => Comprobante::where([
                ['comprobante.estado',1],
                ['comprobante.tipo_comprobante','01'],
                ['comprobante.habilitado',1],
                ['comprobante.integrado',0],
                ['p.id_configuracion_empresa',$request->id_configuracion_empresa]
            ])->join('envio as e','comprobante.id_envio','e.id_envio')
                ->join('pedido as p', 'e.id_pedido','p.id_pedido')->orderBy('comprobante.secuencial','asc')->get(),
        ]);
    }
}
