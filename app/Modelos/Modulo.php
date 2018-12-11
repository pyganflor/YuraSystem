<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $table = 'modulo';
    protected $primaryKey = 'id_modulo';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_modulo',
        'nombre',   // unico
        'fecha_registro',
        'estado',
        'area',
        'descripcion',
        'id_sector',
    ];

    public function sector()
    {
        return $this->belongsTo('\yura\Modelos\Sector', 'id_sector');
    }

    public function lotes()
    {
        return $this->hasMany('\yura\Modelos\Lote', 'id_modulo');
    }

    public function lotes_activos()
    {
        return $this->hasMany('\yura\Modelos\Lote', 'id_modulo')->where('estado', '=', 1);
    }
}