<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Conductor extends Model
{
    protected $table = 'conductor';
    protected $primaryKey = 'id_conductor';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_transportista',
        'nombre',
        'ruc',
        'estado',
        'tipo_identificacion'
    ];
}
