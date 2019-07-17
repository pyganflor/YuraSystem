<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use yura\Modelos\DetalleEspecificacionEmpaque;
use yura\Modelos\Submenu;
use yura\Modelos\ProductoYuraVenture;

class ProductoVentureController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.gestion.configuracion_facturacion.productos_venture.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Administración', 'subtitulo' => 'Productos venture'],
                'presentacion_venture' => getCodigoArticuloVenture(),
                'presentaciones_yuraSystem' => DetalleEspecificacionEmpaque::select('id_variedad','id_clasificacion_ramo','tallos_x_ramos','longitud_ramo','id_unidad_medida')->distinct()->get(),
            ]);
    }

    public function vincularProductosVenture(Request $request){
        $valida = Validator::make($request->all(), [
            'presentacion_yura_sistem' => 'required',
            'presentacion_venture' => 'required',
        ],[
            'presentacion_yura_sistem.required' => 'Debe seleccionar la presentación creada en el Yura',
            'presentacion_venture.required' => 'Debe seleccionar la presentación creada en el Venture'
        ]);

        if (!$valida->fails()) {

            $objProductoYuraVenture = new ProductoYuraVenture;
            $objProductoYuraVenture->presentacion_yura = $request->presentacion_yura_sistem;
            $objProductoYuraVenture->codigo_venture = $request->presentacion_venture;
            if($objProductoYuraVenture->save()){
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se han vinvulado las presentaciones con éxito</p>'
                    . '</div>';
                $modelProductoYuraVenture = ProductoYuraVenture::all()->last();
                bitacora('productos_yura_venture', $modelProductoYuraVenture->id_productos_yura_venture, 'I', 'Vinculación de un producto del venture con el yura');
            }else{
                $success = false;
                $msg = '<div class="alert alert-danger text-center">' .
                    '<p> Ha ocurrido un error al vincular las presentaciones, intente nuevamente</p>'
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

    public function listadoProdcutosVinculados(){
        return view('adminlte.gestion.configuracion_facturacion.productos_venture.partials.listado',[
            'productos_vinculados' => getProductosVinculadosYuraVenture()
        ]);
    }

    public function deleteProdcutosVinculados(Request $request){
        $success = false;
        $msg = '<div class="alert alert-danger text-center">' .
            '<p> Ha ocurrido un error al eliminar la vinculación del producto</p>'
            . '</div>';

        if(ProductoYuraVenture::destroy($request->id_vinculacion)){
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p>Se ha eliminado la vinculación del producto con éxito</p>'
                . '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }
}
