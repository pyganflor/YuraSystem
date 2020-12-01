<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    protected $table = 'banco';
    protected $primaryKey = 'id_banco';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado',
        'fecha_registro',
    ];
}
