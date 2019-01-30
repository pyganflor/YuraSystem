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
        'id_comprobante',
        'clave_acceso',
        'estado',
        'fecha_emision',
        'tipo_comprobante',
        'monto_total'
    ];
}
