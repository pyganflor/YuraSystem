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

    function getCantidadHorasTrabajo()
    {
        $r = difFechas($this->getLastFechaClasificacion(), $this->getFechaHoraInicio());
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

    function getFechaHoraInicio()
    {
        return $this->fecha_ingreso . ' ' . $this->hora_inicio . ':00';
    }
}
