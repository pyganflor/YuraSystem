<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use yura\Modelos\Submenu;
use yura\Modelos\Transportista;

class TransportistaController extends Controller
{
   public function incio(Request $request){
       return view('adminlte.gestion.postcocecha.transportistas.inicio',
       [
           'url' => $request->getRequestUri(),
           'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
           'text' => ['titulo' => 'Transportistas', 'subtitulo' => 'módulo de postcosecha']
       ]);
   }

   public function buscar(Request $request){

       $busqueda = $request->has('nombre') ? espacios($request->nombre) : '';
       $bus = str_replace(' ', '%%', $busqueda);

       $listado = Transportista::where('esatdo',isset($request->estado) ? $request->estado : 1);

       if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($bus) {
           $q->Where('transportista.nombre_empresa', 'like', '%' . $bus . '%');
       });

       $listado = $listado->orderBy('transportista.nombre_empresa', 'asc')->paginate(20);

       $datos = [
           'listado' => $listado
       ];
       return view('adminlte.gestion.postcocecha.transportistas.partials.listado', $datos);
   }

   public function create_transportista(Request $request){
        return view('adminlte.gestion.postcocecha.transportistas.form.add_transportista',[
            'dataTransportista' =>  Transportista::where('id_transportista',$request->id_transportista)->first()
        ]);
   }

   public function store_transportista(Request $request){
       $valida = Validator::make($request->all(), [
           'nombre_empresa' => 'required',
           'ruc' => 'required',
           'encargado' => 'required',
           'ruc_encargado' => 'required',
           'telefono_encargado' => 'required',
           'direccion_empresa' => 'required',
       ]);

       if (!$valida->fails()) {
           $msg = '';
           if(empty($request->id_transportista)){
               $objTransportista = new Transportista;
               $palabra = 'Inserción';
               $accion   = 'I';
           }else{
               $objTransportista = Transportista::find($request->id_transportista);
               $palabra = 'Actualización';
               $accion   = 'U';
           }

           $objTransportista->nombre_empresa = $request->nombre_empresa;
           $objTransportista->ruc = $request->ruc;
           $objTransportista->encargado = $request->encargado;
           $objTransportista->ruc_encargado = $request->ruc_encargado;
           $objTransportista->telefono_encargado = $request->telefono_encargado;
           $objTransportista->direccion_empresa = $request->direccion_empresa;

           if ($objTransportista->save()) {
               $model = Transportista::all()->last();
               $success = true;
               $msg = '<div class="alert alert-success text-center">' .
                   '<p> Se ha guardado la empresa transportista ' . $objTransportista->nombre . '  exitosamente</p>'
                   . '</div>';
               bitacora('transportista', $model->id_transportista, $accion, $palabra . ' satisfactoria de una nueva empresa transportista');
           }else{
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
}
