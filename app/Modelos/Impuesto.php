<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Impuesto extends Model
{
    protected $table = 'impuesto';
    protected $primaryKey = 'id_impuesto';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre'
    ];
}
