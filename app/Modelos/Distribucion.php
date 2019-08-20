<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Distribucion extends Model
{
    protected $table = 'distribucion';
    protected $primaryKey = 'id_distribucion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_distribucion',
        'id_marcacion',
        'ramos',
        'piezas',
        'fecha_registro',
        'estado',
        'pos_pieza',
    ];

    public function marcacion()
    {
        return $this->belongsTo('\yura\Modelos\Marcacion', 'id_marcacion');
    }

    public function distribuciones_coloraciones()
    {
        return $this->hasMany('\yura\Modelos\DistribucionColoracion', 'id_distribucion');
    }

    public function distribuciones_coloraciones_mayor_cero()
    {
        return $this->hasMany('\yura\Modelos\DistribucionColoracion', 'id_distribucion')->where('cantidad','>',0);
    }

    public function getDistribucionMarcacionByMarcCol($marc_col)
    {
        return DistribucionColoracion::All()->where('id_distribucion', $this->id_distribucion)
            ->where('id_marcacion_coloracion', $marc_col)->first();
    }
}
