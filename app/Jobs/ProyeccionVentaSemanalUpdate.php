<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProyeccionVentaSemanalUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $desde;
    protected $hasta;
    protected $id_variedad;
    protected $id_liente;

    public function __construct($desde = 0, $hasta = 0, $idVariedad = 0, $idCliente = 0)
    {
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->id_variedad = $idVariedad;
        $this->id_liente = $idCliente;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Artisan::call('proyeccion:venta_semana_real', [
            'semana_desde' => $this->desde,
            'semana_hasta' => $this->hasta,
            'id_cliente' => $this->id_liente,
            'id_variedad' => $this->id_variedad,
        ]);
    }
}
