<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PHPExcel_IOFactory;
use yura\Modelos\Actividad;
use yura\Modelos\ActividadManoObra;
use yura\Modelos\ActividadProducto;
use yura\Modelos\CostosSemana;
use yura\Modelos\CostosSemanaManoObra;
use yura\Modelos\ManoObra;
use yura\Modelos\Producto;

class UploadCostosMasivoDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'costos:importar_file_details {url=0} {concepto=0} {criterio=0} {sobreescribir=0}';

    /**
     * url = nombre completo del archivo
     * concepto => I, insumos _ M, mano de obra
     * criterio => V, dinero _ C, cantidad
     * sobreescribir => S, si _ I, sumar a lo anterior
     *
     * @var string
     */
    protected $description = 'Comando para subir los costos mediante un excel con los detalles por fecha';

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
        Log::info('<<<<< ! >>>>> Ejecutando comando "costos:importar_file_details" <<<<< ! >>>>>');

        $url = $this->argument('url');
        $concepto_importar = $this->argument('concepto');
        $criterio_importar = $this->argument('criterio');
        $sobreescribir = $this->argument('sobreescribir');

        $document = PHPExcel_IOFactory::load($url);
        $activeSheetData = $document->getActiveSheet()->toArray(null, true, true, true);

        if ($concepto_importar == 'I')
            $this->importar_insumos($activeSheetData, $concepto_importar, $criterio_importar, $sobreescribir);
        else
            $this->importar_mano_obra($activeSheetData, $concepto_importar, $criterio_importar, $sobreescribir);

        $time_duration = difFechas(date('Y-m-d H:i:s'), $ini)->h . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->m . ':' . difFechas(date('Y-m-d H:i:s'), $ini)->s;
        Log::info('<*> DURACION: ' . $time_duration . '  <*>');
        Log::info('<<<<< * >>>>> Fin satisfactorio del comando "costos:importar_file_details" <<<<< * >>>>>');
    }

    public function importar_insumos($activeSheetData, $concepto_importar, $criterio_importar, $sobreescribir = false)
    {
        $titles = $activeSheetData[1];
        $lista = [];
        foreach ($activeSheetData as $pos_row => $row) {
            $anno = explode('/', $row['A'])[2];
            $mes = explode('/', $row['A'])[0];
            $mes = strlen($mes) == 1 ? '0' . $mes : $mes;
            $dia = explode('/', $row['A'])[1];
            $dia = strlen($dia) == 1 ? '0' . $dia : $dia;
            $fecha = $anno . '-' . $mes . '-' . $dia;
            $semana = getSemanaByDate($fecha);
            $producto = DB::table('producto')->where('nombre', 'like', mb_strtoupper($row['C']) . '%')->get();  //query
            if (count($producto) == 1) {
                $producto = $producto[0];
                $actividad = DB::table('actividad')->where('nombre', 'like', mb_strtoupper($row['D']) . '%')->first();  //query
                if ($semana != '' && $actividad != '' && $producto != '') {
                    $existe = false;
                    for ($i = 0; $i < count($lista); $i++) {
                        if ($lista[$i]['semana'] == $semana->codigo && $lista[$i]['actividad']->id_actividad == $actividad->id_actividad && $lista[$i]['producto']->id_producto == $producto->id_producto) {
                            $lista[$i]['cantidad'] += $row['F'];
                            $lista[$i]['valor'] += $row['H'];
                            $existe = true;
                        }
                    }
                    if (!$existe) {
                        array_push($lista, [
                            'semana' => $semana->codigo,
                            'actividad' => $actividad,
                            'producto' => $producto,
                            'cantidad' => $row['F'],
                            'valor' => $row['H'],
                        ]);
                    }
                }
            }
        }
        foreach ($lista as $item) {
            $act_prod = ActividadProducto::All()
                ->where('id_actividad', $item['actividad']->id_actividad)
                ->where('id_producto', $item['producto']->id_producto)
                ->first();
            if ($act_prod == '') {
                $act_prod = new ActividadProducto();
                $act_prod->id_actividad = $item['actividad']->id_actividad;
                $act_prod->id_producto = $item['producto']->id_producto;
                $act_prod->save();
                $act_prod = ActividadProducto::All()->last();
            }
            $costo_semana = CostosSemana::All()
                ->where('codigo_semana', $item['semana'])
                ->where('id_actividad_producto', $act_prod->id_actividad_producto)
                ->first();
            if ($costo_semana == '') {
                $costo_semana = new CostosSemana();
                $costo_semana->codigo_semana = $item['semana'];
                $costo_semana->id_actividad_producto = $act_prod->id_actividad_producto;
                $costo_semana->valor = 0;
                $costo_semana->cantidad = 0;
            }
            if ($sobreescribir == 'S') {
                $costo_semana->valor = $item['valor'];
            } else {
                $costo_semana->valor += $item['valor'];
            }
            $costo_semana->save();
        }
    }

    public function importar_mano_obra($activeSheetData, $concepto_importar, $criterio_importar, $sobreescribir = false)
    {
        $titles = $activeSheetData[1];
        foreach ($activeSheetData as $pos_row => $row) {
            dd('mano_obra', $pos_row, $row);
        }
    }
}
