<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class ActividadProducto extends Model
{
    protected $table = 'actividad_producto';
    protected $primaryKey = 'id_actividad_producto';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_actividad',
        'id_producto',
        'estado',
        'fecha_registro',
    ];

    public function actividad()
    {
        return $this->belongsTo('\yura\Modelos\Actividad', 'id_actividad');
    }

    public function producto()
    {
        return $this->belongsTo('\yura\Modelos\Producto', 'id_producto');
    }
}
