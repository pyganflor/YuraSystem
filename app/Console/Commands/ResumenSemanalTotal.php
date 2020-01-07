<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use yura\Modelos\ProyeccionVentaSemanalReal;
use yura\Modelos\ResumenSemanalTotal as ModelResumenSemanaTotal;
use DB;

class ResumenSemanalTotal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resumen_total:semanal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agrupa el campo valor de la tabla proyeccion_venta_semanal_real para obtener el dinero total por semana';

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

        $dataProyeccionVentaSemanalReal = ProyeccionVentaSemanalReal::select(
            'codigo_semana',
            DB::raw('sum(valor) as valor')
        )->groupBy('codigo_semana')->get();

        foreach($dataProyeccionVentaSemanalReal as $data){
            $objResumenSemanalTotal = ModelResumenSemanaTotal::All()
            ->where('codigo_semana',$data->codigo_semana)->first();
            
            if(isset($objResumenSemanalTotal)){
                $objResumenSemanalTotal = ModelResumenSemanaTotal::find($objResumenSemanalTotal->id_resumen_semanal_total);
            }else{
                $objResumenSemanalTotal= new ModelResumenSemanaTotal;
            }

            $objResumenSemanalTotal->codigo_semana = $data->codigo_semana;
            if($data->valor > 0)
                $objResumenSemanalTotal->valor = $data->valor;
            $objResumenSemanalTotal->save();
        }

    }
}
