<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ClasificacionUnitaria extends Model
{
    protected $table = 'clasificacion_unitaria';
    protected $primaryKey = 'id_clasificacion_unitaria';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_clasificacion_unitaria',
        'id_configuracion_empresa',
        'id_unidad_medida',
        'nombre',
        'fecha_registro',
        'estado',
        'id_clasificacion_ramo_estandar',
        'id_clasificacion_ramo_real',
        'tallos_x_ramo',
        'color',    // white|black
    ];

    public function configuracion_empresa()
    {
        return $this->belongsTo('\yura\Modelos\ConfiguracionEmpresa', 'id_configuracion_empresa');
    }

    public function unidad_medida()
    {
        return $this->belongsTo('\yura\Modelos\UnidadMedida', 'id_unidad_medida');
    }

    public function clasificacion_ramo_estandar()
    {
        return $this->belongsTo('\yura\Modelos\ClasificacionRamo', 'id_clasificacion_ramo_estandar');
    }

    public function clasificacion_ramo_real()
    {
        return $this->belongsTo('\yura\Modelos\ClasificacionRamo', 'id_clasificacion_ramo_real');
    }
}
