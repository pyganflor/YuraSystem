<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ImpuestoDetalleFactura extends Model
{
    protected $table = 'impuesto_detalle_factura';
    protected $primaryKey = 'id_impuesto_detalle_factura';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_detalle_factura',
        'codigo_impuesto',
        'codigo_porcentaje',
        'base_imponible',
        'valor'
    ];
}
