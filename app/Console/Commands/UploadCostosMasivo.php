<?php

namespace yura\Console\Commands;

use Illuminate\Console\Command;
use PHPExcel_IOFactory;
use yura\Modelos\Actividad;
use yura\Modelos\ActividadManoObra;
use yura\Modelos\ActividadProducto;
use yura\Modelos\CostosSemana;
use yura\Modelos\CostosSemanaManoObra;
use yura\Modelos\ManoObra;
use yura\Modelos\Producto;

class UploadCostosMasivo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload_costos:masivos {url=0} {criterio=0} {modelo=0}';

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
        $criterio = ['C','V'];
        $modelo=['P','MO'];

        if($this->argument('url')==='0')
            dd('La url esta vacía');

        if($this->argument('criterio')==='0' || in_array($this->argument('criterio'),$criterio))
            dd('El criterio esta vacío o no existe, ingrese: V=> valores, C=> cantidades');

        if($this->argument('modelo')==='0' || in_array($this->argument('modelo'),$modelo))
            dd('El modelo esta vacío o no existe, ingrese: P=> Productos, MO=> Mano de obra');

        $document = PHPExcel_IOFactory::load($this->argument('url'));
        $activeSheetData = $document->getActiveSheet()->toArray(null, true, true, true);
        $titles = $activeSheetData[1];

        switch ($this->argument('modelo')){
            case 'P':
                $this->costos($activeSheetData,$titles);
                break;
            case 'MO':
                $this->manoObra($activeSheetData,$titles);
                break;
        }

    }

    public function costos($activeSheetData,$titles){
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

    public function manoObra($activeSheetData,$titles){
        foreach ($activeSheetData as $pos_row => $row) {
            if ($pos_row > 1) {
                if ($row['A'] != '' && $row['B'] != '') {
                    $actividad = Actividad::All()->where('estado', 1)
                        ->where('nombre', str_limit(mb_strtoupper(espacios($row['A'])), 50))->first();

                    $manoObra = ManoObra::All()->where('estado', 1)
                        ->where('nombre', str_limit(mb_strtoupper(espacios($row['B'])), 250))->first();

                    if (isset($actividad) && isset($manoObra)) {
                        $actManoObra = ActividadManoObra::All()
                            ->where('estado', 1)
                            ->where('id_actividad', $actividad->id_actividad)
                            ->where('id_mano_obra', $manoObra->id_mano_obra)
                            ->first();
                        if (!isset($actManoObra)) {
                            $model = new ActividadManoObra();
                            $model->id_actividad = $actividad->id_actividad;
                            $model->id_producto = $manoObra->id_mano_obra;
                            $model->fecha_registro = date('Y-m-d H:i:s');
                            $model->save();
                            $actManoObra = ActividadProducto::All()->last();
                            bitacora('actividad_mano_obra', $actManoObra->id_actividad_mano_obra, 'I', 'Inserción satisfactoria de un nuevo vínculo actividad_mano_obra');
                        }

                        foreach ($titles as $pos_title => $t) {
                            if (!in_array($pos_title, ['A', 'B'])) {
                                $codigo_semana = intval($t);
                                $value = floatval(str_replace(',', '', $row[$pos_title]));
                                $costosSemanaMO = CostosSemanaManoObra::All()
                                    ->where('codigo_semana', $codigo_semana)
                                    ->where('id_actividad_producto', $actManoObra->id_actividad_mano_obra)
                                    ->first();
                                if (!isset($costosSemanaMO)) {
                                    $model = new CostosSemanaManoObra();
                                    $model->id_actividad_mano_obra = $actManoObra->id_actividad_mano_obra;
                                    $model->codigo_semana = $codigo_semana;
                                    $model->fecha_registro = date('Y-m-d H:i:s');
                                    if ($this->argument('criterio') === 'V')  // dinero
                                        $model->valor = $value;
                                    else    //cantidad de personas
                                        $model->cantidad = $value;
                                } else {
                                    if ($this->argument('criterio') === 'V')  // dinero
                                        $costosSemanaMO->valor = $value;
                                    else    //
                                        $costosSemanaMO->cantidad = $value;
                                }
                                $costosSemanaMO->fecha_registro = date('Y-m-d H:i:s');
                                $costosSemanaMO->save();
                                bitacora('costos_semana_mano_obra', $costosSemanaMO->id_costos_semana, 'I', 'Inserción satisfactoria de un nuevo costos_semana');
                            }
                        }
                    }
                }
            }
        }
    }
}
