<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class IndicadorVariedadSemana extends Model
{
    protected $table = 'indicador_variedad_semana';
    protected $primaryKey = 'id_indicador_variedad_semana';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_indicador_variedad',
        'valor',
        'codigo_semana',
    ];

    public function indicador_variedad()
    {
        return $this->belongsTo('yura\Modelos\IndicadorVariedad', 'id_indicador_variedad');
    }
}
