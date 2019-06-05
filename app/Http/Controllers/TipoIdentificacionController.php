<?php

namespace yura\Http\Controllers;
use yura\Modelos\TipoIdentificacion;
use yura\Modelos\Submenu;
use DB;
use Validator;
use Illuminate\Http\Request;

class TipoIdentificacionController extends Controller
{
    public function index(Request $request){
        return view('adminlte.gestion.configuracion_facturacion.tipo_identificacion.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Facturación', 'subtitulo' => 'tipos de identificación']
            ]);
    }

    public function buscar_tipo_identificacion(Request $request){

        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);

        $listado = DB::table('tipo_identificacion as ti');

        $listado->where('ti.estado', $request->estado != '' ? $request->estado : 1);

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($bus) {
            $q->orWhere('ti.codigo', 'like', '%' . $bus . '%');
            $q->orWhere('ti.nombre', 'like', '%' . $bus . '%');
        });

        $listado = $listado->orderBy('ti.nombre', 'asc')->get();

        $datos = [
            'listado' => $listado
        ];

        return view('adminlte.gestion.configuracion_facturacion.tipo_identificacion.partials.listado', $datos);
    }

    public function add_tipo_identificacion(Request $request){
        return view('adminlte.gestion.configuracion_facturacion.tipo_identificacion.form.add_tipo_identificacion',[
            'data_tipo_identificacion' => TipoIdentificacion::where('id_tipo_identificacion',$request->id_tipo_identificacion)->first()
        ]);
    }

    public function store_tipo_identificacion(Request $request){

        $valida = Validator::make($request->all(), [
            'nombre' => 'required',
            'codigo' => 'required|numeric|unique:tipo_identificacion,codigo',
        ]);

        if (!$valida->fails()) {
            if(empty($request->id_tipo_identificacion)){
                $objTipoIdentificacion = new TipoIdentificacion;
                $palabra = 'Inserción';
                $accion   = 'I';
            }else{
                $objTipoIdentificacion = TipoIdentificacion::find($request->id_tipo_identificacion);
                $palabra = 'Actualización';
                $accion   = 'U';
            }

            $objTipoIdentificacion->codigo = $request->codigo;
            $objTipoIdentificacion->nombre = $request->nombre;

            if ($objTipoIdentificacion->save()) {
                $model = TipoIdentificacion::all()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado el tipo de identificacion ' . $objTipoIdentificacion->nombre . '  exitosamente</p>'
                    . '</div>';
                bitacora('tipo_identificacion', $model->id_tipo_identificacion, $accion, $palabra . ' satisfactoria de un nuevo tipo de identificacion');
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

    public function actualizar_estado_tipo_identificacion(Request $request){

        $model = TipoIdentificacion::find($request->id_tipo_identificacion);
        if ($model != '') {
            $model->estado = $model->estado == 1 ? 0 : 1;
            if ($model->save()) {
                bitacora('tipo_identificacion', $model->id_tipo_identificacion, 'U', 'Actualización satisfactoria del estado del tipo de identificaión'. $model->nombre);

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
