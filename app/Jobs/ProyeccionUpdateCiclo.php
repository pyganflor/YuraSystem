<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;

class ProyeccionUpdateCiclo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id_ciclo;
    protected $semana_poda_siembra;
    protected $curva;
    protected $poda_siembra;
    protected $plantas_iniciales;
    protected $desecho;
    protected $conteo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id_ciclo, $semana_poda_siembra, $curva, $poda_siembra, $plantas_iniciales, $desecho, $conteo)
    {
        $this->id_ciclo = $id_ciclo;
        $this->semana_poda_siembra = $semana_poda_siembra;
        $this->curva = $curva;
        $this->poda_siembra = $poda_siembra;
        $this->plantas_iniciales = $plantas_iniciales;
        $this->desecho = $desecho;
        $this->conteo = $conteo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Artisan::call('proyeccion:update_ciclo', [
            'id_ciclo' => $this->id_ciclo,
            'semana_poda_siembra' => $this->semana_poda_siembra,
            'curva' => $this->curva,
            'poda_siembra' => $this->poda_siembra,
            'plantas_iniciales' => $this->plantas_iniciales,
            'desecho' => $this->desecho,
            'conteo' => $this->conteo,
        ]);
    }
}
