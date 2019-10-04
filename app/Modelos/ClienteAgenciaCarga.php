<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ClienteAgenciaCarga extends Model
{
    protected $table = 'cliente_agenciacarga';
    protected $primaryKey = 'id_cliente_agencia_carga';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cliente',
        'id_agencia_carga',
    ];

    public function agencia_carga()
    {
        return $this->belongsTo('\yura\Modelos\AgenciaCarga', 'id_agencia_carga');
    }

    public function cliente()
    {
        return $this->belongsTo('\yura\Modelos\Cliente', 'id_cliente');
    }

    public function contacto_cliente_agencia_carga(){
        return $this->hasMany('\yura\Modelos\ContactoClienteAgenciaCarga','id_cliente_agencia_carga');
    }
}
