<?php

namespace yura\Http\Controllers\Indicadores;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Cosecha;
use yura\Modelos\IndicadorVariedad;
use yura\Modelos\ResumenSemanaCosecha;
use yura\Modelos\Variedad;

class Area
{
    public static function area_produccion_4_semanas_atras()
    {
        $desde = opDiasFecha('-', 28, date('Y-m-d'));
        $hasta = opDiasFecha('-', 7, date('Y-m-d'));

        $model = getIndicadorByName('D7');  // Calibre (-7 días)
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

            $model->valor = round($area / count($semanas_4), 2);
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
                $area = 0;
                $data_4semanas = getAreaCiclosByRango($semanas_4[0]->semana, $semanas_4[3]->semana, $var->id_variedad);
                foreach ($data_4semanas['variedades'] as $v) {
                    foreach ($v['ciclos'] as $c) {
                        foreach ($c['areas'] as $a) {
                            $area += $a;
                        }
                    }
                }

                $ind->valor = round($area / count($semanas_4), 2);
                $ind->save();
            }
        }
    }

    public static function ciclo_4_semanas_atras()
    {
        $model = getIndicadorByName('DA1');  // Ciclo (-4 semanas)
        if ($model != '') {
            $desde = opDiasFecha('-', 28, date('Y-m-d'));
            $hasta = opDiasFecha('-', 7, date('Y-m-d'));

            $fechas = DB::table('semana as s')
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

            $data_ciclos = getCiclosCerradosByRango($fechas[0]->semana, $fechas[3]->semana, 'T');
            $ciclo = $data_ciclos['ciclo'];

            $model->valor = $ciclo;
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
                $data_ciclos = getCiclosCerradosByRango($fechas[0]->semana, $fechas[3]->semana, $var->id_variedad);
                $ciclo = $data_ciclos['ciclo'];
                $ind->valor = $ciclo;
                $ind->save();
            }
        }
    }

    public static function ramos_m2_anno_4_semanas_atras()
    {
        $desde = opDiasFecha('-', 28, date('Y-m-d'));
        $hasta = opDiasFecha('-', 7, date('Y-m-d'));

        $model = getIndicadorByName('D8');  // Calibre (-7 días)
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
                $data_ciclos = getCiclosCerradosByRango($semanas_4[0]->semana, $semanas_4[3]->semana, $var->id_variedad);
                $ciclo = $data_ciclos['ciclo'];
                $area_cerrada = $data_ciclos['area_cerrada'];
                $tallos_ciclo = $data_ciclos['tallos_cosechados'];

                $calibre = 0;
                $resumen = ResumenSemanaCosecha::All()
                    ->where('estado', 1)
                    ->where('id_variedad', $var->id_variedad)
                    ->where('codigo_semana', '>=', $semanas_4[0]->semana)
                    ->where('codigo_semana', '<=', $semanas_4[3]->semana);
                $cant_calibres = 0;
                foreach ($resumen as $r) {
                    $c = $r->calibre;
                    if ($c > 0)
                        $cant_calibres++;
                    $calibre += $c;
                }

                //$data_cosecha = getCosechaByRango($semanas_4[0]->semana, $semanas_4[3]->semana, $var->id_variedad);
                //$calibre_ciclo = $data_cosecha['calibre'];

                $calibre_ciclo = $cant_calibres > 0 ? round($calibre / $cant_calibres, 2) : 0;
                $ramos_ciclo = $calibre_ciclo > 0 ? round($tallos_ciclo / $calibre_ciclo, 2) : 0;

                $ciclo_ano = $area_cerrada > 0 ? round(365 / $ciclo, 2) : 0;

                Log::info('VAR = ' . $var->siglas . ', calibre = ' . $calibre_ciclo . ', tallos = ' . $tallos_ciclo . ', ciclo = ' . $ciclo_ano . ', ramos_ciclo = ' . $ramos_ciclo . ', area_cerrada = ' . $area_cerrada);

                $mensual = [
                    'ciclo_ano' => $ciclo_ano,
                    'ciclo' => $ciclo,
                    'area_cerrada' => $area_cerrada,
                    'tallos_m2' => $area_cerrada > 0 ? round($tallos_ciclo / $area_cerrada, 2) : 0,
                    'ramos_m2' => $area_cerrada > 0 ? round($ramos_ciclo / $area_cerrada, 2) : 0,
                    'ramos_m2_anno' => $area_cerrada > 0 ? round($ciclo_ano * round($ramos_ciclo / $area_cerrada, 2), 2) : 0,
                ];

                $ind->valor = $mensual['ramos_m2_anno'];
                $ind->save();
            }
        }
    }

    public static function tallos_m2_4_semanas_atras()
    {
        $model = getIndicadorByName('D12');  // Tallos/m2 (-4 meses)
        if ($model != '') {
            $desde_sem = getSemanaByDate(opDiasFecha('-', 28, date('Y-m-d')));
            $hasta_sem = getSemanaByDate(opDiasFecha('-', 7, date('Y-m-d')));

            $data_ciclos = getCiclosCerradosByRango($desde_sem->codigo, $hasta_sem->codigo, 'T');

            $model->valor = $data_ciclos['area_cerrada'] > 0 ? round($data_ciclos['tallos_cosechados'] / $data_ciclos['area_cerrada'], 2) : 0;
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
                $data_ciclos = getCiclosCerradosByRango($desde_sem->codigo, $hasta_sem->codigo, $var->id_variedad);

                $ind->valor = $data_ciclos['area_cerrada'] > 0 ? round($data_ciclos['tallos_cosechados'] / $data_ciclos['area_cerrada'], 2) : 0;
                $ind->save();
            }
        }
    }

    public static function ramos_m2_4_semanas_atras()
    {
        $model = getIndicadorByName('DA2');  // Ramos/m2 (-4 meses)
        if ($model != '') {
            $desde = opDiasFecha('-', 28, date('Y-m-d'));
            $hasta = opDiasFecha('-', 7, date('Y-m-d'));

            $fechas = DB::table('semana as s')
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

            $data_ciclos = getCiclosCerradosByRango($fechas[0]->semana, $fechas[3]->semana, 'T');
            $area_cerrada = $data_ciclos['area_cerrada'];
            $tallos = $data_ciclos['tallos_cosechados'];

            $data_cosecha = getCosechaByRango($fechas[0]->semana, $fechas[3]->semana, 'T');
            $calibre = $data_cosecha['calibre'];
            $calibre > 0 ? $ramos = round($tallos / $calibre, 2) : $ramos = 0;

            $model->valor = $area_cerrada > 0 ? round($ramos / $area_cerrada, 2) : 0;
            $model->save();
        }
    }
}