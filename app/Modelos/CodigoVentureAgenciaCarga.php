<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class CodigoVentureAgenciaCarga extends Model
{
    protected $table = 'codigo_venture_agencia_carga';
    protected $primaryKey = 'id_codigo_venture_agencia_carga';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_agencia_carga',
        'id_configuracion_empresa',
        'codigo',
        'fecha_registro'
    ];

    public function agencia_carga(){
        return $this->belongsTo('App/Modelos/AgenciaCarga','id_agencia_carga');
    }
}
