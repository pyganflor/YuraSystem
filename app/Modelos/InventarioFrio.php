<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class InventarioFrio extends Model
{
    protected $table = 'inventario_frio';
    protected $primaryKey = 'id_inventario_frio';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_inventario_frio',
        'fecha_registro',
        'estado',
        'fecha_ingreso',
        'cantidad',
        'disponibles',
        'id_variedad',
        'id_clasificacion_ramo',
        'id_empaque_e',
        'id_empaque_p',
        'id_unidad_medida',
        'tallos_x_ramo',
        'longitud_ramo',
        'disponibilidad',
        'descripcion',
        'basura',
    ];

    public function variedad()
    {
        return $this->belongsTo('\yura\Modelos\Variedad', 'id_variedad');
    }

    public function clasificacion_ramo()
    {
        return $this->belongsTo('\yura\Modelos\ClasificacionRamo', 'id_clasificacion_ramo');
    }

    public function empaque_e()
    {
        return $this->belongsTo('\yura\Modelos\Empaque', 'id_empaque_e');
    }

    public function empaque_p()
    {
        return $this->belongsTo('\yura\Modelos\Empaque', 'id_empaque_p');
    }

    public function unidad_medida()
    {
        return $this->belongsTo('\yura\Modelos\UnidadMedida', 'id_unidad_medida');
    }
}
