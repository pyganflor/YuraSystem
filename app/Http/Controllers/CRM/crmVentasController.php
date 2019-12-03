<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\Indicador;
use yura\Modelos\Pedido;
use yura\Modelos\ProyeccionVentaSemanalReal;
use yura\Modelos\ResumenVentaDiaria;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;

class crmVentasController extends Controller
{
    public function inicio(Request $request)
    {
        /* =========== TODAY ============= */
        $pedidos_today = Pedido::All()->where('estado', 1)->where('fecha_pedido', date('Y-m-d'));
        $cajas = 0;
        $valor = 0;
        foreach ($pedidos_today as $p) {
            if (!getFacturaAnulada($p->id_pedido)) {
                $cajas += $p->getCajas();
                $valor += $p->getPrecioByPedido();
            }
        }
        $today = [
            'cajas' => $cajas,
            'valor' => $valor
        ];

        /* =========== SEMANAL ============= */
        /*$pedidos_semanal = Pedido::All()->where('estado', 1)
            ->where('fecha_pedido', '>=', opDiasFecha('-', 7, date('Y-m-d')))
            ->where('fecha_pedido', '<=', opDiasFecha('-', 1, date('Y-m-d')));
        $valor = 0;
        $cajas = 0;
        $tallos = 0;
        foreach ($pedidos_semanal as $p) {
            if (!getFacturaAnulada($p->id_pedido)) {
                $valor += $p->getPrecioByPedido();
                $cajas += $p->getCajas();
                $tallos += $p->getTallos();
            }
        }
        $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
        $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
        $precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

        $semanal = [
            'valor' => $valor,
            'cajas' => $cajas,
            'precio_x_ramo' => $precio_x_ramo,
            'precio_x_tallo' => $precio_x_tallo,
        ];*/

        /* ======= AÑOS ======= */
        $annos = DB::table('historico_ventas')
            ->select('anno')->distinct()
            ->orderBy('anno')->distinct()
            ->get();

        $data = Indicador::whereIn('nombre',['D3','D4','D13','D14'])->select('valor')->get();


        return view('adminlte.crm.ventas.inicio', [
            'today' => $today,
            //'semanal' => $semanal,
            'annos' => $annos,
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'precioPromedioRamo' => $data[0]->valor,
            'dinero' =>$data[1]->valor,
            'cajasEquivalentes'=>$data[2]->valor,
            'precioXTallo'=>$data[3]->valor,
        ]);
    }

    public function filtrar_graficas(Request $request)
    {
        $desde = $request->desde;
        $hasta = $request->hasta;

        $arreglo_annos = [];
        if ($request->has('annos') && count($request->annos) > 0) {
            $view = '_annos';

            $fechas = [];

            $data = [];
            $periodo = 'mensual';

            foreach ($request->annos as $anno) {
                $arreglo_valores = [];
                $arreglo_fisicas = [];
                $arreglo_cajas = [];
                $arreglo_precios = [];

                foreach (getMeses(TP_NUMERO) as $mes) {
                    $query = DB::table('historico_ventas')
                        ->select(DB::raw('sum(valor) as valor'), DB::raw('sum(cajas_fisicas) as cajas_fisicas'),
                            DB::raw('sum(cajas_equivalentes) as cajas_equivalentes'),
                            DB::raw('sum(precio_x_ramo) as precio_x_ramo'))
                        ->where('anno', '=', $anno)
                        ->where('mes', '=', $mes);
                    $count_query = DB::table('historico_ventas')
                        ->select(DB::raw('count(*) as count'))
                        ->where('anno', '=', $anno)
                        ->where('mes', '=', $mes);

                    if ($request->id_variedad != '') {
                        $query = $query->where('id_variedad', '=', $request->id_variedad);
                        $count_query = $count_query->where('id_variedad', '=', $request->id_variedad);
                    }
                    if ($request->x_cliente == 'true' && $request->id_cliente != '') {
                        $query = $query->where('id_cliente', '=', $request->id_cliente);
                        $count_query = $count_query->where('id_cliente', '=', $request->id_cliente);
                    }
                    $query = $query->get();
                    $count_query = $count_query->get();

                    array_push($arreglo_valores, count($query) > 0 ? round($query[0]->valor, 2) : 0);
                    array_push($arreglo_fisicas, count($query) > 0 ? round($query[0]->cajas_fisicas, 2) : 0);
                    array_push($arreglo_cajas, count($query) > 0 ? round($query[0]->cajas_equivalentes, 2) : 0);
                    array_push($arreglo_precios, (count($query) > 0 && $count_query[0]->count > 0) ? round($query[0]->precio_x_ramo / $count_query[0]->count, 2) : 0);
                }
                array_push($arreglo_annos, [
                    'anno' => $anno,
                    'valores' => $arreglo_valores,
                    'fisicas' => $arreglo_fisicas,
                    'equivalentes' => $arreglo_cajas,
                    'precios' => $arreglo_precios,
                ]);
            }
        } else {
            $view = 'graficas';

            if ($request->diario == 'true') {
                $periodo = 'diario';

                $array_valor = [];
                $array_cajas = [];
                $array_precios = [];
                if ($request->total == 'true') {
                    $fechas = DB::table('pedido as p')
                        ->select('p.fecha_pedido as dia')->distinct()
                        ->where('p.estado', '=', 1)
                        ->where('p.fecha_pedido', '>=', $request->desde)
                        ->where('p.fecha_pedido', '<=', $request->hasta)
                        ->orderBy('p.fecha_pedido')
                        ->get();

                    foreach ($fechas as $f) {

                        $objResumenVentaDiaria = ResumenVentaDiaria::where('fecha_pedido',$f->dia)->get();
                        foreach($objResumenVentaDiaria as $ventaDiaria){
                            $array_valor[]=$ventaDiaria->valor;
                            $array_cajas[]=$ventaDiaria->cajas_equivalentes;
                            $array_precios[]=$ventaDiaria->precio_x_ramo;
                        }
                        /*$pedidos_semanal = Pedido::All()->where('estado', 1)
                            ->where('fecha_pedido', '=', $f->dia);
                        $valor = 0;
                        $cajas = 0;
                        $tallos = 0;
                        foreach ($pedidos_semanal as $p) {
                            if (!getFacturaAnulada($p->id_pedido)) {
                                $valor += $p->getPrecioByPedido();
                                $cajas += $p->getCajas();
                                $tallos += $p->getTallos();
                            }
                        }
                        $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
                        $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
                        $precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

                        array_push($array_valor, $valor);
                        array_push($array_cajas, $cajas);
                        array_push($array_precios, $precio_x_ramo);*/
                    }

                } else if ($request->x_cliente == 'true' && $request->id_cliente != '') {
                    $fechas = DB::table('pedido as p')
                        ->select('p.fecha_pedido as dia')->distinct()
                        ->where('p.estado', '=', 1)
                        ->where('p.fecha_pedido', '>=', $request->desde)
                        ->where('p.fecha_pedido', '<=', $request->hasta)
                        ->where('p.id_cliente', '=', $request->id_cliente)
                        ->orderBy('p.fecha_pedido')
                        ->get();

                    foreach ($fechas as $f) {
                        $pedidos_semanal = Pedido::All()->where('estado', 1)
                            ->where('fecha_pedido', '=', $f->dia)
                            ->where('id_cliente', '=', $request->id_cliente);
                        $valor = 0;
                        $cajas = 0;
                        $tallos = 0;
                        foreach ($pedidos_semanal as $p) {
                            if (!getFacturaAnulada($p->id_pedido)) {
                                $valor += $p->getPrecioByPedido();
                                $cajas += $p->getCajas();
                                $tallos += $p->getTallos();
                            }
                        }
                        $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
                        $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
                        //$precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

                        array_push($array_valor, $valor);
                        array_push($array_cajas, $cajas);
                        array_push($array_precios, $precio_x_ramo);
                    }
                }
                $data = [
                    'valores' => $array_valor,
                    'cajas' => $array_cajas,
                    'precios' => $array_precios,
                ];
            } else if ($request->semanal == 'true') {
                $periodo = 'semanal';

                $array_valor = [];
                $array_cajas = [];
                $array_precios = [];

                $fechas = DB::table('semana as s')
                    ->select('s.codigo as semana')->distinct()
                    ->Where(function ($q) use ($desde, $hasta) {
                        $q->where('s.fecha_inicial', '>=', $desde)
                            ->where('s.fecha_inicial', '<=', $hasta);
                    })->orWhere(function ($q) use ($desde, $hasta) {
                        $q->where('s.fecha_final', '>=', $desde)
                            ->Where('s.fecha_final', '<=', $hasta);
                    })->orderBy('codigo')->get();

                if ($request->total == 'true') {

                    $intevalo=[];
                    foreach ($fechas as $fecha)
                        $intevalo[]=$fecha->semana;

                    $dataProyeccionVentalSemanalReal = ProyeccionVentaSemanalReal::whereIn('codigo_semana',$intevalo)
                        ->select('codigo_semana',
                            DB::raw('SUM(cajas_equivalentes) as cajas'),
                            DB::raw('SUM(valor)as valor')
                        )->groupBy('codigo_semana')->get();

                    $defRamosXCaja =getConfiguracionEmpresa()->ramos_x_caja;
                    foreach($dataProyeccionVentalSemanalReal as $data){
                        $ramos_estandar = $data->cajas * $defRamosXCaja;
                        $array_valor[]=round($data->valor,2); // Dinero
                        $array_cajas[]=round($data->cajas,2); //cajas equivalentes
                        $array_precios[]= $ramos_estandar > 0 ? round($data->valor / $ramos_estandar,2 ) : 0; //precio x ramo
                    }
                    /*foreach ($fechas as $codigo) {
                        $semana = Semana::All()->where('codigo', '=', $codigo->semana)->first();
                        $pedidos = Pedido::All()->where('estado', 1)
                            ->where('fecha_pedido', '>=', $semana->fecha_inicial)
                            ->where('fecha_pedido', '<=', $semana->fecha_final);
                        $valor = 0;
                        $cajas = 0;
                        $tallos = 0;

                        foreach ($pedidos as $p) {
                            if (!getFacturaAnulada($p->id_pedido)) {
                                $valor += $p->getPrecioByPedido();
                                $cajas += $p->getCajas();
                                $tallos += $p->getTallos();
                            }
                        }
                        $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
                        $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
                        $precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

                        array_push($array_valor, $valor);
                        array_push($array_cajas, $cajas);
                        array_push($array_precios, $precio_x_ramo);
                    }*/

                } else if ($request->x_cliente == 'true' && $request->id_cliente != '') {

                    foreach ($fechas as $fecha)
                        $intevalo[]=$fecha->semana;

                    $dataProyeccionVentalSemanalReal = ProyeccionVentaSemanalReal::whereIn('codigo_semana',$intevalo)
                            ->select('codigo_semana',
                                DB::raw('SUM(cajas_equivalentes) as cajas'),
                                DB::raw('SUM(valor)as valor')
                            )->groupBy('codigo_semana')->where('id_cliente','=',$request->id_cliente)->get();

                    $defRamosXCaja =getConfiguracionEmpresa()->ramos_x_caja;
                    foreach($dataProyeccionVentalSemanalReal as $data){
                        $ramos_estandar = $data->cajas * $defRamosXCaja;
                        $array_valor[]=round($data->valor,2); // Dinero
                        $array_cajas[]=round($data->cajas,2); //cajas equivalentes
                        $array_precios[]= $ramos_estandar > 0 ? round($data->valor / $ramos_estandar,2 ) : 0; //precio x ramo
                    }
                    /*foreach ($fechas as $codigo) {
                        $semana = Semana::All()->where('codigo', '=', $codigo->semana)->first();
                        $pedidos = Pedido::All()->where('estado', 1)
                            ->where('id_cliente', '>=', $request->id_cliente)
                            ->where('fecha_pedido', '>=', $semana->fecha_inicial)
                            ->where('fecha_pedido', '<=', $semana->fecha_final);
                        $valor = 0;
                        $cajas = 0;
                        $tallos = 0;

                        foreach ($pedidos as $p) {
                            if (!getFacturaAnulada($p->id_pedido)) {
                                $valor += $p->getPrecioByPedido();
                                $cajas += $p->getCajas();
                                $tallos += $p->getTallos();
                            }
                        }
                        $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
                        $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
                        $precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

                        array_push($array_valor, $valor);
                        array_push($array_cajas, $cajas);
                        array_push($array_precios, $precio_x_ramo);
                    }*/
                }

                $data = [
                    'valores' => $array_valor,
                    'cajas' => $array_cajas,
                    'precios' => $array_precios,
                ];
            }
        }

        return view('adminlte.crm.ventas.partials.' . $view, [
            'labels' => $fechas,
            'arreglo_annos' => $arreglo_annos,
            'data' => $data,
            'periodo' => $periodo,
        ]);
    }

    public function desglose_indicador(Request $request)
    {
        $fechas = DB::table('pedido')
            ->select('fecha_pedido as dia')->distinct()
            ->where('estado', 1)
            ->where('fecha_pedido', '>=', opDiasFecha('-', 7, date('Y-m-d')))
            ->where('fecha_pedido', '<=', opDiasFecha('-', 1, date('Y-m-d')))
            ->orderBy('fecha_pedido')
            ->get();

        $arreglo_variedades = [];
        foreach (getVariedades() as $v) {
            $array_valores = [];
            $array_cajas = [];
            $array_precios = [];
            $array_tallos = [];

            $flag = false;
            foreach ($fechas as $dia) {
                $pedidos_semanal = Pedido::All()->where('estado', 1)
                    ->where('fecha_pedido', '=', $dia->dia);
                $valor = 0;
                $cajas = 0;
                $tallos = 0;
                foreach ($pedidos_semanal as $p) {
                    if (!getFacturaAnulada($p->id_pedido)) {
                        $valor += $p->getPrecioByPedidoVariedad($v->id_variedad);
                        $cajas += $p->getCajasByVariedad($v->id_variedad);
                        $tallos += $p->getTallosByVariedad($v->id_variedad);
                        if ($valor > 0)
                            $flag = true;
                    }
                }
                $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
                $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
                $precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

                array_push($array_valores, $valor);
                array_push($array_cajas, $cajas);
                array_push($array_precios, $precio_x_ramo);
                array_push($array_tallos, $precio_x_tallo);
            }

            if ($flag == true)
                array_push($arreglo_variedades, [
                    'variedad' => $v,
                    'valores' => $array_valores,
                    'cajas' => $array_cajas,
                    'precios' => $array_precios,
                    'tallos' => $array_tallos,
                ]);
        }

        return view('adminlte.crm.ventas.partials._desglose_indicador', [
            'labels' => $fechas,
            'arreglo_variedades' => $arreglo_variedades,
            'option' => $request->option,
        ]);
    }
}
