<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\Ciclo;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\Cosecha;

class crmAreaController extends Controller
{
    public function inicio(Request $request)
    {
        /* =========== TODAY ============= */
        /*$pedidos_today = Pedido::All()->where('estado', 1)->where('fecha_pedido', date('Y-m-d'));
        $cajas = 0;
        $valor = 0;
        foreach ($pedidos_today as $p) {
            $cajas += $p->getCajas();
            $valor += $p->getPrecio();
        }
        $today = [
            'cajas' => $cajas,
            'valor' => $valor
        ];*/

        /* =========== SEMANAL ============= */
        $semana14 = getSemanaByDate(opDiasFecha('-', 7 * getConfiguracionEmpresa()->semanas_ciclo, date('Y-m-d')));

        $ciclos_ini = Ciclo::All()->where('estado', 1)
            ->where('fecha_inicio', '>=', $semana14->fecha_inicial)
            ->where('fecha_inicio', '<=', $semana14->fecha_final);

        $area = 0;
        $ciclo = 0;
        foreach ($ciclos_ini as $c) {
            $area += $c->area;
            $fin = date('Y-m-d');
            if ($c->fecha_fin != '')
                $fin = $c->fecha_fin;
            $ciclo += difFechas($fin, $c->fecha_inicio)->days;
        }
        $ciclo = count($ciclos_ini) > 0 ? round($ciclo / count($ciclos_ini), 2) : 0;

        $labels = DB::table('clasificacion_verde as v')
            ->select('v.fecha_ingreso as dia')->distinct()
            ->where('v.fecha_ingreso', '>=', opDiasFecha('-', 7, date('Y-m-d')))
            ->where('v.fecha_ingreso', '<=', opDiasFecha('-', 1, date('Y-m-d')))
            ->get();
        $tallos = 0;
        $ramos = 0;
        foreach ($labels as $dia) {
            $verde = ClasificacionVerde::All()->where('fecha_ingreso', '=', $dia->dia)->first();
            $cosecha = Cosecha::All()->where('fecha_ingreso', '=', $dia->dia)->first();
            if ($verde != '') {
                $ramos += $verde->getTotalRamosEstandar();
                $tallos += $cosecha->getTotalTallos();
            }
        }

        $semanal = [
            'area' => $area,
            'ciclo' => $ciclo,
            'tallos' => $tallos,
            'ramos' => $ramos,
        ];

        /* ======= AÑOS ======= */
        $annos = DB::table('ciclo')
            ->select(DB::raw('year(fecha_inicio) as anno'))->distinct()
            ->get();

        return view('adminlte.crm.crm_area.inicio', [
            'semanal' => $semanal,
            'annos' => $annos,
        ]);
    }

}
