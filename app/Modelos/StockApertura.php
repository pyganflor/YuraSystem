<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class StockApertura extends Model
{
    protected $table = 'stock_apertura';
    protected $primaryKey = 'id_stock_apertura';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_stock_apertura',
        'fecha_registro',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'cantidad_tallos',
        'cantidad_disponible',
        'disponibilidad',
        'id_variedad',
        'id_clasificacion_unitaria',
        'dias',
        'id_lote_re',
    ];

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function clasificacion_unitaria()
    {
        return $this->belongsTo('\yura\Modelos\ClasificacionUnitaria', 'id_clasificacion_unitaria');
    }

    public function lote_re()
    {
        return $this->belongsTo('\yura\Modelos\LoteRE', 'id_lote_re');
    }

    public function calcularFechaFin()
    {
        $fecha = strtotime('+' . $this->dias . ' day', strtotime($this->fecha_inicio));
        $fecha = date('Y-m-d', $fecha);
        return $fecha;
    }

<<<<<<< HEAD
=======
    public function calcularDisponibles()
    {

    }

>>>>>>> f7d939a64537592b1e24eedf8cf21d3e9742e791
    public function getDisponibles()
    {
        $l = StockApertura::All()->where('disponibilidad', '=', 1)
            ->where('id_variedad', '=', $this->id_variedad)
            ->where('id_clasificacion_unitaria', '=', $this->id_clasificacion_unitaria)
            ->where('fecha_inicio', '<', $this->fecha_inicio);

        $r = $this->cantidad_disponible;

        foreach ($l as $item) {
            $r += $item->cantidad_disponible;
        }

        return $r;
    }
}
