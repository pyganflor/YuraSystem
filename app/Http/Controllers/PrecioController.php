<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Cliente;
use yura\Modelos\DetalleCliente;
use yura\Modelos\DetalleEspecificacionEmpaque;
use yura\Modelos\Precio;
use yura\Modelos\Submenu;
use yura\Modelos\ClientePedidoEspecificacion;
use yura\Modelos\Especificacion;
use Validator;

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

    public function buscar_cliente(Request $request)
    {
        return view('adminlte.gestion.postcocecha.precio.partials.listado_cliente', [
            'clientes' => Cliente::join('detalle_cliente as dc', 'cliente.id_cliente', 'dc.id_cliente')->where('dc.estado', 1)->orderBy('dc.nombre', 'asc')->paginate(20)
        ]);
    }

    public function buscar_especificacion(Request $request)
    {
        return view('adminlte.gestion.postcocecha.precio.partials.listado_especificacion', [
            'especificacion' => Especificacion::where([['tipo', 'N'], ['estado', 1]])->paginate(20),
        ]);
    }

    public function form_asignar_precio_especificacion_cliente(Request $request)
    {
        return view('adminlte.gestion.postcocecha.precio.form.add_precio_especificacion_cliente', [
            'clientes' => DetalleCliente::where('estado', 1)->select('nombre', 'id_cliente')->get(),
            'cliente_pedido_especificacion' => ClientePedidoEspecificacion::join('especificacion as esp', 'cliente_pedido_especificacion.id_especificacion', 'esp.id_especificacion')
                ->where([
                    ['cliente_pedido_especificacion.id_especificacion', $request->id_especificacion],
                    ['tipo', 'N']
                ])->get(),
            'id_especificacion' => $request->id_especificacion
        ]);
    }

    public function form_asignar_precio_cliente_especificacion(Request $request)
    {
        return view('adminlte.gestion.postcocecha.precio.form.add_precio_cliente_especificacion', [
            'especificaciones_cliente' => ClientePedidoEspecificacion::join('especificacion as esp', 'cliente_pedido_especificacion.id_especificacion', 'esp.id_especificacion')
                ->where([
                    ['id_cliente', $request->id_cliente],
                    ['tipo', 'N']
                ])->get(),
        ]);
    }

    public function add_input(Request $request)
    {
        return DetalleCliente::where('estado', 1)->select('nombre', 'id_cliente')->get();
    }

    public function store_precio_especificacio_cliente(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'arrPrecios' => 'required|Array',
        ]);

        if (!$valida->fails()) {

            foreach ($request->arrPrecios as $data) {
                $msg = '';
                if (empty($data['id_cliente_pedido_especificacion'])) {
                    $objClientePedidioEspecificacion = new ClientePedidoEspecificacion;
                    $palabra = 'Inserción';
                    $accion = 'I';
                } else {
                    $objClientePedidioEspecificacion = ClientePedidoEspecificacion::find($data['id_cliente_pedido_especificacion']);
                    $palabra = 'Actualización';
                    $accion = 'U';
                }

                $objClientePedidioEspecificacion->id_cliente = $data['id_cliente'];
                $objClientePedidioEspecificacion->precio = $data['precio'];
                $objClientePedidioEspecificacion->id_especificacion = $request->id_especificacion;

                if ($objClientePedidioEspecificacion->save()) {
                    $model = ClientePedidoEspecificacion::all()->last();
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado la especificacion a los cliented seleccionados exitosamente</p>'
                        . '</div>';
                    bitacora('cliente_pedidio_especificacion', $model->id_cliente_pedido_especificacion, $accion, $palabra . ' satisfactoria de una especificacion_cliente');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function store_precio_cliente_especificacion(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'arrPrecios' => 'required|Array',
            'id_cliente' => 'required',
        ]);
        $msg = '';
        $success = true;
        if (!$valida->fails()) {
            foreach ($request->arrPrecios as $data) {
                $precio = getPrecioByClienteDetEspEmp($request->id_cliente, $data['id_detalle_especificacionempaque']);

                if ($precio == '') {
                    $precio = new Precio();
                    $precio->id_cliente = $request->id_cliente;
                    $precio->id_detalle_especificacionempaque = $data['id_detalle_especificacionempaque'];
                    $new = true;
                } else{
                    $precio = Precio::find($precio->id_precio);
                    $new = false;
                }

                $precio->cantidad = $data['precio'];

                if ($precio->save()) {
                    if ($new) {
                        $precio = Precio::all()->last();
                        bitacora('precio', $precio->id_precio, 'U', 'Actualización satisfactoria de los precios de las especificaciones del cliente');
                    }
                } else {
                    $success = false;
                    $det_esp = DetalleEspecificacionEmpaque::find($data['id_detalle_especificacionempaque']);
                    $msg .= '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar el precio para: ' .
                        $det_esp->variedad->siglas . ' ' . $det_esp->clasificacion_ramo . ' ' .
                        explode('|', $det_esp->especificacion_empaque->nombre)[0] . ' ' . $det_esp->empaque_p->nombre . ' ' .
                        $det_esp->cantidad . ' ramos por caja.</p>'
                        . '</div>';
                }
            }
            if ($success)
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se han actualizado los precios de las especificaciones del cliente exitosamente</p>'
                    . '</div>';
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }
}
