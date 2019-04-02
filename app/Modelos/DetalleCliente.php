<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleCliente extends Model
{
    protected $table = 'detalle_cliente';
    protected $primaryKey = 'id_detalle_cliente';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'direccion',
        'provincia',
        'codigo_pais',
        'telefono',
        'ruc',
        'correo',
        'id_cliente',
        'codigo_iva',
        'codigo_identificacion',
        'estado'
    ];
}
