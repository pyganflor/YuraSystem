<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use yura\Modelos\ProyeccionModuloSemana;

class UpdateTallosCosechadosProyeccion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $semana;
    protected $variedad;
    protected $modulo;

    public function __construct($semana, $variedad, $modulo)
    {
        $this->semana = $semana;
        $this->variedad = $variedad;
        $this->modulo = $modulo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $model = ProyeccionModuloSemana::All()
            ->where('estado', 1)
            ->where('id_modulo', $this->modulo)
            ->where('id_variedad', $this->variedad)
            ->where('semana', $this->semana)
            ->first();

        if ($model != '') {
            $model->cosechados = getTallosCosechadosByModSemVar($this->modulo, $this->semana, $this->variedad);
            $model->save();
        }
    }
}