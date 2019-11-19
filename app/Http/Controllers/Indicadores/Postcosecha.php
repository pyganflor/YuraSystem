<?php

namespace yura\Http\Controllers\Indicadores;

use yura\Modelos\ClasificacionVerde;

class Postcosecha
{
    public static function calibre_7_dias_atras()
    {
        $dia_7_atras = opDiasFecha('-', 7, date('Y-m-d'));
        $dia_1_atras = opDiasFecha('-', 1, date('Y-m-d'));

        $model = getIndicadorByName('D1');  // Calibre (7 días)
        if ($model != '') {
            $valor = getCalibreByRangoVariedad($dia_7_atras, $dia_1_atras, 'T');
            $model->valor = $valor;
            $model->save();
        }
    }

    public static function tallos_clasificados_7_dias_atras()
    {
        $model = getIndicadorByName('D2');  // Tallos clasificados (7 días)
        if ($model != '') {
            $verdes = ClasificacionVerde::All()->where('estado', 1)
                ->where('fecha_ingreso', '>=', opDiasFecha('-', 7, date('Y-m-d')))
                ->where('fecha_ingreso', '<=', opDiasFecha('-', 1, date('Y-m-d')));
            $valor = 0;
            foreach ($verdes as $v) {
                $valor += $v->total_tallos();
            }
            $model->valor = $valor;
            $model->save();
        }
    }
}