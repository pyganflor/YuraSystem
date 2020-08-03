<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Storage as Almacenamiento;

class cronImportarCostos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:importar_costos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para cargar los archivos costos_I.xls y costos_M.xls en la carpeta public/storage/pdf_loads';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "cron:importar_costos" <<<<< ! >>>>>');

        $files = Almacenamiento::disk('pdf_loads')->files('');
        foreach ($files as $nombre_archivo) {
            $url = public_path('storage\pdf_loads\\' . $nombre_archivo);

            Artisan::call('costos:importar_file', [
                'url' => $url,
                'concepto' => substr(explode('.', $nombre_archivo)[0], -1),
                'criterio' => 'V',
                'sobreescribir' => true,
            ]);

            unlink($url);
        }

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "cron:importar_costos" <<<<< * >>>>>');
    }
}
