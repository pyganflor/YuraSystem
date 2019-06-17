<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\HistoricoVentas;
use yura\Modelos\Pais;
use yura\Modelos\Submenu;

class tblVentasController extends Controller
{
    public function inicio(Request $request)
    {
        $annos = DB::table('historico_ventas')
            ->select(DB::raw('anno'))->distinct()
            ->orderBy('anno')
            ->orderBy('mes')
            ->get();

        return view('adminlte.crm.tbl_ventas.inicio', [
            'annos' => $annos,
            'clientes' => getClientes(),

            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function filtrar_tablas(Request $request)
    {
        if ($request->annos == '')
            $annos = [date('Y')];
        else
            $annos = explode(' - ', $request->annos);

        if ($request->desde != '' && $request->hasta != '') {
            if ($request->rango == 'A') {   // Anual
                $view = 'anual';
                if ($request->desde >= 1 && $request->desde <= 12 && $request->hasta >= 1 && $request->hasta <= 12 && $request->desde <= $request->hasta) {
                    $data = $this->getTablasByRangoAnual($request->desde, $request->hasta, $request->cliente, $request->variedad, $annos, $request->criterio);
                } else {
                    return '<div class="alert alert-warning text-center">Los meses ingresados están incorrectos</div>';
                }
            } else {    // Mensual
                $view = 'mensual';
                if ($request->desde >= 1 && $request->desde <= 12 && $request->hasta >= 1 && $request->hasta <= 12 && $request->desde <= $request->hasta) {
                    $data = $this->getTablasByRangoMensual($request->desde, $request->hasta, $request->cliente, $request->variedad, $annos, $request->criterio, $request->acumulado);
                } else {
                    return '<div class="alert alert-warning text-center">Los meses ingresados están incorrectos</div>';
                }
            }

            return view('adminlte.crm.tbl_ventas.partials.' . $view, [
                'data' => $data,
                'acumulado' => $request->acumulado,
                'criterio' => $request->criterio,
                'cliente' => $request->cliente,
                'desde' => $request->desde,
                'hasta' => $request->hasta,
            ]);
        } else {
            return '<div class="alert alert-warning text-center">Debes ingresar desde - hasta</div>';
        }
    }

    public function navegar_tabla(Request $request)
    {
        if ($request->desde >= 1 && $request->desde <= 12 && $request->hasta >= 1 && $request->hasta <= 12 && $request->desde <= $request->hasta) {
            $view = 'mensual';
            $data = $this->getTablasByRangoMensual($request->desde, $request->hasta, $request->filtro_cliente, $request->filtro_variedad, [$request->anno], $request->criterio, $request->acumulado);
        } else {
            return '<div class="alert alert-warning text-center">Los meses ingresados están incorrectos</div>';
        }

        return view('adminlte.crm.tbl_ventas.partials.' . $view, [
            'data' => $data,
            'acumulado' => $request->acumulado,
            'criterio' => $request->criterio,
            'variedad' => $request->filtro_variedad,
            'cliente' => $request->filtro_cliente,
            'desde' => $request->desde,
            'hasta' => $request->hasta,
        ]);
    }

    public function getTablasByRangoAnual($mes_inicial, $mes_final, $cliente, $variedad, $annos, $criterio)
    {
        sort($annos);
        $labels = $annos;
        $filas = [];
        $valores = [];

        if (strlen($mes_inicial) != 2)
            $mes_inicial = '0' . $mes_inicial;
        if (strlen($mes_final) != 2)
            $mes_final = '0' . $mes_final;

        if ($cliente == 'A') {  // acumulado
            foreach ($labels as $a) {
                $valor = 0;

                $ventas = DB::table('historico_ventas')
                    ->select('id_historico_ventas as id')->distinct()
                    ->where(DB::raw('anno'), '=', $a)
                    ->where(DB::raw('mes'), '>=', $mes_inicial)
                    ->where(DB::raw('mes'), '<=', $mes_final);

                if ($variedad != 'A')
                    $ventas = $ventas->where('id_variedad', $variedad);

                $ventas = $ventas->get();

                foreach ($ventas as $obj) {
                    $venta = HistoricoVentas::find($obj->id);

                    if ($criterio == 'V')   // valor
                        $valor += round($venta->valor, 2);
                    if ($criterio == 'F')   // cajas fisicas
                        $valor += round($venta->cajas_fisicas, 2);
                    if ($criterio == 'Q')   // cajas equivalentes
                        $valor += round($venta->cajas_equivalentes, 2);
                    if ($criterio == 'P')   // cajas equivalentes
                        $valor += round($venta->precio_x_ramo, 2);
                }

                if ($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
                    array_push($valores, $valor);
                if ($criterio == 'P')
                    array_push($valores, count($ventas) > 0 ? round($valor / count($ventas), 2) : 0);
            }

            array_push($filas, [
                'encabezado' => '',
                'valores' => $valores,
            ]);
        } else if ($cliente == 'T') {   // todos los clientes
            foreach (getClientes() as $cli) {
                $valores = [];
                $total_x_cli = 0;
                foreach ($labels as $a) {
                    $valor = 0;

                    $ventas = DB::table('historico_ventas')
                        ->select('id_historico_ventas as id')->distinct()
                        ->where(DB::raw('id_cliente'), '=', $cli->id_cliente)
                        ->where(DB::raw('anno'), '=', $a)
                        ->where(DB::raw('mes'), '>=', $mes_inicial)
                        ->where(DB::raw('mes'), '<=', $mes_final);

                    if ($variedad != 'A')
                        $ventas = $ventas->where('id_variedad', $variedad);

                    $ventas = $ventas->get();

                    foreach ($ventas as $obj) {
                        $venta = HistoricoVentas::find($obj->id);

                        if ($criterio == 'V')   // valor
                            $valor += round($venta->valor, 2);
                        if ($criterio == 'F')   // cajas fisicas
                            $valor += round($venta->cajas_fisicas, 2);
                        if ($criterio == 'Q')   // cajas equivalentes
                            $valor += round($venta->cajas_equivalentes, 2);
                        if ($criterio == 'P')   // cajas equivalentes
                            $valor += round($venta->precio_x_ramo, 2);
                    }
                    $total_x_cli += $valor;

                    if ($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
                        array_push($valores, $valor);
                    if ($criterio == 'P')
                        array_push($valores, count($ventas) > 0 ? round($valor / count($ventas), 2) : 0);
                }

                if ($total_x_cli > 0) { // agregar solo los clientes q tengan datos q mosstrar
                    array_push($filas, [
                        'encabezado' => $cli,
                        'valores' => $valores,
                    ]);
                }
            }
        } else if ($cliente == 'P') {   // todos los paises
            foreach (Pais::All() as $pais) {
                $valores = [];
                foreach ($labels as $a) {
                    $valor = 0;

                    $ventas = DB::table('historico_ventas as h')
                        ->join('detalle_cliente as dc', 'dc.id_cliente', '=', 'h.id_cliente')
                        ->select('h.id_historico_ventas as id')->distinct()
                        ->where('dc.estado', '=', 1)
                        ->where('dc.codigo_pais', '=', $pais->codigo)
                        ->where(DB::raw('h.anno'), '=', $a)
                        ->where(DB::raw('h.mes'), '>=', $mes_inicial)
                        ->where(DB::raw('h.mes'), '<=', $mes_final);

                    if ($variedad != 'A')
                        $ventas = $ventas->where('h.id_variedad', $variedad);

                    $ventas = $ventas->get();

                    foreach ($ventas as $obj) {
                        $venta = HistoricoVentas::find($obj->id);

                        if ($criterio == 'V')   // valor
                            $valor += round($venta->valor, 2);
                        if ($criterio == 'F')   // cajas fisicas
                            $valor += round($venta->cajas_fisicas, 2);
                        if ($criterio == 'Q')   // cajas equivalentes
                            $valor += round($venta->cajas_equivalentes, 2);
                        if ($criterio == 'P')   // cajas equivalentes
                            $valor += round($venta->precio_x_ramo, 2);
                    }

                    if ($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
                        array_push($valores, $valor);
                    if ($criterio == 'P')
                        array_push($valores, count($ventas) > 0 ? round($valor / count($ventas), 2) : 0);
                }

                if ($valor > 0) {   // agregar solo los que tengan datos que mostrar
                    array_push($filas, [
                        'encabezado' => $pais,
                        'valores' => $valores,
                    ]);
                }
            }
        } else {    // un cliente
            foreach ($labels as $a) {
                $valor = 0;

                $ventas = DB::table('historico_ventas')
                    ->select('id_historico_ventas as id')->distinct()
                    ->where(DB::raw('id_cliente'), '=', $cliente)
                    ->where(DB::raw('anno'), '=', $a)
                    ->where(DB::raw('mes'), '>=', $mes_inicial)
                    ->where(DB::raw('mes'), '<=', $mes_final);

                if ($variedad != 'A')
                    $ventas = $ventas->where('id_variedad', $variedad);

                $ventas = $ventas->get();

                foreach ($ventas as $obj) {
                    $venta = HistoricoVentas::find($obj->id);

                    if ($criterio == 'V')   // valor
                        $valor += round($venta->valor, 2);
                    if ($criterio == 'F')   // cajas fisicas
                        $valor += round($venta->cajas_fisicas, 2);
                    if ($criterio == 'Q')   // cajas equivalentes
                        $valor += round($venta->cajas_equivalentes, 2);
                    if ($criterio == 'P')   // cajas equivalentes
                        $valor += round($venta->precio_x_ramo, 2);
                }

                if ($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
                    array_push($valores, $valor);
                if ($criterio == 'P')
                    array_push($valores, count($ventas) > 0 ? round($valor / count($ventas), 2) : 0);
            }

            array_push($filas, [
                'encabezado' => getCliente($cliente),
                'valores' => $valores,
            ]);
        }

        return [
            'labels' => $labels,
            'filas' => $filas,
        ];
    }

    public function getTablasByRangoMensual($mes_inicial, $mes_final, $cliente, $variedad, $annos, $criterio, $acumulado)
    {
        sort($annos);
        $labels = $annos;
        $meses = [];
        $filas = [];
        $valores = [];

        if ($cliente == 'A') { // Acumulado
            foreach ($labels as $pos => $l) {
                for ($m = $mes_inicial; $m <= $mes_final; $m++) {
                    if ($pos == 0) {
                        array_push($meses, getMeses()[$m - 1]);
                    }

                    if (strlen($m) != 2)
                        $m = '0' . $m;

                    $ventas = DB::table('historico_ventas')
                        ->select('id_historico_ventas as id')->distinct()
                        ->where(DB::raw('anno'), '=', $l);

                    if ($acumulado == 'true') {
                        $ventas = $ventas->where(DB::raw('mes'), '>=', 1)->where(DB::raw('mes'), '<=', $m);
                    } else {
                        $ventas = $ventas->where(DB::raw('mes'), '=', $m);
                    }

                    if ($variedad != 'A')
                        $ventas = $ventas->where('id_variedad', $variedad);

                    $ventas = $ventas->get();

                    $valor = 0;

                    foreach ($ventas as $obj) {
                        $venta = HistoricoVentas::find($obj->id);

                        if ($criterio == 'V')   // valor
                            $valor += round($venta->valor, 2);
                        if ($criterio == 'F')   // cajas fisicas
                            $valor += round($venta->cajas_fisicas, 2);
                        if ($criterio == 'Q')   // cajas equivalentes
                            $valor += round($venta->cajas_equivalentes, 2);
                        if ($criterio == 'P')   // cajas equivalentes
                            $valor += round($venta->precio_x_ramo, 2);
                    }

                    if ($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
                        array_push($valores, $valor);
                    if ($criterio == 'P')
                        array_push($valores, count($ventas) > 0 ? round($valor / count($ventas), 2) : 0);
                }
            }

            array_push($filas, [
                'encabezado' => '',
                'valores' => $valores,
            ]);
        } else if ($cliente == 'T') {  // Todos los clientes
            foreach (getClientes() as $pos_cli => $cli) {
                $valores = [];
                $total_x_cli = 0;

                foreach ($labels as $pos => $l) {
                    for ($m = $mes_inicial; $m <= $mes_final; $m++) {
                        if ($pos == 0 && $pos_cli == 0) {
                            array_push($meses, getMeses()[$m - 1]);
                        }

                        if (strlen($m) != 2)
                            $m = '0' . $m;

                        $ventas = DB::table('historico_ventas')
                            ->select('id_historico_ventas as id')->distinct()
                            ->where(DB::raw('anno'), '=', $l)
                            ->where(DB::raw('id_cliente'), '=', $cli->id_cliente);

                        if ($acumulado == 'true') {
                            $ventas = $ventas->where(DB::raw('mes'), '>=', 1)->where(DB::raw('mes'), '<=', $m);
                        } else {
                            $ventas = $ventas->where(DB::raw('mes'), '=', $m);
                        }

                        if ($variedad != 'A')
                            $ventas = $ventas->where('id_variedad', $variedad);

                        $ventas = $ventas->get();

                        $valor = 0;

                        foreach ($ventas as $obj) {
                            $venta = HistoricoVentas::find($obj->id);

                            if ($criterio == 'V')   // valor
                                $valor += round($venta->valor, 2);
                            if ($criterio == 'F')   // cajas fisicas
                                $valor += round($venta->cajas_fisicas, 2);
                            if ($criterio == 'Q')   // cajas equivalentes
                                $valor += round($venta->cajas_equivalentes, 2);
                            if ($criterio == 'P')   // cajas equivalentes
                                $valor += round($venta->precio_x_ramo, 2);
                        }
                        $total_x_cli += $valor;

                        if ($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
                            array_push($valores, $valor);
                        if ($criterio == 'P')
                            array_push($valores, count($ventas) > 0 ? round($valor / count($ventas), 2) : 0);
                    }
                }

                if ($total_x_cli > 0) { // agregar solo los clientes q tengan datos q mostrar
                    array_push($filas, [
                        'encabezado' => $cli,
                        'valores' => $valores,
                    ]);
                }
            }
        } else if ($cliente == 'P') {  // Todos los clientes
            foreach (Pais::All() as $pos_pais => $pais) {
                $valores = [];
                $total_x_pais = 0;
                foreach ($labels as $pos => $l) {
                    for ($m = $mes_inicial; $m <= $mes_final; $m++) {
                        if ($pos == 0 && $pos_pais == 0) {
                            array_push($meses, getMeses()[$m - 1]);
                        }

                        if (strlen($m) != 2)
                            $m = '0' . $m;

                        $ventas = DB::table('historico_ventas as h')
                            ->join('detalle_cliente as dc', 'dc.id_cliente', '=', 'h.id_cliente')
                            ->select('h.id_historico_ventas as id')->distinct()
                            ->where('dc.estado', '=', 1)
                            ->where('dc.codigo_pais', '=', $pais->codigo)
                            ->where(DB::raw('h.anno'), '=', $l);

                        if ($acumulado == 'true') {
                            $ventas = $ventas->where(DB::raw('mes'), '>=', 1)->where(DB::raw('mes'), '<=', $m);
                        } else {
                            $ventas = $ventas->where(DB::raw('mes'), '=', $m);
                        }

                        if ($variedad != 'A')
                            $ventas = $ventas->where('h.id_variedad', $variedad);

                        $ventas = $ventas->get();

                        $valor = 0;

                        foreach ($ventas as $obj) {
                            $venta = HistoricoVentas::find($obj->id);

                            if ($criterio == 'V')   // valor
                                $valor += round($venta->valor, 2);
                            if ($criterio == 'F')   // cajas fisicas
                                $valor += round($venta->cajas_fisicas, 2);
                            if ($criterio == 'Q')   // cajas equivalentes
                                $valor += round($venta->cajas_equivalentes, 2);
                            if ($criterio == 'P')   // cajas equivalentes
                                $valor += round($venta->precio_x_ramo, 2);
                        }
                        $total_x_pais += $valor;

                        if ($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
                            array_push($valores, $valor);
                        if ($criterio == 'P')
                            array_push($valores, count($ventas) > 0 ? round($valor / count($ventas), 2) : 0);
                    }
                }

                if ($total_x_pais > 0) {    // agregar solo los años que tienen datos q mostrar
                    array_push($filas, [
                        'encabezado' => $pais,
                        'valores' => $valores,
                    ]);
                }
            }
        } else {    // Un cliente
            foreach ($labels as $pos => $l) {
                for ($m = $mes_inicial; $m <= $mes_final; $m++) {
                    if ($pos == 0) {
                        array_push($meses, getMeses()[$m - 1]);
                    }

                    if (strlen($m) != 2)
                        $m = '0' . $m;

                    $ventas = DB::table('historico_ventas')
                        ->select('id_historico_ventas as id')->distinct()
                        ->where(DB::raw('anno'), '=', $l)
                        ->where(DB::raw('id_cliente'), '=', $cliente);

                    if ($acumulado == 'true') {
                        $ventas = $ventas->where(DB::raw('mes'), '>=', 1)->where(DB::raw('mes'), '<=', $m);
                    } else {
                        $ventas = $ventas->where(DB::raw('mes'), '=', $m);
                    }

                    if ($variedad != 'A')
                        $ventas = $ventas->where('id_variedad', $variedad);

                    $ventas = $ventas->get();

                    $valor = 0;

                    foreach ($ventas as $obj) {
                        $venta = HistoricoVentas::find($obj->id);

                        if ($criterio == 'V')   // valor
                            $valor += round($venta->valor, 2);
                        if ($criterio == 'F')   // cajas fisicas
                            $valor += round($venta->cajas_fisicas, 2);
                        if ($criterio == 'Q')   // cajas equivalentes
                            $valor += round($venta->cajas_equivalentes, 2);
                        if ($criterio == 'P')   // cajas equivalentes
                            $valor += round($venta->precio_x_ramo, 2);
                    }

                    if ($criterio == 'V' || $criterio == 'F' || $criterio == 'Q')
                        array_push($valores, $valor);
                    if ($criterio == 'P')
                        array_push($valores, count($ventas) > 0 ? round($valor / count($ventas), 2) : 0);
                }
            }

            array_push($filas, [
                'encabezado' => getCliente($cliente),
                'valores' => $valores,
            ]);
        }

        return [
            'labels' => $labels,
            'meses' => $meses,
            'filas' => $filas,
        ];
    }
}