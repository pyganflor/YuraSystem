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
    protected $signature = 'resumen_saldo_proyeccion:venta_semanal {desde=0} {hasta=0}';

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

        $variedades = Variedad::where('estado',1)->select('id_variedad')->get();
        $semanaInicio= $this->argument('desde') == 0
                            ? Semana::select(DB::raw('min(codigo) as codigo_semana'))->first()->codigo_semana
                            : $this->argument('desde');
        $semanaFin =  $this->argument('hasta') == 0
                            ? getSemanaByDate(now()->subDays(7)->toDateString())->codigo
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
            $saldoFinal = '';
            $y=0;
            if($variedad->id_variedad ==2)
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
                if ($y == 0) { //Primera iteración
                    $firstSemanaResumenSemanaCosechaByVariedad = (int)$objSemanaActual->firstSemanaResumenSemanaCosechaByVariedad($variedad->id_variedad);
                    if ($firstSemanaResumenSemanaCosechaByVariedad > $semana) {
                        $saldoInicial = $objSemanaActual->getSaldo($variedad->id_variedad);
                    } elseif ($firstSemanaResumenSemanaCosechaByVariedad < $semana) {
                        $saldoInicial = $objSemanaActual->getLastSaldoInicial($variedad->id_variedad, $semana);
                    } else {
                        $saldoInicial = $objSemanaActual->firstSaldoInicialByVariedad($variedad->id_variedad);
                    }

                }

                $saldoFinal = isset($objSemanaPasada) ? $objSemanaPasada->getSaldo($variedad->id_variedad) + $saldoInicial : $objSemanaActual->getSaldo($variedad->id_variedad) + $saldoInicial;

                if ($y > 0)
                    $saldoInicial = $saldoFinal;

                $objResumenSaldoProyeccionVentaSemanal->saldo_inicial=$saldoInicial;
                $objResumenSaldoProyeccionVentaSemanal->saldo_final=$saldoFinal;
                $objResumenSaldoProyeccionVentaSemanal->id_variedad = $variedad->id_variedad;
                $objResumenSaldoProyeccionVentaSemanal->codigo_semana = $semana;
                $objResumenSaldoProyeccionVentaSemanal->save();

                dump("Variedad: " . $variedad->id_variedad . " Semana: " . $semana . " Saldo inicial: " . $saldoInicial);
                $semanaPasada = $semana;
                $saldoFinal = $saldoInicial;
                $y++;

            }
        }


    }
}
