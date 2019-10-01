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
            $variedades = getVariedades();
            if($desde<=$hasta){
                $semanas = [];
                if ($desde != 0)
                    $semana_desde = Semana::All()->where('estado', 1)->where('codigo', $desde)->first();
                else
                    $semana_desde = getSemanaByDate(now()->toDateString());
                if ($hasta != 0)
                    $semana_hasta = Semana::All()->where('estado', 1)->where('codigo', $hasta)->first();
                else
                    $semana_hasta = getSemanaByDate(now()->toDateString());

                info('SEMANA DESDE: ' . $desde . ' => ' . $semana_desde->codigo);
                info('SEMANA HASTA: ' . $hasta . ' => ' . $semana_hasta->codigo);
                for($i=$semana_desde;$i<=$semana_hasta;$i++){
                    $existSemana= Semana::where('codigo', $i)->first();
                    if(isset($existSemana->codigo)){
                        $semanas[]=$existSemana->codigo;
                    }
                }

                foreach ($semanas as $semana) {

                    $objSemana =  Semana::where('codigo', $semana)->first();

                    $pedidos = Pedido::where('pedido.estado',true)
                        ->whereBetween('fecha_pedido',[$objSemana->fecha_inicial,$objSemana->fecha_final]);

                    if($idCliente>0)
                        $pedidos->join('cliente as c','pedido.id_cliente','c.id_cliente')
                            ->where('pedido.id_cliente',$idCliente);

                    $pedidos = $pedidos->get();

                    foreach($pedidos as $pedido) {
                        foreach($variedades as $variedad){



                        }
                    }
                }


            }else{
                Info('La semana hasta no pueder ser menor a la semana desde en el comando VentaSemanalReal');
            }
    }
}
