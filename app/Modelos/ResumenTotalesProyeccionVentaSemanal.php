<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ResumenTotalesProyeccionVentaSemanal extends Model
{
    protected $table = 'resumen_totales_proyeccion_venta_semanal';
    protected $primaryKey = 'id_resumen_totales_proyeccion_venta_semanal';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_variedad',
        'codigo_semana',
        'cajas_fisicas',
        'cajas_equivalentes',
        'dinero',
        'fecha_registro',
    ];
}
