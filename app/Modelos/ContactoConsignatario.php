<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ContactoConsignatario extends Model
{
    protected $table = 'contacto_consignatario';
    protected $primaryKey = 'id_contacto_consignatario';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_consignatario',
        'nombre',
        'direccion',
        'telefono',
        'correo',
        'identificacion',
        'codigo_pais',
        'ciudad',
        'fecha_registro'
    ];

}
