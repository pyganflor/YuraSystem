<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;

class IndicadorSemanal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $desde;
    protected $hasta;

    public function __construct($desde = 0, $hasta = 0)
    {
        $this->desde = $desde;
        $this->hasta = $hasta;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Artisan::call('indicador_semana:update', [
            'desde' => $this->desde,
            'hasta' => $this->hasta,
        ]);
    }
}
