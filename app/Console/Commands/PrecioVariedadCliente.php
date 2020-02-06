<?php

namespace yura\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use yura\Modelos\Pedido;
use yura\Modelos\Cliente;
use yura\Modelos\PrecioVariedadCliente as PrecioPromedio;

class PrecioVariedadCliente extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'precio:variedad_x_cliente';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtiene el precio promedio por ramo equivalente de cada variedad por cliente, de los pedido anterirores a un mes de la fecha actual';

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
        $fechaActual = now();
        $fechaInicioBusqueda = Carbon::parse($fechaActual)->subMonth(1);
        Info("Fecha actual: ". $fechaActual->toDateString());
        Info("Fecha fin: ". $fechaInicioBusqueda);

        $clientes = Cliente::join('detalle_cliente as dc','cliente.id_cliente','dc.id_cliente')
            ->where([
                ['dc.estado',1],
                ['cliente.estado',1]
            ])->get();
        $idsClientes =[];

        foreach ($clientes as $cliente)
            $idsClientes[] = $cliente->id_cliente;


        $pedidos = Pedido::where('fecha_pedido','>=',$fechaInicioBusqueda->format('Y-m-d'))
            ->whereIn('id_cliente',$idsClientes)->get();

        $preciosXvariedad=[];
        $calibreEstandar = getCalibreRamoEstandar()->nombre;
        foreach ($pedidos as $pedido) {
            foreach($pedido->detalles as $detPed){
                foreach($detPed->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp){
                    foreach ($esp_emp->detalles as $n => $detEspEmp){
                       $calibreDetEspemp = $detEspEmp->clasificacion_ramo->nombre;
                       $ramoEquivalente = $calibreDetEspemp/$calibreEstandar;
                       $arrDetEspEmp = explode("|",$detPed->precio);
                        foreach ($arrDetEspEmp as $item) {
                            $x = explode(";",$item);
                            $precio = $x[0]/$ramoEquivalente;
                            if($detEspEmp->id_detalle_especificacionempaque == $x[1]){
                                $preciosXvariedad[$pedido->id_cliente][$detEspEmp->id_variedad][] = $precio;
                            }
                        }
                    }
                }
            }
        }
        $precioPromedioCliente = [];
        foreach($preciosXvariedad as $idCliente => $precioXvariedad){
            foreach ($precioXvariedad as $idVariedad => $precio) {
                $precioPromedioCliente[$idCliente][$idVariedad]= number_format((array_sum($precio) / count($precio)),2,".","");
            }
        }

        foreach ($precioPromedioCliente as $idCliente => $variedad) {
            foreach ($variedad as $idVariedad => $precio) {
                $dataPrecioVariedad = PrecioPromedio::where([
                    ['id_cliente',$idCliente],
                    ['id_variedad',$idVariedad]
                ])->first();

                if(isset($dataPrecioVariedad)){
                    $objPrecioVariedad = PrecioPromedio::find($dataPrecioVariedad->id_precio_variedad_cliente);
                    $objPrecioVariedad->precio = $precio;
                    $objPrecioVariedad->save();
                }else{
                    $objPrecioVariedad = new PrecioPromedio;
                    $objPrecioVariedad->id_cliente = $idCliente;
                    $objPrecioVariedad->id_variedad = $idVariedad;
                    $objPrecioVariedad->precio = $precio;
                    $objPrecioVariedad->save();
                }
            }
        }
    }
}
