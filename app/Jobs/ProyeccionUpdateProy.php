<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;

class ProyeccionUpdateProy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id_proyeccion_modulo;
    protected $semana;
    protected $tipo;
    protected $curva;
    protected $semana_poda_siembra;
    protected $plantas_iniciales;
    protected $desecho;
    protected $tallos_planta;
    protected $tallos_ramo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id_proyeccion_modulo, $semana, $tipo, $curva, $semana_poda_siembra, $plantas_iniciales, $desecho, $tallos_planta, $tallos_ramo)
    {
        $this->id_proyeccion_modulo = $id_proyeccion_modulo;
        $this->semana = $semana;
        $this->tipo = $tipo;
        $this->curva = $curva;
        $this->semana_poda_siembra = $semana_poda_siembra;
        $this->plantas_iniciales = $plantas_iniciales;
        $this->desecho = $desecho;
        $this->tallos_planta = $tallos_planta;
        $this->tallos_ramo = $tallos_ramo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Artisan::call('proyeccion:update_proy', [
            'id_proyeccion_modulo' => $this->id_proyeccion_modulo,
            'semana' => $this->semana,
            'tipo' => $this->tipo,
            'curva' => $this->curva,
            'semana_poda_siembra' => $this->semana_poda_siembra,
            'plantas_iniciales' => $this->plantas_iniciales,
            'desecho' => $this->desecho,
            'tallos_planta' => $this->tallos_planta,
            'tallos_ramo' => $this->tallos_ramo,
        ]);
    }
}
