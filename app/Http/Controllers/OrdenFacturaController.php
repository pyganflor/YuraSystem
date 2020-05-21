<?php

namespace yura\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Validator;
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
                'text' => ['titulo' => 'Ordenar facturas', 'subtitulo' => 'módulo de ventas'],
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
                ['comprobante.ficticio',0],
                ['p.id_configuracion_empresa',$request->id_configuracion_empresa],
            ])->join('envio as e','comprobante.id_envio','e.id_envio')
                ->join('pedido as p', 'e.id_pedido','p.id_pedido')->orderBy('comprobante.secuencial','asc')
                ->whereBetween('comprobante.fecha_emision',[$request->fecha_desde,$request->fecha_hasta])
                ->get(),
        ]);
    }

    public function update_secuencial_factura(Request $request){
        $valida = Validator::make($request->all(), [
            'arr_comprobante.*.secuencial' => 'required',
            'arr_comprobante.*.id_comprobante' => 'required',
        ],[
            'arr_comprobante.*.secuencial.required' => 'No se logro capturar el numero de la factura a actualizar',
            'arr_comprobante.*.id_comprobante.required' => 'No se logro capturar el identificador de la factura a actualizar'
        ]);

        if (!$valida->fails()) {
            $msg="";
            foreach ($request->arr_comprobante as $comprobante) {
                $objComprobante = Comprobante::find($comprobante['id_comprobante']);
                $objComprobante->update(['secuencial'=>$comprobante['secuencial']]);
                if($objComprobante){
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se han actualizado los números de factura seleccionados exitosamente </p>'
                        . '</div>';
                }else{
                    $success = false;
                    $msg .= '<div class="alert alert-danger text-center">' .
                        '<p> No se ha actualizado el numero de factura '.$comprobante['secuencial'].', intente nuevamente </p>'
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
