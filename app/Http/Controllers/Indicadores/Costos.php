<?php
/**
 * Created by PhpStorm.
 * User: Rafael Prats
 * Date: 2020-01-09
 * Time: 12:19
 */

namespace yura\Http\Controllers\Indicadores;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Cosecha;
use yura\Modelos\Area;
use yura\Modelos\ResumenCostosSemanal;

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

    public static function costos_fijos_1_semana_atras()
    {
        $model = getIndicadorByName('C7');  // Costos Insumos (-1 semana)
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
            $otros_gastos = DB::table('otros_gastos')
                ->select(DB::raw('sum(gip) as cant_gip'), DB::raw('sum(ga) as cant_ga'))
                ->where('codigo_semana', $semana->codigo)
                ->get()[0];

            $valor = $otros_gastos->cant_gip + $otros_gastos->cant_ga;

            $model->valor = $semana->codigo . ':' . round($valor, 2);
            $model->save();
        }
    }

    public static function costos_regalias_1_semana_atras()
    {
        $model = getIndicadorByName('C8');  // Costos Insumos (-1 semana)
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

            $resumen = ResumenCostosSemanal::All()
                ->where('codigo_semana', $semana->codigo)
                ->first();

            $model->valor = $semana->codigo . ':' . round($resumen->regalias, 2);
            $model->save();
        }
    }

    public static function costos_campo_ha_4_semana_atras()
    {
        $model = getIndicadorByName('C3');  // Costos Campo/ha/semana (-4 semanas)
        if ($model != '') {
            $dias = 7;
            $last_semana = '';
            $valor = 0;
            while ($valor <= 0) {
                $dias += 7;
                $last_semana = getSemanaByDate(opDiasFecha('-', $dias, date('Y-m-d')));
                if ($last_semana != '') {
                    $valor = DB::table('costos_semana')
                        ->select(DB::raw('sum(valor) as cant'))
                        ->where('codigo_semana', $last_semana->codigo)
                        ->get()[0]->cant;
                } else {
                    return false;
                }
            }

            $sem_desde = getSemanaByDate(opDiasFecha('-', 21, $last_semana->fecha_inicial));
            $sem_hasta = $last_semana;

            $area_trabajo = Area::All()
                ->where('estado', 1)
                ->where('nombre', 'CAMPO')
                ->first();
            $insumos = DB::table('costos_semana as c')
                ->select(DB::raw('sum(c.valor) as cant'))
                ->join('actividad_producto as ac', 'ac.id_actividad_producto', '=', 'c.id_actividad_producto')
                ->join('actividad as a', 'a.id_actividad', '=', 'ac.id_actividad')
                ->where('a.id_area', '=', $area_trabajo->id_area)
                ->where('c.codigo_semana', '>=', $sem_desde->codigo)
                ->where('c.codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;
            $mano_obra = DB::table('costos_semana_mano_obra as c')
                ->select(DB::raw('sum(c.valor) as cant'))
                ->join('actividad_mano_obra as am', 'am.id_actividad_mano_obra', '=', 'c.id_actividad_mano_obra')
                ->join('actividad as a', 'a.id_actividad', '=', 'am.id_actividad')
                ->where('a.id_area', '=', $area_trabajo->id_area)
                ->where('c.codigo_semana', '>=', $sem_desde->codigo)
                ->where('c.codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;
            $otros = DB::table('otros_gastos as o')
                ->select(DB::raw('sum(o.gip + o.ga) as cant'))
                ->where('o.id_area', '=', $area_trabajo->id_area)
                ->where('o.codigo_semana', '>=', $sem_desde->codigo)
                ->where('o.codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;

            Log::info('mp = ' . $insumos . ', mo = ' . $mano_obra . ', otros = ' . $otros . ', desde = ' . $sem_desde->codigo . ', hasta = ' . $sem_hasta->codigo . ', area = ' . $area_trabajo->id_area);

            $costos_total = $insumos + $mano_obra + $otros;
            $area = getIndicadorByName('D7');   // Área en producción (-4 semanas)

            $valor = $area->valor > 0 ? round(($costos_total / 4) / ($area->valor / 10000), 2) : 0;
            $model->valor = $valor . '|' . $insumos . '+' . $mano_obra . '+' . $otros . '/' . ($area->valor / 10000);
            $model->save();
        }
    }

    public static function costos_cosecha_tallo_4_semana_atras()
    {
        $model = getIndicadorByName('C4');  // Costos Cosecha x Tallo (-4 semanas)
        if ($model != '') {
            $dias = 7;
            $last_semana = '';
            $valor = 0;
            while ($valor <= 0) {
                $dias += 7;
                $last_semana = getSemanaByDate(opDiasFecha('-', $dias, date('Y-m-d')));
                if ($last_semana != '') {
                    $valor = DB::table('costos_semana')
                        ->select(DB::raw('sum(valor) as cant'))
                        ->where('codigo_semana', $last_semana->codigo)
                        ->get()[0]->cant;
                } else {
                    return false;
                }
            }

            $sem_desde = getSemanaByDate(opDiasFecha('-', 35, $last_semana->fecha_inicial));
            $sem_hasta = $last_semana;

            $area_trabajo = Area::All()
                ->where('estado', 1)
                ->where('nombre', 'COSECHA')
                ->first();
            $insumos = DB::table('costos_semana as c')
                ->select(DB::raw('sum(c.valor) as cant'))
                ->join('actividad_producto as ac', 'ac.id_actividad_producto', '=', 'c.id_actividad_producto')
                ->join('actividad as a', 'a.id_actividad', '=', 'ac.id_actividad')
                ->where('a.id_area', '=', $area_trabajo->id_area)
                ->where('c.codigo_semana', '>=', $sem_desde->codigo)
                ->where('c.codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;
            $mano_obra = DB::table('costos_semana_mano_obra as c')
                ->select(DB::raw('sum(c.valor) as cant'))
                ->join('actividad_mano_obra as am', 'am.id_actividad_mano_obra', '=', 'c.id_actividad_mano_obra')
                ->join('actividad as a', 'a.id_actividad', '=', 'am.id_actividad')
                ->where('a.id_area', '=', $area_trabajo->id_area)
                ->where('c.codigo_semana', '>=', $sem_desde->codigo)
                ->where('c.codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;
            $otros = DB::table('otros_gastos as o')
                ->select(DB::raw('sum(o.gip + o.ga) as cant'))
                ->where('o.id_area', '=', $area_trabajo->id_area)
                ->where('o.codigo_semana', '>=', $sem_desde->codigo)
                ->where('o.codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;

            $costos_total = $insumos + $mano_obra + $otros;

            $cosechas = Cosecha::All()->where('estado', 1)
                ->where('fecha_ingreso', '>=', $sem_desde->fecha_inicial)
                ->where('fecha_ingreso', '<=', $sem_hasta->fecha_final);
            $tallos = 0;
            foreach ($cosechas as $c) {
                $tallos += $c->getTotalTallos();
            }

            $model->valor = $tallos > 0 ? round(($costos_total / $tallos) * 100, 2) : 0;
            $model->save();
        }
    }

    public static function costos_postcosecha_tallo_4_semana_atras()
    {
        $model = getIndicadorByName('C5');  // Costos Postcosecha x Tallo (-4 semanas)
        if ($model != '') {
            $dias = 7;
            $last_semana = '';
            $valor = 0;
            while ($valor <= 0) {
                $dias += 7;
                $last_semana = getSemanaByDate(opDiasFecha('-', $dias, date('Y-m-d')));
                if ($last_semana != '') {
                    $valor = DB::table('costos_semana')
                        ->select(DB::raw('sum(valor) as cant'))
                        ->where('codigo_semana', $last_semana->codigo)
                        ->get()[0]->cant;
                } else {
                    return false;
                }
            }

            $sem_desde = getSemanaByDate(opDiasFecha('-', 35, $last_semana->fecha_inicial));
            $sem_hasta = $last_semana;

            $area_trabajo = Area::All()
                ->where('estado', 1)
                ->where('nombre', 'POSTCOSECHA')
                ->first();
            $insumos = DB::table('costos_semana as c')
                ->select(DB::raw('sum(c.valor) as cant'))
                ->join('actividad_producto as ac', 'ac.id_actividad_producto', '=', 'c.id_actividad_producto')
                ->join('actividad as a', 'a.id_actividad', '=', 'ac.id_actividad')
                ->where('a.id_area', '=', $area_trabajo->id_area)
                ->where('c.codigo_semana', '>=', $sem_desde->codigo)
                ->where('c.codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;
            $mano_obra = DB::table('costos_semana_mano_obra as c')
                ->select(DB::raw('sum(c.valor) as cant'))
                ->join('actividad_mano_obra as am', 'am.id_actividad_mano_obra', '=', 'c.id_actividad_mano_obra')
                ->join('actividad as a', 'a.id_actividad', '=', 'am.id_actividad')
                ->where('a.id_area', '=', $area_trabajo->id_area)
                ->where('c.codigo_semana', '>=', $sem_desde->codigo)
                ->where('c.codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;
            $otros = DB::table('otros_gastos as o')
                ->select(DB::raw('sum(o.gip + o.ga) as cant'))
                ->where('o.id_area', '=', $area_trabajo->id_area)
                ->where('o.codigo_semana', '>=', $sem_desde->codigo)
                ->where('o.codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;

            $costos_total = $insumos + $mano_obra + $otros;

            $cosechas = Cosecha::All()->where('estado', 1)
                ->where('fecha_ingreso', '>=', $sem_desde->fecha_inicial)
                ->where('fecha_ingreso', '<=', $sem_hasta->fecha_final);
            $tallos = 0;
            foreach ($cosechas as $c) {
                $tallos += $c->getTotalTallos();
            }

            $model->valor = $tallos > 0 ? round(($costos_total / $tallos) * 100, 2) : 0;
            $model->save();
        }
    }

    public static function costos_total_tallo_4_semana_atras()
    {
        $model = getIndicadorByName('C6');  // Costos Total x Tallo (-4 semanas)
        if ($model != '') {
            $dias = 7;
            $last_semana = '';
            $valor = 0;
            while ($valor <= 0) {
                $dias += 7;
                $last_semana = getSemanaByDate(opDiasFecha('-', $dias, date('Y-m-d')));
                if ($last_semana != '') {
                    $valor = DB::table('costos_semana')
                        ->select(DB::raw('sum(valor) as cant'))
                        ->where('codigo_semana', $last_semana->codigo)
                        ->get()[0]->cant;
                } else {
                    return false;
                }
            }

            $sem_desde = getSemanaByDate(opDiasFecha('-', 35, $last_semana->fecha_inicial));
            $sem_hasta = $last_semana;

            $insumos = DB::table('costos_semana as c')
                ->select(DB::raw('sum(c.valor) as cant'))
                ->where('c.codigo_semana', '>=', $sem_desde->codigo)
                ->where('c.codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;
            $mano_obra = DB::table('costos_semana_mano_obra as c')
                ->select(DB::raw('sum(c.valor) as cant'))
                ->where('c.codigo_semana', '>=', $sem_desde->codigo)
                ->where('c.codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;
            $otros = DB::table('otros_gastos as o')
                ->select(DB::raw('sum(o.gip + o.ga) as cant'))
                ->where('o.codigo_semana', '>=', $sem_desde->codigo)
                ->where('o.codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;

            $costos_total = $insumos + $mano_obra + $otros;

            $cosechas = Cosecha::All()->where('estado', 1)
                ->where('fecha_ingreso', '>=', $sem_desde->fecha_inicial)
                ->where('fecha_ingreso', '<=', $sem_hasta->fecha_final);
            $tallos = 0;
            foreach ($cosechas as $c) {
                $tallos += $c->getTotalTallos();
            }

            $model->valor = $tallos > 0 ? round(($costos_total / $tallos) * 100, 2) : 0;
            $model->save();
        }
    }

    public static function costos_m2_16_semanas_atras()
    {
        $model = getIndicadorByName('C9');  // Costos/m2 (-16 semanas)
        if ($model != '') {
            $dias = 7;
            $last_semana = '';
            $valor = 0;
            while ($valor <= 0) {
                $dias += 7;
                $last_semana = getSemanaByDate(opDiasFecha('-', $dias, date('Y-m-d')));
                if ($last_semana != '') {
                    $valor = DB::table('costos_semana')
                        ->select(DB::raw('sum(valor) as cant'))
                        ->where('codigo_semana', $last_semana->codigo)
                        ->get()[0]->cant;
                } else {
                    return false;
                }
            }

            $sem_desde = getSemanaByDate(opDiasFecha('-', 112, $last_semana->fecha_inicial));   // 16 semana atras
            $sem_hasta = $last_semana;

            $costos = DB::table('resumen_costos_semanal')
                ->select(DB::raw('sum(mano_obra + insumos + fijos + regalias) as cant'))
                ->where('codigo_semana', '>=', $sem_desde->codigo)
                ->where('codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;
            $area = DB::table('resumen_area_semanal')
                ->select(DB::raw('sum(area) as cant'))
                ->where('codigo_semana', '>=', $sem_desde->codigo)
                ->where('codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;

            //dd($last_semana->codigo, $sem_desde->codigo, $sem_hasta->codigo, $costos . '/' . $area);
            $valor = $area > 0 ? round(($costos / ($area / 16)) * 3, 2) : 0;
            $model->valor = $valor;
            $model->save();
        }
    }

    public static function costos_m2_52_semanas_atras()
    {
        $model = getIndicadorByName('C10');  // Costos/m2 (-52 semanas)
        if ($model != '') {
            $dias = 7;
            $last_semana = '';
            $valor = 0;
            while ($valor <= 0) {
                $dias += 7;
                $last_semana = getSemanaByDate(opDiasFecha('-', $dias, date('Y-m-d')));
                if ($last_semana != '') {
                    $valor = DB::table('costos_semana')
                        ->select(DB::raw('sum(valor) as cant'))
                        ->where('codigo_semana', $last_semana->codigo)
                        ->get()[0]->cant;
                } else {
                    return false;
                }
            }

            $sem_desde = getSemanaByDate(opDiasFecha('-', 364, $last_semana->fecha_inicial));   // 52 semana atras
            $sem_hasta = $last_semana;

            $costos = DB::table('resumen_costos_semanal')
                ->select(DB::raw('sum(mano_obra + insumos + fijos + regalias) as cant'))
                ->where('codigo_semana', '>=', $sem_desde->codigo)
                ->where('codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;
            $area = DB::table('resumen_area_semanal')
                ->select(DB::raw('sum(area) as cant'))
                ->where('codigo_semana', '>=', $sem_desde->codigo)
                ->where('codigo_semana', '<=', $sem_hasta->codigo)
                ->get()[0]->cant;

            //dd($last_semana->codigo, $sem_desde->codigo, $sem_hasta->codigo, $costos . '/' . $area);
            $valor = $area > 0 ? round($costos / ($area / 52), 2) : 0;
            $model->valor = $valor;
            $model->save();
        }
    }

    public static function rentabilidad_4_meses()
    {
        $model = getIndicadorByName('R1');  // Rentabilidad (-4 meses)
        if ($model != '') {
            $valor = getIndicadorByName('D9')->valor - getIndicadorByName('C9')->valor;
            $model->valor = $valor;
            $model->save();
        }
    }

    public static function rentabilidad_1_anno()
    {
        $model = getIndicadorByName('R2');  // Rentabilidad (-1 año)
        if ($model != '') {
            $valor = getIndicadorByName('D10')->valor - getIndicadorByName('C10')->valor;
            $model->valor = $valor;
            $model->save();
        }
    }
}