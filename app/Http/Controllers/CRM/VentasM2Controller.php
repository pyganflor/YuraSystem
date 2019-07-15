<?php

namespace yura\Http\Controllers\CRM;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\Pedido;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;

class VentasM2Controller extends Controller
{
    public function inicio(Request $request)
    {
        $array_variedades = [];
        $total_mensual = 0;
        $total_anual = 0;
        foreach (getVariedades() as $var) {
            /* =========================== 4 SEMANAS ======================== */
            $desde = opDiasFecha('-', 28, date('Y-m-d'));
            $hasta = opDiasFecha('-', 7, date('Y-m-d'));
            $semana_desde = getSemanaByDate($desde);
            $semana_hasta = getSemanaByDate($hasta);
            /* --------------------------- dinero --------------------------- */
            $venta = getVentaByRango($semana_desde->codigo, $semana_hasta->codigo, $var->id_variedad);
            /* --------------------------- area cerrada --------------------------- */
            $ciclos = getCiclosCerradosByRango($semana_desde->codigo, $semana_hasta->codigo, $var->id_variedad);
            $area = $ciclos['area_cerrada'];
            /* --------------------------- ciclo año --------------------------- */
            $ciclos['ciclo'] > 0 ? $ciclo_anno = round(365 / $ciclos['ciclo']) : $ciclo_anno = 0;

            /* =========================== 1 AÑO ======================== */
            $fecha_hasta = date('Y-m-d', strtotime('last month'));
            $fecha_desde = date('Y-m-d', strtotime('last year'));

            $data_venta_anual = DB::table('historico_ventas')
                ->select(DB::raw('sum(valor) as cant'))
                ->where('id_variedad', '=', $var->id_variedad)
                ->where('anno', '=', substr($fecha_desde, 0, 4))
                ->where('mes', '>=', substr($fecha_desde, 5, 2))
                ->get()[0]->cant;
            if (substr($fecha_desde, 0, 4) != substr($fecha_hasta, 0, 4)) {
                $data_venta_anual += DB::table('historico_ventas')
                    ->select(DB::raw('sum(valor) as cant'))
                    ->where('id_variedad', '=', $var->id_variedad)
                    ->where('anno', '=', substr($fecha_hasta, 0, 4))
                    ->where('mes', '<=', substr($fecha_hasta, 5, 2))
                    ->get()[0]->cant;
            }
            $semana_desde = getSemanaByDate(opDiasFecha('-', 91, date('Y-m-d')));
            $semana_hasta = getSemanaByDate(date('Y-m-d'));
            $data = getAreaCiclosByRango($semana_desde->codigo, $semana_hasta->codigo, $var->id_variedad);
            $data_area_anual = getAreaActivaFromData($data['variedades'], $data['semanas']);

            if (($venta > 0 && $area > 0) || ($data_area_anual > 0 && $data_venta_anual > 0)) {
                array_push($array_variedades, [
                    'variedad' => $var,
                    'venta' => $venta['valor'],
                    'area_cerrada' => $area,
                    'ciclo_anno' => $ciclo_anno,
                    'area_anual' => $data_area_anual,
                    'venta_anual' => $data_venta_anual,
                ]);

                if ($area > 0)
                    $total_mensual += round(($venta['valor'] / $area) * $ciclo_anno, 2);
                if ($data_area_anual > 0)
                    $total_anual += round(($data_venta_anual / round($data_area_anual * 10000, 2)), 2);
            }
        }

        return view('adminlte.crm.ventas_m2.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'variedades' => $array_variedades,
            'total_mensual' => $total_mensual,
            'total_anual' => $total_anual,
        ]);
    }
}