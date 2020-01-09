<?php
/**
 * Created by PhpStorm.
 * User: Rafael Prats
 * Date: 2020-01-09
 * Time: 12:19
 */

namespace yura\Http\Controllers\Indicadores;

use Illuminate\Support\Facades\DB;

class Costos
{
    public static function mano_de_obra_1_semana_atras()
    {
        $model = getIndicadorByName('C1');  // Costos Mano de Obra (-1 semana)
        if ($model != '') {
            $dias = 7;
            $semana = '';
            $valor = 0;
            while ($valor <= 0) {
                $dias += 7;
                $semana = getSemanaByDate(opDiasFecha('-', $dias, date('Y-m-d')));
                if ($semana != '') {
                    $valor = DB::table('costos_semana_mano_obra')
                        ->select(DB::raw('sum(valor) as cant'))
                        ->where('codigo_semana', $semana->codigo)
                        ->get()[0]->cant;
                } else {
                    return false;
                }
            }

            $model->valor = $semana->codigo . ':' . round($valor, 2);
            $model->save();
        }
    }
}