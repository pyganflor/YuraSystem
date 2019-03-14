<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Modelos\Pedido;
use yura\Modelos\StockEmpaquetado;
use yura\Modelos\Submenu;
use yura\Modelos\Variedad;

class DespachosController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.postcocecha.despachos.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'annos' => DB::table('semana as s')
                ->select('s.anno')->distinct()
                ->where('s.estado', '=', 1)->orderBy('s.anno')->get(),
            'variedades' => Variedad::All()->where('estado', '=', 1),
            'unitarias' => getUnitarias(),
        ]);
    }

    public function listar_resumen_pedidos(Request $request)
    {
        $listado = [];

        if ($request->fecha != '') {
            $listado = DB::table('pedido as p')
                ->join('cliente as c', 'c.id_cliente', '=', 'p.id_cliente')
                ->join('detalle_cliente as dc', 'dc.id_cliente', '=', 'p.id_cliente')
                ->select('p.*')->distinct()
                ->where('dc.estado', '=', 1)
                ->where('c.estado', '=', 1)
                ->where('p.estado', '=', 1)
                //->where('p.empaquetado', '=', 0)
                ->where('p.fecha_pedido', '=', $request->fecha)
                ->orderBy('dc.nombre', 'asc')
                ->get();

            $ids_pedidos = [];
            foreach ($listado as $item) {
                array_push($ids_pedidos, $item->id_pedido);
            }

            $ramos_x_variedad = DB::table('detalle_especificacionempaque as dee')
                ->join('especificacion_empaque as ee', 'dee.id_especificacion_empaque', '=', 'ee.id_especificacion_empaque')
                ->join('cliente_pedido_especificacion as cpe', 'ee.id_especificacion', '=', 'cpe.id_especificacion')
                ->join('detalle_pedido as dp', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
                ->select('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos', 'dee.longitud_ramo', 'dee.id_unidad_medida',
                    DB::raw('sum(dee.cantidad * ee.cantidad * dp.cantidad) as cantidad'))
                ->whereIn('dp.id_pedido', $ids_pedidos)
                ->groupBy('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos', 'dee.longitud_ramo', 'dee.id_unidad_medida')
                ->orderBy('dp.id_pedido', 'desc')
                ->get();

            $variedades = DB::table('detalle_especificacionempaque as dee')
                ->join('especificacion_empaque as ee', 'dee.id_especificacion_empaque', '=', 'ee.id_especificacion_empaque')
                ->join('cliente_pedido_especificacion as cpe', 'ee.id_especificacion', '=', 'cpe.id_especificacion')
                ->join('detalle_pedido as dp', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
                ->select('dee.id_variedad')->distinct()
                ->whereIn('dp.id_pedido', $ids_pedidos)
                ->get();
        }

        $datos = [
            'listado' => $listado,
            'fecha' => $request->fecha,
            'ramos_x_variedad' => $ramos_x_variedad,
            'variedades' => $variedades,
            'opciones' => $request->opciones
        ];

        return view('adminlte.gestion.postcocecha.despachos.partials.listado', $datos);
    }
}
