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

    public function getDistinctMarcacionesColoracionesByEspEmp($esp_emp)
    {
        $m = DB::table('marcacion as m')
            ->select('m.id_marcacion')->distinct()
            ->where('m.id_detalle_pedido', '=', $this->id_detalle_pedido)
            ->where('m.id_especificacion_empaque', '=', $esp_emp)
            ->get();

        $c = DB::table('coloracion as c')
            ->join('marcacion as m', 'm.id_marcacion', '=', 'c.id_marcacion')
            ->select('c.id_color')->distinct()
            ->where('m.id_detalle_pedido', '=', $this->id_detalle_pedido)
            ->where('m.id_especificacion_empaque', '=', $esp_emp)
            ->get();

        return [
            'marcaciones' => $m,
            'coloraciones' => $c,
        ];
    }
}
