<?php

namespace yura\Http\Controllers\Indicadores;

use yura\Modelos\Pedido;

class Venta
{
    public static function dinero_y_precio_x_ramo_7_dias_atras()
    {
        $model_1 = getIndicadorByName('D3');  // Precio promedio por ramo (7 días)
        $model_2 = getIndicadorByName('D4');  // Dinero ingresado (7 días)
        if ($model_1 != '' && $model_2 != '') {
            $pedidos_semanal = Pedido::All()->where('estado', 1)
                ->where('fecha_pedido', '>=', opDiasFecha('-', 7, date('Y-m-d')))
                ->where('fecha_pedido', '<=', opDiasFecha('-', 1, date('Y-m-d')));
            $valor = 0;
            $ramos_estandar = 0;
            foreach ($pedidos_semanal as $p) {
                $valor += $p->getPrecio();
                $ramos_estandar += $p->getRamosEstandar();
            }
            $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;

            $model_1->valor = $precio_x_ramo;
            $model_1->save();
            $model_2->valor = $valor;
            $model_2->save();
        }
    }
}