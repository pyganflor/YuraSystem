<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleEspecificacionEmpaqueRamosCaja extends Model
{
    protected $table = 'detalle_especificacionempaque_ramos_x_caja';
    protected $primaryKey = 'id_detalle_especificacionempaque_ramos_x_caja';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_detalle_pedido',
        'id_detalle_especificacionempaque',
        'cantidad',
        'fecha_registro'
    ];

}
