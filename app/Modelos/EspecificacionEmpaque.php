<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class EspecificacionEmpaque extends Model
{
    protected $table = 'especificacion_empaque';
    protected $primaryKey = 'id_especificacion_empaque';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_especificacion_empaque',
        'id_especificacion',
        'id_empaque',
        'fecha_registro',
        'estado',
        'cantidad',
        'imagen',
    ];

    public function especificacion()
    {
        return $this->belongsTo('\yura\Modelos\Especificacion', 'id_especificacion');
    }

    public function empaque()
    {
        return $this->belongsTo('\yura\Modelos\Empaque', 'id_empaque');
    }

    public function detalles()
    {
        return $this->hasMany('\yura\Modelos\DetalleEspecificacionEmpaque', 'id_especificacion_empaque');
    }
    
    public function ramos_x_caja($idDetPed){
        $ramos = 0;

        foreach($this->detalles as $det_esp_emp){
            $ramos_modificado = getRamosXCajaModificado($idDetPed,$det_esp_emp->id_detalle_especificacionempaque);
            $ramos+= (isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad);
        }

        return $ramos;
    }
}
