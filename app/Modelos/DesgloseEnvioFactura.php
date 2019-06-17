<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DesgloseEnvioFactura extends Model
{
    protected $table = 'desglose_envio_factura';
    protected $primaryKey = 'id_desglose_envio_factura';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_comprobante',
        'codigo_principal',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'descuento',
        'precio_total_sin_impuesto'
    ];
}
