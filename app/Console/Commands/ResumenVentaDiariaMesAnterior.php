<?php

namespace yura\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use yura\Modelos\Pedido;
use yura\Modelos\ResumenVentaDiaria;
use DB;

class ResumenVentaDiariaMesAnterior extends Command
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

        Info('Comienzo del comando resumen_venta_diaria:mes_anterior a las '. now()->format('H:i:s'));
        Info("Fecha de inicio de busqueda:". $inicio);
        Info("Fecha de fin de busqueda:". $fin);

        $comienzo = $tiempo_inicial = microtime(true);

        $fechas = DB::table('pedido as p')  //Filtro de fecha donde solo hay pedidos
            ->select('p.fecha_pedido as dia')->distinct()
            ->where('p.estado', '=', 1)
            ->whereBetween('p.fecha_pedido',[$inicio,$fin])
            ->orderBy('p.fecha_pedido')->get();

        //dump($inicio,$fin);
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

            if(isset($ventaDiaria)){
                $objVentaDiaria= ResumenVentaDiaria::find($ventaDiaria->id_resumen_venta_diaria);
            }else{
                $objVentaDiaria= new ResumenVentaDiaria;
            }
            $objVentaDiaria->fecha_pedido= $f->dia;
            $objVentaDiaria->valor= $valor;
            $objVentaDiaria->cajas_equivalentes= $cajas;
            $objVentaDiaria->precio_x_ramo= $precio_x_ramo;
            $objVentaDiaria->save();
        }

        $deleteData =ResumenVentaDiaria::where('fecha_pedido','<=',Carbon::parse($inicio)->subDay()->toDateString())->select('id_resumen_venta_diaria')->get();
        foreach ($deleteData as $deleteDat)
            ResumenVentaDiaria::destroy($deleteDat->id_resumen_venta_diaria);

        $final = microtime(true);
        Info("El comando resumen_venta_diaria:mes_anterior se completo en : " . (number_format(($final - $comienzo), 2, ".", "")) . " segundos");
    }

}
