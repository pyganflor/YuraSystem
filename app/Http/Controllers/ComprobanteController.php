<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\Submenu;
use yura\Modelos\marca;
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

    public function burcar_comprobantes(Request $request){

        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);

        $listado = DB::table('comprobante as c')
            ->where('c.estado', 1);

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($bus) {
            $q->orWhere('c.codigo', 'like', '%' . $bus . '%');
            $q->orWhere('c.nombre', 'like', '%' . $bus . '%');
        });

        $listado = $listado->orderBy('c.nombre', 'asc')->paginate(10);

        $datos = [
            'listado' => $listado
        ];
        return view('adminlte.gestion.facturacion.comprobantes.partials.listado', $datos);
    }

    public function add_comprobantes(){
        return view('adminlte.gestion.facturacion.comprobantes.forms.add_comprobante');
    }
}
