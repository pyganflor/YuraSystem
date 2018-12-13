<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ClienteAgenciaCarga extends Model
{
    protected $table = 'cliente_agenciacarga';
    protected $primaryKey = 'id_cliente_agencia_carga';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cliente','id_agencia_carga',
    ];


}
