<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\DetalleEspecificacionEmpaque;
use yura\Modelos\Submenu;

class ProductoVentureController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.gestion.configuracion_facturacion.productos_venture.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Administración', 'subtitulo' => 'Productos venture'],
                'presentacion_venture' => getCodigoArticuloVenture(),
                'presentaciones_yuraSystem' => DetalleEspecificacionEmpaque::select('id_variedad','id_clasificacion_ramo','longitud_ramo','id_unidad_medida')->distinct()->get()
            ]);
    }
}
