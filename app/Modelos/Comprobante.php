<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    protected $table = 'comprobante';
    protected $primaryKey = 'id_comprobante';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'fecha_registro',
        'estado'
    ];
}
