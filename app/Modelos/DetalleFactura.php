<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleFactura extends Model
{
    protected $table = 'detalle_factura';
    protected $primaryKey = 'id_detalle_factura';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_comprobante',
        'razon_social_emisor',
        'nombre_comercial_emisor',
        'direccion_matriz_emisor',
        'direccion_establecimiento_emisor',
        'obligado_contabilidad',
        'tipo_identificacion_comprador',
        'razon_social_comprador',
        'identificacion_comprador',
        'total_sin_impuestos',
        'total_descuento',
        'propina',
        'importe_total'
    ];

    public function factura(){
        return $this->belongsTo('yura\Modelos\Comprobante','id_comprobante');
    }
}
