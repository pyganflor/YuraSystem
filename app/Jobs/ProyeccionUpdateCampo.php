<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use yura\Modelos\ProyeccionModulo;

class ProyeccionUpdateCampo implements ShouldQueue
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
        $proy = ProyeccionModulo::find($this->id);
        if ($this->campo == 'Tipo') {
            $proy->tipo = $this->valor;
            $proy->save();
        }
    }
}
