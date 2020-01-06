<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class CostosSemanaManoObra extends Model
{
    protected $table = 'costos_semana_mano_obra';
    protected $primaryKey = 'id_costos_semana_mano_obra';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_actividad_mano_obra',
        'codigo_semana',
        'valor',
        'cantidad',
        'fecha_registro',
    ];

    public function actividad_mano_obra()
    {
        return $this->belongsTo('\yura\Modelos\ActividadManoObra', 'id_actividad_mano_obra');
    }
}
