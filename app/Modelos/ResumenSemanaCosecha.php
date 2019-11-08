<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ResumenSemanaCosecha extends Model
{

    protected $table = 'resumen_semana_cosecha';
    protected $primaryKey = 'id_resumen_semana_cosecha';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_variedad',
        'codigo_Semana',
        'fecha_registro',
        'estado',
        'cajas',
        'tallos',
        'tallos_proyectados',
        'cajas_proyectadas',
        'plantas_iniciales',
        'calibre',
        'area',
        'desecho'
    ];

    public function variedad(){
        return $this->belongsTo('yura\Modelos\Variedad','id_variedad');
    }
}
