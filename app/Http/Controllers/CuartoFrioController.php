<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\Empaque;
use yura\Modelos\InventarioFrio;
use yura\Modelos\Submenu;
use yura\Modelos\UnidadMedida;
use yura\Modelos\Variedad;

class CuartoFrioController extends Controller
{
    public function inicio(Request $request)
    {
        $inventarios = [];
        $query = DB::table('inventario_frio as i')
            ->select(DB::raw('sum(i.disponibles) as disponibles'),
                'i.id_variedad', 'i.id_clasificacion_ramo', 'i.id_empaque_p', 'i.tallos_x_ramo', 'i.longitud_ramo', 'i.id_unidad_medida')
            ->join('variedad as v', 'v.id_variedad', '=', 'i.id_variedad')
            ->join('clasificacion_ramo as r', 'r.id_clasificacion_ramo', '=', 'i.id_clasificacion_ramo')
            ->where('i.estado', '=', 1)
            ->where('i.disponibles', '>', 0)
            ->where('i.disponibilidad', '=', 1)
            ->where('i.basura', '=', 0)
            ->groupBy('i.id_variedad', 'i.id_clasificacion_ramo', 'i.id_empaque_p', 'i.tallos_x_ramo', 'i.longitud_ramo', 'i.id_unidad_medida')
            ->orderBy('v.nombre')
            ->orderBy('r.nombre', 'desc')
            ->orderBy('i.fecha_ingreso')
            ->get();

        foreach ($query as $q) {
            $dias = [];
            for ($i = 0; $i <= 9; $i++) {
                $ingresos = DB::table('inventario_frio as i')
                    ->select(DB::raw('sum(i.disponibles) as cant'))
                    ->where('i.estado', '=', 1)
                    ->where('i.disponibles', '>', 0)
                    ->where('i.disponibilidad', '=', 1)
                    ->where('i.basura', '=', 0)
                    ->where('i.id_variedad', '=', $q->id_variedad)
                    ->where('i.id_clasificacion_ramo', '=', $q->id_clasificacion_ramo)
                    ->where('i.id_empaque_p', '=', $q->id_empaque_p)
                    ->where('i.tallos_x_ramo', '=', $q->tallos_x_ramo)
                    ->where('i.longitud_ramo', '=', $q->longitud_ramo)
                    ->where('i.id_unidad_medida', '=', $q->id_unidad_medida);
                if ($i == 9)
                    $ingresos = $ingresos->where('i.fecha_ingreso', '<=', opDiasFecha('-', $i, date('Y-m-d')));
                else
                    $ingresos = $ingresos->where('i.fecha_ingreso', '=', opDiasFecha('-', $i, date('Y-m-d')));
                $ingresos = $ingresos->get();

                array_push($dias, [
                    'dia' => $i,
                    'cantidad' => $ingresos[0]->cant,
                ]);
            }
            array_push($inventarios, [
                'variedad' => Variedad::find($q->id_variedad),
                'peso' => ClasificacionRamo::find($q->id_clasificacion_ramo),
                'presentacion' => Empaque::find($q->id_empaque_p),
                'tallos_x_ramo' => $q->tallos_x_ramo,
                'longitud_ramo' => $q->longitud_ramo,
                'unidad_medida' => UnidadMedida::find($q->id_unidad_medida),
                'disponibles' => $q->disponibles,
                'dias' => $dias
            ]);
        }

        return view('adminlte.gestion.postcocecha.cuarto_frio.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],

            'inventarios' => $inventarios
        ]);
    }
}