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
        'fecha_registro',
        'estado',   // default: 0
        'guia_madre',
        'guia_hija',
        'dae',
        'email',
        'telefono',
        'direccion',
        'codigo_pais'

    ];

    public function detalles()
    {
        return $this->hasMany('\yura\Modelos\DetalleEnvio', 'id_envio');
    }
}
