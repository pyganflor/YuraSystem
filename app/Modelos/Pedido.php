<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'variedad', // String con los ids de las variedades incluidas en el pedido separados por "|"
        'tipo_especificacion',  // N => Normal; T => Flor Tinturada
        'confirmado',
    ];

    public function detalles()
    {
        return $this->hasMany('\yura\Modelos\DetallePedido', 'id_pedido');
    }

    public function envios()
    {
        return $this->hasMany('\yura\Modelos\Envio', 'id_pedido');
    }

    public function cliente()
    {
        return $this->belongsTo('\yura\Modelos\Cliente', 'id_cliente');
    }

    public function getLastDistribucion()
    {
        $l = DB::table('distribucion as d')
            ->join('marcacion as m', 'm.id_marcacion', '=', 'd.id_marcacion')
            ->join('detalle_pedido as dp', 'dp.id_detalle_pedido', '=', 'm.id_detalle_pedido')
            ->select('d.pos_pieza', 'd.id_distribucion')
            ->where('dp.id_pedido', '=', $this->id_pedido)
            ->orderBy('d.pos_pieza', 'desc')
            ->get();
        $distr = '';
        if (count($l) > 0) {
            $distr = Distribucion::find($l[0]->id_distribucion);
        }
        return $distr;
    }

    public function haveDistribucion()  // 1 -> Es de tipo 'O' y no tiene distribucion; 2 -> es de tipo 'O' y tiene distribucion; 0 -> es 'N'
    {
        if ($this->tipo_especificacion == 'O') {
            $flag = true;
            foreach ($this->detalles as $detalle) {
                foreach ($detalle->marcaciones as $marcacion) {
                    if (count($marcacion->distribuciones) == 0)
                        $flag = false;
                }
            }
            if ($flag)
                return 2;
            else
                return 1;
        } else
            return 0;
    }
}
