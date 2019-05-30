<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use yura\Modelos\Envio;
use yura\Modelos\FacturaClienteTercero;
use yura\Modelos\Submenu;
use yura\Modelos\Comprobante;

class FueController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.crm.fue.inicio',[
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'text' => ['titulo'=>'Actualización de Datos de Exportación','subtitulo'=>'módulo CRM']
        ]);
    }

    public function buscar(Request $request){
        return view('adminlte.crm.fue.partials.listado',[
            'facturas' => Comprobante::where([
                'tipo_comprobante'=>'01',
                'estado'=>'5',
                'fecha_emision' => ($request->get('busqueda') != null && !empty($request->get('busqueda')) ? $request->get('busqueda') : now()->toDateString())
            ])->get()
        ]);
    }

    public function actualizar_fue(Request $request){
        $valida = Validator::make($request->all(), [
            'arr_datos.*.codigo_dae' => 'required',
            'arr_datos.*.guia_madre' => 'required',
            'arr_datos.*.guia_hija' => 'required',
            'arr_datos.*.manifiesto' => 'required',
            'arr_datos.*.dae' => 'required',
            'arr_datos.*.peso' => 'required',
            'arr_datos.*.id_comprobante' => 'required'
        ]);

        if (!$valida->fails()) {
            $success = false;
            $msg = '<div class="alert alert-warning text-center">' .
                '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                . '</div>';
            //dd($request->all());
            foreach($request->arr_datos as $item){
                $factura_tercero = getFacturaClienteTercero(getComprobante($item['id_comprobante'])->id_envio);
                $comprobante = getComprobante($item['id_comprobante']);
                $objEnvio = Envio::find($comprobante->id_envio);

                if($factura_tercero != null){
                    $objFacturatercero = FacturaClienteTercero::where('id_envio',$comprobante->id_envio);
                    $objFacturatercero->update([ 'codigo_dae'=> $item['codigo_dae'], 'dae' => $item['dae'] ]);
                }else{
                    $objEnvio->codigo_dae =  $item['codigo_dae'];
                    $objEnvio->dae = $item['dae'];
                }

                $objEnvio->guia_madre = $item['guia_madre'];
                $objEnvio->guia_hija = $item['guia_hija'];

                $objComprobante = Comprobante::find($item['id_comprobante']);
                $objComprobante->peso = $item['peso'];
                $objComprobante->manifiesto = $item['manifiesto'];

                if($objEnvio->save() && $objComprobante->save()){
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se han actualizado los datos exitosamente</p>'
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


    public function reporte_fue(){
        return view('adminlte.crm.fue.partials.listado_inicio');
    }
    public function reporte_fue_filtrado(Request $request){
        return view('adminlte.crm.fue.partials.listado_filtrado');
    }
}
