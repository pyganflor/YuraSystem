<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $table = 'sector';
    protected $primaryKey = 'id_sector';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_sector',
        'nombre',   // unico
        'fecha_registro',
        'estado',
        'descripcion',
    ];

    public function modulos()
    {
        return $this->hasMany('\yura\Modelos\Modulo', 'id_sector');
    }

    public function modulos_activos()
    {
        return $this->hasMany('\yura\Modelos\Modulo', 'id_sector')->where('estado', '=', 1);
    }
}