<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class FacturaClienteTercero extends Model
{
    protected $table = 'factura_cliente_tercero';
    protected $primaryKey = 'id_factura_cliente_tercero';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_envio',
        'nombre_cliente_tercero',
        'codigo_identificacion',
        'identificacion',
        'codigo_impuesto',
        'codigo_impuesto_porcentaje',
        'codigo_pais',
        'provincia',
        'correo',
        'telefono',
        'almacen',
        'direccion',
        'dae',
        'puerto_entrada',
        'tipo_credito',
        'id_marca',
        'codigo_dae'
    ];

    public function envio()
    {
        return $this->belongsTo('\yura\Modelos\Envio', 'id_envio');
    }

    public function marca_caja(){
        return $this->belongsTo('\yura\Modelos\Marca','id_marca');
    }
}
