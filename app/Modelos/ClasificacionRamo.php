<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ClasificacionRamo extends Model
{
    protected $table = 'clasificacion_ramo';
    protected $primaryKey = 'id_clasificacion_ramo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_clasificacion_ramo',
        'id_configuracion_empresa',
        'nombre',
        'fecha_registro',
        'estado',
        'id_unidad_medida',
    ];

    public function configuracion_empresa()
    {
        return $this->belongsTo('\yura\Modelos\ConfiguracionEmpresa', 'id_configuracion_empresa');
    }

    public function unidad_medida()
    {
        return $this->belongsTo('\yura\Modelos\UnidadMedida', 'id_unidad_medida');
    }
}
