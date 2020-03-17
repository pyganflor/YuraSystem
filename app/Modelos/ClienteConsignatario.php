<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ClienteConsignatario extends Model
{
    protected $table = 'cliente_consignatario';
    protected $primaryKey = 'id_cliente_consignatario';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cliente',
        'id_consignatario',
        'fecha_registro',
        'default',
        'estado'
    ];

    /*public function cliente(){
        return $this->belongsTo('yura\Modelos\Cliente','id_cliente');
    }*/
}
