<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use yura\Modelos\Pedido;
use yura\Modelos\ResumenVentaDiaria;

use DB;

class VentaDiariaMesAnterior extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resumen_venta_diaria:mes_anterior';

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
        $inicio = now()->subDay()->subMonth()->toDateString();
        $fin =  now()->subDay()->toDateString();

        $fechas = DB::table('pedido as p')
            ->select('p.fecha_pedido as dia')->distinct()
            ->where('p.estado', '=', 1)
            ->whereBetween('p.fecha_pedido',[$inicio,$fin])
            ->orderBy('p.fecha_pedido')->get();
        dump($inicio,$fin);
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

            $ventaDiaria = ResumenVentaDiaria::where('fecha_pedido', $f->dia)->first();

            if(isset($existeData)){
<<<<<<< HEAD
                $objVentaDiaria= ResumenVentaDiaria::find($ventaDiaria->id_venta_diaria);
            }else{
                $objVentaDiaria= new ResumenVentaDiaria;
=======
                $objVentaDiaria= VentaDiaria::find($ventaDiaria->id_venta_diaria);
            }else{
                $objVentaDiaria= new VentaDiaria;
>>>>>>> 251f7fe766ebdabd5fbbeb32a7abf6a89b9edb5b
            }
            $objVentaDiaria->fecha_pedido= $f->dia;
            $objVentaDiaria->valor= $valor;
            $objVentaDiaria->cajas_equivalentes= $cajas;
            $objVentaDiaria->precio_x_ramo= $precio_x_ramo;
            $objVentaDiaria->save();
        }
    }

}
