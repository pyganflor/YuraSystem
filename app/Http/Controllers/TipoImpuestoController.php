<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Impuesto;
use yura\Modelos\Submenu;
use yura\Modelos\TipoImpuesto;
use Validator;
use DB;

class TipoImpuestoController extends Controller
{
    public function index(Request $request){
        return view('adminlte.gestion.configuracion_facturacion.tipo_impuesto.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Facturación', 'subtitulo' => 'tipos de impuesto'],
                'impuestos' => Impuesto::all()
            ]);
    }

    public function buscar_tipo_impuesto(Request $request){

        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);
        $listado = DB::table('tipo_impuesto as ti');

        $listado->where('ti.estado', $request->estado != '' ? $request->estado : 1);

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($bus) {
            $q->orWhere('ti.codigo', 'like', '%' . $bus . '%');
            $q->orWhere('ti.porcentaje', 'like', '%' . $bus . '%');
        });

        $listado = $listado->orderBy('ti.id_tipo_impuesto', 'desc')->where('codigo_impuesto',!empty($request->codigo_impuesto) ? $request->codigo_impuesto : 2)->get();

        $datos = [
            'listado' => $listado
        ];

        return view('adminlte.gestion.configuracion_facturacion.tipo_impuesto.partials.listado', $datos);
    }

    public function add_tipo_impuesto(Request $request){
        return view('adminlte.gestion.configuracion_facturacion.tipo_impuesto.form.add_tipo_impuesto',[
            'data_tipo_impuesto' => TipoImpuesto::where('id_tipo_impuesto',$request->id_tipo_impuesto)->first(),
            'impuestos'          => Impuesto::all()

        ]);
    }

    public function store_tipo_impuesto(Request $request){

        $valida = Validator::make($request->all(), [
            'porcentaje' => 'required',
            'codigo'     => 'required|numeric|unique:tipo_impuesto,codigo',
            'impuesto'   => 'required',
            'descripcion'=> 'required'
        ]);

        if (!$valida->fails()) {
            if(empty($request->id_tipo_impuesto)){
                $objTipoIva = new TipoImpuesto;
                $palabra = 'Inserción';
                $accion   = 'I';
            }else{
                $objTipoIva = TipoImpuesto::find($request->id_tipo_impuesto);
                $palabra = 'Actualización';
                $accion   = 'U';
            }

            $objTipoIva->codigo          = $request->codigo;
            $objTipoIva->porcentaje      = $request->porcentaje;
            $objTipoIva->codigo_impuesto = $request->impuesto;
            $objTipoIva->descripcion     = $request->descripcion;

            if ($objTipoIva->save()) {
                $model = TipoImpuesto::all()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado el tipo de impuesto ' . $objTipoIva->nombre . '  exitosamente</p>'
                    . '</div>';
                bitacora('tipo_impuesto', $model->id_tipo_impuesto, $accion, $palabra . ' satisfactoria de un nuevo tipo de impuesto');
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

    public function actualizar_estado_tipo_impuesto(Request $request){

        $model = TipoImpuesto::find($request->id_tipo_impuesto);
        if ($model != '') {
            $model->estado = $model->estado == 1 ? 0 : 1;
            if ($model->save()) {
                bitacora('tipo_impuesto', $model->id_tipo_impuesto, 'U', 'Actualización satisfactoria del estado del tipo de impuesto'. $model->nombre);

                return [
                    'success' => true,
                    'estado' => $model->estado == 1 ? true : false,
                    'mensaje' => '',
                ];
            } else {
                return [
                    'success' => false,
                    'estado' => '',
                    'mensaje' => '<div class="alert alert-info text-center">Ha ocurrido un problema al guardar en el sistema</div>',
                ];
            }
        } else {
            return [
                'success' => false,
                'estado' => '',
                'mensaje' => '<div class="alert alert-info text-center">No se ha encontrado en el sistema el parámetro</div>',
            ];
        }
    }

    public function get_tipo_impuestos(Request $request){
        return TipoImpuesto::where('codigo_impuesto',$request->codigo_impuesto)
            ->select('codigo','porcentaje','descripcion')->get();
    }
}
