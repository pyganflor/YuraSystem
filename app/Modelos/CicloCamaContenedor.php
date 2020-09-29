<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class CicloCamaContenedor extends Model
{
    protected $table = 'ciclo_cama_contenedor';
    protected $primaryKey = 'id_ciclo_cama_contenedor';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_ciclo_cama',
        'fecha_registro',
        'id_contenedor_propag',
        'cantidad',
    ];

    public function ciclo_cama()
    {
        return $this->belongsTo('\yura\Modelos\CicloCama', 'id_ciclo_cama');
    }

    public function contenedor()
    {
        return $this->belongsTo('\yura\Modelos\ContenedorPropag', 'id_contenedor_propag');
    }
}
