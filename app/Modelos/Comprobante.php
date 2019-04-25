<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Comprobante extends Model
{
    protected $table = 'comprobante';
    protected $primaryKey = 'id_comprobante';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_comprobante',
        'id_envio',
        'clave_acceso',
        'estado',
        'fecha_emision',
        'tipo_comprobante',
        'monto_total',
        'numero_comprobante'
    ];

    public function envio(){
        return $this->belongsTo('yura\Modelos\Envio', 'id_envio');
    }

    public function detalle_guia_remision(){
        return $this->belongsTo('yura\Modelos\DetalleGuia\Remision','id_comprobante');
    }
}
