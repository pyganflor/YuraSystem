<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    protected $table = 'contacto';
    protected $primaryKey = 'id_contacto';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'correo',
        'telefono',
        'direccion',
    ];
}
