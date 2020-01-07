<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividad';
    protected $primaryKey = 'id_actividad';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'id_area',
        'estado',
        'fecha_registro',
    ];

    public function area()
    {
        return $this->belongsTo('\yura\Modelos\Area', 'id_area');
    }

    public function productos()
    {
        return $this->hasMany('\yura\Modelos\ActividadProducto', 'id_actividad');
    }

    public function manos_obra()
    {
        return $this->hasMany('\yura\Modelos\ActividadManoObra', 'id_actividad');
    }
}
