<?php

namespace yura\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use yura\Modelos\Actividad;
use yura\Modelos\ActividadManoObra;
use yura\Modelos\ActividadProducto;
use yura\Modelos\CostosSemana;
use yura\Modelos\CostosSemanaManoObra;
use yura\Modelos\ManoObra;
use yura\Modelos\Producto;

class ImportarCostos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;
    protected $activeSheetData;
    protected $concepto_importar;
    protected $criterio_importar;
    protected $sobreescribir;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($activeSheetData, $concepto_importar, $criterio_importar, $sobreescribir = false)
    {
        $this->activeSheetData = $activeSheetData;
        $this->concepto_importar = $concepto_importar;
        $this->criterio_importar = $criterio_importar;
        $this->sobreescribir = $sobreescribir;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        set_time_limit(120);

        $activeSheetData = $this->activeSheetData;
        $titles = $activeSheetData[1];
        foreach ($activeSheetData as $pos_row => $row) {
            if ($pos_row > 1) {
                if ($row['A'] != '' && $row['B'] != '') {
                    $actividad = Actividad::All()->where('estado', 1)
                        ->where('nombre', str_limit(mb_strtoupper(espacios($row['A'])), 50))->first();
                    if ($this->concepto_importar == 'I') { // insumos
                        $producto = Producto::All()->where('estado', 1)
                            ->where('nombre', str_limit(mb_strtoupper(espacios($row['B'])), 250))->first();
                        $concepto = 'insumo';
                    } else {    // mano de obra
                        $producto = ManoObra::All()->where('estado', 1)
                            ->where('nombre', str_limit(mb_strtoupper(espacios($row['B'])), 250))->first();
                        $concepto = 'mano de obra';
                    }

                    if ($actividad != '' && $producto != '') {
                        if ($this->concepto_importar == 'I') // insumos
                            $act_prod = ActividadProducto::All()
                                ->where('estado', 1)
                                ->where('id_actividad', $actividad->id_actividad)
                                ->where('id_producto', $producto->id_producto)
                                ->first();
                        else    // mano de obra
                            $act_prod = ActividadManoObra::All()
                                ->where('estado', 1)
                                ->where('id_actividad', $actividad->id_actividad)
                                ->where('id_mano_obra', $producto->id_mano_obra)
                                ->first();

                        if ($act_prod == '') {
                            if ($this->concepto_importar == 'I') { // insumos
                                $model = new ActividadProducto();
                                $model->id_producto = $producto->id_producto;
                            } else {    // mano de obra
                                $model = new ActividadManoObra();
                                $model->id_mano_obra = $producto->id_mano_obra;
                            }
                            $model->id_actividad = $actividad->id_actividad;
                            $model->fecha_registro = date('Y-m-d H:i:s');
                            $model->save();
                            if ($this->concepto_importar == 'I') {   // insumos
                                $act_prod = ActividadProducto::All()->last();
                                bitacora('actividad_producto', $act_prod->id_actividad_producto, 'I', 'Inserción satisfactoria de un nuevo vínculo actividad_producto');
                            } else {    // mano de obra
                                $act_prod = ActividadManoObra::All()->last();
                                bitacora('actividad_mano_obra', $act_prod->id_actividad_mano_obra, 'I', 'Inserción satisfactoria de un nuevo vínculo actividad_mano_obra');
                            }
                        }

                        foreach ($titles as $pos_title => $t) {
                            if (!in_array($pos_title, ['A', 'B'])) {
                                $codigo_semana = intval($t);
                                if ($codigo_semana > 0) {
                                    $value = floatval(str_replace(',', '', $row[$pos_title]));
                                    if ($this->concepto_importar == 'I') // insumos
                                        $costos = CostosSemana::All()
                                            ->where('codigo_semana', $codigo_semana)
                                            ->where('id_actividad_producto', $act_prod->id_actividad_producto)
                                            ->first();
                                    else    // mano de obra
                                        $costos = CostosSemanaManoObra::All()
                                            ->where('codigo_semana', $codigo_semana)
                                            ->where('id_actividad_mano_obra', $act_prod->id_actividad_mano_obra)
                                            ->first();
                                    if ($costos == '') {    // es nuevo
                                        if ($this->concepto_importar == 'I') { // insumos
                                            $model = new CostosSemana();
                                            $model->id_actividad_producto = $act_prod->id_actividad_producto;
                                        } else {    // mano de obra
                                            $model = new CostosSemanaManoObra();
                                            $model->id_actividad_mano_obra = $act_prod->id_actividad_mano_obra;
                                        }
                                        $model->codigo_semana = $codigo_semana;
                                        $model->fecha_registro = date('Y-m-d H:i:s');
                                        if ($this->criterio_importar == 'V')  // dinero
                                            $model->valor = $value;
                                        else    // cantidad
                                            $model->cantidad = $value;

                                        $model->save();
                                        if ($this->concepto_importar == 'I') { // insumos
                                            $costos = CostosSemana::All()->last();
                                            bitacora('costos_semana', $costos->id_costos_semana, 'I', 'Inserción satisfactoria de un nuevo costos_semana');
                                        } else {    // mano de obra
                                            $costos = CostosSemanaManoObra::All()->last();
                                            bitacora('costos_semana_mano_obra', $costos->id_costos_semana_mano_obra, 'I', 'Inserción satisfactoria de un nuevo costos_semana_mano_obra');
                                        }
                                    } else {    // ya existe
                                        if ($this->sobreescribir == true) {
                                            if ($this->criterio_importar == 'V')  // dinero
                                                $costos->valor = $value;
                                            else    // cantidad
                                                $costos->cantidad = $value;

                                            $costos->save();
                                            if ($this->concepto_importar == 'I') // insumos
                                                bitacora('costos_semana', $costos->id_costos_semana, 'U', 'Modificación satisfactoria de un costos_semana');
                                            else    // mano de obra
                                                bitacora('costos_semana_mano_obra', $costos->id_costos_semana_mano_obra, 'U', 'Modificación satisfactoria de un costos_semana_mano_obra');
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
