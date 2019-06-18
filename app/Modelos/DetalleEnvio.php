<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleEnvio extends Model
{
    protected $table = 'detalle_envio';
    protected $primaryKey = 'id_detalle_envio';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_especificacion',
        'id_envio',
        'id_aerolinea',    // default: null
        'cantidad',
        'envio', //agrupción de los detalles de envíos
        'form'
    ];

    public function envio()
    {
        return $this->belongsTo('\yura\Modelos\Envio', 'id_envio');
    }

    public function especificacion()
    {
        return $this->belongsTo('\yura\Modelos\Especificacion', 'id_especificacion');
    }

    public function aerolinea(){

        return $this->belongsTo('\yura\Modelos\Aerolinea', 'id_aerolinea');
    }

}
