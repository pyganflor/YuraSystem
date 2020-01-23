<?php

namespace yura\Http\Controllers\Indicadores;

use Illuminate\Support\Facades\DB;
use yura\Modelos\Indicador;
use yura\Modelos\Pedido;
use yura\Modelos\Variedad;
use yura\Modelos\IndicadorVariedad;

class Venta
{
    public static function dinero_y_precio_x_ramo_7_dias_atras()
    {
        $model_1 = getIndicadorByName('D3');  // Precio promedio por ramo (-7 días)
        $model_2 = getIndicadorByName('D4');  // Dinero ingresado (-7 días)
        if ($model_1 != '' && $model_2 != '') {
            $pedidos_semanal = Pedido::All()->where('estado', 1)
                ->where('fecha_pedido', '>=', opDiasFecha('-', 7, date('Y-m-d')))
                ->where('fecha_pedido', '<=', opDiasFecha('-', 1, date('Y-m-d')));
            $valor = 0;
            $ramos_estandar = 0;
            foreach ($pedidos_semanal as $p) {
                if (!getFacturaAnulada($p->id_pedido)) {
                    $valor += $p->getPrecioByPedido();
                    $ramos_estandar += $p->getRamosEstandar();
                }
            }
            $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
            $model_1->valor = $precio_x_ramo;
            $model_1->save();

            $model_2->valor = $valor;
            $model_2->save();

            /* ============================== INDICADOR x VARIEDAD ================================= */
            foreach (Variedad::All() as $var) {
                $ind_1 = IndicadorVariedad::All()
                    ->where('id_indicador', $model_1->id_indicador)
                    ->where('id_variedad', $var->id_variedad)
                    ->first();
                $ind_2 = IndicadorVariedad::All()
                    ->where('id_indicador', $model_2->id_indicador)
                    ->where('id_variedad', $var->id_variedad)
                    ->first();
                if ($ind_1 == '') {   // es nuevo
                    $ind_1 = new IndicadorVariedad();
                    $ind_1->id_indicador = $model_1->id_indicador;
                    $ind_1->id_variedad = $var->id_variedad;
                }
                if ($ind_2 == '') {   // es nuevo
                    $ind_2 = new IndicadorVariedad();
                    $ind_2->id_indicador = $model_2->id_indicador;
                    $ind_2->id_variedad = $var->id_variedad;
                }

                $valor = 0;
                $ramos_estandar = 0;
                foreach ($pedidos_semanal as $p) {
                    if (!getFacturaAnulada($p->id_pedido)) {
                        $valor += $p->getPrecioByPedidoVariedad($var->id_variedad);
                        $ramos_estandar += $p->getRamosEstandarByVariedad($var->id_variedad);
                    }
                }
                $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
                $ind_1->valor = $precio_x_ramo;
                $ind_1->save();

                $ind_2->valor = $valor;
                $ind_2->save();
            }
        }
    }

    public static function dinero_m2_anno_4_meses_atras()
    {
        $model = getIndicadorByName('D9');  // Venta $/m2/año (-4 meses)
        if ($model != '') {
            $desde_sem = getSemanaByDate(opDiasFecha('-', 112, date('Y-m-d')));
            $hasta_sem = getSemanaByDate(opDiasFecha('-', 7, date('Y-m-d')));

            $venta_mensual = DB::table('proyeccion_venta_semanal_real')
                ->select(DB::raw('sum(valor) as cant'))
                ->where('estado', 1)
                ->where('codigo_semana', '>=', $desde_sem->codigo)
                ->where('codigo_semana', '<=', $hasta_sem->codigo)
                ->get()[0]->cant;

            $semana_desde = getSemanaByDate(opDiasFecha('-', 98, date('Y-m-d')));   // 13 semanas atras
            $semana_hasta = getSemanaByDate(date('Y-m-d'));
            $data = getAreaCiclosByRango($semana_desde->codigo, $semana_hasta->codigo, 'T');
            $area_anual = getAreaActivaFromData($data['variedades'], $data['semanas']);

            $model->valor = $area_anual > 0 ? round(($venta_mensual / round($area_anual * 10000, 2)) * 3, 2) : 0;
            $model->save();
        }
    }

    public static function dinero_m2_anno_1_anno_atras()
    {
        $model = getIndicadorByName('D10');  // Venta $/m2/año (-1 año)
        if ($model != '') {
            $fecha_hasta = date('Y-m-d', strtotime('last month'));
            $fecha_desde = date('Y-m-d', strtotime('last year'));

            $venta_anual = DB::table('historico_ventas')
                ->select(DB::raw('sum(valor) as cant'))
                ->where('anno', '=', substr($fecha_desde, 0, 4))
                ->where('mes', '>=', substr($fecha_desde, 5, 2))
                ->get()[0]->cant;
            if (substr($fecha_desde, 0, 4) != substr($fecha_hasta, 0, 4)) {
                $venta_anual += DB::table('historico_ventas')
                    ->select(DB::raw('sum(valor) as cant'))
                    ->where('anno', '=', substr($fecha_hasta, 0, 4))
                    ->where('mes', '<=', substr($fecha_hasta, 5, 2))
                    ->get()[0]->cant;
            }

            $semana_desde = getSemanaByDate(opDiasFecha('-', 98, date('Y-m-d')));   // 13 semanas atras
            $semana_hasta = getSemanaByDate(date('Y-m-d'));
            $data = getAreaCiclosByRango($semana_desde->codigo, $semana_hasta->codigo, 'T');
            $area_anual = getAreaActivaFromData($data['variedades'], $data['semanas']);

            $model->valor = $area_anual > 0 ? round(($venta_anual / round($area_anual * 10000, 2)), 2) : 0;
            $model->save();
        }
    }

    public static function cajas_equivalentes_vendidas_7_dias_atras()
    {
        $variedades = self::variedades();
        $pedidos_semanal = Pedido::where('estado', 1)
            ->where('fecha_pedido', '>=', opDiasFecha('-', 7, date('Y-m-d')))
            ->where('fecha_pedido', '<=', opDiasFecha('-', 1, date('Y-m-d')))->get();
        $cajasEquivalentes = 0;
        $dataGeneral = [];
        foreach ($pedidos_semanal as $pedido) {
            $cajasEquivalentes += $pedido->getCajas();
            foreach ($variedades as $variedad) {
                $dataGeneral[$variedad->id_variedad][] = [
                    'cajas_x_variedad' => $pedido->getCajasByVariedad($variedad->id_variedad)
                ];
            }

        }

        $indicadorD13 = Indicador::where('nombre', 'D13');
        $indicadorD13->update(['valor' => $cajasEquivalentes]);
        $modelIndicadorD13 = Indicador::where('nombre', 'D13')->first();

        foreach ($dataGeneral as $idVariedad => $data) {
            $cantidadCajas = 0;
            foreach ($data as $cajas)
                $cantidadCajas += $cajas['cajas_x_variedad'];

            //-------- INDICADOR D13 POR VARIEDAD ---------//
            $dataIndicadorD13Variedad = IndicadorVariedad::where([
                ['id_variedad', $idVariedad],
                ['id_indicador', $modelIndicadorD13->id_indicador]
            ])->first();
            if (isset($dataIndicadorD13Variedad)) {
                $objIndicadorD13Variedad = IndicadorVariedad::find($dataIndicadorD13Variedad->id_indicador_variedad);
            } else {
                $objIndicadorD13Variedad = new IndicadorVariedad;
            }
            $objIndicadorD13Variedad->id_variedad = $idVariedad;
            $objIndicadorD13Variedad->id_indicador = $modelIndicadorD13->id_indicador;
            $objIndicadorD13Variedad->valor = $cantidadCajas;
            $objIndicadorD13Variedad->save();
        }
    }

    public static function precio_por_tallo_7_dias_atras()
    {
        $pedidos_semanal = Pedido::where('estado', 1)
            ->where('fecha_pedido', '>=', opDiasFecha('-', 7, date('Y-m-d')))
            ->where('fecha_pedido', '<=', opDiasFecha('-', 1, date('Y-m-d')))->get();
        $valor = 0;
        $tallos = 0;
        foreach ($pedidos_semanal as $pedido) {
            $valor += $pedido->getPrecioByPedido();
            $tallos += $pedido->getTallos();
        }
        $precioXTallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;
        $indicadorD14 = getIndicadorByName('D14');  // Precio por tallo (-7 dias)
        $indicadorD14->valor = $precioXTallo;
        $indicadorD14->save();

        /* ============================== INDICADOR x VARIEDAD ================================= */
        foreach (Variedad::All() as $var) {
            $ind = IndicadorVariedad::All()
                ->where('id_indicador', $indicadorD14->id_indicador)
                ->where('id_variedad', $var->id_variedad)
                ->first();
            if ($ind == '') {   // es nuevo
                $ind = new IndicadorVariedad();
                $ind->id_indicador = $indicadorD14->id_indicador;
                $ind->id_variedad = $var->id_variedad;
            }
            $valor = 0;
            $tallos = 0;
            foreach ($pedidos_semanal as $pedido) {
                $valor += $pedido->getPrecioByPedidoVariedad($var->id_variedad);
                $tallos += $pedido->getTallosByVariedad($var->id_variedad);
            }
            $precioXTallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

            $ind->valor = $precioXTallo;
            $ind->save();
        }
    }

    public static function variedades()
    {
        return Variedad::where('estado', 1)->select('id_variedad')->get();
    }
}
