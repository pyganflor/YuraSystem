<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClasificacionBlanco extends Model
{
    protected $table = 'clasificacion_blanco';
    protected $primaryKey = 'id_clasificacion_blanco';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_clasificacion_blanco',
        'personal',
        'hora_inicio',
        'fecha_registro',
        'estado',
        'fecha_ingreso',
    ];

    public function inventarios_frio()
    {
        return $this->hasMany('\yura\Modelos\InventarioFrio', 'id_clasificacion_blanco');
    }

    public function total_ramos()
    {
        $r = 0;
        foreach ($this->inventarios_frio as $inv) {
            $r += $inv->cantidad;
        }
        return $r;
    }

    public function total_ramosByVariedad($variedad)
    {
        $r = 0;
        foreach ($this->inventarios_frio as $inv) {
            if ($inv->id_variedad == $variedad)
                $r += $inv->cantidad;
        }
        return $r;
    }

    function getRendimiento()
    {
        if (count($this->inventarios_frio) > 0 && $this->personal > 0 && $this->getCantidadHorasTrabajo() > 0) {
            $r = $this->total_ramos() / $this->personal;
            $r = $r / $this->getCantidadHorasTrabajo();

            return round($r, 2);
        } else {
            return 0;
        }
    }

    function getRendimientoByVariedad($variedad)
    {
        if (count($this->inventarios_frio) > 0 && $this->personal > 0 && $this->getCantidadHorasTrabajoByVariedad($variedad) > 0) {
            $r = $this->total_ramosByVariedad($variedad) / $this->personal;
            $r = $r / $this->getCantidadHorasTrabajoByVariedad($variedad);

            return round($r, 2);
        } else {
            return 0;
        }
    }

    function getCantidadHorasTrabajo()
    {
        $r = difFechas($this->getLastFechaClasificacion(), $this->getFechaHoraInicio());
        return round($r->h + ($r->i / 60), 2);
    }

    function getCantidadHorasTrabajoByVariedad($variedad)
    {
        $r = difFechas($this->getLastFechaClasificacionByVariedad($variedad), $this->getFechaHoraInicio());
        return round($r->h + ($r->i / 60), 2);
    }

    function getLastFechaClasificacion()
    {
        $r = DB::table('inventario_frio')
            ->select(DB::raw('max(fecha_registro) as fecha'))
            ->where('estado', '=', 1)
            ->where('fecha_ingreso', 'like', $this->fecha_ingreso . '%')
            ->get();
        if (count($r) > 0)
            return $r[0]->fecha;
        else
            return '';
    }

    function getLastFechaClasificacionByVariedad($variedad)
    {
        $r = DB::table('inventario_frio')
            ->select(DB::raw('max(fecha_registro) as fecha'))
            ->where('estado', '=', 1)
            ->where('id_variedad', '=', $variedad)
            ->where('fecha_ingreso', 'like', $this->fecha_ingreso . '%')
            ->get();
        if (count($r) > 0)
            return $r[0]->fecha;
        else
            return '';
    }

    function getFechaHoraInicio()
    {
        return $this->fecha_ingreso . ' ' . $this->hora_inicio . ':00';
    }

    function getIntervalosHoras()
    {
        $r = [];
        $listado_fechas = DB::table('inventario_frio')
            ->select('fecha_registro')->distinct()
            ->where('estado', '=', 1)
            ->where('fecha_ingreso', '=', $this->fecha_ingreso)
            ->orderBy('fecha_registro')
            ->get();

        $listado = [];
        foreach ($listado_fechas as $item)
            array_push($listado, $item->fecha_registro);

        foreach ($listado as $item) {
            $intervalo = [
                'fecha_inicio' => substr($item, 0, 10),
                'fecha_fin' => substr(opHorasFecha('+', 1, substr($item, 0, 13) . ':00'), 0, 10),
                'fecha_inicio_full' => substr($item, 0, 13) . ':00',
                'fecha_fin_full' => opHorasFecha('+', 1, substr($item, 0, 13) . ':00'),
                'hora_inicio' => substr($item, 11, 2) . ':00',
                'hora_fin' => substr(opHorasFecha('+', 1, substr($item, 0, 13) . ':00'), 11, 2) . ':00',
            ];
            if (!in_array($intervalo, $r)) {
                array_push($r, $intervalo);
            }
        }

        return $r;
    }

    function getInventariosByIntervaloFecha($inicio, $fin)
    {
        $listado = DB::table('inventario_frio as inv')
            ->select('inv.id_variedad', 'inv.id_clasificacion_ramo', 'inv.id_empaque_p', 'inv.tallos_x_ramo', 'inv.longitud_ramo',
                'inv.id_unidad_medida', DB::raw('sum(cantidad) as cantidad'))
            ->where('estado', '=', 1)
            ->where('fecha_ingreso', '=', $this->fecha_ingreso)
            ->where('fecha_registro', '>=', $inicio)
            ->where('fecha_registro', '<', $fin)
            ->groupBy('inv.id_variedad', 'inv.id_clasificacion_ramo', 'inv.id_empaque_p', 'inv.tallos_x_ramo', 'inv.longitud_ramo',
                'inv.id_unidad_medida')
            ->orderBy('inv.id_variedad')
            ->get();

        return $listado;
    }

    function getVariedadesByIntervaloFecha($inicio, $fin)
    {
        $listado = DB::table('inventario_frio as inv')
            ->select('inv.id_variedad')->distinct()
            ->where('estado', '=', 1)
            ->where('fecha_ingreso', '=', $this->fecha_ingreso)
            ->where('fecha_registro', '>=', $inicio)
            ->where('fecha_registro', '<', $fin)
            ->get();

        return $listado;
    }

    function getTotalRamosByVariedadIntervaloFecha($variedad, $inicio, $fin)
    {
        $r = DB::table('inventario_frio as inv')
            ->select(DB::raw('sum(cantidad) as cantidad'))
            ->where('estado', '=', 1)
            ->where('fecha_ingreso', '=', $this->fecha_ingreso)
            ->where('id_variedad', '=', $variedad)
            ->where('fecha_registro', '>=', $inicio)
            ->where('fecha_registro', '<', $fin)
            ->groupBy('inv.id_variedad')
            ->orderBy('inv.id_variedad')
            ->get();

        if (count($r) > 0)
            return $r[0]->cantidad;
        else
            return 0;
    }

    function getTotalRamosByIntervaloFecha($inicio, $fin)
    {
        $r = 0;
        foreach ($this->getInventariosByIntervaloFecha($inicio, $fin) as $inv) {
            $r += $inv->cantidad;
        }
        return $r;
    }

    function getDesecho()
    {
        $stock = DB::table('stock_empaquetado')
            ->select(DB::raw('sum(cantidad_ingresada) as ingreso'), DB::raw('sum(cantidad_armada) as armada'))
            ->where('estado', '=', 1)
            ->where('fecha_registro', 'like', $this->fecha_ingreso . '%')
            ->get();

        if ($stock[0]->ingreso > 0 && $stock[0]->armada > 0) {
            return round(($stock[0]->armada / $stock[0]->ingreso) * 100, 2) - 100;
        } else {
            return 0;
        }
    }

    function getDesechoByVariedad($variedad)
    {
        $stock = DB::table('stock_empaquetado')
            ->select(DB::raw('sum(cantidad_ingresada) as ingreso'), DB::raw('sum(cantidad_armada) as armada'))
            ->where('estado', '=', 1)
            ->where('id_variedad', '=', $variedad)
            ->where('fecha_registro', 'like', $this->fecha_ingreso . '%')
            ->get();

        if ($stock[0]->ingreso > 0 && $stock[0]->armada > 0) {
            return round(($stock[0]->armada / $stock[0]->ingreso) * 100, 2) - 100;
        } else {
            return 0;
        }
    }
}