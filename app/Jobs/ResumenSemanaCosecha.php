<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;

class ResumenSemanaCosecha implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $semana_desde;
    protected $semana_hasta;
    protected $variedad;

    public function __construct($semana_desde = 0, $semana_hasta = 0, $variedad = 0)
    {
        $this->semana_desde = $semana_desde;
        $this->semana_hasta = $semana_hasta;
        $this->variedad = $variedad;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Artisan::call('proyeccion:venta_semanal_real', [
            'semana_desde' => $this->semana_desde,
            'semana_hasta' => $this->semana_hasta,
            'variedad' => $this->variedad,
        ]);
    }
}
