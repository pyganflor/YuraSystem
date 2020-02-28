<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ProyeccionUpdateSemanal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    protected $semana_desde;
    protected $semana_hasta;
    protected $variedad;
    protected $modulo;
    protected $restriccion;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($semana_desde = 0, $semana_hasta = 0, $variedad = 0, $modulo = 0, $restriccion = 0)
    {
        $this->semana_desde = $semana_desde;
        $this->semana_hasta = $semana_hasta;
        $this->variedad = $variedad;
        $this->modulo = $modulo;
        $this->restriccion = $restriccion;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Artisan::call('proyeccion:update_semanal', [
            'semana_desde' => $this->semana_desde,
            'semana_hasta' => $this->semana_hasta,
            'variedad' => $this->variedad,
            'modulo' => $this->modulo,
            'restriccion' => $this->restriccion,
        ]);
    }
}