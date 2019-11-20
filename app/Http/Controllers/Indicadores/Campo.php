<?php

namespace yura\Http\Controllers\Indicadores;

use yura\Modelos\Cosecha;

class Campo
{
    public static function tallos_cosechados_7_dias_atras()
    {
        $model = getIndicadorByName('D11');  // Tallos cosechados (-7 días)
        if ($model != '') {
            $cosechas = Cosecha::All()->where('estado', 1)
                ->where('fecha_ingreso', '>=', opDiasFecha('-', 7, date('Y-m-d')))
                ->where('fecha_ingreso', '<=', opDiasFecha('-', 1, date('Y-m-d')));
            $valor = 0;
            foreach ($cosechas as $c) {
                $valor += $c->getTotalTallos();
            }
            $model->valor = $valor;
            $model->save();
        }
    }
}