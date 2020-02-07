<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Cliente;
use yura\Modelos\HistoricoVentas;
use yura\Modelos\Pedido;
use yura\Modelos\Sector;
use yura\Modelos\Semana;

class UpdateHistoricoVentas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'historico_ventas:update {desde=0} {hasta=0}';

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
        $ini = date('Y-m-d H:i:s');
        Log::info('<<<<< ! >>>>> Ejecutando comando "historico_ventas:update" <<<<< ! >>>>>');

        $desde_par = $this->argument('desde');
        $hasta_par = $this->argument('hasta');

        if ($desde_par <= $hasta_par) {
            if ($desde_par != 0)
                $semana_desde = Semana::All()->where('estado', 1)->where('codigo', $desde_par)->first();
            else
                $semana_desde = getSemanaByDate(date('Y-m-d'));
            if ($hasta_par != 0)
                $semana_hasta = Semana::All()->where('estado', 1)->where('codigo', $hasta_par)->first();
            else
                $semana_hasta = getSemanaByDate(date('Y-m-d'));

            $array_semanas = [];
            for ($i = $semana_desde->codigo; $i <= $semana_hasta->codigo; $i++) {
                $semana = Semana::All()
                    ->where('estado', 1)
                    ->where('codigo', $i)->first();
                if ($semana != '')
                    if (!in_array($semana, $array_semanas)) {
                        array_push($array_semanas, $semana);
                    }
            }

            $array_meses = [];
            foreach ($array_semanas as $sem) {
                $mes = [
                    'mes' => substr($sem->fecha_inicial, 5, 2),
                    'anno' => substr($sem->fecha_inicial, 0, 4)
                ];

                if (!in_array($mes, $array_meses))
                    array_push($array_meses, $mes);
            }

            foreach ($array_meses as $mes) {
                foreach (Cliente::All()->where('estado', 1) as $cli) {
                    $pedidos = DB::select("select * from pedido where year(fecha_pedido) = " . $mes['anno'] . " and month(fecha_pedido) = " . $mes['mes'] . " and id_cliente = " . $cli->id_cliente);

                    foreach (getVariedades() as $var) {
                        $model = HistoricoVentas::All()
                            ->where('id_cliente', $cli->id_cliente)
                            ->where('id_variedad', $var->id_variedad)
                            ->where('mes', $mes['mes'])
                            ->where('anno', $mes['anno'])
                            ->first();

                        if ($model == '') { // es nuevo
                            $model = new HistoricoVentas();
                            $model->id_cliente = $cli->id_cliente;
                            $model->id_variedad = $var->id_variedad;
                            $model->mes = $mes['mes'];
                            $model->anno = $mes['anno'];
                        }

                        $valor = 0;
                        $cajas_fisicas = 0;
                        $cajas_equivalentes = 0;
                        foreach ($pedidos as $p) {
                            $p = Pedido::find($p->id_pedido);
                            if (!getFacturaAnulada($p->id_pedido)) {
                                $valor += $p->getPrecioByPedidoVariedad($var->id_variedad);
                                $cajas_fisicas += $p->getCajasFullByVariedad($var->id_variedad);
                                $cajas_equivalentes += $p->getCajasByVariedad($var->id_variedad);
                            }
                        }
                        $precio_x_ramo = $cajas_equivalentes > 0 ? round($valor / ($cajas_equivalentes * getConfiguracionEmpresa()->ramos_x_caja), 2) : 0;

                        $model->valor = $valor;
                        $model->cajas_fisicas = $cajas_fisicas;
                        $model->cajas_equivalentes = $cajas_equivalentes;
                        $model->precio_x_ramo = $precio_x_ramo;

                        if (!$model->save()) {
                            Log::info('ERROR: Ocurrió un problema con el pedido #' . $p->id_pedido . ' - variedad ' . $var->siglas);
                            return false;
                        }
                    }
                }
            }
        }
        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "historico_ventas:update" <<<<< * >>>>>');
    }
}