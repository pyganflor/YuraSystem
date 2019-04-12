<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Submenu;
use yura\Modelos\Usuario;
use Validator;

class EmisionComprobanteController extends Controller
{
    public function index(Request $request){
        return view('adminlte.gestion.configuracion_facturacion.emision_comprobantes.incio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'text' => ['titulo'=>'Emisión pdf','subtitulo'=>'módulo de emision de pdf'],
            'exist_punto_acceso' => Usuario::where('punto_acceso','!=',null)->get(),
            'usuario'  => Usuario::where('id_rol','!=',1)->get(),
        ]);
    }

    public function add_punto_emision(Request $request){

        return view('adminlte.gestion.configuracion_facturacion.emision_comprobantes.form.add_punto_emision',[
            'cant_punto_emision' => $request->cant_punto_emision == 0 ? $request->cant_punto_emision = 1 : $request->cant_punto_emision,
            'usuario'  => Usuario::where('id_rol','!=',1)->get(),
        ]);
    }

    public function store_punto_emision(Request $request){

        $valida = Validator::make($request->all(), [
            'arrPuntosEmision' => 'required|Array',
        ]);

        if (!$valida->fails()) {

            $existPuntoAcceso = Usuario::where('punto_acceso','!=',null)->get();
            foreach ($existPuntoAcceso as $item) {
                $objUsuario = Usuario::find($item->id_usuario);
                $objUsuario->punto_acceso=null;
                $objUsuario->save();
            }
            foreach ($request->arrPuntosEmision as $key => $punto_emsion) {

                $objUsuario = Usuario::where('id_usuario',$punto_emsion[1]);

                if($objUsuario->update(["punto_acceso" => $punto_emsion[0]])){
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se han guardado los puntos de acceso exitosamente</p>'
                        . '</div>';
                    bitacora('usuario', $punto_emsion[1], 'U','usuario actualizado satisfactoriamente');
                } else {
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }

            }
        }
        else {
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
        ];
    }
}
