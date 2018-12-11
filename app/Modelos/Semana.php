<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Semana extends Model
{
    protected $table = 'semana';
    protected $primaryKey = 'id_semana';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'anno',
        'codigo',
        'fecha_inicial',
        'fecha_final',
        'curva',
        'descecho',
        'desecho',
        'semana_poda',
        'semana_siembra',
        'fecha_registro',
        'estado',
        'id_variedad',
    ];

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }
}
