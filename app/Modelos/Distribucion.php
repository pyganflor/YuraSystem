<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Distribucion extends Model
{
    protected $table = 'distribucion';
    protected $primaryKey = 'id_distribucion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_distribucion',
        'id_marcacion',
        'ramos',
        'piezas',
        'fecha_registro',
        'estado',
        'pos_pieza',
    ];

    public function marcacion()
    {
        return $this->belongsTo('\yura\Modelos\Marcacion', 'id_marcacion');
    }
}