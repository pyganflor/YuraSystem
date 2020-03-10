<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;
use yura\Modelos\Semana;
use DB;

class UpdateSaldosProyVentaSemanal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *s
     * @return void
     */
    public $timeout = 1200;
    public $desde;
    public $idVariedad;
    public function __construct($desde,$idVariedad)
    {
        $this->desde = $desde;
        $this->idVariedad = $idVariedad;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $hasta=Semana::select(DB::raw('max(codigo) as codigo'))->first();
        Artisan::call('resumen_saldo_proyeccion:venta_semanal', [
            'desde'=>$this->desde,
            'hasta'=> $hasta->codigo,
            'variedad' => $this->idVariedad
        ]);
    }
}
