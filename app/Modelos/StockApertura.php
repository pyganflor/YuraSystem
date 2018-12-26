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

    public function calcularDisponibles()
    {
        $r = $this->cantidad_disponible;
        $unitaria = $this->clasificacion_unitaria;
        $ramo_estandar = $unitaria->clasificacion_ramo_estandar;
        $ramo_real = $unitaria->clasificacion_ramo_real;

        $f_e = round($ramo_estandar->nombre / explode('|', $unitaria->nombre)[0], 2);
        $f_r = round($ramo_real->nombre / explode('|', $unitaria->nombre)[0], 2);
        if ($unitaria->unidad_medida->tipo == 'L') {
            $f_e = $f_r = explode('|', $unitaria->nombre)[1];
        }

        return [
            'estandar' => round($r / $f_e, 2),
            'real' => round($r / $f_r, 2),
        ];
    }

    public function getDisponibles($tipo_ramo)
    {
        $l = StockApertura::All()->where('disponibilidad', '=', 1)->where('estado', '=', 1)
            ->where('id_variedad', '=', $this->id_variedad)
            ->where('id_clasificacion_unitaria', '=', $this->id_clasificacion_unitaria)
            ->where('fecha_inicio', '<', $this->fecha_inicio);

        $r = $this->calcularDisponibles()['' . $tipo_ramo];

        foreach ($l as $item) {
            $r += $item->calcularDisponibles()['' . $tipo_ramo];
        }

        return $r;
    }
}
