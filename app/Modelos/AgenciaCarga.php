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
        'cdigo'
    ];

    public function cliente()
    {
        return $this->belongsTo('\yura\Modelos\Cliente', 'id_cliente');
    }

}
