<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ContactoClienteAgenciaCarga extends Model
{
    protected $table = 'contactos_cliente_agenciacarga';
    protected $primaryKey = 'id_contactos_cliente_agenciacarga';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cliente_agencia_carga',
        'contacto',
        'correo',
        'direccion',
        'fecha_registro'
    ];

    public function cliente_agencia_carga()
    {
        return $this->hasMany('\yura\Modelos\ClienteAgenciaCarga', 'id_agencia_carga');
    }
}
