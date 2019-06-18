<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    protected $table = 'unidad_medida';
    protected $primaryKey = 'id_unidad_medida';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_unidad_medida',
        'nombre',
        'siglas',
        'fecha_registro',
        'estado',
        'tipo',
    ];

    public function unitaria()
    {
        return $this->hasOne('\yura\Modelos\ClasificacionUnitaria', 'id_unidad_medida');
    }
}
