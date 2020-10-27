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

            $venta_mensual = DB::table('resumen_semanal_total')
                ->select(DB::raw('sum(valor) as cant'))
                //->where('estado', 1)
                ->where('codigo_semana', '>=', $desde_sem->codigo)
                ->where('codigo_semana', '<=', $hasta_sem->codigo)
                ->get()[0]->cant;

            $semana_desde = getSemanaByDate(opDiasFecha('-', 112, $desde_sem->fecha_inicial));   // 16 semanas atras
            $semana_hasta = $desde_sem;

            //$data = getAreaCiclosByRango($semana_desde->codigo, $semana_hasta->codigo, 'T');
            $data = getAreaCiclosByRango($desde_sem->codigo, $hasta_sem->codigo, 'T');
            $area_anual = getAreaActivaFromData($data['variedades'], $data['semanas']) * 10000;

            //dd($desde_sem->codigo, $hasta_sem->codigo, $venta_mensual, $semana_desde->codigo, $semana_hasta->codigo, $area_anual);

            $model->valor = $area_anual > 0 ? round(($venta_mensual / $area_anual) * 3, 2) : 0;
            $model->save();

            /* ============================== INDICADOR x VARIEDAD ================================= */
            foreach (Variedad::All() as $var) {
                $ind = IndicadorVariedad::All()
                    ->where('id_indicador', $model->id_indicador)
                    ->where('id_variedad', $var->id_variedad)
                    ->first();
                if ($ind == '') {   // es nuevo
                    $ind = new IndicadorVariedad();
                    $ind->id_indicador = $model->id_indicador;
                    $ind->id_variedad = $var->id_variedad;
                }
                $venta_mensual = DB::table('proyeccion_venta_semanal_real')
                    ->select(DB::raw('sum(valor) as cant'))
                    ->where('estado', 1)
                    ->where('id_variedad', $var->id_variedad)
                    ->where('codigo_semana', '>=', $desde_sem->codigo)
                    ->where('codigo_semana', '<=', $hasta_sem->codigo)
                    ->get()[0]->cant;

                $semana_desde = getSemanaByDate(opDiasFecha('-', 112, $desde_sem->fecha_inicial));   // 16 semanas atras
                $semana_hasta = $desde_sem;
                //$data = getAreaCiclosByRango($semana_desde->codigo, $semana_hasta->codigo, $var->id_variedad);
                $data = getAreaCiclosByRango($desde_sem->codigo, $hasta_sem->codigo, $var->id_variedad);
                $area_anual = getAreaActivaFromData($data['variedades'], $data['semanas']) * 10000;

                $ind->valor = $area_anual > 0 ? round(($venta_mensual / $area_anual) * 3, 2) : 0;
                $ind->save();
            }
        }
    }

    public static function dinero_m2_anno_1_anno_atras()
    {
        $model = getIndicadorByName('D10');  // Venta $/m2/año (-1 año)
        if ($model != '') {
            $desde_sem = getSemanaByDate(opDiasFecha('-', 364, date('Y-m-d')));   // 52 semanas atras
            $hasta_sem = getSemanaByDate(opDiasFecha('-', 7, date('Y-m-d')));

            $venta_mensual = DB::table('resumen_semanal_total')
                ->select(DB::raw('sum(valor) as cant'))
                ->where('codigo_semana', '>=', $desde_sem->codigo)
                ->where('codigo_semana', '<=', $hasta_sem->codigo)
                ->get()[0]->cant;

            $semana_hasta = getSemanaByDate(opDiasFecha('-', 112, date('Y-m-d')));  // 16 semana atras
            $semana_desde = getSemanaByDate(opDiasFecha('-', 364, $semana_hasta->fecha_inicial));   // 16 + 52 semanas atras

            //$data = getAreaCiclosByRango($semana_desde->codigo, $semana_hasta->codigo, 'T');
            $data = getAreaCiclosByRango($desde_sem->codigo, $hasta_sem->codigo, 'T');
            $area_anual = getAreaActivaFromData($data['variedades'], $data['semanas']) * 10000;

            $model->valor = $area_anual > 0 ? round($venta_mensual / $area_anual, 2) : 0;
            $model->save();
        }
    }

    public static function cajas_equivalentes_vendidas_7_dias_atras()
    {
        $model = getIndicadorByName('D13'); // Cajas equivalentes vendidas (-7 días)
        $pedidos_semanal = Pedido::where('estado', 1)
            ->where('fecha_pedido', '>=', opDiasFecha('-', 7, date('Y-m-d')))
            ->where('fecha_pedido', '<=', opDiasFecha('-', 1, date('Y-m-d')))->get();
        $valor = 0;
        foreach ($pedidos_semanal as $pedido) {
            $valor += $pedido->getCajas();
        }

        $model->valor = $valor;
        $model->save();

        /* ============================== INDICADOR x VARIEDAD ================================= */
        foreach (Variedad::All() as $var) {
            $ind = IndicadorVariedad::All()
                ->where('id_indicador', $model->id_indicador)
                ->where('id_variedad', $var->id_variedad)
                ->first();
            if ($ind == '') {   // es nuevo
                $ind = new IndicadorVariedad();
                $ind->id_indicador = $model->id_indicador;
                $ind->id_variedad = $var->id_variedad;
            }
            $valor = 0;
            foreach ($pedidos_semanal as $pedido) {
                $valor += $pedido->getCajasByVariedad($var->id_variedad);
            }

            $ind->valor = $valor;
            $ind->save();
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
