<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Submenu;
use yura\Modelos\Cliente;
use Validator;
use yura\Modelos\DatosExportacion;
use yura\Modelos\ClienteDatoExportacion;
use DB;

class DatosExportacionController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.gestion.postcocecha.datos_exportacion.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'text' => ['titulo'=>'Datos de exportación','subtitulo'=>'módulo de postcocecha']
        ]);
    }

    public function buscar(Request $request)
    {
       // dd($request->all());
        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);
        $mi_busqueda_toupper = mb_strtoupper($bus);
        $mi_busqueda_tolower = mb_strtolower($bus);

        $listado = DB::table('dato_exportacion as de')
            ->where('de.estado',!isset($request->estado) ? 1 : $request->estado);

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($mi_busqueda_toupper,$mi_busqueda_tolower) {
            $q->Where('de.nombre', 'like', '%' . $mi_busqueda_toupper . '%');
            $q->orWhere('de.nombre', 'like', '%' . $mi_busqueda_tolower . '%');
        });

        $listado = $listado->orderBy('de.nombre', 'asc')->get();
        $datos = [
            'listado' => $listado
        ];
        return view('adminlte.gestion.postcocecha.datos_exportacion.partials.listado', $datos);
    }

    public function add_dato_exportacion(Request $request){
        return view('adminlte.gestion.postcocecha.datos_exportacion.form.add_dato_exportacion');
    }

    public function add_input_dato_exportacion(Request $request){

        return view('adminlte.gestion.postcocecha.datos_exportacion.form.partials.input_dato_exportacion',[
            'cant_rows'=> $request->cant_rows,
            'dato_exportacion'=> DatosExportacion::where('id_dato_exportacion',$request->id_dato_exportacion)->first()
        ]);
    }

    public function store_datos_exportacion(Request $request){
        $valida = Validator::make($request->all(), [
            'arrDatosExportacion' => 'required',
        ]);

        if (!$valida->fails()) {
            $msg='';
            foreach ($request->arrDatosExportacion as $datosExportacion){
                empty($datosExportacion['id_dato_exportacion']) ? $objDatoExportacion = new DatosExportacion : $objDatoExportacion = DatosExportacion::find($datosExportacion['id_dato_exportacion']);
                $objDatoExportacion->nombre = $datosExportacion['nombre'];
                if($objDatoExportacion->save()) {
                    $model = DatosExportacion::all()->last();
                    bitacora('dato_exportacion', $model->id_dato_exportacion, 'I', 'Inserción satisfactoria de un nuevo dato de exportación');
                    $success = true;
                    $msg .= '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado el dato de exportación '. $objDatoExportacion->nombre .'  exitosamente</p>'
                        . '</div>';
                } else {
                    $success = false;
                    $msg .= '<div class="alert alert-warning text-center">' .
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

    public function update_estado_datos_exportacion(Request $request){
        $request->estado == 1 ? $estado = 0 : $estado = 1;
        $objDatoExportacion = DatosExportacion::find($request->id_dato_exportacion);
        $objDatoExportacion->estado = $estado;
        $msg ='';
        if($objDatoExportacion->save()) {
            $model = DatosExportacion::all()->last();
            $success = true;
            $msg .= '<div class="alert alert-success text-center">' .
                '<p> Se ha desactivado el dato de exportación exitosamente</p>'
                . '</div>';
            bitacora('dato_exportacion', $model->id_dato_exportacion, 'U', 'Actualización satisfactoria del estado del dato de exportación'.$model->nombre.'');
        } else {
            $success = false;
            $msg .= '<div class="alert alert-warning text-center">' .
                '<p> Ha ocurrido un problema al actualizar la información al sistema</p>'
                . '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function form_asignacion_dato_exportacion(Request $request){
        return view('adminlte.gestion.postcocecha.datos_exportacion.form.asignar_dato_exportacion',[
            'listado' => Cliente::join('detalle_cliente as dc','cliente.id_cliente','dc.id_cliente')
            ->where([
                ['dc.estado',1],
                ['cliente.estado',1]
            ])->select('dc.nombre','cliente.id_cliente')->orderBy('dc.nombre','asc')->get(),
            'id_dato_exportacion' => $request->id_dato_exportacion,
            'asginacion' => ClienteDatoExportacion::where('id_dato_exportacion',$request->id_dato_exportacion)->select('id_cliente')->get()
        ]);
    }

    public function asignar_dato_exportacion(Request $request){

        if($request->check == "true"){
            $objClienteDatoExportacion = new ClienteDatoExportacion;
            $objClienteDatoExportacion->id_cliente        = $request->id_cliente;
            $objClienteDatoExportacion->id_dato_exportacion = $request->id_dato_exportacion;
            $cliente = getDatosCliente($request->id_cliente)->select('nombre')->first();
            if($objClienteDatoExportacion->save()){
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha agregado exitosamente el dato de exportación al cliente '.$cliente->nombre.'</p>'
                    . '</div>';
                $success = true;
            }else{
                $msg = '<div class="alert alert-danger text-center">' .
                    '<p> hubo un error asignando el dato de exportación al cliente '.$cliente->nombre.', intente nuevamente</p>'
                    . '</div>';
                $success = false;
            }
        }else{

            $objClienteDatoExportacion = ClienteDatoExportacion::where([
                ['id_cliente',$request->id_cliente],
                ['id_dato_exportacion',$request->id_dato_exportacion]
            ]);

            if($objClienteDatoExportacion->delete()){
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha eliminado exitosamente el dato de exportación al cliente</p>'
                    . '</div>';
                $success = true;
            }else{
                $msg = '<div class="alert alert-danger text-center">' .
                    '<p> hubo un error eliminando el dato de exportación al cliente, intente nuevamente</p>'
                    . '</div>';
                $success = false;
            }
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

}
