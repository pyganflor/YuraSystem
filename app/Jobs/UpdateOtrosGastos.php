<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;

class UpdateOtrosGastos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
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
        Artisan::call('otros_gastos:update', [
            'desde' => $this->desde,
            'hasta' => $this->hasta,
        ]);
    }
}
