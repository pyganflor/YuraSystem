<?php

namespace yura\Http\Controllers\CRM;

use Carbon\Carbon;
use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Pedido;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;

class VentasM2Controller extends Controller
{
    public function inicio(Request $request)
    {
        $array_variedades = [];
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

            if ($venta > 0 && $area > 0)
                array_push($array_variedades, [
                    'variedad' => $var,
                    'venta' => $venta['valor'],
                    'area_cerrada' => $area,
                    'ciclo_anno' => $ciclo_anno,
                ]);
        }

        return view('adminlte.crm.ventas_m2.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'variedades' => $array_variedades,
        ]);
    }
}