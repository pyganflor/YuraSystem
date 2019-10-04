<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ProyeccionVentaSemanal extends Model
{
    protected $table = 'proyeccion_venta_semanal';
    protected $primaryKey = 'id_proyeccion_venta_semanal';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cliente',
        'id_variedad',
        'fecha_registro',
        'estado',
        'valor',
        'cajas_equivalentes',
        'codigo_semana',
        'cajas_fisicas',
    ];

}
