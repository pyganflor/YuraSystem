<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ProyeccionModuloSemana extends Model
{
    protected $table = 'proyeccion_modulo_semana';
    protected $primaryKey = 'id_proyeccion_modulo_semana';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_modulo',
        'id_variedad',
        'fecha_registro',
        'estado',
        'semana',
        'tipo',
        'info',
        'cosechados',
        'proyectados',
        'plantas_iniciales',
        'plantas_actuales',
        'fecha_inicio',
        'activo',
        'area',
        'tallos_planta',
        'tallos_ramo',
        'curva',
        'poda_siembra',
        'semana_poda_siembra',
        'desecho',
        'tabla',
    ];

    public function modulo()
    {
        return $this->belongsTo('\yura\Modelos\Modulo', 'id_modulo');
    }

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }
}