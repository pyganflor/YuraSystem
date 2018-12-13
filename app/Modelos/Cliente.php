<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';
    protected $primaryKey = 'id_cliente';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'estado'
    ];

    public function detalles()
    {
        return $this->hasMany('\yura\Modelos\DetalleCliente', 'id_cliente');
    }

    public function cliente_agencia_carga()
    {
        return $this->hasMany('\yura\Modelos\ClienteAgenciaCarga', 'id_cliente');
    }

    public function especificaciones()
    {
        return $this->hasMany('\yura\Modelos\Especificacion', 'id_cliente');
    }
}