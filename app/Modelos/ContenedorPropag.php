<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ContenedorPropag extends Model
{
    protected $table = 'contenedor_propag';
    protected $primaryKey = 'id_contenedor_propag';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',   // unico
        'fecha_registro',
        'estado',
        'cantidad', // cantidad de plantas por unidad
    ];
}