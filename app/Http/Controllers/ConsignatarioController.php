<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use yura\Modelos\Consignatario;
use yura\Modelos\ContactoConsignatario;
use yura\Modelos\Pais;
use yura\Modelos\Submenu;
use yura\Modelos\ClienteConsignatario;
use DB;

class ConsignatarioController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.gestion.postcocecha.consignatarios.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Consignatarios', 'subtitulo' => 'módulo clientes'],
            ]);
    }

    public function buscar_listado(Request $request){
        //dd($request->all());
        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);
        $mi_busqueda_toupper = mb_strtoupper($bus);
        $mi_busqueda_tolower = mb_strtolower($bus);

        $listado = Consignatario::where('estado',isset($request->estado) ? $request->estado : 1);

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($busqueda) {
            $q->Where('consignatario.nombre', 'like', '%' . $busqueda . '%');
        });

        $listado = $listado->orderBy('consignatario.nombre', 'asc')->paginate(20);

        $datos = [
            'listado' => $listado
        ];
        return view('adminlte.gestion.postcocecha.consignatarios.partials.listado', $datos);
    }

    public function addConsignatario(Request $request){
        return view('adminlte.gestion.postcocecha.consignatarios.from.add_consignatario',[
            'dataPais'=>Pais::orderBy('nombre','asc')->get(),
            'dataConsignatario' => Consignatario::where('id_consignatario',$request->id_consignatario)->first()
        ]);
    }

    public function storeConsignatario(Request $request){
        //dd($request->all());
        $valida = Validator::make($request->all(), [
            'nombre'              => 'required',
            'identificacion'      => 'required',
            'pais'                => 'required',
            'ciudad'              => 'required',
            'correo'              => 'required',
            'telefono'            => 'required',
            'direccion'           => 'required',
        ]);

        if(!$valida->fails()) {

            if(empty($request->id_consignatario)){
                $objConsignatario = new Consignatario;
                $letra = "I";
                $accion = "Inserción";
            }else{
                $objConsignatario = Consignatario::find($request->id_consignatario);
                $letra = "U";
                $accion = "Actualización";
            }
            $objConsignatario->nombre  = $request->nombre;
            $objConsignatario->direccion  = $request->direccion;
            $objConsignatario->telefono  = $request->telefono;
            $objConsignatario->correo  = $request->correo;
            $objConsignatario->codigo_pais  = $request->pais;
            $objConsignatario->ciudad  = $request->ciudad;
            $objConsignatario->identificacion  = $request->identificacion;

            if($objConsignatario->save()) {
                $model = Consignatario::all()->last();
                if($request->contacto == "true"){
                    if(empty($request->id_contacto_consignatario)){
                        $objContatoConsignatario = new ContactoConsignatario;
                        $letra = "I";
                        $accion = "Inserción";
                    }else{
                        $objContatoConsignatario = ContactoConsignatario::find($request->id_contacto_consignatario);
                        $letra = "U";
                        $accion = "Actualización";
                    }
                    $objContatoConsignatario->id_consignatario = $model->id_consignatario;
                    $objContatoConsignatario->nombre = $request->nombre_contacto;
                    $objContatoConsignatario->identificacion = $request->identificacion_contacto;
                    $objContatoConsignatario->telefono = $request->telefono_contacto;
                    $objContatoConsignatario->codigo_pais = $request->pais_contacto;
                    $objContatoConsignatario->ciudad = $request->ciudad_contacto;
                    $objContatoConsignatario->correo = $request->correo_contacto;
                    $objContatoConsignatario->direccion = $request->direccion_contacto;
                    if($objContatoConsignatario->save()){
                        $model = ContactoConsignatario::all()->last();
                        bitacora('contacto_consignatario', $model->id_contacto_consignatario, $letra, $accion.' satisfactoria del contacto para el consignatario '.$request->nombre);
                    }
                }
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado el consignatario ' . $request->nombre . '  exitosamente</p>'
                    . '</div>';
                bitacora('consignatario', $model->id_consignatario, $letra, $accion.' satisfactoria de un nuevo consignatario');
            }else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
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

    public function updateEstadoConsignatario(Request $request){
        $msg = '<div class="alert alert-danger text-center">
                <p> Hubo un error al actualizar el estado del consignatario, intente nuevamente </p>
                 </div>';
        $success = false;
        $objEmpaque = Consignatario::find($request->id_consignatario);
        $objEmpaque->estado = $request->estado == 1 ? 0 : 1;
        $request->estado == 1 ? $accion = "desactivado" : $accion = "activado";
        if($objEmpaque->save()){
            $msg = '<div class="alert alert-success text-center">
                <p> El consignatario ha sido '.$accion.' con éxito</p>
                 </div>';
            $success =true;
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

}
