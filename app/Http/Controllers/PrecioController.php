<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Cliente;
use yura\Modelos\DetalleCliente;
use yura\Modelos\Submenu;
use yura\Modelos\ClientePedidoEspecificacion;
use yura\Modelos\Especificacion;

class PrecioController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.postcocecha.precio.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Precios', 'subtitulo' => 'módulo de postcosecha']
            ]);
    }

    public function buscar(Request $request)
    {
        return view('adminlte.gestion.postcocecha.precio.partials.listado', [
            'especificacion' => Especificacion::where([['tipo', 'N'], ['estado', 1]])->paginate(20),
            'clientes' => Cliente::join('detalle_cliente as dc', 'cliente.id_cliente', 'dc.id_cliente')->where('dc.estado', 1)->orderBy('dc.nombre', 'asc')->paginate(20)
        ]);
    }

    public function form_asignar_precio(Request $request)
    {
        return view('adminlte.gestion.postcocecha.precio.form.add_precio_especificacion', [
            'clientes' => DetalleCliente::where('estado', 1)->select('nombre', 'id_cliente')->get(),
            'cliente_pedido_especificacion' => ClientePedidoEspecificacion::where('id_especificacion', $request->id_especificacion)->get()
        ]);
    }

    public function add_input(Request $request)
    {
        return DetalleCliente::where('estado', 1)->select('nombre', 'id_cliente')->get();
    }

    public function store_precio(Request $request)
    {
        dd($request->all());
    }
}
