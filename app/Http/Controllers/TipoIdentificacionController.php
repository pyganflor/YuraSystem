<?php

namespace yura\Http\Controllers;
use yura\Modelos\Submenu;

use Illuminate\Http\Request;

class TipoIdentificacionController extends Controller
{
    public function index(Request $request){
        return view('adminlte.gestion.facturacion.tipo_identificacion.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Facturación', 'subtitulo' => 'tipos de identificación']
            ]);
    }

    public function buscar_tipo_identificacion(Request $request){

        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);

        $listado = DB::table('comprobante as c');

        $listado->where('c.estado', $request->estado != '' ? $request->estado : 1);

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
}
