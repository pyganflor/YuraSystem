<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use yura\Modelos\DetalleEspecificacionEmpaque;
use yura\Modelos\Submenu;
use yura\Modelos\ProductoYuraVenture;
use Illuminate\Support\Str;

class ProductoVentureController extends Controller
{
    public function inicio(Request $request){
        $data=[];
        $det_esp_emp = DetalleEspecificacionEmpaque::select('id_variedad','id_clasificacion_ramo','tallos_x_ramos','longitud_ramo','id_unidad_medida','id_detalle_especificacionempaque')->distinct()->get();
        foreach ($det_esp_emp as $x =>  $dsp)
            if(getDetalleEspecificacionEmpaque($dsp->id_detalle_especificacionempaque)->especificacion_empaque->especificacion->tipo !== "O")
                $data[$dsp->id_variedad][] = $dsp;

        $data_completa = [];
        foreach ($data as $item)
            foreach($item as $i)
                $data_completa[] = $i;

        //$products_vinculado = getProductosVinculadosYuraVenture();
        /*$pv = [];
        foreach ($products_vinculado as $item) {
            $pv[] = Str::Slug($item->presentacion_yura);
        }*/
        
        return view('adminlte.gestion.configuracion_facturacion.productos_venture.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Administración', 'subtitulo' => 'Productos venture'],
                'presentacion_venture' => getCodigoArticuloVenture(),
                'presentaciones_yura_system' => $data_completa,
                'productos_vinculados' =>[],// $pv,
                'empresas' =>getConfiguracionEmpresa(null, true)
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
            $objProductoYuraVenture->id_configuracion_empresa = $request->id_configuracion_empresa;
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

    public function listadoProdcutosVinculados(Request $request){
        return view('adminlte.gestion.configuracion_facturacion.productos_venture.partials.listado',[
            'productos_vinculados' => getProductosVinculadosYuraVenture($request->id_configuracion_empresa,"N")
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

    public function listarProductos(Request $request){

        $products_vinculado = getProductosVinculadosYuraVenture($request->id_configuracion_empresa);
        $pv = [];
        foreach ($products_vinculado as $item) {
            $pv[] = $item->presentacion_yura;
        }
        return [
            'prodc_vinculado'=>$pv,
            'articulo_venture'=> getCodigoArticuloVenture($request->id_configuracion_empresa)
        ];
    }
}


