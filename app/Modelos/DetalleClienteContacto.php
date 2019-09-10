<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleClienteContacto extends Model
{
    protected $table = 'detalle_cliente_contacto';
    protected $primaryKey = 'id_detalle_cliente_contacto';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_detalle_cliente',
        'id_contacto',
    ];

    public function contacto(){
        return $this->hasMany('yura\Modelos\Contacto','id_contacto');
    }

    public function cliente(){
        return $this->belongsTo('yura\Modelos\Cliente','id_detalle_cliente');
    }
}
