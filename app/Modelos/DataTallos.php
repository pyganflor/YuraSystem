<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class DataTallos extends Model
{
    protected $table = 'data_tallos';
    protected $primaryKey = 'id_data_tallos';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_detalle_pedido',
        'mallas',
        'ramos_x_caja',
        'tallos_x_caja',
        'tallos_x_malla',
        'tallos_x_ramo',
        'fecha_registro',
    ];

    public function detalle_pedido(){
        return $this->hasMany('yura\Modelos\DetallePedido','id_detalle_pedido');
    }

}
