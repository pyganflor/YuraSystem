<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Pedido;

class EmpaquetarPedidosAnulados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pedido:empaquetar_anulados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commando para empaquetar los pedidos pasados que estén anulados';

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
        $pedidos = Pedido::All()
            ->where('estado', 1)
            ->where('empaquetado', 0);

        if (count($pedidos) > 0) {
            Log::info('<<<<< ! >>>>> Ejecutando comando "pedido:empaquetar_anulados" <<<<< ! >>>>>');
            foreach ($pedidos as $ped) {
                if (getFacturaAnulada($ped->id_pedido)) {
                    $ped->empaquetado = 1;
                    $ped->variedad = '';

                    $ped->save();
                    Log::info('Pedido #' . $ped->id_pedido . ' procesado');
                }
            }
            Log::info('<<<<< * >>>>> Fin satisfactorio del comando "pedido:empaquetar_anulados" <<<<< * >>>>>');

        }
    }
}
