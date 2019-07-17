<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class AgenciaCarga extends Model
{
    protected $table = 'agencia_carga';
    protected $primaryKey = 'id_agencia_carga';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'codigo',
        'identificacion',
        'correo',
        'correo2',
        'correo3'
    ];

    public function cliente_agencia_carga()
    {
        return $this->hasMany('\yura\Modelos\ClienteAgenciaCarga', 'id_agencia_carga');
    }

}
