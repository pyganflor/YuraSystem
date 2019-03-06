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
    public function inicio(Request $request){
        return view('adminlte.gestion.postcocecha.precio.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Precios', 'subtitulo' => 'módulo de postcocecha']
            ]);
    }

    public function buscar(Request $request){
        return view('adminlte.gestion.postcocecha.precio.partials.listado',[
            'especificacion' => Especificacion::where([['tipo','N'],['estado',1]])->paginate(20),
            'clientes' => Cliente::join('detalle_cliente as dc','cliente.id_cliente','dc.id_cliente')->where('dc.estado',1)->orderBy('dc.nombre','asc')->paginate(20)
        ]);
    }

    public function form_asignar_precio(Request $request){
        return view('adminlte.gestion.postcocecha.precio.form.add_precio_especificacion',[
            'clientes' => DetalleCliente::where('estado',1)->select('nombre','id_cliente')->get(),
            'cliente_pedido_especificacion' => ClientePedidoEspecificacion::where('id_especificacion',$request->id_especificacion)->get()
        ]);
    }

    public function add_input(Request $request){
        return DetalleCliente::where('estado',1)->select('nombre','id_cliente')->get();
    }

    public function store_precio(Request $request){

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
        }else {
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
