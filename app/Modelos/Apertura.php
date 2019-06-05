<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Apertura extends Model
{
    protected $table = 'apertura';
    protected $primaryKey = 'id_apertura';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_apertura',
        'id_recepcion',
        'id_semana',
        'fecha_ingreso',
        'cantidad_semanas',
        'fecha_registro',
        'estado',
    ];

    public function recepcion()
    {
        return $this->belongsTo('\yura\Modelos\Recepcion', 'id_recepcion');
    }

    public function semana()
    {
        return $this->belongsTo('\yura\Modelos\Semana', 'id_semana');
    }
}
