<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class StockGuarde extends Model
{
    protected $table = 'stock_guarde';
    protected $primaryKey = 'id_stock_guarde';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_stock_guarde',
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
}
