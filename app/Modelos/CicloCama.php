<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class CicloCama extends Model
{
    protected $table = 'ciclo_cama';
    protected $primaryKey = 'id_ciclo_cama';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cama',
        'fecha_registro',
        'activo',
        'fecha_inicio',
        'fecha_fin',
        'plantas_muertas',
        'esq_x_planta', // conteo de la cantidad de esquejes que se cosecha por semana
        'id_variedad',
    ];

    public function cama()
    {
        return $this->belongsTo('\yura\Modelos\Cama', 'id_cama');
    }

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function contenedores()
    {
        return $this->hasMany('\yura\Modelos\CicloCamaContenedor', 'id_ciclo_cama');
    }

    public function getPlantasProductivas()
    {
        $valor = 0;
        foreach ($this->contenedores as $c) {
            $valor += $c->cantidad * $c->contenedor->cantidad;
        }
        return $valor;
    }

    public function getDiasVida()
    {
        return difFechas(date('Y-m-d'), $this->fecha_inicio)->days;
    }

    public function getEsquejesCosechados()
    {
        return 0;
    }
}
