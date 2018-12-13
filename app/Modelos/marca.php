<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class marca extends Model
{
    protected $table = 'marcas';
    protected $primaryKey = 'id_marca';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion'
    ];
}
