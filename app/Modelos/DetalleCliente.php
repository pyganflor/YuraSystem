<?php

namespace yura\Modelos;

use DB;
use Illuminate\Database\Eloquent\Model;

class DetalleCliente extends Model
{
    protected $table = 'detalle_cliente';
    protected $primaryKey = 'id_detalle_cliente';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'direccion',
        'provincia',
        'codigo_pais',
        'telefono',
        'ruc',
        'correo',
        'id_cliente',
        'codigo_impuesto',
        'codigo_porcentaje_impuesto',
        'codigo_identificacion',
        'estado',
        'almacen',
        'puerto_entrada',
        'tipo_credito',
        'id_marca'
    ];

    public function informacion_adicional($nombre_campo){
        return $this->hasMany('yura\Modelos\Documento','codigo')->where('nombre_campo',$nombre_campo)->first();
    }

    public function informacion_adicional_correo(){
        return $this->hasMany('yura\Modelos\Documento','codigo')->where('nombre_campo','correo')->get();
    }

    public function marca_caja(){
        return $this->belongsTo('yura\Modelos\Marca','id_marca');
    }

    public function cliente(){
        return $this->belongsTo('yura\Modelos\Cliente','id_cliente');
    }

    public function tipo_impuesto(){
        //dd($this->codigo_impuesto,$this->codigo_porcentaje_impuesto);
        $impuesto =  TipoImpuesto::where([
            ['codigo_impuesto',$this->codigo_impuesto],
            ['codigo',$this->codigo_porcentaje_impuesto]
        ])->first();
        return [
            'tipo_empuesto'=>$impuesto->descripcion,
            'porcentaje' =>$impuesto->porcentaje
            ];
    }


}
