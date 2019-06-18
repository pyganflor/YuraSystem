<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $table = 'color';
    protected $primaryKey = 'id_color';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_color',
        'estado',
        'nombre',
        'fondo',
        'texto',
    ];
}