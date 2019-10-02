<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use yura\Modelos\Pedido;
use yura\Modelos\DetallePedido;
use yura\Modelos\Cliente;
use yura\Modelos\DetalleCliente;
use yura\Modelos\ProyeccionVentaSemanal;
use yura\Modelos\Semana;
use yura\Modelos\Variedad;

class VentaSemanalReal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proyeccion:venta_semanal_real {semana_desde=0} {semana_hasta=0} {id_cliente=0} {variedad=0} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rigstra el bturo por cliente y variedad de todos los pedidos que no tengan una factura anulada';

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
            $desde = $this->argument('semana_desde');
            $hasta = $this->argument('semana_hasta');
            $variedad = $this->argument('variedad');
            $idCliente = $this->argument('id_cliente');
            Info("Variables recibidas, desde: ".$desde. "hasta: ".$hasta. " variedad: ".$variedad ." idCliente: ".$idCliente);

            $variedades = Variedad::where('estado', 1);
            if($variedad != 0)
                $variedades->where('id_variedad',$variedad);

            $variedades = $variedades->get();

            if($desde<=$hasta){
                $semanas = [];
                $objSemana= [];
                if($desde != 0){
                    $semana_desde = Semana::where('estado', 1)->where('codigo', $desde)->first();
                    if(!isset($semana_desde))
                        return false;
                }else {
                    $semana_desde = getSemanaByDate(now()->toDateString());
                }

                if($hasta != 0){
                    $semana_hasta = Semana::where('estado', 1)->where('codigo', $hasta)->first();
                    if(!isset($semana_hasta))
                        return false;

                }else{
                    $semana_hasta = getSemanaByDate(now()->toDateString());
                }
                Info('SEMANA DESDE: ' . $semana_desde->codigo);
                Info('SEMANA HASTA: ' . $semana_hasta->codigo );

                //dd($semana_desde->codigo,$semana_hasta->codigo);
                for($i=$semana_desde->codigo;$i<=$semana_hasta->codigo;$i++){
                    $existSemana= Semana::where('codigo', $i)->first();
                    //dump($existSemana->codigo);
                    if(isset($existSemana->codigo)){
                        $semanas[]=$existSemana->codigo;
                        $objSemana[] =$existSemana;
                    }
                }

                foreach ($semanas as $x => $semana) {

                    $clientes = Cliente::where('estado',1)
                        ->select('id_cliente');

                    if($idCliente>0)
                        $clientes->where('cliente.id_cliente',$idCliente);

                    $clientes = $clientes->get();

                    //dd($clientes);
                    foreach($clientes as $cliente) {
                        foreach($variedades as $variedad){
                            $pedidos = Pedido::where([
                                ['pedido.estado',true],
                                ['id_cliente',$cliente->id_cliente]
                            ])->whereBetween('fecha_pedido',[$objSemana[$x]->fecha_inicial,$objSemana[$x]->fecha_final])->get();

                            $objProyeccionentaSemanal = ProyeccionVentaSemanal::where([
                                ['id_variedad',$variedad->id_variedad],
                                ['id_cliente',$cliente->id_cliente],
                                ['codigo_semana',$semana]
                            ])->first();

                            if(!isset($objProyeccionentaSemanal)){
                                $objProySem = new ProyeccionVentaSemanal;
                                $objProySem->id_cliente = $cliente->id_cliente;
                                $objProySem->id_variedad = $variedad->id_variedad;
                                $objProySem->codigo_semana = $semana;
                            }
                            $objProySem->valor =0;
                            $objProySem->cajas_equivalentes = 0;
                            $objProySem->cajas_fisicas = 0;

                            foreach ($pedidos as $pedido) {
                                if(in_array($variedad->id_variedad,$pedido->getVariedades()))
                                $objProySem->valor += $pedido->getPrecioByVariedad($variedad->id_variedad);
                                $objProySem->cajas_equivalentes += $pedido->getCajasByVariedad($variedad->id_variedad);
                                $objProySem->cajas_fisicas += $pedido->getCajasFisicasByVariedad($variedad->id_variedad);
                            }
                            $objProySem->save();
                        }
                    }
                }


            }else{
                Info('La semana hasta no pueder ser menor a la semana desde en el comando VentaSemanalReal');
            }
    }
}
