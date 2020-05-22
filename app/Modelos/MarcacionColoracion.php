<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class MarcacionColoracion extends Model
{
    protected $table = 'marcacion_coloracion';
    protected $primaryKey = 'id_marcacion_coloracion';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_marcacion',
        'id_coloracion',
        'fecha_registro',
        'estado',
        'cantidad',
        'precio',
        'id_detalle_especificacionempaque',
    ];

    public function detalle_especificacionempaque()
    {
        return $this->belongsTo('\yura\Modelos\DetalleEspecificacionEmpaque', 'id_detalle_especificacionempaque');
    }

    public function marcacion()
    {
        return $this->belongsTo('\yura\Modelos\Marcacion', 'id_marcacion');
    }

    public function coloracion()
    {
        return $this->belongsTo('\yura\Modelos\Coloracion', 'id_coloracion');
    }

    public function distribuciones_coloraciones(){
        return $this->hasMany('\yura\Modelos\DistribucionColoracion', 'id_marcacion_coloracion');
    }
}
