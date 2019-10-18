<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use yura\Modelos\ProyeccionModulo;
use yura\Modelos\Semana;

class ProyeccionUpdateCampo implements ShouldQueue
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
        $proy = ProyeccionModulo::find($this->id);
        $semana_ini = $proy->semana;
        $semana_fin = getLastSemanaByVariedad($proy->id_variedad);
        if ($this->campo == 'Tipo') {
            $proy->tipo = $this->valor;
        }
        if ($this->campo == 'Curva') {
            if (count(explode('-', $proy->curva)) != count(explode('-', $this->valor))) {  // hay que mover la curva
                $sum_semanas_old = $proy->semana_poda_siembra + count(explode('-', $proy->curva));
                $sum_semanas_new = $proy->semana_poda_siembra + count(explode('-', $this->valor));

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
                        ->where('id_variedad', '=', $proy->id_variedad)
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
                        ->where('id_variedad', '=', $proy->id_variedad)
                        ->first();

                    if ($semana_new != '') {
                        $i++;
                    }
                    $next++;
                }

                $next_proy = ProyeccionModulo::All()
                    ->where('estado', 1)
                    ->where('id_modulo', $proy->id_modulo)
                    ->where('id_semana', $semana_old->id_semana)
                    ->first();

                //dd($semana_new->codigo, $next_proy->semana->codigo);
                $proy->curva = $this->valor;
                if ($next_proy != '') {
                    if ($semana_new->codigo > $next_proy->semana->codigo) { // se trata de mover hacia adelante
                        $proyecciones = ProyeccionModulo::where('estado', 1)
                            ->where('fecha_inicio', '>', $next_proy->fecha_inicio)
                            ->where('id_modulo', $proy->id_modulo)
                            ->get();
                        foreach ($proyecciones as $_proy) {
                            $_proy->delete();
                        }
                        $next_proy->id_semana = $semana_new->id_semana;
                        $next_proy->fecha_inicio = $semana_new->fecha_final;

                        $next_proy->save();
                    }
                }
            } else {    // es del mismo tamaño la curva
                $proy->curva = $this->valor;
            }
        }
        if ($this->campo == 'SemanaCosecha') {
            if ($proy->semana_poda_siembra != $this->valor) {  // hay que mover la curva
                $sum_semanas_old = $proy->semana_poda_siembra + count(explode('-', $proy->curva));
                $sum_semanas_new = $this->valor + count(explode('-', $proy->curva));

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
                        ->where('id_variedad', '=', $proy->id_variedad)
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
                        ->where('id_variedad', '=', $proy->id_variedad)
                        ->first();

                    if ($semana_new != '') {
                        $i++;
                    }
                    $next++;
                }

                $next_proy = ProyeccionModulo::All()
                    ->where('estado', 1)
                    ->where('id_modulo', $proy->id_modulo)
                    ->where('id_semana', $semana_old->id_semana)
                    ->first();

                //dd($semana_new->codigo, $next_proy->semana->codigo);
                $proy->semana_poda_siembra = $this->valor;
                if ($next_proy != '') {
                    if ($semana_new->codigo > $next_proy->semana->codigo) { // se trata de mover hacia adelante
                        $proyecciones = ProyeccionModulo::where('estado', 1)
                            ->where('fecha_inicio', '>', $next_proy->fecha_inicio)
                            ->where('id_modulo', $proy->id_modulo)
                            ->get();
                        foreach ($proyecciones as $_proy) {
                            $_proy->delete();
                        }
                        $next_proy->id_semana = $semana_new->id_semana;
                        $next_proy->fecha_inicio = $semana_new->fecha_final;

                        $next_proy->save();
                    }
                }
            } else {    // es del mismo tamaño la curva
                $proy->semana_poda_siembra = $this->valor;
            }
        }
        if ($this->campo == 'PlantasIniciales') {
            $proy->plantas_iniciales = $this->valor;
        }
        if ($this->campo == 'Desecho') {
            $proy->desecho = $this->valor;
        }
        if ($this->campo == 'TallosPlanta') {
            $proy->tallos_planta = $this->valor;
        }
        if ($this->campo == 'TallosRamo') {
            $proy->tallos_ramo = $this->valor;
        }
        $proy->save();

    }
}
