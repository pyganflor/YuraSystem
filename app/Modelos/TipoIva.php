<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class TipoIva extends Model
{
    protected $table = 'tipo_iva';
    protected $primaryKey = 'id_tipo_iva';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'porcentaje',
        'estado'
    ];
}
