<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedido';
    protected $primaryKey = 'id_pedido';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_cliente',
        'estado',
        'descripcion',
        'fecha_pedido',
        'empaquetado',
        'variedad',
        'tipo_especificacion',
    ];

    public function detalles()
    {
        return $this->hasMany('\yura\Modelos\DetallePedido', 'id_pedido');
    }

    public function cliente()
    {
        return $this->belongsTo('\yura\Modelos\Cliente', 'id_cliente');
    }

    public function haveDistribucion()
    {
        if ($this->tipo_especificacion == 'O') {
            foreach ($this->detalles as $detalle) {
                foreach ($detalle->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp) {
                    foreach ($esp_emp->marcaciones as $marcacion) {
                        if (count($marcacion->distribuciones) == 0)
                            return false;
                    }
                }
            }
            return true;
        } else
            return false;
    }
}
