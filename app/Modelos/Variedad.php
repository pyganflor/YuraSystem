<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;

class Variedad extends Model
{
    protected $table = 'variedad';
    protected $primaryKey = 'id_variedad';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_variedad',
        'nombre',
        'siglas',
        'unidad_de_medida',
        'cantidad',
        'minimo_apertura',
        'maximo_apertura',
        'estandar_apertura',
        'fecha_registro',
        'estado',
        'id_planta',
    ];

    public function planta()
    {
        return $this->belongsTo('\yura\Modelos\Planta', 'id_planta');
    }

    public function getDisponiblesToFecha($fecha)
    {
        $r = 0;
        $r_a = 0;   // acumulado
        $l = StockApertura::All()->where('estado', '=', 1)
            ->where('id_variedad', '=', $this->id_variedad);
        $pedidos = 0;
        foreach ($l as $item) {
            if (opDiasFecha('+', $item->dias, $item->fecha_inicio) == $fecha) {
                $r += $item->calcularDisponibles()['estandar_ingresados'];
                $r_a += $item->getDisponiblesAll('estandar_ingresados');
            }
        }
        if ($r_a > 0) {
            $fecha_p = Pedido::All()->where('estado', '=', 1)
                ->sortBy('fecha_pedidos')->first()->fecha_pedido;
            if ($fecha >= $fecha_p)
                $dias_last_pedido = difFechas($fecha, $fecha_p)->days;
            else
                $dias_last_pedido = 0;

            for ($i = 1; $i <= $dias_last_pedido; $i++) {
                $pedidos += $this->getPedidosToFecha(opDiasFecha('-', $i, $fecha));
            }
        }

        //dd($r_a);

        return [
            'cosechado' => round($r,2),
            'saldo' => round($r_a - $pedidos, 2),
            'acumulado' => round($r_a, 2),
        ];
    }

    public function getPedidosToFecha($fecha)
    {
        $r = 0;
        $l = getResumenPedidosByFecha($fecha, $this->id_variedad);
        foreach ($l as $item) {
            $ramo = ClasificacionRamo::find($item->id_clasificacion_ramo);
            $estandar = getCalibreRamoEstandar();
            $conversion = round(($ramo->nombre / $estandar->nombre) * $item->cantidad, 2);
            $r += $conversion;
        }
        $l = getResumenPedidosByFechaOfTallos($fecha, $this->id_variedad);
        foreach ($l as $item) {
            $ramo = ClasificacionRamo::find($item->id_clasificacion_ramo);
            $estandar = getCalibreRamoEstandar();
            $conversion = round(($ramo->nombre / $estandar->nombre) * $item->cantidad, 2);
            $r += $conversion;
        }
        return $r;
    }
}
