<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Modelos\Pedido;
use yura\Modelos\StockEmpaquetado;
use yura\Modelos\Submenu;
use yura\Modelos\Variedad;

class ClasificacionBlancoController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.postcocecha.clasificacion_blanco.inicio', [
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
        $variedades = [];
        $grosores = [];
        $ramos_x_variedad = [];
        $stock_frio = [];

        if ($request->fecha != '') {
            $listado = DB::table('pedido as p')
                ->join('cliente as c', 'c.id_cliente', '=', 'p.id_cliente')
                ->join('detalle_cliente as dc', 'dc.id_cliente', '=', 'p.id_cliente')
                ->select('p.*')->distinct()
                ->where('dc.estado', '=', 1)
                ->where('c.estado', '=', 1)
                ->where('p.estado', '=', 1)
                ->where('p.empaquetado', '=', 0)
                ->where('p.fecha_pedido', '=', $request->fecha)
                ->orderBy('dc.nombre', 'asc')
                ->get();

            $variedades = DB::table('detalle_especificacionempaque as dep')
                ->join('especificacion_empaque as ee', 'ee.id_especificacion_empaque', '=', 'dep.id_especificacion_empaque')
                ->join('cliente_pedido_especificacion as cpe', 'cpe.id_especificacion', '=', 'ee.id_especificacion')
                ->join('detalle_pedido as dp', 'dp.id_cliente_especificacion', '=', 'cpe.id_cliente_pedido_especificacion')
                ->join('pedido as p', 'p.id_pedido', '=', 'dp.id_pedido')
                ->join('variedad as v', 'v.id_variedad', '=', 'dep.id_variedad')
                ->select('dep.id_variedad')->distinct()
                ->where('p.estado', '=', 1)
                ->where('p.fecha_pedido', '=', $request->fecha)
                ->where('p.empaquetado', '=', 0)
                ->orderBy('v.nombre', 'asc')
                ->get();

            $grosores = DB::table('detalle_especificacionempaque as dep')
                ->join('especificacion_empaque as ee', 'ee.id_especificacion_empaque', '=', 'dep.id_especificacion_empaque')
                ->join('cliente_pedido_especificacion as cpe', 'cpe.id_especificacion', '=', 'ee.id_especificacion')
                ->join('detalle_pedido as dp', 'dp.id_cliente_especificacion', '=', 'cpe.id_cliente_pedido_especificacion')
                ->join('pedido as p', 'p.id_pedido', '=', 'dp.id_pedido')
                ->join('grosor_ramo as gr', 'gr.id_grosor_ramo', '=', 'dep.id_grosor_ramo')
                ->select('dep.id_grosor_ramo')->distinct()
                ->where('p.estado', '=', 1)
                ->where('p.fecha_pedido', '=', $request->fecha)
                ->where('p.empaquetado', '=', 0)
                ->orderBy('gr.nombre', 'asc')
                ->get();

            $ramos_x_variedad = DB::table('pedido as p')
                ->join('detalle_pedido as dp', 'dp.id_pedido', '=', 'p.id_pedido')
                ->join('cliente_pedido_especificacion as cpe', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
                ->join('especificacion_empaque as ee', 'ee.id_especificacion', '=', 'cpe.id_especificacion')
                ->join('detalle_especificacionempaque as dee', 'dee.id_especificacion_empaque', '=', 'ee.id_especificacion_empaque')
                ->join('variedad as v', 'v.id_variedad', '=', 'dee.id_variedad')
                ->select(DB::raw('sum(dee.cantidad * ee.cantidad * dp.cantidad) as cantidad'),
                    'dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos', 'dee.longitud_ramo', 'dee.id_unidad_medida')
                ->where('p.estado', '=', 1)
                ->where('p.empaquetado', '=', 0)
                ->where('p.fecha_pedido', '=', $request->fecha)
                ->groupBy('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos', 'dee.longitud_ramo', 'dee.id_unidad_medida')
                ->orderBy('v.siglas', 'asc')
                ->get();

            $stock_frio = DB::table('stock_empaquetado as se')
                ->join('variedad as v', 'v.id_variedad', '=', 'se.id_variedad')
                ->select('se.*')
                ->where('se.estado', '=', 1)
                ->where('se.fecha_ingreso', '=', $request->fecha)
                ->orderBy('v.nombre', 'asc')
                ->get();
        }

        $datos = [
            'listado' => $listado,
            'variedades' => $variedades,
            'grosores' => $grosores,
            'ramos_x_variedad' => $ramos_x_variedad,
            'stock_frio' => $stock_frio,
            'fecha' => $request->fecha,
        ];

        return view('adminlte.gestion.postcocecha.clasificacion_blanco.partials.listado', $datos);
    }

    public function empaquetar(Request $request)
    {
        $stock_frio = DB::table('stock_empaquetado as se')
            ->join('variedad as v', 'v.id_variedad', '=', 'se.id_variedad')
            ->select('se.*')
            ->where('se.estado', '=', 1)
            ->where('se.fecha_ingreso', '=', $request->fecha)
            ->orderBy('v.nombre', 'asc')
            ->get();

        return view('adminlte.gestion.postcocecha.clasificacion_blanco.partials.empaquetado', [
            'stock_frio' => $stock_frio,
            'fecha' => $request->fecha
        ]);
    }

    public function update_stock_empaquetado(Request $request)
    {
        $success = true;
        $msg = '';
        if (count($request->arreglo) > 0) {
            foreach ($request->arreglo as $item) {
                $stock = StockEmpaquetado::find($item['id']);
                $stock->cantidad_empaquetada = $item['cantidad'];
                $stock->empaquetado = 1;

                if ($stock->save()) {
                    bitacora('stock_empaquetado', $stock->id_stock_empaquetado, 'U', 'Actualizacion satisfactoria del stock_empaquetado');
                } else {
                    $success = false;
                    $msg .= '<div class="alert alert-warning text-center">' .
                        'Ha ocurrido un problema con la variedad: "' . Variedad::find($stock->id_variedad)->nombre .
                        '"</div>';
                }
            }
            /* =========== ACTUALIZAR TABLA PEDIDOS ===============*/
            $pedidos = Pedido::All()->where('estado', '=', 1)->where('fecha_pedido', '=', $request->fecha);
            foreach ($pedidos as $pedido) {
                $pedido->empaquetado = 1;
                if ($pedido->save()) {
                    bitacora('pedido', $pedido->id_pedido, 'U', 'Actualizacion satisfactoria del pedido');
                } else {
                    $success = false;
                    $msg .= '<div class="alert alert-warning text-center">' .
                        'Ha ocurrido un problema con el pedido: "' . $pedido->descripcion .
                        '"</div>';
                }
            }

            if ($success) {
                $msg = '<div class="alert alert-success text-center">' .
                    'Se ha guardado toda la información satisfactoriamente' .
                    '</div>';
            }
        } else {
            $success = false;
            $msg = '<div class="alert alert-warning text-center">' .
                'Al menos debe enviar alguna información relacionada con una variedad' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }
}