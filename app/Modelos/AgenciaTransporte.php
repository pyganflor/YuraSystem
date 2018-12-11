<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class AgenciaTransporte extends Model
{
    protected $table = 'agencia_transporte';
    protected $primaryKey = 'id_agencia_transporte';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'tipo_agencia',
        'estado'
    ];
}
