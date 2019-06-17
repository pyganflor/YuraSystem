<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Aerolinea extends Model
{
    protected $table = 'aerolinea';
    protected $primaryKey = 'id_aerolinea';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'tipo_agencia',
        'estado',
        'codigo'
    ];
}
