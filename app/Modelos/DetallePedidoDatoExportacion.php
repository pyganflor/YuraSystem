<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetallePedidoDatoExportacion extends Model
{
    protected $table = 'detallepedido_datoexportacion';
    protected $primaryKey = 'id_detallepedido_datoexportacion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_detalle_pedido',
        'id_dato_exportacion',
        'valor',
    ];
}
