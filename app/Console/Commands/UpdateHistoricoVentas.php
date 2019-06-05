<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use yura\Modelos\HistoricoVentas;
use yura\Modelos\Pedido;
use yura\Modelos\Sector;

class UpdateHistoricoVentas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'historico_ventas:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Añadir los pedidos a la tabla historico_ventas';

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
        $msg = [];

        $pedidos = Pedido::All()->where('estado', 1)
            ->where('historico', 0)
            ->where('fecha_pedido', '<', date('Y-m-d'));

        foreach ($pedidos as $p) {
            foreach ($p->getVariedades() as $v) {
                $historico = HistoricoVentas::All()
                    ->where('id_cliente', $p->id_cliente)
                    ->where('id_variedad', $v)
                    ->where('mes', substr($p->fecha_pedido, 5, 2))
                    ->where('anno', substr($p->fecha_pedido, 0, 4))
                    ->first();

                if ($historico != '') {
                    $historico->valor += $p->getPrecioByVariedad($v);
                    $historico->cajas_fisicas += $p->getCajasFisicasByVariedad($v);
                    $historico->cajas_equivalentes += $p->getCajasByVariedad($v);
                    $historico->precio_x_ramo = round($historico->valor / ($historico->cajas_fisicas * getConfiguracionEmpresa()->ramos_x_caja), 2);
                } else {
                    $historico = new HistoricoVentas();
                    $historico->id_cliente = $p->id_cliente;
                    $historico->id_variedad = $v;
                    $historico->anno = substr($p->fecha_pedido, 0, 4);
                    $historico->mes = substr($p->fecha_pedido, 5, 2);

                    $historico->valor = $p->getPrecioByVariedad($v);
                    $historico->cajas_fisicas = $p->getCajasFisicasByVariedad($v);
                    $historico->cajas_equivalentes = $p->getCajasByVariedad($v);
                    $historico->precio_x_ramo = round($historico->valor / ($historico->cajas_fisicas * getConfiguracionEmpresa()->ramos_x_caja), 2);
                }

                if (!$historico->save()) {
                    $msg[] = 'ERROR: Ocurrió un problema con el pedido #' . $p->id_pedido . ' - variedad ' . $v->siglas;
                    return false;
                }
            }
            $p->historico = 1;
            $p->save();
            Log::info('Pedido #' . $p->id_pedido . ' procesado');
            $msg[] = 'Pedido #' . $p->id_pedido . ' procesado';
        }
        dd($msg);
    }
}