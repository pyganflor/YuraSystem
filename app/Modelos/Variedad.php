<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Variedad extends Model
{
    protected $table = 'variedad';
    protected $primaryKey = 'id_variedad';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_variedad',
        'nombre',
        'siglas',
        'unidad_de_medida',
        'cantidad',
        'minimo_apertura',
        'maximo_apertura',
        'estandar_apertura',
        'fecha_registro',
        'estado',
        'id_planta',
    ];

    public function planta()
    {
        return $this->belongsTo('\yura\Modelos\Planta', 'id_planta');
    }
}
