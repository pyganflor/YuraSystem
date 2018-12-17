<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Cliente;
use yura\Modelos\DetalleEnvio;
use yura\Modelos\Pedido;
use DB;
use yura\Modelos\Submenu;
use yura\Modelos\DetalleCliente;
use yura\Modelos\ClientePedidoEspecificacion;
use yura\Modelos\ClienteAgenciaCarga;
use yura\Modelos\Envio;

class PedidoVentaController extends Controller
{
    public function listar_pedidos(Request $request){
        return view('adminlte.gestion.postcocecha.pedidos_ventas.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu'   => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text'     => ['titulo' => 'Pedidos', 'subtitulo' => 'módulo de pedidos'],
                'clientes' => DB::table('cliente as c')
                              ->join('detalle_cliente as dt','c.id_cliente','=','dt.id_cliente')
                              ->where('dt.estado',1) ->get(),
                'annos'   => DB::table('pedido as p')->select(DB::raw('YEAR(p.fecha_pedido) as anno'))
                             ->distinct()->get(),
            ]);
    }

    public function buscar_pedidos(Request $request){

        $busquedaCliente= $request->has('id_cliente') ? $request->id_cliente : '';
        $busquedaAnno   = $request->has('anno') ? $request->anno : '';
        $busquedaDesde  = $request->has('desde') ? $request->desde : '';
        $busquedaHasta  = $request->has('hasta') ? $request->hasta : '';
        $listado = DB::table('pedido as p')
            ->join('cliente_pedido_especificacion as cpe', 'p.id_cliente','=','cpe.id_cliente')
            ->join('especificacion as esp','cpe.id_especificacion','=','esp.id_especificacion')
            ->join('detalle_cliente as dt','p.id_cliente','=','dt.id_cliente')
            ->select('p.*','dt.nombre','p.fecha_pedido','dt.id_cliente')->where('dt.estado',1);

        if ($request->anno != '')
            $listado = $listado->where(DB::raw('YEAR(p.fecha_pedido)'), $busquedaAnno );

        //if ($request->desde != '' && $request->hasta != '')
            $listado = $listado->whereBetween('p.fecha_pedido', [!empty($busquedaDesde) ? $busquedaDesde : '2000-01-01',!empty($busquedaHasta) ? $busquedaHasta : Pedido::select('fecha_pedido')->orderBy('fecha_pedido','desc')->take(1)->get()[0]->fecha_pedido]);

            if ($request->id_cliente != '')
            $listado = $listado->where('p.id_cliente',$busquedaCliente );

        $listado = $listado->distinct()->orderBy('p.fecha_pedido', 'desc')->simplePaginate(20);

        $datos = [
            'listado'    => $listado,
            'idCliente'  => $request->id_cliente,
        ];
        return view('adminlte.gestion.postcocecha.pedidos_ventas.partials.listado',$datos);
    }

    public function cargar_especificaciones(Request $request){
        return [
           'especificaciones'=> ClientePedidoEspecificacion::where('id_cliente',$request->id_cliente)
                ->join('especificacion as e', 'cliente_pedido_especificacion.id_especificacion','e.id_especificacion')
                ->select('cliente_pedido_especificacion.id_cliente_pedido_especificacion','e.nombre')->get(),
           'agencias_carga'=> ClienteAgenciaCarga::where('id_cliente',$request->id_cliente)
                ->join('agencia_carga as ac', 'cliente_agenciacarga.id_agencia_carga','=','ac.id_agencia_carga')
                ->select('ac.id_agencia_carga','ac.nombre')->get()
        ];
    }
}
