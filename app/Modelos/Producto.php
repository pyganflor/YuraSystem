<?php

namespace yura;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'area';
    protected $primaryKey = 'id_area';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado',
        'fecha_registro',
    ];
}
