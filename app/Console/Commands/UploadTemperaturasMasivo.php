<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PHPExcel_IOFactory;
use yura\Modelos\Temperatura;

class UploadTemperaturasMasivo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temperaturas:importar {url=0}';

    /**
     * url = nombre completo del archivo
     *
     * @var string
     */
    protected $description = 'Comando para importar un archivo excel con las temperaturas diarias';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "temperaturas:importar" <<<<< ! >>>>>');

        $url = $this->argument('url');

        $document = PHPExcel_IOFactory::load($url);
        $activeSheetData = $document->getActiveSheet()->toArray(null, true, true, true);

        $this->importar($activeSheetData);

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "temperaturas:importar" <<<<< * >>>>>');
    }

    public function importar($activeSheetData)
    {
        $titles = $activeSheetData[1];
        foreach ($activeSheetData as $pos_row => $row) {
            if ($pos_row > 1) {
                $model = Temperatura::All()
                    ->where('estado', 1)
                    ->where('fecha', $row['A'])
                    ->first();
                if ($model == '') {
                    $model = new Temperatura();
                    $model->fecha = $row['A'];
                }
                $model->minima = $row['B'];
                $model->maxima = $row['C'];
                $model->lluvia = $row['D'];

                $model->save();
            }
        }
    }
}