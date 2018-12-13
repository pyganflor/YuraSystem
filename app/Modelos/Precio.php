<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Precio extends Model
{
    protected $table = 'precio';
    protected $primaryKey = 'id_precio';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_precio',
        'id_variedad',
        'fecha_registro',
        'estado',
        'cantidad',
        'id_clasificacion_ramo',
    ];

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function clasificacion_ramo()
    {
        return $this->belongsTo('\yura\Modelos\ClasificacionRamo', 'id_clasificacion_ramo');
    }
}
