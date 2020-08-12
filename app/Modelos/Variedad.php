<?php

namespace yura\Modelos;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'minimo_apertura',
        'maximo_apertura',
        'estandar_apertura',
        'fecha_registro',
        'estado',
        'id_planta',
        'tipo',
        'tallos_x_ramo_estandar',
        'tallos_x_malla',
        'color',
        'proy_inicio_cosecha_poda',  // inicio de cosecha proyectado para las podas
        'proy_inicio_cosecha_siembra',  // inicio de cosecha proyectado para las siembras,
        'proy_curva_poda',  // curva proyectada para las podas,
        'proy_curva_siembra',  // curva proyectada para las siembras,
    ];

    public function planta()
    {
        return $this->belongsTo('\yura\Modelos\Planta', 'id_planta');
    }

    public function clasificaciones()
    {
        return $this->hasMany('\yura\Modelos\VariedadClasificacionUnitaria', 'id_variedad');
    }

    public function regalias()
    {
        return $this->hasMany('\yura\Modelos\Regalias', 'id_variedad');
    }

    function regaliasBySemana($semana)
    {
        return $this->regalias->where('codigo_semana', $semana)->first();
    }

    function getRegaliasLastSemana($semana)
    {
        $r = DB::table('regalias')
            ->select('codigo_semana', 'valor')
            ->where('id_variedad', $this->id_variedad)
            ->where('codigo_semana', '<', $semana)
            ->orderBy('codigo_semana')
            ->get();
        return count($r) > 0 ? $r[count($r) - 1] : '';
    }

    public function getClasificacion($id)
    {
        return $this->clasificaciones->where('id_clasificacion_unitaria', $id)->first();
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
            'cosechado' => round($r, 2),
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