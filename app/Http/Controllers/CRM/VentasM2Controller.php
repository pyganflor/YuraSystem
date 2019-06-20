<?php

namespace yura\Http\Controllers\CRM;

use Carbon\Carbon;
use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;

class VentasM2Controller extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.crm.ventas_m2.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function chart_m2(Request $request)
    {
        $hasta = date('m');
        $desde = date('m');

        if ($desde == '12') {
            $desde = '01';
            $desde_anno = date('Y');
        } else {
            $desde = $desde + 1;
            $desde_anno = date('Y') - 1;
        }
        if (strlen($desde) != 2)
            $desde = '0' . $desde;

        $desde = $desde_anno . '-' . $desde . '-01';
        $hasta = date('Y') . '-' . $hasta . '-01';

        $meses = [];
        for ($i = 1, $x = 1; $x <= 12; $i += 28) {
            $item = [
                'mes' => substr(opDiasFecha('+', $i, $desde), 5, 2),
                'anno' => substr(opDiasFecha('+', $i, $desde), 0, 4),
            ];
            if (!in_array($item, $meses)) {
                array_push($meses, $item);
                $x++;
            }
        }

        $array_valor = [];
        foreach ($meses as $mes) {
            $venta = getHistoricoVentaByMes($mes['mes'], $mes['anno'], $request->variedad);
            $area_cerrada = getCiclosCerradosByRango($mes['anno'] . '-' . $mes['mes'] . '-01', date("Y-m-t", strtotime($mes['anno'] . '-' . $mes['mes'] . '-01')), $request->variedad, false);
            $area_cerrada = $area_cerrada['area_cerrada'];
            $valor = $area_cerrada > 0 ? round($venta / $area_cerrada, 2) : 0;

            array_push($array_valor, $valor);
        }

        return view('adminlte.crm.ventas_m2.partials.chart_m2', [
            'meses' => $meses,
            'array_valor' => $array_valor,
        ]);
    }

    public function chart_m2_anno(Request $request)
    {
        $semanas = [];
        $semana_actual = getSemanaByDate(date('Y-m-d'))->codigo;
        if (substr($semana_actual, 2) == '52') {
            $semana_inicial = substr(date('Y'), 2) . '01';
        } else {
            $semana_inicial = substr(date('Y'), 2) - 1 . substr($semana_actual, 2) + 1;
        }

        for ($i = $semana_inicial; $i <= $semana_actual; $i++) {
            $sem = Semana::All()
                ->where('estado', 1)
                ->where('codigo', $i)->first();

            if ($sem != '')
                array_push($semanas, $sem);
        }

        $array_valor = [];

        foreach ($semanas as $sem) {
            $venta = getVentaByRango($sem->codigo, $sem->codigo, $request->variedad);
            $ciclos = getCiclosCerradosByRango($sem->codigo, $sem->codigo, $request->variedad, true);
            $area_cerrada = $ciclos['area_cerrada'];
            $ciclo_anno = $ciclos['ciclo'] > 0 ? round(365 / $ciclos['ciclo'], 2) : 0;
            $valor = $area_cerrada > 0 ? round(($venta / $area_cerrada) * $ciclo_anno, 2) : 0;

            array_push($array_valor, $valor);
        }
        return view('adminlte.crm.ventas_m2.partials.chart_m2_anno', [
            'semanas' => $semanas,
            'array_valor' => $array_valor,
        ]);
    }
}