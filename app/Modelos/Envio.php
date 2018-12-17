<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    protected $table = 'envio';
    protected $primaryKey = 'id_envio';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_pedido',
        'fecha_envio',
        'estado',
    ];
}
