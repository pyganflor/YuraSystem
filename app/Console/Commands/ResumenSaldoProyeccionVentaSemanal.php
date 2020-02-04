<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use yura\Modelos\Semana;
use yura\Modelos\Variedad;
use yura\Modelos\ResumenSaldoProyeccionVentaSemanal as ResumenSaldoProyVentaSemanal;
use DB;

class ResumenSaldoProyeccionVentaSemanal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resumen_saldo_proyeccion:venta_semanal {desde=0} {hasta=0} {variedad=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza un resumen de los saldos inicial y final para la proyección de las ventas semanales';

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
    public function handle(){

        Info('Comienzo del comando resumen_saldo_proyeccion:venta_semanal a las '. now()->format('H:i:s'));
        $tiempo_inicial =  $tiempo_inicial = microtime(true);
        Info("Variables recibidas, desde: ".$this->argument('desde'). "hasta: ". $this->argument('hasta') . " variedad: ".$this->argument('variedad'));

        $this->argument('variedad') != 0
            ? $idVariedad = $this->argument('variedad')
            : null;

        $semanaInicio= $this->argument('desde') == 0
                            ? getSemanaByDate(now()->subDays(7)->toDateString())->codigo
                            : $this->argument('desde');
        $semanaFin =  $this->argument('hasta') == 0
                            ? Semana::orderBy('codigo','desc')->first()->codigo//getSemanaByDate(now()->toDateString())->codigo
                            : $this->argument('hasta');

        $variedades = Variedad::where(function ($query){
            if(isset($idVariedad))
                $query->where('id_variedad',$idVariedad);
        })->where('estado',1)->select('id_variedad')->get();



        $semanas=[];
        for ($x=$semanaInicio;$x<=$semanaFin;$x++){
            $existsSemana = Semana::where('codigo',$x)->exists();
            if($existsSemana){
                $semanas[]=$x;
            }
        }
        /*$cajasEquivalentesAnnoAnterior=0;
        $cajasFisicasAnnoAterior=0;
        $valorAnnoAnterior=0;
        $ramosxCajaEmpresa = getConfiguracionEmpresa()->ramos_x_caja;*/

        foreach ($variedades as $variedad){
            $semanaPasada = '';
            $y=0;
            foreach ($semanas as $semana) {
                $dataResumenSaldoProyeccionVentaSemanal = ResumenSaldoProyVentaSemanal::where([
                    ['id_variedad',$variedad->id_variedad],
                    ['codigo_semana',$semana]
                ])->select('id_resumen_saldo_proy_venta_semanal')->first();

                isset($dataResumenSaldoProyeccionVentaSemanal)
                    ? $objResumenSaldoProyeccionVentaSemanal = ResumenSaldoProyVentaSemanal::find($dataResumenSaldoProyeccionVentaSemanal->id_resumen_saldo_proy_venta_semanal)
                    : $objResumenSaldoProyeccionVentaSemanal = new ResumenSaldoProyVentaSemanal;

                $objSemanaActual = getObjSemana($semana);
                $objSemanaPasada = getObjSemana($semanaPasada);

                if ($y == 0) {
                    $firstSemanaResumenSemanaCosechaByVariedad = (int)$objSemanaActual->firstSemanaResumenSemanaCosechaByVariedad($variedad->id_variedad);
                    if ($firstSemanaResumenSemanaCosechaByVariedad > $semana) {
                        $valorSaldoInicial = $objSemanaActual->getSaldo($variedad->id_variedad);
                        $valorSaldoFinal = $valorSaldoInicial;
                    } elseif ($firstSemanaResumenSemanaCosechaByVariedad < $semana) {
                        $existeData = ResumenSaldoProyVentaSemanal::where([
                            ['id_variedad',$variedad->id_variedad],
                            ['codigo_semana',$semana]
                        ])->select('saldo_inicial')->first();
                        if(isset($existeData)){
                            $valorSaldoInicial = $existeData->saldo_inicial;
                        }else{
                            $valorSaldoInicial = $objSemanaActual->getLastSaldoInicial($variedad->id_variedad, $semana);
                        }
                        $valorSaldoFinal = $objSemanaActual->getLastSaldoFinal($variedad->id_variedad,$semana);
                    } else {
                        $valorSaldoInicial = $objSemanaActual->firstSaldoInicialByVariedad($variedad->id_variedad);
                        $valorSaldoFinal = $valorSaldoInicial+round($objSemanaActual->getSaldo($variedad->id_variedad),2);
                    }
                }

                $saldoF = isset($objSemanaPasada) ? $objSemanaPasada->getSaldo($variedad->id_variedad) + $valorSaldoInicial : $objSemanaActual->getSaldo($variedad->id_variedad) + $valorSaldoInicial;
                $saldoI = round($objSemanaActual->getSaldo($variedad->id_variedad),2)+$valorSaldoFinal;

                if ($y > 0){
                    $valorSaldoInicial = $saldoF;
                    $valorSaldoFinal = $saldoI;
                }

                ///  TOMA EN CUANTA EL AÑO PASADO PARA AFECTAR EL SALDO INICIAL Y FINAL   ///
               /* $proyeccionAnnoActual = ProyeccionVentaSemanalReal::where(function($query) use ($idVariedad){
                    if(isset($idVariedad))
                        $query->where('id_variedad',$idVariedad);
                })->where('codigo_semana',$semana)->get();


                foreach ($proyeccionAnnoActual as $item) {
                    if($item->cajas_fisicas == 0 && $semanaActual < $semana){
                        $cF = $this->cajasFisicasAnnoAnterior($idVariedad,$item->cliente->id_cliente);
                        $cajasEquivalentesAnnoAnterior += $cF->cajas_fisicas_anno_anterior*$item->cliente->factor;
                        //$cajasFisicasAnnoAterior+= $cF->cajas_fisicas_anno_anterior;
                        $ramosTotales = $cF->cajas_fisicas_anno_anterior*$item->cliente->factor*$ramosxCajaEmpresa;
                        $precioPromedio = $item->cliente->precio_promedio($idVariedad);
                        $valorAnnoAnterior += $ramosTotales*(isset($precioPromedio) ? $precioPromedio->precio : 0);
                    }
                }*/

                $objResumenSaldoProyeccionVentaSemanal->saldo_inicial=$valorSaldoInicial;
                $objResumenSaldoProyeccionVentaSemanal->saldo_final=$valorSaldoFinal;
                $objResumenSaldoProyeccionVentaSemanal->id_variedad = $variedad->id_variedad;
                $objResumenSaldoProyeccionVentaSemanal->codigo_semana = $semana;
                $objResumenSaldoProyeccionVentaSemanal->save();
                $semanaPasada = $semana;
                $y++;

            }
        }
        $tiempo_final = microtime(true);
        Info("Fin del comando resumen_saldo_proyeccion:venta_semanal");
        Info("El script se completo en : ".(number_format(($tiempo_final-$tiempo_inicial),2,".","")). " segundos");
    }
}
