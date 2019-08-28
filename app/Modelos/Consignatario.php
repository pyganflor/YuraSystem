<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Consignatario extends Model
{
    protected $table = 'consignatario';
    protected $primaryKey = 'id_consignatario';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'correo',
        'identificacion',
        'codigo_pais',
        'fecha_registro',
        'ciudad'
    ];

    public function pais(){
        return Pais::where('codigo',$this->codigo_pais)->first();
    }

    public function contacto_consignatario(){
        return $this->belongsTo('yura\Modelos\ContactoConsignatario','id_consignatario');
    }
}
