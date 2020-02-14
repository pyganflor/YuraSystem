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

    public function indicador()
    {
        return $this->belongsTo('yura\Modelos\Indicador', 'id_indicador');
    }

    public function semanas()
    {
        return $this->hasMany('yura\Modelos\IndicadorVariedadSemana', 'id_indicador_variedad');
    }

    public function getSemanas($desde, $hasta)
    {
        return IndicadorVariedadSemana::where('id_indicador_variedad', $this->id_indicador_variedad)
            ->where('codigo_semana', '>=', $desde)->where('codigo_semana', '<=', $hasta)->orderBy('codigo_semana', 'asc')->get();
    }

    public function getSemana($semana)
    {
        return $this->semanas->where('codigo_semana', $semana)->first();
    }
}
