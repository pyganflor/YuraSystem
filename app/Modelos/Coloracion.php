<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Coloracion extends Model
{
    protected $table = 'coloracion';
    protected $primaryKey = 'id_coloracion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_coloracion',
        'id_color',
        'fecha_registro',
        'estado',
        'cantidad',
        'id_marcacion',
        'id_detalle_especificacionempaque',
    ];

    public function marcacion()
    {
        return $this->belongsTo('\yura\Modelos\Marcacion', 'id_marcacion');
    }

    public function color()
    {
        return $this->belongsTo('\yura\Modelos\Color', 'id_color');
    }

    public function detalle_especificacionempaque()
    {
        return $this->belongsTo('\yura\Modelos\DetalleEspecificacionEmpaque', 'id_detalle_especificacionempaque');
    }
}
