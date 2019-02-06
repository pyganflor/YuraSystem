<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Marcacion extends Model
{
    protected $table = 'marcacion';
    protected $primaryKey = 'id_marcacion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_marcacion',
        'nombre',
        'fecha_registro',
        'estado',
        'cantidad',
        'id_especificacion_empaque',
    ];

    public function especificacion_empaque()
    {
        return $this->belongsTo('\yura\Modelos\EspecificacionEmpaque', 'id_especificacion_empaque');
    }

    public function coloraciones()
    {
        return $this->hasMany('\yura\Modelos\Coloracion', 'id_marcacion');
    }

    public function distribuciones()
    {
        return $this->hasMany('\yura\Modelos\Distribucion', 'id_marcacion');
    }
}