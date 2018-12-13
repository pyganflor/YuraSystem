<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClasificacionVerde extends Model
{
    protected $table = 'clasificacion_verde';
    protected $primaryKey = 'id_clasificacion_verde';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_clasificacion_verde',
        'fecha_registro',
        'estado',
        'fecha_ingreso',
        'id_semana',
    ];

    public function lotes_re()
    {
        return $this->hasMany('\yura\Modelos\LoteRE', 'id_clasificacion_verde');
    }

    public function lotes_reByVariedad($variedad)
    {
        return LoteRE::All()->where('id_clasificacion_verde','=',$this->id_clasificacion_verde)->where('id_variedad', '=', $variedad);
    }

    public function detalles()
    {
        return $this->hasMany('\yura\Modelos\DetalleClasificacionVerde', 'id_clasificacion_verde');
    }

    public function recepciones()
    {
        return $this->hasMany('\yura\Modelos\RecepcionClasificacionVerde', 'id_clasificacion_verde');
    }

    public function semana()
    {
        return $this->belongsTo('\yura\Modelos\Semana', 'id_semana');
    }

    public function tallos_x_variedad($variedad)
    {
        $r = 0;
        foreach ($this->detalles as $item) {
            if ($item->id_variedad == $variedad)
                $r += ($item->cantidad_ramos * $item->tallos_x_ramos);
        }
        return $r;
    }

    public function total_tallos()
    {
        $r = 0;
        foreach ($this->detalles as $item) {
            $r += ($item->cantidad_ramos * $item->tallos_x_ramos);
        }
        return $r;
    }

    public function total_tallos_recepcion()
    {
        $r = 0;
        foreach ($this->recepciones as $item) {
            $r += $item->recepcion->cantidad_tallos();
        }

        return $r;
    }

    public function total_tallos_recepcionByVariedad($variedad)
    {
        $r = 0;
        foreach ($this->recepciones as $item) {
            $r += $item->recepcion->tallos_x_variedad($variedad);
        }
        return $r;
    }

    public function total_ramos()
    {
        $r = 0;
        foreach ($this->detalles as $item) {
            $r += $item->cantidad_ramos;
        }
        return $r;
    }

    public function desecho()
    {
        $total = 0;
        foreach ($this->recepciones as $item) {
            $total += $item->recepcion->cantidad_tallos();
        }

        return round(100 - round(($this->total_tallos() * 100) / $total, 2),2);
    }

    public function getRamosByvariedadUnitaria($variedad, $unitaria)
    {
        $r = 0;
        foreach ($this->detalles as $detalle) {
            if ($detalle->id_variedad == $variedad && $detalle->id_clasificacion_unitaria == $unitaria) {
                $r += $detalle->cantidad_ramos;
            }
        }
        return $r;
    }

    public function getTallosByvariedadUnitaria($variedad, $unitaria)
    {
        $r = 0;
        foreach ($this->detalles as $detalle) {
            if ($detalle->id_variedad == $variedad && $detalle->id_clasificacion_unitaria == $unitaria) {
                $r += $detalle->cantidad_ramos * $detalle->tallos_x_ramos;
            }
        }
        return $r;
    }

    public function variedades()
    {
        $l = DB::table('detalle_clasificacion_verde as d')
            ->select('d.id_variedad')->distinct()
            ->where('id_clasificacion_verde', '=', $this->id_clasificacion_verde)->get();
        $r = [];
        foreach ($l as $item) {
            array_push($r, Variedad::find($item->id_variedad));
        }
        return $r;
    }

    public function unitarias()
    {
        $l = DB::table('detalle_clasificacion_verde as d')
            ->select('d.id_clasificacion_unitaria')->distinct()
            ->where('id_clasificacion_verde', '=', $this->id_clasificacion_verde)->get();
        $r = [];
        foreach ($l as $item) {
            array_push($r, ClasificacionUnitaria::find($item->id_clasificacion_unitaria));
        }
        return $r;
    }
}