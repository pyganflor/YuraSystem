<?php
/**
 * Created by PhpStorm.
 * User: Rafael Prats
 * Date: 2019-11-19
 * Time: 11:20
 */

namespace yura\Http\Controllers\Indicadores;


use yura\Modelos\ClasificacionVerde;

class Verde
{
    public static function tallos_clasificados_7_atras()
    {
        $model = getIndicadorByName('D2');  // Tallos clasificados (7 días)
        if ($model != '') {
            $verdes = ClasificacionVerde::All()->where('estado', 1)
                ->where('v.fecha_ingreso', '>=', opDiasFecha('-', 7, date('Y-m-d')))
                ->where('v.fecha_ingreso', '<=', opDiasFecha('-', 1, date('Y-m-d')));
            $valor = 0;
            foreach ($verdes as $v) {
                $valor += $v->total_tallos();
            }
            $model->valor = $valor;
            $model->save();
        }
    }
}