<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Submenu;
use yura\Modelos\TipoIva;
use Validator;
use DB;

class TipoIvaController extends Controller
{
    public function index(Request $request){
        return view('adminlte.gestion.configuracion_facturacion.tipo_iva.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Facturación', 'subtitulo' => 'tipos de iva']
            ]);
    }

    public function buscar_tipo_iva(Request $request){

        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);

        $listado = DB::table('tipo_iva as ti');

        $listado->where('ti.estado', $request->estado != '' ? $request->estado : 1);

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($bus) {
            $q->orWhere('ti.codigo', 'like', '%' . $bus . '%');
            $q->orWhere('ti.porcentaje', 'like', '%' . $bus . '%');
        });

        $listado = $listado->orderBy('ti.id_tipo_iva', 'desc')->get();

        $datos = [
            'listado' => $listado
        ];

        return view('adminlte.gestion.configuracion_facturacion.tipo_iva.partials.listado', $datos);
    }

    public function add_tipo_iva(Request $request){
        return view('adminlte.gestion.configuracion_facturacion.tipo_iva.form.add_tipo_iva',[
            'data_tipo_iva' => TipoIva::where('id_tipo_iva',$request->id_tipo_iva)->first()
        ]);
    }

    public function store_tipo_iva(Request $request){

        $valida = Validator::make($request->all(), [
            'porcentaje' => 'required',
            'codigo' => 'required|numeric|unique:tipo_iva,codigo',
        ]);

        if (!$valida->fails()) {
            if(empty($request->id_tipo_iva)){
                $objTipoIva = new TipoIva;
                $palabra = 'Inserción';
                $accion   = 'I';
            }else{
                $objTipoIva = TipoIva::find($request->id_tipo_iva);
                $palabra = 'Actualización';
                $accion   = 'U';
            }
            $objTipoIva->codigo = $request->codigo;
            $objTipoIva->porcentaje = $request->porcentaje;

            if ($objTipoIva->save()) {
                $model = TipoIva::all()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado el tipo de iva ' . $objTipoIva->nombre . '  exitosamente</p>'
                    . '</div>';
                bitacora('tipo_iva', $model->id_tipo_iva, $accion, $palabra . ' satisfactoria de un nuevo tipo de iva');
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

    public function actualizar_estado_tipo_iva(Request $request){

        $model = TipoIva::find($request->id_tipo_iva);
        if ($model != '') {
            $model->estado = $model->estado == 1 ? 0 : 1;
            if ($model->save()) {
                bitacora('tipo_iva', $model->id_tipo_iva, 'U', 'Actualización satisfactoria del estado del tipo de iva'. $model->nombre);

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
}
