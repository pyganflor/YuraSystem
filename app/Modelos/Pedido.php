<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedido';
    protected $primaryKey = 'id_pedido';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cliente',
        'estado',
        'descripcion',
<<<<<<< HEAD
        'fecha_pedido'
=======
        'fecha_pedido',
        'empaquetado'
>>>>>>> f7d939a64537592b1e24eedf8cf21d3e9742e791
    ];

    public function detalles()
    {
        return $this->hasMany('\yura\Modelos\DetallePedido', 'id_pedido');
    }

    public function cliente()
    {
        return $this->belongsTo('\yura\Modelos\Cliente', 'id_cliente');
    }
}