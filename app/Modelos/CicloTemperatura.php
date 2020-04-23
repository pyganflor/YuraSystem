<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class CicloTemperatura extends Model
{
    protected $table = 'ciclo_temperatura';
    protected $primaryKey = 'id_ciclo_temperatura';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_ciclo',
        'estado',
        'num_semana',
        'acumulado',
    ];

    public function ciclo()
    {
        return $this->belongsTo('\yura\Modelos\Ciclo', 'id_ciclo');
    }
}