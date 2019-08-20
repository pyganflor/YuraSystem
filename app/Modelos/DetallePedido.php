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
        'precio',
        'orden'
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


    public function marcacionesByEspEmp($esp_emp)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));

        return Marcacion::All()->where('id_detalle_pedido', $this->id_detalle_pedido)
            ->where('id_especificacion_empaque', $esp_emp);
    }

    public function coloraciones()
    {
        return $this->hasMany('\yura\Modelos\Coloracion', 'id_detalle_pedido');
    }

    public function coloracionesByEspEmp($esp_emp)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));

        return Coloracion::All()->where('id_detalle_pedido', $this->id_detalle_pedido)
            ->where('id_especificacion_empaque', $esp_emp);
    }

    public function getColoracionesMarcacionesByEspEmp($esp_emp)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        return [
            'coloraciones' => Coloracion::where('id_detalle_pedido', $this->id_detalle_pedido)
                ->where('id_especificacion_empaque', $esp_emp)->get(),
            'marcaciones' => Marcacion::where('id_detalle_pedido', $this->id_detalle_pedido)
                ->where('id_especificacion_empaque', $esp_emp)->get(),
        ];
    }

    public function haveDistribucion()
    {
        if (count($this->marcaciones) > 0) {
            foreach ($this->marcaciones as $m) {
                if (count($m->distribuciones) == 0)
                    return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public function haveDistribucionByEspEmp($esp_emp)
    {
        if (count($this->marcaciones) > 0) {
            foreach ($this->marcacionesByEspEmp($esp_emp) as $m) {
                if (count($m->distribuciones) == 0)
                    return false;
            }
            return true;
        } else {
            return false;
        }
    }
}
