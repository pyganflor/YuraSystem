<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use yura\Modelos\DesgloseRecepcion;
use yura\Modelos\Recepcion;

class DeleteRecepciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recepciones:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eliminar las recepciones y sus desgloses inactivas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $desgloses = DesgloseRecepcion::All()->where('estado', 0);
        $query = DB::table('desglose_recepcion')
            ->select('id_recepcion as id')->distinct()
            ->get();
        $ids_recep = [];
        foreach ($query as $q)
            array_push($ids_recep, $q->id);
        $recepciones = DB::table('recepcion')
            ->select('id_recepcion as id')->distinct()
            ->whereNotIn('id_recepcion', $ids_recep)
            ->get();
        if (count($desgloses) > 0 || count($recepciones) > 0) {
            Log::info('<<<<< ! >>>>> Ejecutando comando "recepciones:delete" <<<<< ! >>>>>');
            foreach ($desgloses as $dr) {
                $recepcion = $dr->recepcion;
                $id_desg = $dr->id_desglose_recepcion;
                $dr->delete();
                Log::info('Se ha eliminado el "desglose_recepcion" con id: #' . $id_desg);
                if (count($recepcion->desgloses) == 0) {
                    $id_recep = $recepcion->id_recepcion;
                    $msg = '';
                    if (count($recepcion->clasificaciones_verdes) > 0)
                        $msg = ' con sus respectivas relaciones en la tabla "recepcion_clasificacion_verde"';
                    $recepcion->delete();
                    Log::info('Se ha eliminado la "recepcion" con id: #' . $id_recep . $msg);
                }
            }
            foreach ($recepciones as $recepcion) {
                $recepcion = Recepcion::find($recepcion->id);
                $id_recep = $recepcion->id_recepcion;
                $msg = '';
                if (count($recepcion->clasificaciones_verdes) > 0)
                    $msg = ' con sus respectivas relaciones en la tabla "recepcion_clasificacion_verde"';
                $recepcion->delete();
                Log::info('Se ha eliminado la "recepcion" con id: #' . $id_recep . $msg);
            }
            Log::info('<<<<< * >>>>> Fin satisfactorio del comando "recepciones:delete" <<<<< * >>>>>');
        }
    }
}