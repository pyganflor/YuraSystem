<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ClientePedidoEspecificacion extends Model
{
    protected $table = 'cliente_pedido_especificacion';
    protected $primaryKey = 'id_cliente_pedido_especificacion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cliente',
        'id_especificacion',
        'fecha_registro',
        'estado',
    ];

    public function cliente()
    {
        return $this->belongsTo('\yura\Modelos\Cliente', 'id_cliente');
    }

    public function especificacion()
    {
        return $this->belongsTo('\yura\Modelos\Especificacion', 'id_especificacion');
    }
}
