<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetallePedido extends Model
{
    protected $table = 'detalle_pedido';
    protected $primaryKey = 'id_detalle_pedido';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cliente_especificacion',
        'id_pedido',
        'id_agencia_carga',
        'cantidad',
        'fecha_registro',
        'precio'
    ];

    public function cliente_especificacion()
    {
        return $this->belongsTo('yura\Modelos\ClientePedidoEspecificacion', 'id_cliente_especificacion');
    }

    public function agencia_carga()
    {
        return $this->belongsTo('yura\Modelos\AgenciaCarga', 'id_agencia_carga');
    }

    public function pedido()
    {
        return $this->belongsTo('yura\Modelos\Pedido', 'id_pedido');
    }

    public function marcaciones()
    {
        return $this->hasMany('\yura\Modelos\Marcacion', 'id_detalle_pedido');
    }

    public function coloraciones()
    {
        return $this->hasMany('\yura\Modelos\Coloracion', 'id_detalle_pedido');
    }

    public function coloracionesByEspEmp($esp_emp)
    {
        return Coloracion::All()->where('id_detalle_pedido', $this->id_detalle_pedido)
            ->where('id_especificacion_empaque', $esp_emp);
    }

    public function getColoracionesMarcacionesByEspEmp($esp_emp)
    {
        return [
            'coloraciones' => Coloracion::where('id_detalle_pedido', $this->id_detalle_pedido)
                ->where('id_especificacion_empaque', $esp_emp)->get(),
            'marcaciones' => Marcacion::where('id_detalle_pedido', $this->id_detalle_pedido)
                ->where('id_especificacion_empaque', $esp_emp)->get(),
        ];
    }
}
