<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use yura\Modelos\Semana;
use DB;

class UpdateSaldosProyVentaSemanal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
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
        $hasta=Semana::where(DB::raw('MAX(codigo) as codigo)'))->first();
        Artisan::call('resumen_saldo_proyeccion:venta_semanal', [
            'desde'=>$this->desde,
            'hasta'=> $hasta,
            'id_variedad' => $this->id_variedad
        ]);
    }
}
