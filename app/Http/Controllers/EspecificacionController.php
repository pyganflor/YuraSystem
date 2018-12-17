<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\Cliente;
use yura\Modelos\Empaque;
use yura\Modelos\Variedad;
use yura\Modelos\Especificacion;
use yura\Modelos\EspecificacionEmpaque;
use yura\Modelos\DetalleEspecificacionEmpaque;
use yura\Modelos\ClientePedidoEspecificacion;
use yura\Modelos\UnidadMedida;
use Validator;
use DB;
use Storage as Almacenamiento;


class EspecificacionController extends Controller
{
    public function admin_especificaciones(Request $request)
    {
        return view('adminlte.gestion.postcocecha.clientes.partials.especificaciones', [
            'cliente' => Cliente::find($request->id_cliente),
        ]);
    }

    public function add_especificacion(Request $request)
    {
        return view('adminlte.gestion.postcocecha.clientes.partials.forms.add_especificacion', [
            'cliente' => Cliente::find($request->id_cliente),
        ]);
    }

    public function ver_especificacion(Request $request)
    {
        return view('adminlte.gestion.postcocecha.clientes.partials._forms.add_especificacion', [
            'especificacion' => Especificacion::find($request->id_especificacion),
        ]);
    }

    public function cargar_form_especificacion_empaque(Request $request)
    {
        return view('adminlte.gestion.postcocecha.clientes.partials.forms._detalles', [
            'cajas' => Empaque::All()->where('tipo', '=', 'C')->where('estado', '=', 1),
            'cant_detalles' => $request->cant_detalles,
        ]);
    }

    public function cargar_form_detalle_especificacion_empaque(Request $request)
    {
        return view('adminlte.gestion.postcocecha.clientes.partials.forms._desgloses', [
            'envolturas' => Empaque::All()->where('tipo', '=', 'E')->where('estado', '=', 1),
            'presentaciones' => Empaque::All()->where('tipo', '=', 'P')->where('estado', '=', 1),
            'pesajes' => ClasificacionRamo::All()->where('estado', '=', 1),
            'variedades' => Variedad::All()->where('estado', '=', 1),
            'pos_form_detalles' => $request->pos_form_detalles,
            'cant_desgloses' => $request->cant_desgloses,
        ]);
    }

    public function store_especificacion(Request $request)
    {


        $validaDataGeneral = Validator::make($request->all(), [
            'nombre' => 'required',
            'descripcion' => 'required',
        ]);

        if (!$validaDataGeneral->fails()) {

            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se han guardado exitosamente todos los datos</p>'
                . '</div>';
            if (empty($request->id_especificacion)) {

                $objEspecificacion = new Especificacion;
                $accion = 'Inserción';
                $accionLetra = 'I';

            } else {

                $objEspecificacion = Especificacion::find($request->id_especificacion);
                $accion = 'Actualización';
                $accionLetra = 'U';
            }

           /* $objEspecificacion->id_cliente  = $request->id_cliente;*/
            $objEspecificacion->nombre      = $request->nombre;
            $objEspecificacion->descripcion = $request->descripcion;

            if ($objEspecificacion->save()) {

                $modelEspcificacion = Especificacion::all()->last();

                bitacora('especificacion', $modelEspcificacion->nombre, $accionLetra, $accion . ' satisfactoria de un nuevo empaque');

                for ($i = 1; $i <= $request->cant_forms_detalles; $i++) {
                   // dd($request->input('cant_forms_desgloses_'.$i));
                    //*********** VALIDA IMAGEN ************//
                    if ($request->hasFile('imagen_' . $i)) {

                        $archivo = $request->file('imagen_' . $i);
                        $input = array('image' => $archivo);
                        $reglas = array('image' => 'required|image|mimes:jpeg,jpeg,jpg|max:2000');
                        $validacion = Validator::make($input, $reglas);

                        if ($validacion->fails()) {

                            $msg = '<div class="alert alert-danger text-center">' .
                                '<p>¡Imagen no válida!</p>' .
                                '</div>';
                            $modelEspcificacion = Especificacion::find($modelEspcificacion->id_especificacion);
                            $modelEspcificacion->delete();
                            $success = false;

                        } else {

                            $nombre_original = $archivo->getClientOriginalName();
                            $extension = $archivo->getClientOriginalExtension();
                            $imagen = "imagen_especificaciones_" . date('Y_d_m_H_i_s') . mt_rand(). "-." . $extension;
                            $r1 = Almacenamiento::disk('imagenes')->put($imagen, \File::get($archivo));
                            if (!$r1) {
                                $msg = '<div class="alert alert-danger text-center">' .
                                    '<p>¡No se pudo subir la imagen!</p>' .
                                    '</div>';
                                $success = false;

                                $modelEspcificacion = Especificacion::find($modelEspcificacion->id_especificacion);
                                $modelEspcificacion->delete();

                            }
                        }
                    }
                    //*********** FIN VALIDA IMAGEN ************//

                    //************ INSERTA Ó ACTUALIZA ESPECIFICACIÓN EMPAQUE *************//
                    if (!$request->has('id_esp_empaque_' . $i)) {
                        $objEspecificacionEmpaque = new EspecificacionEmpaque;
                        $accion = 'Inserción';
                        $accionLetra = 'I';
                    } else {
                        $objEspecificacionEmpaque = EspecificacionEmpaque::find($request->id_esp_empaque_ . $i);
                        $accion = 'Actualización';
                        $accionLetra = 'U';
                    }

                    $objEspecificacionEmpaque->id_especificacion = $modelEspcificacion->id_especificacion;
                    $objEspecificacionEmpaque->id_empaque = $request->input('id_empaque_' . $i);
                    $objEspecificacionEmpaque->cantidad = $request->input('cantidad_'. $i);
                    $objEspecificacionEmpaque->imagen = isset($imagen) ? $imagen: '';

                    if ($objEspecificacionEmpaque->save()) {

                        $modelEspcificacionEmpaque = EspecificacionEmpaque::all()->last();

                        bitacora('especificacion_empaque', $modelEspcificacion->nombre, $accionLetra, $accion . ' satisfactoria de una nueva especificación de empaque');
                        //************ FIN INSERTA Ó ACTUALIZA ESPECIFICACIÓN EMPAQUE *************//

                        //************ INSERTA Ó ACTUALIZA DETALLES ESPECIFICACION EMPAQUE (DESGLOSES) *************//
                         for ($j = 1; $j <= $request->input('cant_forms_desgloses_'.$i); $j++) {

                            if (!$request->has('id_detalle_esp_emp_' . $i . "_" . $j)) {
                                $objEspecificacionEmpaqueDetalle = new DetalleEspecificacionEmpaque;
                                $accion = 'Inserción';
                                $accionLetra = 'I';
                            } else {
                                $objEspecificacionEmpaqueDetalle = DetalleEspecificacionEmpaque::find($request->id_detalle_espemp_ . $i . "_" . $j);
                                $accion = 'Actualización';
                                $accionLetra = 'U';
                            }

                            if($request->input('cant_forms_desgloses_'.$i) == 1){

                                if(!valida_especificacion($request->input('id_variedad_'.$i.'_'.$j),$request->input('id_clasificacion_ramo_'.$i.'_'.$j),$request->id_empaque_.$i, $request->input('cantidad_'.$i.'_'.$j))){

                                    if($accion === 'Inserción' ){
                                        $objEspecificacionEmpaqueDelete = EspecificacionEmpaque::where('id_especificacion', $modelEspcificacion->id_especificacion);
                                        $objEspecificacionEmpaqueDelete->delete();

                                        $modelEspcificacion = Especificacion::find($modelEspcificacion->id_especificacion);
                                        $modelEspcificacion->delete();

                                        $success = false;
                                        $msg = '<div class="alert alert-warning text-center">' .
                                            '<p> No se puede crear un paquete con las especificaciones antes descritas ya que sobrepasa la cantidad de ramos por empaque configuradas o no existe el detalle del empaque</p>';
                                    }

                                }

                            }

                            $objEspecificacionEmpaqueDetalle->id_especificacion_empaque = $modelEspcificacionEmpaque->id_especificacion_empaque;
                            $objEspecificacionEmpaqueDetalle->id_variedad = $request->input('id_variedad_' . $i . '_' . $j);
                            $objEspecificacionEmpaqueDetalle->id_clasificacion_ramo = $request->input('id_clasificacion_ramo_' . $i . '_' . $j);
                            $objEspecificacionEmpaqueDetalle->cantidad = $request->input('cantidad_' . $i . '_' . $j);
                            $objEspecificacionEmpaqueDetalle->id_empaque_e = $request->input('id_empaque_e_' . $i . '_' . $j);
                            $objEspecificacionEmpaqueDetalle->id_empaque_p = $request->input('id_empaque_p_' . $i . '_' . $j);
                            !empty($request->input('tallos_X_ramos' . $i . '_' . $j))
                                ? $objEspecificacionEmpaqueDetalle->tallos_x_ramos = $request->input('tallos_X_ramos' . $i . '_' . $j)
                                : '';


                            if ($objEspecificacionEmpaqueDetalle->save()) {

                                $modelEspecificacionEmpaqueDetalle = DetalleEspecificacionEmpaque::all()->last();
                                bitacora('detalle_especificacionempaque', $modelEspecificacionEmpaqueDetalle->id_detalle_especificacionempaque, $accionLetra, $accion . ' satisfactoria de un nuevo detalle de especificación de empaque');

                            } else {

                                if ($accion === 'Inserción') {

                                    Almacenamiento::disk('imagenes')->delete($imagen);

                                    $objEspecificacionEmpaqueDetalleDelete = DetalleEspecificacionEmpaque::find($modelEspcificacionEmpaque->id_especificacion_empaque);
                                    $objEspecificacionEmpaqueDetalleDelete->delete();

                                    $objEspecificacionEmpaqueDelete = EspecificacionEmpaque::where('id_especificacion', $modelEspcificacion->id_especificacion);
                                    $objEspecificacionEmpaqueDelete->delete();

                                    $modelEspcificacion = Especificacion::find($modelEspcificacion->id_especificacion);
                                    $modelEspcificacion->delete();

                                }
                                $success = false;
                                $msg = '<div class="alert alert-warning text-center">' .
                                    '<p> Ha ocurrido un problema al guardar el desglose del detalle de la especificación</p>';
                            }
                         }
                        //************ FIN INSERTA Ó ACTUALIZA DETALLES ESPECIFICACION EMPAQUE (DESGLOSES) *************//
                    } else {

                        if ($accion === 'Inserción') {

                            Almacenamiento::disk('imagenes')->delete($imagen);
                            $objEspecificacionEmpaqueDelete = EspecificacionEmpaque::where('id_especificacion', $modelEspcificacion->id_especificacion);

                             $objEspecificacionEmpaqueDelete->delete();

                            $modelEspcificacion = Especificacion::find($modelEspcificacion->id_especificacion);
                            $modelEspcificacion->delete();
                        }

                        $success = false;
                        $msg = '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la información al sistema</p>';
                    }
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar el nombre o descripción de al especificación</p>';
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($validaDataGeneral->errors()->all() as $mi_error) {
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

    public function ver_especificaciones(Request $request){
        return view('adminlte.gestion.postcocecha.clientes.partials.list_especificaciones');
    }

    public function  listar_especificaciones(Request $request){

        $listado = DB::table('especificacion')->orderBy('nombre', 'asc')->paginate(10);

        $datos = [
            'listado' => $listado,
            'id_especificaciones' => ClientePedidoEspecificacion::where('id_cliente',$request->id_cliente)->select('id_especificacion')->get()
        ];

        return view('adminlte.gestion.postcocecha.clientes.partials.table_especificaciones', $datos);
    }

    public function update_especificaciones(Request $request){
       // dd($request->all());
        $objEspecificaciones = Especificacion::find($request->id_especificacion);
        $objEspecificaciones->estado = ($request->estado == 1) ?  0 :  1;
        if($objEspecificaciones->save()){
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha actualizado exitosamente el estado</p>'
                . '</div>';
        }else{
            $success = false;
            $msg = '<div class="alert alert-danger text-center">' .
                '<p> Hubo un error al actualizar el estado, intente nuevamente</p>'
                . '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];

    }

    public  function asignar_especificacion(Request $request){

        $success = false;
        $msg = '<div class="alert alert-success text-center">' .
            '<p> Hubo un error al procesar la petición, intente nuevamente</p>'
            . '</div>';

        if($request->accion == 1){

            $existClienteEspecificacion =  DB::table('cliente_pedido_especificacion')->where([
                ['id_cliente', $request->id_cliente],
                ['id_especificacion',  $request->id_especificacion]
            ])->get();

            if(count($existClienteEspecificacion) > 0){
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> La especificaion ya esta asignada a este cliente</p>'
                    . '</div>';

            }else{
                $objClientePedidoEspecificacion = new ClientePedidoEspecificacion;
                $objClientePedidoEspecificacion->id_cliente          = $request->id_cliente;
                $objClientePedidoEspecificacion->id_especificacion = $request->id_especificacion;

                if($objClientePedidoEspecificacion->save()){
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha agregado exitosamente la especificaión al cliente</p>'
                        . '</div>';
                }
            }
        }else{
            $objClientePedidoEspecificacion =  ClientePedidoEspecificacion::where([
                ['id_cliente', $request->id_cliente],
                ['id_especificacion',  $request->id_especificacion]
            ]);

            if($objClientePedidoEspecificacion->delete()){
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha eliminado la especificaión del cliente exitosamente</p>'
                    . '</div>';
            }
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];

    }

    public function obtener_calsificacion_ramos(Request $request){
        return UnidadMedida::where('tipo',$request->tipo_unidad_medida)
        ->join('clasificacion_ramo as cr','unidad_medida.id_unidad_medida','=','cr.id_unidad_medida')
        ->get();
    }
}
