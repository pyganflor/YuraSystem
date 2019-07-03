<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleCliente extends Model
{
    protected $table = 'detalle_cliente';
    protected $primaryKey = 'id_detalle_cliente';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'direccion',
        'provincia',
        'codigo_pais',
        'telefono',
        'ruc',
        'correo',
        'id_cliente',
        'codigo_iva',
        'codigo_identificacion',
        'estado',
        'almacen',
        'puerto_entrada',
        'tipo_credito',
        'id_marca'
    ];

    public function informacion_adicional($nombre_campo){
        return $this->hasMany('yura\Modelos\Documento','codigo')->where('nombre_campo',$nombre_campo)->first();
    }

    public function informacion_adicional_correo(){
        return $this->hasMany('yura\Modelos\Documento','codigo')->where('nombre_campo','correo')->get();
    }

    public function marca_caja(){
        return $this->belongsTo('yura\Modelos\Marca','id_marca');
    }
}
