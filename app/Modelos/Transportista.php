<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Transportista extends Model
{
    protected $table = 'transportista';
    protected $primaryKey = 'id_transportista';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre_empresa',
        'ruc',
        'encargado',
        'ruc_encargado',
        'telefono_encargado',
        'direccion_empresa',
        'estado',
        'fecha_registro'
    ];
}
