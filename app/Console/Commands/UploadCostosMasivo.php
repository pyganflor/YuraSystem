<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use PHPExcel_IOFactory;
use yura\Modelos\Actividad;
use yura\Modelos\ActividadProducto;
use yura\Modelos\CostosSemana;
use yura\Modelos\Producto;

class UploadCostosMasivo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload_costos:masivos {url=0} {criterio=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para subir los costos masivamente';

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
     
     * @return mixed
     */
    public function handle()
    {
        if($this->argument('url')==='0')
            dd('La url esta vacía');

        if($this->argument('criterio')==='0')
            dd('El criterio esta vacío, ingrese: V=> valores, C=> cantidades');

        $document = PHPExcel_IOFactory::load($this->argument('url'));
        $activeSheetData = $document->getActiveSheet()->toArray(null, true, true, true);

        $titles = $activeSheetData[1];
        //dd($titles,$activeSheetData);
        //dd($activeSheetData);

        foreach ($activeSheetData as $pos_row => $row) {
            if ($pos_row > 1) {
                if ($row['A'] != '' && $row['B'] != '') {
                    $actividad = Actividad::All()->where('estado', 1)
                        ->where('nombre', str_limit(mb_strtoupper(espacios($row['A'])), 50))->first();
                    $producto = Producto::All()->where('estado', 1)
                        ->where('nombre', str_limit(mb_strtoupper(espacios($row['B'])), 250))->first();

                    if (isset($actividad) && isset($producto)) {
                        $act_prod = ActividadProducto::All()
                            ->where('estado', 1)
                            ->where('id_actividad', $actividad->id_actividad)
                            ->where('id_producto', $producto->id_producto)
                            ->first();
                        if ($act_prod == '') {
                            $model = new ActividadProducto();
                            $model->id_actividad = $actividad->id_actividad;
                            $model->id_producto = $producto->id_producto;
                            $model->fecha_registro = date('Y-m-d H:i:s');
                            $model->save();
                            $act_prod = ActividadProducto::All()->last();
                            bitacora('actividad_producto', $act_prod->id_actividad_producto, 'I', 'Inserción satisfactoria de un nuevo vínculo actividad_producto');
                        }

                        foreach ($titles as $pos_title => $t) {
                            if (!in_array($pos_title, ['A', 'B'])) {
                                $codigo_semana = intval($t);
                                $value = floatval(str_replace(',', '', $row[$pos_title]));
                                $costos = CostosSemana::All()
                                    ->where('codigo_semana', $codigo_semana)
                                    ->where('id_actividad_producto', $act_prod->id_actividad_producto)
                                    ->first();
                                if ($costos == '') {
                                    $model = new CostosSemana();
                                    $model->id_actividad_producto = $act_prod->id_actividad_producto;
                                    $model->codigo_semana = $codigo_semana;
                                    $model->fecha_registro = date('Y-m-d H:i:s');
                                    if ($this->argument('criterio') === 'V')  // dinero
                                        $model->valor = $value;
                                    else    //
                                        $model->cantidad = $value;

                                    $model->save();
                                    $costos = CostosSemana::All()->last();
                                    bitacora('costos_semana', $costos->id_costos_semana, 'I', 'Inserción satisfactoria de un nuevo costos_semana');

                                } else {
                                    if ($this->argument('criterio') === 'V')  // dinero
                                        $costos->valor = $value;
                                    else    //
                                        $costos->cantidad = $value;
                                    $costos->fecha_registro = date('Y-m-d H:i:s');
                                    $costos->save();
                                    bitacora('costos_semana', $costos->id_costos_semana, 'I', 'Inserción satisfactoria de un nuevo costos_semana');
                                }

                            }
                        }

                    }
                }
            }
        }
    }
}
