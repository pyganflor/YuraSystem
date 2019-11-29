<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ResumenVentaDiaria extends Model
{
    protected $table = 'venta_diaria';
    protected $primaryKey = 'id_venta_diaria';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'fecha_pedido',
        'valor',
        'cajas_equivalentes',
        'precio_x_ramo',
        'fecha_registro',
    ];

}
