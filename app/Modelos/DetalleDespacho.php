<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleDespacho extends Model
{
    protected $table = 'detalle_despacho';
    protected $primaryKey = 'id_detalle_despacho';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_despacho',
        'id_pedido',
        'fecha_registro',
        'cantidad'
    ];

    public function pedido(){
        return $this->belongsTo('yura\Modelos\Pedido', 'id_pedido');
    }

    public function despacho(){
        return $this->belongsTo('yura\Modelos\Despacho', 'id_despacho' );
    }



}
