<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class IndicadorSemana extends Model
{
    protected $table = 'indicador_semana';
    protected $primaryKey = 'id_indicador_semana';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_indicador',
        'valor',
        'codigo_semana',
    ];

    public function indicador()
    {
        return $this->belongsTo('yura\Modelos\Indicador', 'id_indicador');
    }
}