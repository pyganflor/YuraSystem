<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Icon extends Model
{
    protected $table = 'icono';
    protected $primaryKey = 'id_icono';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'estado',
        'nombre',
    ];
}
