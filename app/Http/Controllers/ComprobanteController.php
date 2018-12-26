<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Submenu;
use yura\Modelos\marca;
use yura\Modelos\Comprobante;
use Validator;
use DB;

class ComprobanteController extends Controller
{
    public function index(Request $request){
        return view('adminlte.gestion.facturacion.comprobantes.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Clientes', 'subtitulo' => 'mmódulo de postcocecha']
            ]);
    }

    public function buscar_comprobantes(Request $request){

        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);

        $listado = DB::table('comprobante as c')
            ->where('c.estado', 1);

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($bus) {
            $q->orWhere('c.codigo', 'like', '%' . $bus . '%');
            $q->orWhere('c.nombre', 'like', '%' . $bus . '%');
        });

        $listado = $listado->orderBy('c.nombre', 'asc')->get();

        $datos = [
            'listado' => $listado
        ];

        return view('adminlte.gestion.facturacion.comprobantes.partials.listado', $datos);
    }

    public function add_comprobantes(Request $request){
        return view('adminlte.gestion.facturacion.comprobantes.forms.add_comprobante',[
            'data_comprobante' => Comprobante::where('id_comprobante',$request->id_comprobante)->first()
        ]);
    }
    
    public function store_comprobantes(Request $request){

        $valida = Validator::make($request->all(), [
            'nombre' => 'required',
            'codigo' => 'required',
        ]);

        if (!$valida->fails()) {
            if(empty($request->id_comprobante)){
                $objComprobante = new Comprobante;
                $palabra = 'Inserción';
                $accion   = 'I';
            }else{
                $objComprobante = Comprobante::find($request->id_comprobante);
                $palabra = 'Actualización';
                $accion   = 'U';
            }

            $objComprobante->codigo = $request->codigo;
            $objComprobante->nombre = $request->nombre;

            if ($objComprobante->save()) {
                $model = Comprobante::all()->last();
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado el comprobante ' . $objComprobante->nombre . '  exitosamente</p>'
                    . '</div>';
                bitacora('comprobante', $model->id_comprobante, $accion, $palabra . ' satisfactoria de un nuevo comprobante');
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

    public function actualizar_estado_comprobantes(Request $request){

        $model = Comprobante::find($request->id_comprobante);
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
