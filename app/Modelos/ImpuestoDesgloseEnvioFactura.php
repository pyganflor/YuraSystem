<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ImpuestoDesgloseEnvioFactura extends Model
{
    protected $table = 'impuesto_desglose_envio_factura';
    protected $primaryKey = 'id_impuesto_desglose_envio_factura';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'codigo_impuesto',
        'id_desglose_envio_factura',
        'codigo_porcentaje',
        'base_imponible',
        'valor'
    ];
}
