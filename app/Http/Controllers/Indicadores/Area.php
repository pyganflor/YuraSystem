<?php

namespace yura\Http\Controllers\Indicadores;

use Illuminate\Support\Facades\DB;

class Area
{
    public static function area_produccion_4_meses_atras()
    {
        $desde = opDiasFecha('-', 28, date('Y-m-d'));
        $hasta = opDiasFecha('-', 7, date('Y-m-d'));

        $model = getIndicadorByName('D7');  // Calibre (7 días)
        if ($model != '') {
            $semanas_4 = DB::table('semana as s')
                ->select('s.codigo as semana')->distinct()
                ->Where(function ($q) use ($desde, $hasta) {
                    $q->where('s.fecha_inicial', '>=', $desde)
                        ->where('s.fecha_inicial', '<=', $hasta);
                })
                ->orWhere(function ($q) use ($desde, $hasta) {
                    $q->where('s.fecha_final', '>=', $desde)
                        ->Where('s.fecha_final', '<=', $hasta);
                })
                ->orderBy('codigo')
                ->get();

            $area = 0;
            $data_4semanas = getAreaCiclosByRango($semanas_4[0]->semana, $semanas_4[3]->semana, 'T');

            foreach ($data_4semanas['variedades'] as $var) {
                foreach ($var['ciclos'] as $c) {
                    foreach ($c['areas'] as $a) {
                        $area += $a;
                    }
                }
            }

            $model->valor = $area;
            $model->save();
        }
    }

    public static function ramos_m2_anno_4_meses_atras()
    {
        $desde = opDiasFecha('-', 28, date('Y-m-d'));
        $hasta = opDiasFecha('-', 7, date('Y-m-d'));

        $model = getIndicadorByName('D8');  // Calibre (7 días)
        if ($model != '') {
            $desde = opDiasFecha('-', 28, date('Y-m-d'));
            $hasta = opDiasFecha('-', 7, date('Y-m-d'));

            $semanas_4 = DB::table('semana as s')
                ->select('s.codigo as semana')->distinct()
                ->Where(function ($q) use ($desde, $hasta) {
                    $q->where('s.fecha_inicial', '>=', $desde)
                        ->where('s.fecha_inicial', '<=', $hasta);
                })
                ->orWhere(function ($q) use ($desde, $hasta) {
                    $q->where('s.fecha_final', '>=', $desde)
                        ->Where('s.fecha_final', '<=', $hasta);
                })
                ->orderBy('codigo')
                ->get();

            $data_ciclos = getCiclosCerradosByRango($semanas_4[0]->semana, $semanas_4[3]->semana, 'T');
            $ciclo = $data_ciclos['ciclo'];
            $area_cerrada = $data_ciclos['area_cerrada'];
            $tallos_ciclo = $data_ciclos['tallos_cosechados'];

            $data_cosecha = getCosechaByRango($semanas_4[0]->semana, $semanas_4[3]->semana, 'T');
            $calibre_ciclo = $data_cosecha['calibre'];
            $ramos_ciclo = $calibre_ciclo > 0 ? round($tallos_ciclo / $calibre_ciclo, 2) : 0;

            $ciclo_ano = $area_cerrada > 0 ? round(365 / $ciclo, 2) : 0;

            $mensual = [
                'ciclo_ano' => $ciclo_ano,
                'ciclo' => $ciclo,
                'area_cerrada' => $area_cerrada,
                'tallos_m2' => $area_cerrada > 0 ? round($tallos_ciclo / $area_cerrada, 2) : 0,
                'ramos_m2' => $area_cerrada > 0 ? round($ramos_ciclo / $area_cerrada, 2) : 0,
                'ramos_m2_anno' => $area_cerrada > 0 ? round($ciclo_ano * round($ramos_ciclo / $area_cerrada, 2), 2) : 0,
            ];

            $model->valor = $mensual['ramos_m2_anno'];
            $model->save();
        }
    }
}