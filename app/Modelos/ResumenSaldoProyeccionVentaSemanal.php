<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ResumenSaldoProyeccionVentaSemanal extends Model
{
    protected $table = 'resumen_saldo_proy_venta_semanal';
    protected $primaryKey = 'id_resumen_saldo_proy_venta_semanal';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_variedad',
        'codigo_semana',
        'saldo_inicial',
        'saldo_final',
        'fecha_registro',
    ];
}
