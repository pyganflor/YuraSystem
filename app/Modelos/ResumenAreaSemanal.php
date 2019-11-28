<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ResumenAreaSemanal extends Model
{
    protected $table = 'resumen_area_semanal';
    protected $primaryKey = 'id_resumen_area_semanal';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_variedad',
        'codigo_semana',
        'area',
        'ciclo',
        'tallos_m2',
        'ramos_m2',
        'ramos_m2_anno',
        'estado',
        'fecha_registro',
    ];

    public function variedad()
    {
        $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }
}