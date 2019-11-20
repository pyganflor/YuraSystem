<?php

namespace yura\Http\Controllers\Indicadores;

use yura\Modelos\ClasificacionBlanco;
use yura\Modelos\ClasificacionVerde;

class Postcosecha
{
    public static function calibre_7_dias_atras()
    {
        $dia_7_atras = opDiasFecha('-', 7, date('Y-m-d'));
        $dia_1_atras = opDiasFecha('-', 1, date('Y-m-d'));

        $model = getIndicadorByName('D1');  // Calibre (-7 días)
        if ($model != '') {
            $valor = getCalibreByRangoVariedad($dia_7_atras, $dia_1_atras, 'T');
            $model->valor = $valor;
            $model->save();
        }
    }

    public static function tallos_clasificados_7_dias_atras()
    {
        $model = getIndicadorByName('D2');  // Tallos clasificados (-7 días)
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

    public static function rendimiento_desecho_7_dias_atras()
    {
        $model_1 = getIndicadorByName('D5');  // Rendimiento (-7 días)
        $model_2 = getIndicadorByName('D6');  // Desecho (-7 días)
        if ($model_1 != '' && $model_2 != '') {
            $fechas = [];
            for ($i = 1; $i <= 7; $i++) {
                array_push($fechas, opDiasFecha('-', $i, date('Y-m-d')));
            }

            $r_ver = 0;
            $r_ver_r = 0;
            $d_ver = 0;
            $count_ver = 0;
            $r_bla = 0;
            $d_bla = 0;
            $count_bla = 0;
            foreach ($fechas as $f) {
                $verde = ClasificacionVerde::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();
                $blanco = ClasificacionBlanco::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();

                if ($verde != '') {
                    $r_ver += $verde->getRendimiento();
                    $r_ver_r += $verde->getRendimientoRamos();
                    $d_ver += $verde->desecho();
                    $count_ver++;
                }
                if ($blanco != '') {
                    $r_bla += $blanco->getRendimiento();
                    $d_bla += $blanco->getDesecho();
                    $count_bla++;
                }
            }

            $rendimiento_desecho = [
                'verde' => [
                    'rendimiento' => $count_ver > 0 ? round($r_ver / $count_ver, 2) : 0,
                    'rendimiento_ramos' => $count_ver > 0 ? round($r_ver_r / $count_ver, 2) : 0,
                    'desecho' => $count_ver > 0 ? round($d_ver / $count_ver, 2) : 0
                ],
                'blanco' => [
                    'rendimiento' => $count_bla > 0 ? round($r_bla / $count_bla, 2) : 0,
                    'desecho' => $count_bla > 0 ? round($d_bla / $count_bla, 2) : 0
                ]
            ];

            $model_1->valor = round(($rendimiento_desecho['blanco']['rendimiento'] + $rendimiento_desecho['verde']['rendimiento_ramos']) / 2, 2);
            $model_1->save();
            $model_2->valor = round(($rendimiento_desecho['blanco']['desecho'] + $rendimiento_desecho['verde']['desecho']) / 2, 2);
            $model_2->save();
        }
    }
}