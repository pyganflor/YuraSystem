<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleEtiquetaFactura extends Model
{
    protected $table = 'detalle_etiqueta_factura';
    protected $primaryKey = 'id_detalle_etiqueta_factura';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'id_etiqueta_factura',
        'empaque',
        'cantidad',
        'id_detalle_especificacion_empaque',
        'siglas',
        'et_inicial',
        'et_final',
        'fecha_registro'
    ];

    public function etiqueta_factura(){
        return $this->belongsTo('yura\Modelos\EtiquetaFactura','id_etiqueta_factura');
    }

    public function detalle_especificacion_empaque(){
        return $this->belongsTo('yura\Modelos\DetalleEspecificacionEmpaque', 'id_detalle_especificacion_empaque');
    }
}
