<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ProductoYuraVenture extends Model
{
    protected $table = 'productos_yura_venture';
    protected $primaryKey = 'id_producto_yura_venture';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'presentacion_yura',
        'codigo_venture',
        'fecha_registro',
    ];

    public function detalle_especificacionempaque(){
        return $this->belongsTo('yura\Modelos\DetalleEspecificacionEmpaque','id_detalle_especificacionempaque');
    }
}
