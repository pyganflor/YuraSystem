<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use yura\Modelos\Ciclo;

class CicloUpdateCampo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;
    protected $campo;
    protected $valor;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $campo, $valor)
    {
        $this->id = $id;
        $this->campo = $campo;
        $this->valor = $valor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ciclo = Ciclo::find($this->id);
        if ($this->campo == 'Tipo') {
            $ciclo->poda_siembra = $this->valor;
            $ciclo->save();
        }
    }
}
