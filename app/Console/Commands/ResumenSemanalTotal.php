<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use yura\Modelos\ProyeccionVentaSemanalReal;
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

        $objProyeccionVentaSemanalReal = ProyeccionVentaSemanalReal::select(
            'codigo_semana',
            DB::raw('sum(valor) as valor')
        )->groupBy('codigo_semana')->get();

        dd($objProyeccionVentaSemanalReal);

    }
}
