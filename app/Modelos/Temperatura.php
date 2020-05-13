<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Temperatura extends Model
{
    protected $table = 'temperatura';
    protected $primaryKey = 'id_temperatura';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'estado',
        'fecha',
        'maxima',
        'minima',
        'lluvia',
    ];
}