<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class EnvioComprobanteElectronico implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $comprobante_xml;
    protected $clave_acceso;

    public function __construct($comprobante_xml,$clave_acceso)
    {
        $this->comprobante_xml = $comprobante_xml;
        $this->clave_acceso    = $clave_acceso;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        enviar_comprobante_xml($this->comprobante_xml,$this->clave_acceso);
    }

    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
    }
}
