<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ProyeccionModulo extends Model
{
    protected $table = 'proyeccion_modulo';
    protected $primaryKey = 'id_proyeccion_modulo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_modulo',
        'id_semana',
        'fecha_registro',
        'estado',
        'id_variedad',
        'tipo', // Poda, Siembra, Cerrado
        'curva',
        'semana_poda_siembra',
        'poda_siembra', // numero de poda o 0 si es siembra
        'plantas_iniciales',
        'desecho',
        'tallos_planta',
        'tallos_ramo',
        'fecha_inicio',
    ];

    public function modulo()
    {
        return $this->belongsTo('\yura\Modelos\Modulo', 'id_modulo');
    }

    public function semana()
    {
        return $this->belongsTo('\yura\Modelos\Semana', 'id_semana');
    }

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }
}
