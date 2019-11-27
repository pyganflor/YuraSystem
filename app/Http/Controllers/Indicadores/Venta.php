<?php

namespace yura\Http\Controllers\Indicadores;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Indicador;
use yura\Modelos\Pedido;

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

    public static function cajas_equivalentes_vendidas_7_dias_atras(){
        $pedidos_semanal = Pedido::where('estado', 1)
            ->where('fecha_pedido', '>=', opDiasFecha('-', 7, date('Y-m-d')))
            ->where('fecha_pedido', '<=', opDiasFecha('-', 1, date('Y-m-d')))->get();
        $cajasEquivalentes=0;
        foreach ($pedidos_semanal as $pedido) {
            $cajasEquivalentes += $pedido->getCajas();
        }

        $indicadorD13 = Indicador::where('nombre','D13');
        $indicadorD13->update('valor',$cajasEquivalentes);
    }
}
