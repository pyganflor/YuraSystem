<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documento';
    protected $primaryKey = 'id_documento';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_documento',
        'entidad',
        'codigo',
        'nombre_campo',
        'tipo_dato',
        'int',
        'float',
        'char',
        'varchar',
        'boolean',
        'date',
        'datetime',
        'fecha_registro',
        'estado',
        'descripcion',
    ];
}
