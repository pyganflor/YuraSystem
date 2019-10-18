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
        'cajas_fisicas_anno_anterior'
    ];

    public function cliente(){
        return $this->belongsTo('yura\Modelos\Cliente','id_cliente');
    }

    public function variedad(){
        return $this->belongsTo('yura\Modelos\Variedad','id_variedad');
    }
}
