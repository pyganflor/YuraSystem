<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Planta extends Model
{
    protected $table = 'planta';
    protected $primaryKey = 'id_planta';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_planta',
        'nombre',   // unico
        'siglas',   // unico
        'fecha_registro',
        'estado',
    ];

    public function variedades()
    {
        return $this->hasMany('\yura\Modelos\Variedad', 'id_planta')->orderBy('nombre');
    }

    public function variedades_activos()
    {
        return $this->hasMany('\yura\Modelos\Variedad', 'id_planta')->where('estado', '=', 1)->orderBy('nombre');
    }
}
