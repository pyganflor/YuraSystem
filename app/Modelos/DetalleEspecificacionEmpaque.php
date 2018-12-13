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
<<<<<<< HEAD
        'tallos_x_ramos'
=======
>>>>>>> f7d939a64537592b1e24eedf8cf21d3e9742e791
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
}
