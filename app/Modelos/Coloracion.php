<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Coloracion extends Model
{
    protected $table = 'coloracion';
    protected $primaryKey = 'id_coloracion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_coloracion',
        'nombre',
        'fondo',
        'texto',
        'fecha_registro',
        'estado',
        'cantidad',
        'id_marcacion',
    ];

    public function marcacion()
    {
        return $this->belongsTo('\yura\Modelos\Marcacion', 'id_marcacion');
    }
}
