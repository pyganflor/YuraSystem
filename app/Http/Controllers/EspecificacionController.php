<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\ClientePedidoEspecificacion;
use yura\Modelos\DetalleEspecificacionEmpaque;
use yura\Modelos\DetallePedido;
use yura\Modelos\Empaque;
use yura\Modelos\Especificacion;
use yura\Modelos\Cliente;
use DB;
use yura\Modelos\EspecificacionEmpaque;
use yura\Modelos\Submenu;
use yura\Modelos\UnidadMedida;
use yura\Modelos\Variedad;
use Validator;

class EspecificacionController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.gestion.postcocecha.especificacion.incio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Especificaciones', 'subtitulo' => 'módulo de especificaciones'],
                'clientes' => Cliente::join('detalle_cliente as dc','cliente.id_cliente','dc.id_cliente')->where('dc.estado',1)->get()
            ]);
    }

    public function listado_especificaciones(Request $request)
    {
        $busqueda   = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $id_cliente = $request->has('id_cliente') ? $request->id_cliente : '';
        $estado     = $request->has('estado') ? $request->estado : '';
        $tipo       = $request->has('tipo') ? $request->tipo : '';

        $listado = Especificacion::where([
                ['especificacion.tipo',($tipo != '' && $tipo != null) ? $tipo : 'N'],
                ['especificacion.estado', $estado != '' ? $estado : 1]
            ]);

        if($busqueda != '' && $busqueda != null)
            $listado->where('especificacion.nombre', 'like', '%' . $busqueda . '%');

        if($id_cliente != '' && $id_cliente != 'undefined')
            $listado->join('cliente_pedido_especificacion as cpe','especificacion.id_especificacion','cpe.id_especificacion')
                ->join('detalle_cliente as dc','cpe.id_cliente','dc.id_cliente')
                ->where([
                    ['cpe.id_cliente',$id_cliente],
                    ['dc.estado',1]
                ]);

        $listado = $listado->orderBy('especificacion.id_especificacion', 'desc')
            ->select('especificacion.id_especificacion','especificacion.tipo','especificacion.estado')->distinct()->paginate(20);
        //dd($listado);
        $datos = [
            'listado' => $listado,
            'variedades' => Variedad::select('nombre','id_variedad')->get(),
            'clasificacion_ramo' => ClasificacionRamo::select('nombre','id_clasificacion_ramo')->get(),
            'empaque' => Empaque::where([
                ['tipo','C'],
                ['estado',1]
            ])->select('nombre','id_empaque')->get(),
            'presentacion' => Empaque::where([
                ['tipo','P'],
                ['estado',1]
            ])->select('nombre','id_empaque')->get(),
            'unidad_medida' => UnidadMedida::where([
                ['tipo','L'],
                ['estado',1]
            ])->get()
        ];
        return view('adminlte.gestion.postcocecha.especificacion.partials.listado', $datos);
    }

    public function form_asignacion_especificacion(Request $request){
        return view('adminlte.gestion.postcocecha.especificacion.form.form_asignar_especificacion',[
            'listado' => Cliente::join('detalle_cliente as dc','cliente.id_cliente','dc.id_cliente')->where([
                ['dc.estado',1],
                ['cliente.estado',1]
            ])->orderBy('dc.nombre','asc')->get(),
            'id_especificacion' => $request->id_especificacion,
            'data_especificacion' => Especificacion::where('id_especificacion',$request->id_especificacion)->get(),
            'asginacion'  => ClientePedidoEspecificacion::where('id_especificacion',$request->id_especificacion)->select('id_cliente')->get()
        ]);
    }

    public function sotre_asignacion_especificacion(Request $request){
        $objClientePedidoEspecificacion = new ClientePedidoEspecificacion;
        $objClientePedidoEspecificacion->id_cliente        = $request->id_cliente;
        $objClientePedidoEspecificacion->id_especificacion = $request->id_especificacion;
        $detalle_cliente = getDatosCliente($request->id_cliente)->select('nombre')->first();
        if($objClientePedidoEspecificacion->save()){
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha agregado exitosamente la especificación al cliente '.$detalle_cliente->nombre.'</p>'
                . '</div>';
        }else{
            $msg = '<div class="alert alert-danger text-center">' .
                '<p> hubo un error asignando la especificación al cliente '.$detalle_cliente->nombre.', intente nuevamente</p>'
                . '</div>';
        }
        return $msg;
    }

    public function verificar_pedido_especificacion(Request $request){

        $cliente_especificacion = ClientePedidoEspecificacion::where([
            ['id_cliente',$request->id_cliente],
            ['id_especificacion',$request->id_especificacion]
        ])->select('id_cliente_pedido_especificacion')->first();
        $existDetallePedido = 0;
        if($cliente_especificacion != null)
            $existDetallePedido = DetallePedido::where('id_cliente_especificacion',$cliente_especificacion->id_cliente_pedido_especificacion)->count();
        return $existDetallePedido;
    }

    public function delete_asignacion_especificacion(Request $request){

        $existClienteEspecificacion =  DB::table('cliente_pedido_especificacion')->where([
                ['id_especificacion',$request->id_especificacion],
                ['id_cliente',$request->id_cliente]
            ])->delete();
        $detalle_cliente = getDatosCliente($request->id_cliente)->select('nombre')->first();
        return '<div class="alert alert-success text-center">' .
            '<p> Se ha eliminado la especificación al cliente '.$detalle_cliente->nombre.', con éxito</p>'
            . '</div>';
    }

    public function nueva_especificacion(Request $request){
        return view('adminlte.gestion.postcocecha.especificacion.form.form_row_especificacion',[
            'cant_row' => $request->cant_rows,
            'variedades' => Variedad::select('nombre','id_variedad')->get(),
            'clasificacion_ramo' => ClasificacionRamo::select('nombre','id_clasificacion_ramo')->get(),
            'empaque' => Empaque::where([
                ['tipo','C'],
                ['estado',1]
            ])->select('nombre','id_empaque')->get(),
            'presentacion' => Empaque::where([
                ['tipo','P'],
                ['estado',1]
            ])->select('nombre','id_empaque')->get(),
            'unidad_medida' => UnidadMedida::where([
                ['tipo','L'],
                ['estado',1]
            ])->get()
        ]);
    }

    public function store_row_especificacion(Request $request){
        //dd($request->all());
        $valida = Validator::make($request->all(), [
            'arrData' => 'required|Array',
            'modo' => 'required',
        ],['modo.required' => 'Debe seleccionar el modo de como quiere que se cree la especificación']);

        if (!$valida->fails()) {
            foreach ($request->arrData as $key => $data){
                $objEspecificacion = new Especificacion;
                $objEspecificacion->estado = 1;

                $x = 0;
                if($request->modo == 1 && $x == $key)
                    $objEspecificacion->save() ? $y = true : $y = false;

                if($request->modo == 0)
                    $objEspecificacion->save() ? $y = true : $y = false;

                $success = false;
                $msg = '<div class="alert alert-danger text-center">' .
                    '<p> Ha ocurrido un error al tratar de crear la especificación, intente nuevamente </p>'
                    . '</div>';

                if($y){
                    $modelEspecificacion = Especificacion::all()->last();
                    $objEspecificacionEmpaque = new EspecificacionEmpaque;
                    $objEspecificacionEmpaque->id_especificacion = $modelEspecificacion->id_especificacion;
                    $objEspecificacionEmpaque->id_empaque = $data['id_empaque'];
                    $objEspecificacionEmpaque->cantidad   = 1;

                    /*if($request->modo == 1 && $x == $key)
                        $objEspecificacionEmpaque->save() ? $z = true : $z = false;

                    if($request->modo == 0)
                        $objEspecificacionEmpaque->save() ? $z = true : $z = false;*/

                    if($objEspecificacionEmpaque->save()) {
                        $modelEspecificacionEmpaque = EspecificacionEmpaque::all()->last();
                        $objDetalleEspecificacionEmpaque = new DetalleEspecificacionEmpaque;
                        $objDetalleEspecificacionEmpaque->id_especificacion_empaque = $modelEspecificacionEmpaque->id_especificacion_empaque;
                        $objDetalleEspecificacionEmpaque->id_variedad = $data['id_variedad'];
                        $objDetalleEspecificacionEmpaque->id_clasificacion_ramo = $data['id_clasificacion_ramo_'];
                        $objDetalleEspecificacionEmpaque->cantidad = $data['ramos_x_caja'];
                        $objDetalleEspecificacionEmpaque->id_empaque_p = $data['id_presentacion'];
                        $objDetalleEspecificacionEmpaque->tallos_x_ramos = $data['tallos_x_ramo'];
                        $objDetalleEspecificacionEmpaque->longitud_ramo = $data['longitud'];
                        $objDetalleEspecificacionEmpaque->id_unidad_medida = $data['id_unidad_medida'];

                        if($objDetalleEspecificacionEmpaque->save()){
                            $modelDetalleEspecificacionEmpaque = DetalleEspecificacionEmpaque::all()->last();
                            $success = true;
                            $msg = '<div class="alert alert-success text-center">' .
                                '<p> Se ha agregado exitosamente la especificación </p>'
                                . '</div>';
                            bitacora('detalle_especificacion_empaque', $modelDetalleEspecificacionEmpaque->id_detalle_especificacionempaque, 'I', 'Inserción satisfactoria de un nuevo detalle de especificación de empaque');
                        }else{
                            EspecificacionEmpaque::destroy($modelEspecificacionEmpaque->id_especificacion_empaque);
                            Especificacion::destroy($modelEspecificacion->id_especificacion);
                        }
                    }else{
                        Especificacion::destroy($modelEspecificacion->id_especificacion);
                    }
                }
                $x++;
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
