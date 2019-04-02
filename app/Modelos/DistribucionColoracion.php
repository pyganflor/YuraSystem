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
        'id_coloracion',
        'id_marcacion',
        'cantidad',
        'fecha_registro',
        'estado',
    ];

    public function marcacion()
    {
        return $this->belongsTo('\yura\Modelos\Marcacion', 'id_marcacion');
    }

    public function coloracion()
    {
        return $this->belongsTo('\yura\Modelos\Coloracion', 'id_coloracion');
    }
}
