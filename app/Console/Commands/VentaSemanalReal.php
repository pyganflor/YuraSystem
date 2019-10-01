<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use yura\Modelos\Pedido;
use yura\Modelos\DetallePedido;
use yura\Modelos\Cliente;
use yura\Modelos\DetalleCliente;
use yura\Modelos\Semana;

class VentaSemanalReal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proyeccion:venta_semanal_real {semana_desde=0} {semana_hasta=0} {variedad=0} {id_cliente=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rigstra el bturo por variedad de todos los pedidos que no tengan una factura anulada';

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
            Info("Variables,   desde: ".$desde. "hasta: ".$hasta. " variedad: ".$variedad ." idCliente: ".$idCliente);
            if($desde<=$hasta){
                $pedidos = Pedido::where('pedido.estado',true);

                if($idCliente>0)
                    $pedidos->join('cliente as c','pedido.id_cliente','c.id_cliente')
                        ->where('pedido.id_cliente',$idCliente);

                $fecha_desde = Semana::where('codigo', $desde)->first();
                $fecha_hasta = Semana::where('codigo', $hasta)->first();

                if($hasta>0 && $desde>0)
                    $pedidos->whereBetween('fecha_pedido',[$fecha_desde->fecha_inicial,$fecha_hasta->fecha_final]);

                $pedidos = $pedidos->get();
                //dd($pedidos);
                $data =[];
                foreach ($pedidos as $pedido){
                    foreach ($pedido->detalles as $det_ped){
                        foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp){
                            foreach ($esp_emp->detalles as $n => $det_esp_emp){
                                $data[] = [
                                    'id_cliente' => $pedido->id_pedido,
                                    'id_variedad' => $det_esp_emp->id_variedad,
                                    'valor' => '',
                                    'cajas_equivalentes' => '',
                                    'estado' => 1,
                                    'codigo_semana' => '',
                                    'cajas_fisicas' => ''
                                ];
                            }
                        }
                    }
                }

                Info("Fechas: desde: ".$fecha_desde->fecha_inicial . " hasta: " . $fecha_hasta->fecha_final);

            }else{
                Info('La semana hasta no pueder ser menor a la semana desde en el comando VentaSemanalReal');
            }
    }
}
