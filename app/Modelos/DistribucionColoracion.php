<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DistribucionColoracion extends Model
{
    protected $table = 'distribucion_coloracion';
    protected $primaryKey = 'id_distribucion_coloracion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_marcacion_coloracion',
        'id_distribucion',
        'cantidad',
        'fecha_registro',
        'estado',
    ];

    public function distribucion()
    {
        return $this->belongsTo('\yura\Modelos\Distribucion', 'id_distribucion');
    }

    public function marcacion_coloracion()
    {
        return $this->belongsTo('\yura\Modelos\MarcacionColoracion', 'id_marcacion_coloracion');
    }
}
