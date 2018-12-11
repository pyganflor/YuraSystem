<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{

    protected $table = 'detalle_pedido';
    protected $primaryKey = 'id_detalle_pedido';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cliente_especificacion',
        'id_pedido',
        'id_agencia_carga',
        'cantidad',
        'fecha'
    ];

    public function cliente_especificacion(){
        return $this->belongsTo('yura\Modelos\ClientePedidoEspecificacion','id_cliente_especificacion');
    }
}
