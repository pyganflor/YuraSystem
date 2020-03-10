<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use yura\Modelos\Ciclo;

class RecalcularCurvas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'curva_cosecha:recalcular';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para recalcular las curvas de cosecha';

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
        $semana_pasada = getSemanaByDate(opDiasFecha('-', 7, date('Y-m-d')));
        $ciclos = DB::table('proyeccion_modulo_semana')
            ->select('modelo')->distinct()
            ->where('estado', 1)
            ->where('tabla', 'C')
            ->where('semana', $semana_pasada->codigo)
            ->where('id_variedad', 3)
            ->where('cosechados', '>', 0)
            ->get();
        foreach ($ciclos as $c) {
            $ciclo = Ciclo::find($c->modelo);
            if ($ciclo->modulo->nombre == '81B') {
                dd($ciclo->getTallosProyectados());
            }
        }
    }
}