<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ResumenSemanalTotal extends Model
{
    protected $table = 'resumen_semanal_total';
    protected $primaryKey = 'id_resumen_semanal_total';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'codigo_semana',
        'valor',
        'fecha_registro',
    ];
}
