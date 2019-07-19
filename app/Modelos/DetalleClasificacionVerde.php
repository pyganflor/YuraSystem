<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleClasificacionVerde extends Model
{
    protected $table = 'detalle_clasificacion_verde';
    protected $primaryKey = 'id_detalle_clasificacion_verde';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'fecha_registro',
        'fecha_ingreso',
        'estado',
        'tallos_x_ramos',
        'cantidad_ramos',
        'id_variedad',
        'id_clasificacion_unitaria',
        'id_clasificacion_verde',
    ];

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function clasificacion_unitaria()
    {
        return $this->belongsTo('\yura\Modelos\ClasificacionUnitaria', 'id_clasificacion_unitaria');
    }

    public function clasificacion_verde()
    {
        return $this->belongsTo('\yura\Modelos\ClasificacionVerde', 'id_clasificacion_verde');
    }

    public function cantidad_tallos()
    {
        return $this->tallos_x_ramos * $this->cantidad_ramos;
    }

    public function cantidad_tallos_estandar()
    {
        return round($this->cantidad_tallos() / explode('|', $this->clasificacion_unitaria->nombre)[1], 2);
    }
}
