<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ManoObra extends Model
{
    protected $table = 'mano_obra';
    protected $primaryKey = 'id_mano_obra';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado',
        'fecha_registro',
    ];

    public function actividades()
    {
        return $this->hasMany('\yura\Modelos\ActividadManoObra', 'id_mano_obra');
    }
}
