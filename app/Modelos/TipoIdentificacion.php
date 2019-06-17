<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class TipoIdentificacion extends Model
{
    protected $table = 'tipo_identificacion';
    protected $primaryKey = 'id_tipo_identificacion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'fecha_registro',
        'estado'
    ];
}
