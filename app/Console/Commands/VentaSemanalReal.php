<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use yura\Jobs\UpdateSaldosProyVentaSemanal;
use yura\Modelos\Pedido;
use yura\Modelos\Cliente;
use yura\Modelos\ProyeccionVentaSemanalReal;
use yura\Modelos\Semana;
use yura\Modelos\Variedad;

class VentaSemanalReal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proyeccion:venta_semanal_real {semana_desde=0} {semana_hasta=0} {id_cliente=0} {variedad=0}';

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
    
            $inicio =  $tiempo_inicial = microtime(true);
            $desde = $this->argument('semana_desde');
            $hasta = $this->argument('semana_hasta');
            $variedad = $this->argument('variedad');
            $idCliente = $this->argument('id_cliente');

            Info("Inicio del comando proyeccion:venta_semanal_real");
            Info("Variables recibidas, desde: ".$desde. "hasta: ".$hasta. " variedad: ".$variedad ." idCliente: ".$idCliente);
            Info("Inicio del comando proyeccion:venta_semanal_real");
            $variedades = Variedad::where('estado', 1);
            
            if($variedad != 0)
                $variedades->where('id_variedad',$variedad);

            $variedades = $variedades->get();

            if($desde<=$hasta){
                $semanas = [];
                $objSemana= [];
                if($desde != 0){
                    $semana_desde = Semana::where('estado', 1)->where('codigo', $desde)->first();
                    if(!isset($semana_desde->codigo)){
                        Info("No existe la semana de inicio ".$desde);
                        return false;
                    }
                }else {
                    $semana_desde = getSemanaByDate(now()->toDateString());
                }

                if($hasta != 0){
                    $semana_hasta = Semana::where('estado', 1)->where('codigo', $hasta)->first();
                    if(!isset($semana_hasta->codigo)){
                        Info("No existe la semana de fin ". $hasta);
                        return false;
                    }
                }else{
                    $semana_hasta = getSemanaByDate(now()->toDateString());
                }
                Info('SEMANA DESDE: ' . $semana_desde->codigo);
                Info('SEMANA HASTA: ' . $semana_hasta->codigo );

                for($i=$semana_desde->codigo;$i<=$semana_hasta->codigo;$i++){
                    $existSemana= Semana::where('codigo', $i)->first();
                    if(isset($existSemana->codigo)){
                        $semanas[] =$existSemana->codigo;
                        $objSemana[] =$existSemana;
                    }
                }

                $clientes = Cliente::where('estado',1)
                    ->select('id_cliente');

                if($idCliente>0)
                    $clientes->where('cliente.id_cliente',$idCliente);

                $clientes = $clientes->get();
                foreach ($semanas as $x => $semana){
                    $arrSemana = str_split($semana,2);
                    $anoAnterior = (int)$arrSemana[0]-1;
                    $semanaAnoAnterior =  $anoAnterior.$arrSemana[1];
                    //dump($semanaAnoAnterior);
                    $fechaAnnoAnterior=Semana::where([
                        ['codigo',$semanaAnoAnterior],
                        ['estado',1]
                    ])->first();

                    //Info("Semana anterior: ".$semanaAnoAnterior);
                    foreach($clientes as $cliente){

                        if(isset($fechaAnnoAnterior)){
                            $pedidosAnnoAnterior = Pedido::where([
                                ['pedido.estado',true],
                                ['id_cliente',$cliente->id_cliente]
                            ])->whereBetween('fecha_pedido',[$fechaAnnoAnterior->fecha_inicial,$fechaAnnoAnterior->fecha_final])->get();
                        }else{
                            $pedidosAnnoAnterior=[];
                        }


                        $pedidos = Pedido::where([
                            ['pedido.estado',true],
                            ['id_cliente',$cliente->id_cliente]
                        ])->whereBetween('fecha_pedido',[$objSemana[$x]->fecha_inicial,$objSemana[$x]->fecha_final])->get();

                        foreach($variedades as $variedad){

                            $objProyeccionVentaSemanal = ProyeccionVentaSemanalReal::where([
                                    ['id_variedad',$variedad->id_variedad],
                                    ['id_cliente',$cliente->id_cliente],
                                    ['codigo_semana',$semana]
                                ])->first();


                            $cajasAnnoAnterior =0;
                            if(count($pedidosAnnoAnterior)>0)
                                foreach ($pedidosAnnoAnterior as $pedidoAnnoAnterior)
                                    $cajasAnnoAnterior+= $pedidoAnnoAnterior->getCajasFullByVariedad($variedad->id_variedad);


                            if(!isset($objProyeccionVentaSemanal)){
                                $objProySemReal = new ProyeccionVentaSemanalReal;
                                $objProySemReal->id_cliente = $cliente->id_cliente;
                                $objProySemReal->id_variedad = $variedad->id_variedad;
                                $objProySemReal->codigo_semana = $semana;
                            }else{
                                $objProySemReal = ProyeccionVentaSemanalReal::find($objProyeccionVentaSemanal->id_proyeccion_venta_semanal_real);
                            }

                            //if($objSemana[$x]->fecha_final >= $fechaActual->toDateString()){ //Comentar cuando se va a recopilar por primera vez toda la informacion en la tabla proyeccion_venta_semanal_real
                                $objProySemReal->valor =0;
                                $objProySemReal->cajas_equivalentes = 0;
                                $objProySemReal->cajas_fisicas = 0;
                                if($cajasAnnoAnterior >0)
                                    $objProySemReal->cajas_fisicas_anno_anterior = $cajasAnnoAnterior;
                               // $objProySemReal->cajas_fisicas_anno_anterior = isset($objProyeccionVentaSemanalAnoAnterior->cajas_fisicas) ? $objProyeccionVentaSemanalAnoAnterior->cajas_fisicas : 0;
                                foreach ($pedidos as $pedido){
                                    if(!getFacturaAnulada($pedido->id_pedido)){
                                        //if($pedido->fecha_pedido >= $fechaActual->toDateString()){ //Comentar cuando se va a recopilar por primera vez toda la informacion en la tabla proyeccion_venta_semanal_real
                                            //Info("Pedido incluido de fecha: ". $pedido->fecha_pedido);
                                            if(in_array($variedad->id_variedad,$pedido->getVariedades())){
                                                $objProySemReal->valor += $pedido->getPrecioByVariedad($variedad->id_variedad);
                                                $objProySemReal->cajas_equivalentes += $pedido->getCajasByVariedad($variedad->id_variedad);
                                                $objProySemReal->cajas_fisicas += $pedido->getCajasFullByVariedad($variedad->id_variedad);
                                            }
                                        /*$proyeccionVentaSemanal = ProyeccionVentaSemanal::where([
                                                ['id_variedad',$variedad->id_variedad],
                                                ['id_cliente',$cliente->id_cliente],
                                                ['codigo_semana',$semana]
                                            ])->first();

                                            if(isset($proyeccionVentaSemanal)){
                                                $objProySemReal->valor_proy = $proyeccionVentaSemanal->valor;
                                                $objProySemReal->cajas_equivalentes_proy = $proyeccionVentaSemanal->cajas_equivalentes;
                                                $objProySemReal->cajas_fisicas_proy = $proyeccionVentaSemanal->cajas_fisicas;
                                            }*/
                                        //}
                                    }
                                }
                                $objProySemReal->save();
                            //}
                        }
                    }
                }
            }else{
                Info('La semana hasta no puede ser menor a la semana desde en el comando VentaSemanalReal');
            }
            UpdateSaldosProyVentaSemanal::dispatch($semana_desde->codigo,0)->onQueue('update_saldos_proy_venta_semanal');
            $fin = microtime(true);
            Info("Fin del comando proyeccion:venta_semanal_real");
            Info("El script se completo en : ".(number_format(($fin-$inicio),2,".","")). " segundos");
    }
}
