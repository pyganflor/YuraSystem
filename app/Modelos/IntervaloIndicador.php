<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class IntervaloIndicador extends Model
{
    protected $table = 'intervalo_indicador';
    protected $primaryKey = 'id_intervalo_indicador';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_indicador',
        'color',
        'desde',
        'hasta',
        'condicional',
        'tipo',
        'fecha_registro',
    ];

    public function indicador(){
        return $this->belongsTo('App\Modelos\Indicador','id_indicador');
    }
}
