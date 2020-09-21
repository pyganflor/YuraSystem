<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Storage as Almacenamiento;

class cronImportarCostosDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:importar_costos_details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para cargar los archivos costos_I_details.xls y costos_M_details.xls en la carpeta public/storage/pdf_loads';

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
        $ini = date('Y-m-d H:i:s');
        Log::info('<<<<< ! >>>>> Ejecutando comando "cron:importar_costos_details" <<<<< ! >>>>>');
        $files = Almacenamiento::disk('pdf_loads')->files('');
        foreach ($files as $nombre_archivo) {
            if (in_array($nombre_archivo, ['costos_I_details.xlsx', 'costos_M_details.xlsx'])) {
                $url = public_path('storage/pdf_loads/' . $nombre_archivo);
                if ($url != '') {
                    Artisan::call('costos:importar_file_details', [
                        'url' => $url,
                        'concepto' => explode('_', $nombre_archivo)[1],
                        'criterio' => 'V',
                        'sobreescribir' => true,
                    ]);

                    unlink($url);
                }
            }
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "cron:importar_costos_details" <<<<< * >>>>>');
    }
}
