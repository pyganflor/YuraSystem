<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Especificacion extends Model
{
    protected $table = 'especificacion';
    protected $primaryKey = 'id_especificacion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_especificacion',
        'fecha_registro',
        'estado',
        'nombre',
        'descripcion',
        'tipo', // N => normal; O => Otras (ordenes semanales)
    ];

    public function cliente()
    {
        return $this->belongsTo('\yura\Modelos\Cliente', 'id_cliente');
    }

    public function especificacionesEmpaque()
    {
        return $this->hasMany('\yura\Modelos\EspecificacionEmpaque', 'id_especificacion');
    }
}