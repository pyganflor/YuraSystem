<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use yura\Modelos\Pedido;
use yura\Modelos\Cliente;
use yura\Modelos\ProyeccionVentaSemanalReal;
use yura\Modelos\ProyeccionVentaSemanal;
use yura\Modelos\Semana;
use yura\Modelos\Variedad;
use DB;

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

                for($i=$semana_desde->codigo;$i<=$semana_hasta->codigo;$i++){
                    $existSemana= Semana::where('codigo', $i)->first();
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

                    foreach($clientes as $cliente) {
                        foreach($variedades as $variedad){
                            $pedidos = Pedido::where([
                                ['pedido.estado',true],
                                ['id_cliente',$cliente->id_cliente]
                            ])->whereBetween('fecha_pedido',[$objSemana[$x]->fecha_inicial,$objSemana[$x]->fecha_final])->get();

                            $objProyeccionentaSemanal = ProyeccionVentaSemanalReal::where([
                                ['id_variedad',$variedad->id_variedad],
                                ['id_cliente',$cliente->id_cliente],
                                ['codigo_semana',$semana]
                            ])->first();

                            if(!isset($objProyeccionentaSemanal)){
                                $objProySemReal = new ProyeccionVentaSemanalReal;
                                $objProySemReal->id_cliente = $cliente->id_cliente;
                                $objProySemReal->id_variedad = $variedad->id_variedad;
                                $objProySemReal->codigo_semana = $semana;
                            }else{
                                $objProySemReal = ProyeccionVentaSemanalReal::find($objProyeccionentaSemanal->id_proyeccion_venta_semanal);
                            }
                            $objProySemReal->valor =0;
                            $objProySemReal->cajas_equivalentes = 0;
                            $objProySemReal->cajas_fisicas = 0;
                            $objProySemReal->valor_proy = 0;
                            $objProySemReal->cajas_equivalentes_proy =0;
                            $objProySemReal->cajas_fisicas_proy = 0;

                            foreach ($pedidos as $pedido){
                                if(in_array($variedad->id_variedad,$pedido->getVariedades())){
                                    $objProySemReal->valor += $pedido->getPrecioByVariedad($variedad->id_variedad);
                                    $objProySemReal->cajas_equivalentes += $pedido->getCajasByVariedad($variedad->id_variedad);
                                    $objProySemReal->cajas_fisicas += $pedido->getCajasFisicasByVariedad($variedad->id_variedad);
                                }
                                $proyeccionVentaSemanal = ProyeccionVentaSemanal::where([
                                    ['id_variedad',$variedad->id_variedad],
                                    ['id_cliente',$cliente->id_cliente],
                                    ['codigo_semana',$semana]
                                ])->first();

                                $objProySemReal->valor_proy = $proyeccionVentaSemanal->valor;
                                $objProySemReal->cajas_equivalentes_proy = $proyeccionVentaSemanal->cajas_equivalentes;
                                $objProySemReal->cajas_fisicas_proy = $proyeccionVentaSemanal->cajas_fisicas;
                            }
                            $objProySemReal->save();
                        }
                    }
                }

            }else{
                Info('La semana hasta no pueder ser menor a la semana desde en el comando VentaSemanalReal');
            }
    }
}
