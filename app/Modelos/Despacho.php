<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Despacho extends Model
{
    protected $table = 'despacho';
    protected $primaryKey = 'id_despacho';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_transportista',
        'id_camion',
        'id_chofer',
        'fecha_despacho',
        'sello_salida',
        'semana',
        'rango_temp',
        'n_viaje',
        'hora_salida',
        'temp',
        'kilometraje',
        'sellos',
        'fecha_registro',
        'horario',
        'resp_ofi_despacho',
        'id_resp_ofi_despacho',
        'aux_cuarto_fri',
        'id_aux_cuarto_fri',
        'guardia_turno',
        'id_guardia_turno',
        'asist_comercial_ext',
        'id_asist_comrecial_ext',
        'resp_transporte',
        'id_resp_transporte',
        'n_despacho',
        'sello_adicional',
        'estado',
        'mail_resp_ofi_despacho',
        'id_configuracion_empresa'
    ];

    public function detalles(){
       return $this->hasMany('\yura\Modelos\DetalleDespacho', 'id_despacho');
    }

    public function conductor(){
       return $this->belongsTo('\yura\Modelos\Conductor', 'id_conductor');
    }

    public function camion(){
       return $this->belongsTo('\yura\Modelos\Camion', 'id_camion');
    }

    public function transportista(){
        $this->belongsTo('\yura\Modelos\Transportista', 'id_transportista');
    }


}
