<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleGuiaRemision extends Model
{
    protected $table = 'detalle_guia_remision';
    protected $primaryKey = 'id_detalle_guia_remision';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_comprobante',
        'id_comprobante_relacionado',
        'razon_social_emisor',
        'direccion_matriz_emisor',
        'direccion_establecimiento_emisor',
        'obligado_contabilidad',
        'destino',
        'identificacion_emisor'
    ];

}
