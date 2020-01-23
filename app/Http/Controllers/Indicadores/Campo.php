<?php

namespace yura\Http\Controllers\Indicadores;

use yura\Modelos\Cosecha;
use yura\Modelos\IndicadorVariedad;
use yura\Modelos\Variedad;

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
                foreach ($cosechas as $c) {
                    $valor += $c->getTotalTallosByVariedad($var->id_variedad);
                }
                $ind->valor = $valor;
                $ind->save();
            }
        }
    }
}