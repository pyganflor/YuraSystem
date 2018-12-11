<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table = 'lote';
    protected $primaryKey = 'id_lote';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_lote',
        'nombre',
        'fecha_registro',
        'estado',
        'area',
        'descripcion',
        'id_modulo',
    ];

    public function modulo()
    {
        return $this->belongsTo('\yura\Modelos\Modulo', 'id_modulo');
    }
}