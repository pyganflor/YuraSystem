<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class CodigoDae extends Model
{
    protected $table = 'codigo_dae';
    protected $primaryKey = 'id_codigo_dae';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'codigo_pais',
        'dae',
        'mes',
        'anno',
        'codigo_dae',
        'estado',
        'fecha_registro'
    ];
}
