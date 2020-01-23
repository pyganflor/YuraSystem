<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Indicador extends Model
{
    protected $table = 'indicador';
    protected $primaryKey = 'id_indicador';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_indicador',
        'nombre',
        'descripcion',
        'valor',
        'fecha_registro',
        'estado',
    ];

    public function intervalos()
    {
        return $this->hasMany('yura\Modelos\IntervaloIndicador', 'id_indicador');
    }

    public function variedades()
    {
        return $this->hasMany('yura\Modelos\IndicadorVariedad', 'id_indicador');
    }

    public function getVariedad($variedad)
    {
        return $this->variedades->where('id_variedad', $variedad)->first();
    }
}
