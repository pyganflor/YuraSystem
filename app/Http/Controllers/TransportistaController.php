<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use yura\Modelos\Submenu;
use yura\Modelos\TipoIdentificacion;
use yura\Modelos\Transportista;
use yura\Modelos\Conductor;
use yura\Modelos\Camion;
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

       $listado = Transportista::where('estado',isset($request->estado) ? $request->estado : 1);

       if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($bus) {
           $q->Where('transportista.nombre_empresa', 'like', '%' . $bus . '%');
       });

       $listado = $listado->orderBy('transportista.nombre_empresa', 'asc')->paginate(10);

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

   public function update_estado(Request $request){
       $objTransportista = Transportista::find($request->id_transportista);
       $objTransportista->estado = $request->estado == 1 ? 0 : 1;
       if($objTransportista->save()){
           $accion = $request->estado == 1 ? "desactivado" : "activado";
           $success = true;
           $msg = '<div class="alert alert-success text-center">' .
               '<p> Se ha '.$accion.' la empresa transportista ' . $objTransportista->nombre . '  exitosamente</p>'
               . '</div>';
           bitacora('transportista', $request->id_transportista, 'U', 'se ha '.$accion.' satisfactoriamenteist la empresa transportista '.$objTransportista->nombre.'');
       }else{
           $accion = $request->estado == 1 ? "desactivar" : "activar";
           $success = false;
           $msg = '<div class="alert alert-warning text-center">' .
               '<p> Ha ocurrido un problema al '.$accion.' la empresa de transporte</p>'
               . '</div>';
       }
       return [
           'mensaje' => $msg,
           'success' => $success
       ];
   }

   public function list_camiones_conductores(Request $request){
       return view('adminlte.gestion.postcocecha.marcas.partials.listado_camiones_coductores',[
           'id_transportista' => $request->id_transportista,
           'conductores' =>Conductor::where('id_transportista',$request->id_transportista)->orderBy('estado','desc')->get(),
           'camiones' => Camion::where('id_transportista',$request->id_transportista)->orderBy('estado','desc')->get()
       ]);
   }

   public function add_camion(Request $request){
        return view('adminlte.gestion.postcocecha.transportistas.form.add_camion',[
            'data_camion' => Camion::where('id_camion',$request->id_camion)->first()
        ]);
   }

   public function add_conductor(Request $request){
       return view('adminlte.gestion.postcocecha.transportistas.form.add_conductor',[
           'data_conductor' => Conductor::where('id_conductor',$request->id_conductor)->first(),
           'dataTipoIdentificacion' => TipoIdentificacion::where('estado',1)->get(),
       ]);
   }

   public function store_camion(Request $request){
       $valida = Validator::make($request->all(), [
           'modelo' => 'required',
           'placa' => 'required',
       ]);

       if (!$valida->fails()) {
           $msg = '';
           if(empty($request->id_camion)){
               $objTransportista = new Camion;
               $palabra = 'Inserción';
               $accion   = 'I';
           }else{
               $objTransportista = Camion::find($request->id_camion);
               $palabra = 'Actualización';
               $accion   = 'U';
           }

           $objTransportista->modelo = $request->modelo;
           $objTransportista->placa = $request->placa;
           $objTransportista->id_transportista = $request->id_transportista;

           if ($objTransportista->save()) {
               $model = Camion::all()->last();
               $success = true;
               $msg = '<div class="alert alert-success text-center">' .
                   '<p> Se ha guardado el camión exitosamente</p>'
                   . '</div>';
               bitacora('camion', $model->id_camion, $accion, $palabra . ' satisfactoria de una nuevo camión');
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

   public function update_estado_camion(Request $request){
       $objCamion = Camion::find($request->id_camion);
       $objCamion->estado = $request->estado == 1 ? 0 : 1;
       if($objCamion->save()){
           $accion = $request->estado == 1 ? "desactivado" : "activado";
           $success = true;
           $msg = '<div class="alert alert-success text-center">' .
               '<p> Se ha '.$accion.' el camión ' . $objCamion->modelo . '  exitosamente</p>'
               . '</div>';
           bitacora('camion', $request->id_camion, 'U', 'se ha '.$accion.' satisfactoriamenteist la empresa transportista '.$objCamion->modelo.'');
       }else{
           $accion = $request->estado == 1 ? "desactivar" : "activar";
           $success = false;
           $msg = '<div class="alert alert-warning text-center">' .
               '<p> Ha ocurrido un problema al '.$accion.' el camión, intente nuevamente</p>'
               . '</div>';
       }
       return [
           'mensaje' => $msg,
           'success' => $success
       ];
   }

    public function store_conductor(Request $request){
        $valida = Validator::make($request->all(), [
            'nombre' => 'required',
            'identificacion' => 'required',
            'tipo_identificacion' => 'required',
        ]);

        if (!$valida->fails()) {
            $msg = '';
            if(empty($request->id_conductor)){
                $objConductor = new Conductor();
                $palabra = 'Inserción';
                $accion   = 'I';
            }else{
                $objConductor = Conductor::find($request->id_conductor);
                $palabra = 'Actualización';
                $accion   = 'U';
            }

            $objConductor->nombre = $request->nombre;
            $objConductor->ruc = $request->identificacion;
            $objConductor->tipo_identificacion = $request->tipo_identificacion;
            $objConductor->id_transportista = $request->id_transportista;

            if ($objConductor->save()) {
                $model = Camion::all()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado el conductor exitosamente</p>'
                    . '</div>';
                bitacora('conductor', $model->id_conductor, $accion, $palabra . ' satisfactoria de una nuevo conductor');
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

   public function update_estado_conductor(Request $request){
       $objConductor = Conductor::find($request->id_conductor);
       $objConductor->estado = $request->estado == 1 ? 0 : 1;
       if($objConductor->save()){
           $accion = $request->estado == 1 ? "desactivado" : "activado";
           $success = true;
           $msg = '<div class="alert alert-success text-center">' .
               '<p> Se ha '.$accion.' el conductor ' . $objConductor->nombre . '  exitosamente</p>'
               . '</div>';
           bitacora('conductor', $request->id_conductor, 'U', 'se ha '.$accion.' satisfactoriamente el conductor '.$objConductor->nombre.'');
       }else{
           $accion = $request->estado == 1 ? "desactivar" : "activar";
           $success = false;
           $msg = '<div class="alert alert-warning text-center">' .
               '<p> Ha ocurrido un problema al '.$accion.' el conductor, intente nuevamente</p>'
               . '</div>';
       }
       return [
           'mensaje' => $msg,
           'success' => $success
       ];
   }
}
