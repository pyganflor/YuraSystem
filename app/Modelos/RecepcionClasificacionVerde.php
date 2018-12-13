<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class RecepcionClasificacionVerde extends Model
{
    protected $table = 'recepcion_clasificacion_verde';
    protected $primaryKey = 'id_recepcion_clasificacion_verde';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'fecha_registro',
        'estado',
        'id_recepcion',
        'id_clasificacion_verde',
    ];

    public function recepcion()
    {
        return $this->belongsTo('\yura\Modelos\Recepcion', 'id_recepcion');
    }

    public function clasificacion_verde()
    {
        return $this->belongsTo('\yura\Modelos\ClasificacionVerde', 'id_clasificacion_verde');
    }
}
