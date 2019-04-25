<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleGuiaRemision extends Model
{
    protected $table = 'detalle_guia_remision';
    protected $primaryKey = 'id_detalle_guia_remision';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_comprobante',
    ];
    public function comprobante(){
        return $this->belongsTo('yura\Modelos\Comprobante','id_comprobante');
    }
}
