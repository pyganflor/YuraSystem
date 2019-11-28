<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use yura\Http\Controllers\Indicadores\Venta;
use yura\Modelos\Pedido;
use yura\Modelos\Variedad;
use yura\Modelos\VentaDiaria;

class VentaDiariaMesAnterior extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'venta_semanal:mes_anterior';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza un resumen de lo vendido del mes pasado con respecto a la fecha actual menos un dia';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $array_valor = [];
        $array_cajas = [];
        $array_precios = [];
        $fechaActual = now();
        $inicio = $fechaActual->subDay()->subMonth()->toDateString();
        $fin = $fechaActual->subDay()->toDateString();

        $fechas = DB::table('pedido as p')
            ->select('p.fecha_pedido as dia')->distinct()
            ->where('p.estado', '=', 1)
            ->whereBetween('p.fecha_pedido',[$inicio,$fin])
            ->orderBy('p.fecha_pedido')->get();

        foreach ($fechas as $f) {

            $valor = 0;
            $cajas = 0;
            $tallos = 0;

            $pedidos = Pedido::where('estado', 1)->where('fecha_pedido', $f->dia)->get();
            foreach ($pedidos as $pedido) {
                if (!getFacturaAnulada($pedido->id_pedido)) {
                    $valor += $pedido->getPrecioByPedido();
                    $cajas += $pedido->getCajas();
                    $tallos += $pedido->getTallos();
                }
            }

            $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
            $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;

            $ventaDiaria = VentaDiaria::where('fecha_pedido', $f->dia)->first();

            if(isset($existeData)){
                $objVentaDiaria= Venta::find($ventaDiaria->id_venta_diaria);
            }else{
                $objVentaDiaria= new Venta;
            }
            $objVentaDiaria->fecha_pedido= $f->dia;
            $objVentaDiaria->valor= $valor;
            $objVentaDiaria->cajas_equivalentes= $cajas;
            $objVentaDiaria->precio_x_ramo= $precio_x_ramo;
            $objVentaDiaria->save();
        }
    }

}
