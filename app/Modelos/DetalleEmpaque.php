<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleEmpaque extends Model
{
    protected $table = 'detalle_empaque';
    protected $primaryKey = 'id_detalle_empaque';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_empaque',
        'id_variedad',
        'id_clasificacion_ramo',
        'cantidad',
    ];

    public function empaque()
    {
        return $this->belongsTo('\yura\Modelos\Empaque', 'id_empaque');
    }

    public function variedad(){
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function  clasificacion_ramo(){
        return $this->belongsTo('\yura\Modelos\ClasificacionRamo', 'id_clasificacion_ramo');
    }

}
