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
        'id_detalle_pedido',
        'id_especificacion_empaque',
    ];

    public function detalle_pedido()
    {
        return $this->belongsTo('\yura\Modelos\Detalle_Pedido', 'id_detalle_pedido');
    }

    public function color()
    {
        return $this->belongsTo('\yura\Modelos\Color', 'id_color');
    }

    public function especificacion_empaque()
    {
        return $this->belongsTo('\yura\Modelos\EspecificacionEmpaque', 'id_especificacion_empaque');
    }
}
