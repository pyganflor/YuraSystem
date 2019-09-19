<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DetalleEspecificacionEmpaque extends Model
{
    protected $table = 'detalle_especificacionempaque';
    protected $primaryKey = 'id_detalle_especificacionempaque';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_detalle_especificacionempaque',
        'id_especificacion_empaque',
        'fecha_registro',
        'estado',
        'id_variedad',
        'id_clasificacion_ramo',
        'cantidad',
        'id_empaque_e',    // el id del empaque de tipo envase-envoltorio
        'id_empaque_p',    // el id del empaque de tipo presentacion
        'tallos_x_ramos',
        'longitud_ramo',
        'id_unidad_medida', // unidad de medida de longitud
        'id_grosor_ramo',   // grosor del ramo
        'total_tallos'
    ];

    public function especificacion_empaque()
    {
        return $this->belongsTo('\yura\Modelos\EspecificacionEmpaque', 'id_especificacion_empaque');
    }

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

    public function grosor_ramo()
    {
        return $this->belongsTo('\yura\Modelos\Grosor', 'id_grosor_ramo');
    }

    public function precioByCliente($cliente)
    {
        return Precio::All()->where('id_cliente', $cliente)
            ->where('id_detalle_especificacionempaque', $this->id_detalle_especificacionempaque)->first();
    }
}