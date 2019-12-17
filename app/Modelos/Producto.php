<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'id_producto';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'estado',
        'fecha_registro',
    ];

    public function actividades()
    {
        return $this->hasMany('\yura\Modelos\ActividadProducto', 'id_producto');
    }
}
