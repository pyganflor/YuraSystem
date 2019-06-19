<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
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
        foreach ($meses as $mes) {
            dd(getHistoricoVentaByMes($mes['mes'], $mes['anno']));
        }


        return view('adminlte.crm.ventas_m2.partials.chart_m2', [
            'meses' => $meses
        ]);
    }

    public function chart_m2_anno(Request $request)
    {
        return view('adminlte.crm.ventas_m2.partials.chart_m2_anno', [
        ]);
    }
}