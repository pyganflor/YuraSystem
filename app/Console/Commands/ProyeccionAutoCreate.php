<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Modelos\Ciclo;
use yura\Modelos\ProyeccionModulo;
use yura\Modelos\Semana;

class ProyeccionAutoCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proyeccion:auto_create {modulo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commando para crear automaticamente proyecciones a un modulo con ciclo activo';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "proyeccion:auto_create" <<<<< ! >>>>>');
        $modulo = $this->argument('modulo');

        $ciclo = Ciclo::All()
            ->where('estado', 1)
            ->where('activo', 1)
            ->where('id_modulo', $modulo)
            ->first();
        $semana = Semana::All()
            ->where('estado', 1)
            ->where('id_variedad', $ciclo->id_variedad)
            ->where('fecha_inicial', '<=', $ciclo->fecha_inicio)
            ->where('fecha_final', '>=', $ciclo->fecha_inicio)
            ->first();

        $sum_semana = intval($ciclo->semana_poda_siembra) + intval(count(explode('-', $ciclo->curva)));
        $codigo = $semana->codigo;
        $new_codigo = $semana->codigo;
        $i = 1;
        $next = 1;
        while ($i < $sum_semana && $new_codigo <= getLastSemanaByVariedad($ciclo->id_variedad)->codigo) {
            $new_codigo = $codigo + $next;
            $query = Semana::All()
                ->where('estado', '=', 1)
                ->where('codigo', '=', $new_codigo)
                ->where('id_variedad', '=', $ciclo->id_variedad)
                ->first();

            if ($query != '') {
                $i++;
            }
            $next++;
        }

        /* ===================== QUITAR PROYECCIONES =================== */
        $proyecciones = ProyeccionModulo::All()
            ->where('estado', 1)
            ->where('id_variedad', $ciclo->id_variedad)
            ->where('id_modulo', $modulo);
        foreach ($proyecciones as $proy) {
            $proy->delete();
        }

        if ($query != '') {
            $proy = new ProyeccionModulo();
            $proy->id_modulo = $ciclo->id_modulo;
            $proy->id_semana = $query->id_semana;
            $proy->id_variedad = $ciclo->id_variedad;
            $proy->tipo = 'P';
            $proy->curva = $ciclo->curva;
            $proy->semana_poda_siembra = $ciclo->semana_poda_siembra;
            $proy->poda_siembra = $ciclo->modulo->getPodaSiembraByCiclo($ciclo->id_ciclo) + 1;
            $proy->plantas_iniciales = $ciclo->plantas_iniciales != '' ? $ciclo->plantas_iniciales : 0;
            $proy->desecho = $ciclo->desecho;
            $proy->tallos_planta = $ciclo->conteo != '' ? $ciclo->conteo : 0;
            $proy->tallos_ramo = $query->tallos_ramo_poda != '' ? $query->tallos_ramo_poda : 0;
            $proy->fecha_inicio = $query->fecha_final;

            $proy->save();
            $proy = ProyeccionModulo::All()->where('estado', 1)->where('id_modulo', $modulo)->last();
            $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
            Log::info('<*> MODULO: ' . getModuloById($modulo)->nombre . '  <*>');


            Log::info('<*> DURACION: ' . $time_duration . '  <*>');

            /* ======================== ACTUALIZAR LA TABLA PROYECCION_MODULO_SEMANA ====================== */
            $semana_fin = DB::table('semana')
                ->select(DB::raw('max(codigo) as max'))
                ->where('estado', '=', 1)
                ->where('id_variedad', '=', $ciclo->id_variedad)
                ->get()[0]->max;

            Artisan::call('proyeccion:update_semanal', [
                'semana_desde' => $proy->semana->codigo,
                'semana_hasta' => $semana_fin,
                'variedad' => $ciclo->id_variedad,
                'modulo' => $modulo,
            ]);
        } else {
            Log::info('<*> La semana se encuentra fuera de lo programado  <*>');
        }

        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "proyeccion:auto_create" <<<<< * >>>>>');
    }
}
