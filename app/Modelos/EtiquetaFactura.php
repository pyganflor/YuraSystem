<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class EtiquetaFactura extends Model
{
    protected $table = 'etiqueta_factura';
    protected $primaryKey = 'id_etiqueta_factura';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'id_pedido',
        'fecha_registro'
    ];

    public function detalles(){
        return $this->hasMany('yura\Modelos\DetalleEtiquetaFactura','id_etiqueta_factura');
    }
}
