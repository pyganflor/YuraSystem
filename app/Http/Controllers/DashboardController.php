<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function recepciones(Request $request)
    {
        $query_labels = DB::table('recepcion as r')
            ->join('desglose_recepcion as dr', 'dr.id_recepcion', '=', 'r.id_recepcion')
            ->select(DB::raw('Day(r.fecha_ingreso) as mes'), DB::raw('year(r.fecha_ingreso) as year'), DB::raw('SUM(dr.cantidad_mallas * dr.tallos_x_malla) as cantidad'))->distinct()
            ->orderBy('r.fecha_ingreso')
            ->groupBy(DB::raw('Day(r.fecha_ingreso)'), DB::raw('year(r.fecha_ingreso)'))
            ->get();

        return view('adminlte.crm.dashboard.recepciones', [
            'labels' => $query_labels
        ]);
    }
}
