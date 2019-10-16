<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class PrecioVariedadCliente extends Model
{
    protected $table = 'precio_variedad_cliente';
    protected $primaryKey = 'id_precio_variedad_cliente';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cliente',
        'id_variedad',
        'fecha_registro',
        'precio'
    ];

    public function cliente(){
        return $this->belongsTo('yura\Modelos\Cliente','id_cliente');
    }

    public function variedad(){
        return $this->belongsTo('yura\Modelos\Variedad','id_variedad');
    }
}
