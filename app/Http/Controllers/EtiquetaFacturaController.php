<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use yura\Modelos\DetalleEtiquetaFactura;
use yura\Modelos\EtiquetaFactura;
use yura\Modelos\Comprobante;
use yura\Modelos\Pedido;
use yura\Modelos\Submenu;
use yura\Modelos\Empaque;

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
        return view('adminlte.gestion.postcocecha.etiquetas_facturas.partials.listado',[
            'pedidos' => Pedido::where([
                ['pedido.fecha_pedido',$request->desde],
                ['pedido.estado',true],
                ['dc.estado',true]
            ])->join('envio as e','pedido.id_pedido','e.id_pedido')
                ->join('cliente as cl','pedido.id_cliente','cl.id_cliente')
                ->join('detalle_cliente as dc','cl.id_cliente','dc.id_cliente')
                ->leftJoin('comprobante as c','e.id_envio','c.id_envio')
                ->select('c.secuencial','dc.nombre as cli_nombre','pedido.id_pedido','c.estado as estado_comprobante')
                ->where('pedido.id_configuracion_empresa', $request->id_configuracion_empresa)->get(),
            'vista' => 'etiqueta_factura'
        ]);
    }

    public function form_etiqueta(Request $request){
        return view('adminlte.gestion.postcocecha.etiquetas_facturas.partials.form_etiquetas',[
            'pedido'=> Pedido::find($request->id_pedido)
        ]);
    }

    public function campos_etiqueta(Request $request){
        return view('adminlte.gestion.postcocecha.etiquetas_facturas.partials.campos_etiquetas',[
            'filas' => $request->filas,
            'empaque' => Empaque::where('tipo','C')->get(),
            'pedido' => getPedido($request->id_pedido)
        ]);
    }

    public function store_etiqueta_factura(Request $request){

        $valida = Validator::make($request->all(), [
            'data.*.siglas' => 'required',
            'data.*.cajas' => 'required',
            'data.*.et_inicial' => 'required',
            'data.*.et_final' => 'required',
            'data.*.id_det_esp_emp' => 'required',
            'data.*.empaque' => 'required',
            'id_pedido' => 'required'
        ], [
            'data.*.siglas.required' => 'Debe escribir las siglas de la etiqueta',
            'data.*.cajas.required' => 'Debe escribir la cantidad de cajas',
            'data.*.et_inicial.required' => 'Debe escribir la etiqueta inicial',
            'data.*.et_final.required' => 'Debe escribir la etiqueta final',
            'data.*.id_det_esp_emp.required' => 'Debe seleccionar la presentación',
            'data.*.empaque.required' => 'Debe seleccionar el empaque',
            'id_pedido.required' => 'No se obtuvo el identificacdor del pedido',
        ]);

        if (!$valida->fails()) {

            $objEtiquetaFactura = new EtiquetaFactura;
            $objEtiquetaFactura->id_pedido = $request->id_pedido;

            if($objEtiquetaFactura->save()){
                $modelEtiquetaFactura = EtiquetaFactura::all()->last();
                $x = 0;
                foreach ($request->data as $item) {
                    $objDetalleEtiquetaFactura = new DetalleEtiquetaFactura;
                    $objDetalleEtiquetaFactura->id_etiqueta_factura = $modelEtiquetaFactura->id_etiqueta_factura;
                    $objDetalleEtiquetaFactura->cantidad = $item['cajas'];
                    $objDetalleEtiquetaFactura->empaque = $item['empaque'];
                    $objDetalleEtiquetaFactura->id_detalle_especificacion_empaque = substr($item['id_det_esp_emp'],0,-1);
                    $objDetalleEtiquetaFactura->siglas = $item['siglas'];
                    $objDetalleEtiquetaFactura->et_inicial = $item['et_inicial'];
                    $objDetalleEtiquetaFactura->et_final = $item['et_final'];
                    if($objDetalleEtiquetaFactura->save()) $x++;
                }
                if($x == count($request->data)){
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se han guardado exitosamente las etiquetas</p>'
                        . '</div>';
                }else{
                    EtiquetaFactura::destroy($request->id_comprobante);
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar las etiquetas, intente de nuevo</p>'
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

    public function delete_etiqueta_factura(Request $request){
        $success = false;
        $msg = '<div class="alert alert-danger text-center">' .
            '<p> Ha ocurrido un problema al eliminar las etiquetas, intente de nuevo</p>'
            . '</div>';
        $etiqueta_comprobante = EtiquetaFactura::where('id_pedido',$request->id_pedido);
        if($etiqueta_comprobante->delete()){
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se han eliminado exitosamente las etiquetas</p>'
                . '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];

    }
}
