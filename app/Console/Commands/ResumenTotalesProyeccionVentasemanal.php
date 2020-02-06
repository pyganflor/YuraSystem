<?php

namespace yura\Console\Commands;

use DB;
use Illuminate\Console\Command;
use yura\Modelos\ProyeccionVentaSemanalReal;

class ResumenTotalesProyeccionVentasemanal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Realiza el resumen de los datos de la fila "Totales" de la proyección de la venta semanal';

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
        /*$existeSemana =ProyeccionVentaSemanalReal::where([
            ['id_variedad', $idVariedad],
            ['codigo_semana',$this->codigo]
        ])->select('codigo_semana')->exists();

        if(!$existeSemana){
            $primeraSemana = ProyeccionVentaSemanalReal::where(function($query) use ($idVariedad){
                if(isset($idVariedad))
                    $query->where('id_variedad', $idVariedad);
            })->select(DB::raw('MIN(codigo_semana) as codigo'))->first();
            $this->codigo = $primeraSemana->codigo;
        }

        $proyeccion = ProyeccionVentaSemanalReal::where([
            ['id_variedad',$idVariedad],
            ['codigo_semana',$this->codigo]
        ])->where(function($query) use ($idsCliente){
            if($idsCliente)
                $query->whereNotIn('id_cliente',$idsCliente);
        })->select(
            DB::raw('sum(valor) as total_valor'),
            DB::raw('sum(cajas_fisicas) as total_cajas_fisicas'),
            DB::raw('sum(cajas_equivalentes) as total_cajas_equivalentes')
        )->groupBy('codigo_semana')->first();

        $valorAnnoAnterior=0;
        $cajasEquivalentesAnnoAnterior=0;
        $totalCajasFisicasAnnoAterior=0;

        if($calculaAnnoAnterior) { //TOMA EN CUENTA LAS CAJAS DEL AÑO PASADO PARRA LA AUTO PROYECCIÓN DEL ANO ACTUAL

            $proyeccionAnnoActual = ProyeccionVentaSemanalReal::where([
                ['id_variedad', $idVariedad],
                ['codigo_semana', $this->codigo]
            ])->where(function ($query) use ($idsClientes) {
                if ($idsClientes)
                    $query->whereIn('id_cliente', $idsClientes);
            })->select('cajas_fisicas_anno_anterior','id_cliente')->get();

            foreach ($proyeccionAnnoActual as $item) {
                if ($item->cajas_fisicas == 0 && $semanaActual < $this->codigo) {

                    $cajasFisicasAnnoAnterior = $item->cajas_fisicas_anno_anterior;
                    $cajasEquivalentesAnnoAnterior += $cajasFisicasAnnoAnterior * $item->cliente->factor;
                    $totalCajasFisicasAnnoAterior += $cajasFisicasAnnoAnterior;
                    $ramosTotales = $cajasFisicasAnnoAnterior * $item->cliente->factor * $ramosxCajaEmpresa;
                    $precioPromedio = $item->cliente->precio_promedio($idVariedad);
                    $valorAnnoAnterior += $ramosTotales * (isset($precioPromedio) ? $precioPromedio->precio : 0);
                }
            }
        }
    }*/
}
