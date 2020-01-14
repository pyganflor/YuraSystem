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

    public static function costos_insumos_1_semana_atras()
    {
        $model = getIndicadorByName('C2');  // Costos Insumos (-1 semana)
        if ($model != '') {
            $dias = 7;
            $semana = '';
            $valor = 0;
            while ($valor <= 0) {
                $dias += 7;
                $semana = getSemanaByDate(opDiasFecha('-', $dias, date('Y-m-d')));
                if ($semana != '') {
                    $valor = DB::table('costos_semana')
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

    public static function costos_campo_ha_4_semana_atras()
    {
        $model = getIndicadorByName('C3');  // Costos Campo/ha/semana (-4 semanas)
        if ($model != '') {
            $sem_desde = getSemanaByDate(opDiasFecha('-', 35, date('Y-m-d')));
            $sem_hasta = getSemanaByDate(opDiasFecha('-', 7, date('Y-m-d')));

            $insumos = DB::table('costos_semana as c')
                ->select(DB::raw('sum(c.valor) as cant'))
                ->join('actividad_producto as ac', 'ac.id_actividad_producto', '=', 'c.id_actividad_producto')
                ->join('actividad as a', 'a.id_actividad', '=', 'ac.id_actividad')
                ->join('area as ar', 'ar.id_area', '=', 'a.id_area')
                ->where('ar.nombre', '=', 'CAMPO')
                ->where('c.codigo_semana', '>=', $sem_desde->codigo)
                ->where('c.codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;
            $mano_obra = DB::table('costos_semana_mano_obra as c')
                ->select(DB::raw('sum(c.valor) as cant'))
                ->join('actividad_mano_obra as am', 'am.id_actividad_mano_obra', '=', 'c.id_actividad_mano_obra')
                ->join('actividad as a', 'a.id_actividad', '=', 'am.id_actividad')
                ->join('area as ar', 'ar.id_area', '=', 'a.id_area')
                ->where('ar.nombre', '=', 'CAMPO')
                ->where('c.codigo_semana', '>=', $sem_desde->codigo)
                ->where('c.codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;

            $costos_total = $insumos + $mano_obra;
            $area = getIndicadorByName('D7');   // Área en producción (-4 semanas)

            $model->valor = round($costos_total / $area->valor, 2);
            $model->save();
        }
    }
}