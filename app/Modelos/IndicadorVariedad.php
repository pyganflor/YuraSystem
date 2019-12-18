<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class IndicadorVariedad extends Model
{
    protected $table = 'indicador_variedad';
    protected $primaryKey = 'id_indicador_variedad';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_indicador',
        'id_variedad',
        'valor',
        'fecha_registro',
    ];

}
