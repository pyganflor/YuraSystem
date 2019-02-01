<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class TipoImpuesto extends Model
{
    protected $table = 'tipo_impuesto';
    protected $primaryKey = 'id_tipo_impuesto';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'codigo_impuesto',
        'codigo',
        'porcentaje',
        'estado'
    ];
}
