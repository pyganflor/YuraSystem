<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class InformacionAdicionalFactura extends Model
{
    protected $table = 'informacion_adicional_factura';
    protected $primaryKey = 'id_informacion_adicional_factura';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_comprobante',
        'direccion',
        'email',
        'telefono',
        'dae',
        'guia_madre',
        'guia_hija'
    ];
}
