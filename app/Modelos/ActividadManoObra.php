<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ActividadManoObra extends Model
{
    protected $table = 'actividad_mano_obra';
    protected $primaryKey = 'id_actividad_mano_obra';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_actividad',
        'id_mano_obra',
        'estado',
        'fecha_registro',
    ];

    public function actividad()
    {
        return $this->belongsTo('\yura\Modelos\Actividad', 'id_actividad');
    }

    public function mano_obra()
    {
        return $this->belongsTo('\yura\Modelos\ManoObra', 'id_mano_obra');
    }
}
