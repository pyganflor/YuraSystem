<?php
/**
 * Created by PhpStorm.
 * User: Rafael Prats
 * Date: 2019-11-19
 * Time: 10:45
 */
namespace yura\Http\Controllers\Indicadores;

class Calibre
{
    public static function dias_atras_7()
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
}
