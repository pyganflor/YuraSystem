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

    public function getRamosEstandar()
    {
        $r = 0;
        foreach ($this->detalles as $det_ped) {
            foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp) {
                foreach ($esp_emp->detalles as $det_esp) {
                    $ramos = $det_ped->cantidad * $esp_emp->cantidad * $det_esp->cantidad;
                    $r += convertToEstandar($ramos, explode('|', getCalibreRamoById($det_esp->id_clasificacion_ramo)->nombre)[0]);
                }
            }
        }
        return $r;
    }

    public function getRamosEstandarByVariedad($variedad)
    {
        $r = 0;
        foreach ($this->detalles as $det_ped) {
            foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp) {
                foreach ($esp_emp->detalles as $det_esp) {
                    if ($det_esp->id_variedad == $variedad) {
                        $ramos = $det_ped->cantidad * $esp_emp->cantidad * $det_esp->cantidad;
                        $r += convertToEstandar($ramos, explode('|', getCalibreRamoById($det_esp->id_clasificacion_ramo)->nombre)[0]);
                    }
                }
            }
        }
        return $r;
    }

    public function getTallos()
    {
        $r = 0;
        foreach ($this->detalles as $det_ped) {
            foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp) {
                foreach ($esp_emp->detalles as $det_esp) {
                    $ramos = $det_ped->cantidad * $esp_emp->cantidad * $det_esp->cantidad;
                    if ($det_esp->tallos_x_ramos != '') {
                        $r += $ramos * $det_esp->tallos_x_ramos;
                    } else {
                        $r += 0;
                    }
                }
            }
        }
        return $r;
    }

    public function getTallosByVariedad($variedad)
    {
        $r = 0;
        foreach ($this->detalles as $det_ped) {
            foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp) {
                foreach ($esp_emp->detalles as $det_esp) {
                    if ($det_esp->id_variedad == $variedad) {
                        $ramos = $det_ped->cantidad * $esp_emp->cantidad * $det_esp->cantidad;
                        if ($det_esp->tallos_x_ramos != '') {
                            $r += $ramos * $det_esp->tallos_x_ramos;
                        } else {
                            $r += 0;
                        }
                    }
                }
            }
        }
        return $r;
    }

    public function getCajas()
    {
        return round($this->getRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2);
    }

    public function getCajasByVariedad($variedad)
    {
        return round($this->getRamosEstandarByVariedad($variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
    }

    public function getPrecio()
    {
        $r = 0;
        foreach ($this->detalles as $det_ped) {
            foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp) {
                foreach ($esp_emp->detalles as $det_esp) {
                    $ramos = $det_ped->cantidad * $esp_emp->cantidad * $det_esp->cantidad;
                    $ramos_col = 0;
                    $precio_col = 0;
                    foreach (Coloracion::All()->where('id_detalle_pedido', $det_ped->id_detalle_pedido)
                                 ->where('id_especificacion_empaque', $esp_emp->id_especificacion_empaque)
                                 ->where('precio', '!=', '') as $col) {
                        $ramos_col += $col->getTotalRamosByDetEsp($det_esp->id_detalle_especificacionempaque);
                        $precio = getPrecioByDetEsp($col->precio, $det_esp->id_detalle_especificacionempaque);
                        $precio_col += ($col->getTotalRamosByDetEsp($det_esp->id_detalle_especificacionempaque) * $precio);
                    }
                    $ramos -= $ramos_col;
                    $precio_final = $ramos * getPrecioByDetEsp($det_ped->precio, $det_esp->id_detalle_especificacionempaque);
                    $precio_final += $precio_col;
                    $r += $precio_final;
                }
            }
        }
        if (count($this->envios) > 0)
            if ($this->envios[0]->comprobante != '') {  // PEDIDO FACTURADO
                return $this->envios[0]->comprobante->monto_total;
            } else {
                if ($this->envios[0]->fatura_cliente_tercero != '') {   // FACTURAR A NOMBRE DE OTRA PERSONA
                    $impuesto = TipoImpuesto::All()
                        ->where('codigo', $this->envios[0]->fatura_cliente_tercero->codigo_impuesto_porcentaje)->first()->porcentaje;
                    if (is_numeric($impuesto)) {
                        $r += $r * ($impuesto / 100);
                    }
                } else {    // FACTURAR A NOMBRE DEL CLIENTE
                    $impuesto = TipoImpuesto::All()
                        ->where('codigo', $this->cliente->detalle()->codigo_porcentaje_impuesto)->first()->porcentaje;
                    if (is_numeric($impuesto)) {
                        $r += $r * ($impuesto / 100);
                    }
                }
            }
        else {    // FACTURAR A NOMBRE DEL CLIENTE
            $impuesto = TipoImpuesto::All()
                ->where('codigo', $this->cliente->detalle()->codigo_porcentaje_impuesto)->first()->porcentaje;
            if (is_numeric($impuesto)) {
                $r += $r * ($impuesto / 100);
            }
        }
        return $r;
    }

    public function getPrecioByVariedad($variedad)
    {
        $r = 0;
        foreach ($this->detalles as $det_ped) {
            foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp) {
                foreach ($esp_emp->detalles as $det_esp) {
                    if ($det_esp->id_variedad == $variedad) {
                        $ramos = $det_ped->cantidad * $esp_emp->cantidad * $det_esp->cantidad;
                        $ramos_col = 0;
                        $precio_col = 0;
                        foreach (Coloracion::All()->where('id_detalle_pedido', $det_ped->id_detalle_pedido)
                                     ->where('id_especificacion_empaque', $esp_emp->id_especificacion_empaque)
                                     ->where('precio', '!=', '') as $col) {
                            $ramos_col += $col->getTotalRamosByDetEsp($det_esp->id_detalle_especificacionempaque);
                            $precio = getPrecioByDetEsp($col->precio, $det_esp->id_detalle_especificacionempaque);
                            $precio_col += ($col->getTotalRamosByDetEsp($det_esp->id_detalle_especificacionempaque) * $precio);
                        }
                        $ramos -= $ramos_col;
                        $precio_final = $ramos * getPrecioByDetEsp($det_ped->precio, $det_esp->id_detalle_especificacionempaque);
                        $precio_final += $precio_col;
                        $r += $precio_final;
                    }
                }
            }
        }
        if (count($this->envios) > 0)
            if ($this->envios[0]->comprobante != '') {  // PEDIDO FACTURADO
                return $this->envios[0]->comprobante->monto_total;
            } else {
                if ($this->envios[0]->fatura_cliente_tercero != '') {   // FACTURAR A NOMBRE DE OTRA PERSONA
                    $impuesto = TipoImpuesto::All()
                        ->where('codigo', $this->envios[0]->fatura_cliente_tercero->codigo_impuesto_porcentaje)->first()->porcentaje;
                    if (is_numeric($impuesto)) {
                        $r += $r * ($impuesto / 100);
                    }
                } else {    // FACTURAR A NOMBRE DEL CLIENTE
                    $impuesto = TipoImpuesto::All()
                        ->where('codigo', $this->cliente->detalle()->codigo_porcentaje_impuesto)->first()->porcentaje;
                    if (is_numeric($impuesto)) {
                        $r += $r * ($impuesto / 100);
                    }
                }
            }
        else {    // FACTURAR A NOMBRE DEL CLIENTE
            $impuesto = TipoImpuesto::All()
                ->where('codigo', $this->cliente->detalle()->codigo_porcentaje_impuesto)->first()->porcentaje;
            if (is_numeric($impuesto)) {
                $r += $r * ($impuesto / 100);
            }
        }
        return $r;
    }

    public function getVariedades() // optimizar consulta
    {
        $r = [];
        foreach ($this->detalles as $det_ped) {
            foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp) {
                foreach ($esp_emp->detalles as $det_esp) {
                    if (!in_array($det_esp->id_variedad, $r)) {
                        array_push($r, $det_esp->id_variedad);
                    }
                }
            }
        }
        return $r;
    }

    public function getCajasFisicas()
    {
        $r = DB::table('detalle_pedido as dp')
            ->select(DB::raw('sum(dp.cantidad) as cantidad'))
            ->where('dp.estado', '=', 1)
            ->where('dp.id_pedido', '=', $this->id_pedido)
            ->get()[0]->cantidad;

        return $r;
    }

    public function getCajasFisicasByVariedad($variedad)
    {
        $r = DB::table('detalle_pedido as dp')
            ->join('cliente_pedido_especificacion as cpe', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
            ->join('especificacion_empaque as esp_emp', 'esp_emp.id_especificacion', '=', 'cpe.id_especificacion')
            ->join('detalle_especificacionempaque as det_esp', 'det_esp.id_especificacion_empaque', '=', 'esp_emp.id_especificacion_empaque')
            ->select(DB::raw('sum(dp.cantidad) as cantidad'))
            ->where('dp.estado', '=', 1)
            ->where('dp.id_pedido', '=', $this->id_pedido)
            ->where('det_esp.id_variedad', '=', $variedad)
            ->get()[0]->cantidad;

        return $r;
    }
}