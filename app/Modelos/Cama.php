<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Cama extends Model
{
    protected $table = 'cama';
    protected $primaryKey = 'id_cama';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',   // unico
        'fecha_registro',
        'estado',
        'area_trabajo', // PLANTAS MADRES, ENRAIZAMIENTO, CONFINAMIENTO
    ];

    public function sector()
    {
        return $this->belongsTo('\yura\Modelos\Sector', 'id_cama');
    }
}
