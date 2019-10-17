<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use yura\Modelos\Ciclo;
use yura\Modelos\ProyeccionModulo;
use yura\Modelos\Semana;

class CicloUpdateCampo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $campo;
    protected $valor;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $campo, $valor)
    {
        $this->id = $id;
        $this->campo = $campo;
        $this->valor = $valor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ciclo = Ciclo::find($this->id);
        $semana_ini = Semana::All()
            ->where('estado', 1)
            ->where('id_variedad', $ciclo->id_variedad)
            ->where('fecha_inicial', '<=', $ciclo->fecha_inicio)
            ->where('fecha_final', '>=', $ciclo->fecha_inicio)
            ->first();
        $semana_fin = getLastSemanaByVariedad($ciclo->id_variedad);
        if ($this->campo == 'Tipo') {
            $ciclo->poda_siembra = $this->valor;
        }
        if ($this->campo == 'Curva') {
            if (count(explode('-', $ciclo->curva)) != count(explode('-', $this->valor))) {  // hay que mover la curva
                $sum_semanas_old = $ciclo->semana_poda_siembra + count(explode('-', $ciclo->curva));
                $sum_semanas_new = $ciclo->semana_poda_siembra + count(explode('-', $this->valor));

                /* ------------------------ OBTENER LAS SEMANAS NEW/OLD ---------------------- */
                $codigo = $semana_ini->codigo;
                $new_codigo = $semana_ini->codigo;
                $i = 1;
                $next = 1;
                while ($i < $sum_semanas_old && $new_codigo <= $semana_fin->codigo) {
                    $new_codigo = $codigo + $next;
                    $semana_old = Semana::All()
                        ->where('estado', '=', 1)
                        ->where('codigo', '=', $new_codigo)
                        ->where('id_variedad', '=', $ciclo->id_variedad)
                        ->first();

                    if ($semana_old != '') {
                        $i++;
                    }
                    $next++;
                }

                $codigo = $semana_ini->codigo;
                $new_codigo = $semana_ini->codigo;
                $i = 1;
                $next = 1;
                while ($i < $sum_semanas_new && $new_codigo <= $semana_fin->codigo) {
                    $new_codigo = $codigo + $next;
                    $semana_new = Semana::All()
                        ->where('estado', '=', 1)
                        ->where('codigo', '=', $new_codigo)
                        ->where('id_variedad', '=', $ciclo->id_variedad)
                        ->first();

                    if ($semana_new != '') {
                        $i++;
                    }
                    $next++;
                }

                $next_proy = ProyeccionModulo::All()
                    ->where('estado', 1)
                    ->where('id_modulo', $ciclo->id_modulo)
                    ->where('id_semana', $semana_old->id_semana)
                    ->first();

                //dd($semana_new->codigo, $next_proy->semana->codigo);
                $ciclo->curva = $this->valor;
                if ($next_proy != '') {
                    $proyecciones = ProyeccionModulo::where('estado', 1)
                        ->where('fecha_inicio', '>', $next_proy->fecha_inicio)
                        ->where('id_modulo', $ciclo->id_modulo)
                        ->get();
                    foreach ($proyecciones as $proy) {
                        $proy->delete();
                    }
                    $next_proy->id_semana = $semana_new->id_semana;
                    $next_proy->fecha_inicio = $semana_new->fecha_final;

                    $next_proy->save();
                }
            } else {    // es del mismo tamaño la curva
                $ciclo->curva = $this->valor;
            }
        }
        $ciclo->save();
    }
}
