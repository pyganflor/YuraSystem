<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ProyeccionVentaSemanalReal extends Model
{
    protected $table = 'proyeccion_venta_semanal_real';
    protected $primaryKey = 'id_proyeccion_venta_semanal_real';
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
        'valor_proy',
        'cajas_equivalentes_proy',
        'cajas_fisicas_proy'
    ];

}
