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

        $variedades = Variedad::where(function ($query){
            if($this->argument('variedad') != 0)
                $query->where('id_variedad',$this->argument('variedad'));
        })->where('estado',1)->select('id_variedad')->get();

        $semanaInicio= $this->argument('desde') == 0
                            ? getSemanaByDate(now()->subDays(7)->toDateString())->codigo
                            : $this->argument('desde');
        $semanaFin =  $this->argument('hasta') == 0
                            ? getSemanaByDate(now()->toDateString())->codigo
                            : $this->argument('hasta');

        $semanas=[];
        for ($x=$semanaInicio;$x<=$semanaFin;$x++){
            $existsSemana = Semana::where('codigo',$x)->exists();
            if($existsSemana){
                $semanas[]=$x;
            }
        }

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
                        ])->first();
                        if(isset($existeData)){
                            $valorSaldoInicial = $existeData->saldo_inicial;
                            //$valorSaldoFinal = $existeData->saldo_final;
                            Info("Saldo Inicial: ".$valorSaldoInicial. "Saldo Final: " );
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

                $objResumenSaldoProyeccionVentaSemanal->saldo_inicial=$valorSaldoInicial;
                $objResumenSaldoProyeccionVentaSemanal->saldo_final=$valorSaldoFinal;
                $objResumenSaldoProyeccionVentaSemanal->id_variedad = $variedad->id_variedad;
                $objResumenSaldoProyeccionVentaSemanal->codigo_semana = $semana;
                $objResumenSaldoProyeccionVentaSemanal->save();
                //dump("Variedad: " . $variedad->id_variedad . " Semana: " . $semana . " Saldo inicial: " . $valorSaldoInicial." Saldo Final: " . $valorSaldoFinal);
                $semanaPasada = $semana;
                $y++;

            }
        }


        $tiempo_final = microtime(true);
        Info("Fin del comando resumen_saldo_proyeccion:venta_semanal");
        Info("El script se completo en : ".(number_format(($tiempo_final-$tiempo_inicial),2,".","")). " segundos");
    }
}
