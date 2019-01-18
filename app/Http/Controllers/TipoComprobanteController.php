<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Submenu;
use yura\Modelos\marca;
use yura\Modelos\TipoComprobante;
use Validator;
use DB;

class TipoComprobanteController extends Controller
{
    public function index(Request $request){
        return view('adminlte.gestion.configuracion_facturacion.tipo_comprobantes.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Facturación', 'subtitulo' => 'tipo_comprobantes de facturación']
            ]);
    }

    public function buscar_tipo_comprobantes(Request $request){

        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);

        $listado = DB::table('tipo_comprobante as c');

        $listado->where('c.estado', $request->estado != '' ? $request->estado : 1);

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($bus) {
            $q->orWhere('c.codigo', 'like', '%' . $bus . '%');
            $q->orWhere('c.nombre', 'like', '%' . $bus . '%');
        });

        $listado = $listado->orderBy('c.nombre', 'asc')->get();

        $datos = [
            'listado' => $listado
        ];

        return view('adminlte.gestion.configuracion_facturacion.tipo_comprobantes.partials.listado', $datos);
    }

    public function add_tipo_comprobantes(Request $request){
        return view('adminlte.gestion.configuracion_facturacion.tipo_comprobantes.forms.add_tipo_comprobante',[
            'data_comprobante' => TipoComprobante::where('id_tipo_comprobante',$request->id_comprobante)->first()
        ]);
    }
    
    public function store_tipo_comprobantes(Request $request){

        $valida = Validator::make($request->all(), [
            'nombre' => 'required',
            'codigo' => 'required|numeric|unique:tipo_comprobante,codigo',
        ]);

        if (!$valida->fails()) {
            if(empty($request->id_comprobante)){
                $objComprobante = new TipoComprobante;
                $palabra = 'Inserción';
                $accion   = 'I';
            }else{
                $objComprobante = TipoComprobante::find($request->id_comprobante);
                $palabra = 'Actualización';
                $accion   = 'U';
            }
            $objComprobante->codigo = $request->codigo;
            $objComprobante->nombre = $request->nombre;

            if ($objComprobante->save()) {
                $model = TipoComprobante::all()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado el tipo de comprobante ' . $objComprobante->nombre . '  exitosamente</p>'
                    . '</div>';
                bitacora('tipo_comprobante', $model->id_comprobante, $accion, $palabra . ' satisfactoria de un nuevo comprobante');
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

    public function actualizar_estado_tipo_comprobantes(Request $request){

        $model = TipoComprobante::find($request->id_comprobante);
        if ($model != '') {
            $model->estado = $model->estado == 1 ? 0 : 1;
            if ($model->save()) {
                bitacora('comprobante', $model->id_comprobante, 'U', 'Actualización satisfactoria del estado del comprobante'. $model->nombre);

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
