<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class VariedadClasificacionUnitaria extends Model
{
    protected $table = 'variedad_clasificacion_unitaria';
    protected $primaryKey = 'id_variedad_clasificacion_unitaria';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_variedad',
        'id_clasificacion_unitaria',
        'estado',
        'fecha_registro',
    ];

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function clasificacion_unitaria()
    {
        return $this->belongsTo('\yura\Modelos\ClasificacionUnitaria', 'id_clasificacion_unitaria');
    }
}