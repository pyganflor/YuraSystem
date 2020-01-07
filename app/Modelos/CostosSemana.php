<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class CostosSemana extends Model
{
    protected $table = 'costos_semana';
    protected $primaryKey = 'id_costos_semana';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_actividad_producto',
        'codigo_semana',
        'valor',
        'cantidad',
        'fecha_registro',
    ];

    public function actividad_producto()
    {
        return $this->belongsTo('\yura\Modelos\ActividadProducto', 'id_actividad_producto');
    }
}