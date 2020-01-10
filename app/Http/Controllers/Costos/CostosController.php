<?php

namespace yura\Http\Controllers\Costos;

use Illuminate\Http\Request;
use yura\Http\Controllers\Controller;
use yura\Modelos\Actividad;
use yura\Modelos\ActividadManoObra;
use yura\Modelos\ActividadProducto;
use yura\Modelos\Area;
use yura\Modelos\CostosSemana;
use yura\Modelos\CostosSemanaManoObra;
use yura\Modelos\ManoObra;
use yura\Modelos\OtrosGastos;
use yura\Modelos\Submenu;
use Validator;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use yura\Modelos\Producto;

class CostosController extends Controller
{
    public function gestion_insumo(Request $request)
    {
        return view('adminlte.gestion.costos.insumo.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'areas' => Area::All()->sortBy('nombre'),
            'actividades' => Actividad::All()->sortBy('nombre'),
            'productos' => Producto::All()->sortBy('nombre'),
        ]);
    }

    public function store_area(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:50|unique:area',
        ], [
            'nombre.unique' => 'El nombre ya existe',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        $msg = '';
        if (!$valida->fails()) {
            $model = new Area();
            $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 50);
            $model->fecha_registro = date('Y-m-d H:i:s');

            if ($model->save()) {
                $model = Area::All()->last();
                $success = true;
                bitacora('area', $model->id_area, 'I', 'Inserción satisfactoria de una nueva area');
            } else {
                $success = false;
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function update_area(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:50',
            'id_area' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'id_area.required' => 'El área es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        $msg = '';
        if (!$valida->fails()) {
            if (count(Area::All()->where('nombre', '=', str_limit(mb_strtoupper(espacios($request->nombre)), 50))
                    ->where('id_area', '!=', $request->id_area)) == 0) {
                $model = Area::find($request->id_area);
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 50);

                if ($model->save()) {
                    $success = true;
                    bitacora('area', $model->id_area, 'U', 'Actualización satisfactoria de una area');
                } else {
                    $success = false;
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> El área "' . espacios($request->nombre) . '" ya se encuentra en el sistema</p>'
                    . '</div>';
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function store_actividad(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:50|unique:area',
            'area' => 'required',
        ], [
            'nombre.unique' => 'El nombre ya existe',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
            'area.required' => 'El área es obligatoria',
        ]);
        $msg = '';
        if (!$valida->fails()) {
            $model = new Actividad();
            $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 50);
            $model->id_area = $request->area;
            $model->fecha_registro = date('Y-m-d H:i:s');

            if ($model->save()) {
                $model = Actividad::All()->last();
                $success = true;
                bitacora('actividad', $model->id_actividad, 'I', 'Inserción satisfactoria de una nueva actividad');
            } else {
                $success = false;
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function update_actividad(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:50',
            'id_actividad' => 'required|',
            'area' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'id_actividad.required' => 'La actividad es obligatoria',
            'nombre.max' => 'El nombre es muy grande',
            'area.required' => 'El área es obligatoria',
        ]);
        $msg = '';
        if (!$valida->fails()) {
            if (count(Actividad::All()->where('nombre', '=', str_limit(mb_strtoupper(espacios($request->nombre)), 50))
                    ->where('id_actividad', '!=', $request->id_actividad)) == 0) {
                $model = Actividad::find($request->id_actividad);
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 50);
                $model->id_area = $request->area;

                if ($model->save()) {
                    $success = true;
                    bitacora('actividad', $model->id_actividad, 'U', 'Actualización satisfactoria de una actividad');
                } else {
                    $success = false;
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> La actividad "' . espacios($request->nombre) . '" ya se encuentra en el sistema</p>'
                    . '</div>';
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function importar_actividad(Request $request)
    {
        return view('adminlte.gestion.costos.insumo.forms.importar_actividad', [
            'areas' => Area::All(),
        ]);
    }

    public function importar_file_actividad(Request $request)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $valida = Validator::make($request->all(), [
            'file_actividad' => 'required',
            'id_area_actividad' => 'required',
        ]);
        $msg = '';
        $success = true;
        if (!$valida->fails()) {

            $document = PHPExcel_IOFactory::load($request->file_actividad);
            $activeSheetData = $document->getActiveSheet()->toArray(null, true, true, true);

            $titles = $activeSheetData[1];

            foreach ($activeSheetData as $pos_row => $row) {
                if ($pos_row > 1) {
                    if ($row['A'] != '') {
                        $nombre = str_limit(mb_strtoupper(espacios($row['A'])), 50);
                        if (count(Actividad::All()->where('nombre', $nombre)) == 0) {
                            $model = new Actividad();
                            $model->nombre = $nombre;
                            $model->id_area = $request->id_area_actividad;
                            $model->fecha_registro = date('Y-m-d');

                            $model->save();
                            $model = Actividad::All()->last();
                            bitacora('actividad', $model->id_actividad, 'I', 'Inserción satisfactoria de una nueva actividad');
                            $msg .= '<li class="bg-green">Se ha importado la actividad: "' . $nombre . '."</li>';
                        }
                    }
                }
            }
        } else {
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $success = false;
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success,
        ];
    }

    public function store_producto(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:250|unique:producto',
        ], [
            'nombre.unique' => 'El nombre ya existe',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        $msg = '';
        if (!$valida->fails()) {
            $model = new Producto();
            $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 250);
            $model->fecha_registro = date('Y-m-d H:i:s');

            if ($model->save()) {
                $model = Producto::All()->last();
                $success = true;
                bitacora('producto', $model->id_producto, 'I', 'Inserción satisfactoria de un nuevo producto');
            } else {
                $success = false;
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function update_producto(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:250',
            'id_producto' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'id_producto.required' => 'El producto es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        $msg = '';
        if (!$valida->fails()) {
            if (count(Producto::All()->where('nombre', '=', str_limit(mb_strtoupper(espacios($request->nombre)), 250))
                    ->where('id_producto', '!=', $request->id_producto)) == 0) {
                $model = Producto::find($request->id_producto);
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 250);

                if ($model->save()) {
                    $success = true;
                    bitacora('producto', $model->id_producto, 'U', 'Actualización satisfactoria de un producto');
                } else {
                    $success = false;
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> El producto "' . espacios($request->nombre) . '" ya se encuentra en el sistema</p>'
                    . '</div>';
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function importar_producto(Request $request)
    {
        return view('adminlte.gestion.costos.insumo.forms.importar_producto', [
        ]);
    }

    public function vincular_actividad_producto(Request $request)
    {
        $actividad = Actividad::find($request->id);
        $productos_vinc = [];
        foreach ($actividad->productos->where('estado', 1) as $p) {
            array_push($productos_vinc, $p->id_producto);
        }

        return view('adminlte.gestion.costos.insumo.forms.vincular_actividad_producto', [
            'actividad' => $actividad,
            'productos_vinc' => $productos_vinc,
            'productos' => Producto::All()->where('estado', 1)->sortBy('nombre'),
        ]);
    }

    public function store_actividad_producto(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'actividad' => 'required',
            'producto' => 'required',
        ], [
            'actividad.required' => 'La actividad es obligatoria',
            'producto.required' => 'El producto es obligatorio',
        ]);
        $msg = '';
        $estado = 1;
        if (!$valida->fails()) {
            $model = ActividadProducto::All()
                ->where('id_actividad', $request->actividad)
                ->where('id_producto', $request->producto)
                ->first();
            if ($model == '') {
                $model = new ActividadProducto();
                $model->id_actividad = $request->actividad;
                $model->id_producto = $request->producto;
                $model->fecha_registro = date('Y-m-d H:i:s');

                if ($model->save()) {
                    $model = ActividadProducto::All()->last();
                    $success = true;
                    bitacora('actividad_producto', $model->actividad_producto, 'I', 'Inserción satisfactoria de un nuevo vínculo actividad_producto');
                } else {
                    $success = false;
                }
            } else {
                $model->estado = $model->estado == 1 ? 0 : 1;
                $estado = $model->estado;
                $success = true;

                $model->save();
                bitacora('producto', $model->id_producto, 'U', 'Modificacion satisfactoria del estado de un producto');
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
            'estado' => $estado,
        ];
    }

    public function importar_file_producto(Request $request)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $valida = Validator::make($request->all(), [
            'file_producto' => 'required',
        ]);
        $msg = '';
        $success = true;
        if (!$valida->fails()) {

            $document = PHPExcel_IOFactory::load($request->file_producto);
            $activeSheetData = $document->getActiveSheet()->toArray(null, true, true, true);

            $titles = $activeSheetData[1];

            foreach ($activeSheetData as $pos_row => $row) {
                if ($pos_row > 1) {
                    if ($row['A'] != '') {
                        $nombre = str_limit(mb_strtoupper(espacios($row['A'])), 250);
                        if (count(Producto::All()->where('nombre', $nombre)) == 0) {
                            $model = new Producto();
                            $model->nombre = $nombre;
                            $model->fecha_registro = date('Y-m-d');

                            $model->save();
                            $model = Producto::All()->last();
                            bitacora('producto', $model->id_producto, 'I', 'Inserción satisfactoria de un nuevo producto');
                            $msg .= '<li class="bg-green">Se ha importado el producto: "' . $nombre . '."</li>';
                        }
                    }
                }
            }
        } else {
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $success = false;
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success,
        ];
    }

    public function importar_file_act_producto(Request $request)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $valida = Validator::make($request->all(), [
            'file_act_producto' => 'required',
        ]);
        $msg = '';
        $success = true;
        $array_ids_prod = [];
        if (!$valida->fails()) {

            $document = PHPExcel_IOFactory::load($request->file_act_producto);
            $activeSheetData = $document->getActiveSheet()->toArray(null, true, true, true);

            $titles = $activeSheetData[1];
            foreach ($activeSheetData as $pos_row => $row) {
                if ($pos_row > 1) {
                    if ($row['A'] != '') {
                        $nombre = str_limit(mb_strtoupper(espacios($row['B'])), 250);
                        $producto = Producto::All()->where('nombre', $nombre)->first();

                        if ($producto != '') {
                            $model = ActividadProducto::All()
                                ->where('id_actividad', $request->id_actividad)
                                ->where('id_producto', $producto->id_producto)
                                ->first();
                            if ($model == '') {
                                $model = new ActividadProducto();
                                $model->id_actividad = $request->id_actividad;
                                $model->id_producto = $producto->id_producto;
                                $model->fecha_registro = date('Y-m-d H:i:s');

                                if ($model->save()) {
                                    $model = ActividadProducto::All()->last();
                                    $success = true;
                                    bitacora('actividad_producto', $model->actividad_producto, 'I', 'Inserción satisfactoria de un nuevo vínculo actividad_producto');
                                } else {
                                    $success = false;
                                }
                            } else {
                                $model->estado = 1;
                                $success = true;

                                $model->save();
                                bitacora('producto', $model->id_producto, 'U', 'Modificación satisfactoria del estado de un producto');
                            }
                            array_push($array_ids_prod, $producto->id_producto);
                            $msg .= '<li class="bg-green">Se ha vinculado el producto: "' . $nombre . '."</li>';
                        }
                    }
                }
            }
        } else {
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $success = false;
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success,
            'ids' => $array_ids_prod,
        ];
    }

    public function delete_actividad(Request $request)
    {
        $model = Actividad::find($request->id_actividad);
        $model->estado = $model->estado == 1 ? 0 : 1;
        $model->save();
        bitacora('actividad', $model->id_actividad, 'U', 'Modificacion satisfactoria del estado de una actividad');

        return [
            'success' => true,
            'mensaje' => '',
        ];
    }

    public function delete_producto(Request $request)
    {
        $model = Producto::find($request->id_producto);
        $model->estado = $model->estado == 1 ? 0 : 1;
        $model->save();
        bitacora('producto', $model->id_producto, 'U', 'Modificacion satisfactoria del estado de un producto');

        return [
            'success' => true,
            'mensaje' => '',
        ];
    }

    /* ==================================== IMPORTAR ===================================== */
    public function costos_importar(Request $request)
    {
        return view('adminlte.gestion.costos.costos_importar', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function importar_file_costos(Request $request)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $valida = Validator::make($request->all(), [
            'file_costos' => 'required',
        ]);
        $msg = '';
        $success = true;
        if (!$valida->fails()) {

            $document = PHPExcel_IOFactory::load($request->file_costos);
            $activeSheetData = $document->getActiveSheet()->toArray(null, true, true, true);

            $titles = $activeSheetData[1];
            foreach ($activeSheetData as $pos_row => $row) {
                if ($pos_row > 1) {
                    if ($row['A'] != '' && $row['B'] != '') {
                        $actividad = Actividad::All()->where('estado', 1)
                            ->where('nombre', str_limit(mb_strtoupper(espacios($row['A'])), 50))->first();
                        if ($request->concepto_importar == 'I') { // insumos
                            $producto = Producto::All()->where('estado', 1)
                                ->where('nombre', str_limit(mb_strtoupper(espacios($row['B'])), 250))->first();
                            $concepto = 'insumo';
                        } else {    // mano de obra
                            $producto = ManoObra::All()->where('estado', 1)
                                ->where('nombre', str_limit(mb_strtoupper(espacios($row['B'])), 250))->first();
                            $concepto = 'mano de obra';
                        }

                        if ($actividad != '' && $producto != '') {
                            if ($request->concepto_importar == 'I') // insumos
                                $act_prod = ActividadProducto::All()
                                    ->where('estado', 1)
                                    ->where('id_actividad', $actividad->id_actividad)
                                    ->where('id_producto', $producto->id_producto)
                                    ->first();
                            else    // mano de obra
                                $act_prod = ActividadManoObra::All()
                                    ->where('estado', 1)
                                    ->where('id_actividad', $actividad->id_actividad)
                                    ->where('id_producto', $producto->id_mano_obra)
                                    ->first();

                            if ($act_prod == '') {
                                if ($request->concepto_importar == 'I') { // insumos
                                    $model = new ActividadProducto();
                                    $model->id_producto = $producto->id_producto;
                                } else {    // mano de obra
                                    $model = new ActividadManoObra();
                                    $model->id_mano_obra = $producto->id_mano_obra;
                                }
                                $model->id_actividad = $actividad->id_actividad;
                                $model->fecha_registro = date('Y-m-d H:i:s');
                                $model->save();
                                if ($request->concepto_importar == 'I') {   // insumos
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
                                    $value = floatval(str_replace(',', '', $row[$pos_title]));
                                    if ($request->concepto_importar == 'I') // insumos
                                        $costos = CostosSemana::All()
                                            ->where('codigo_semana', $codigo_semana)
                                            ->where('id_actividad_producto', $act_prod->id_actividad_producto)
                                            ->first();
                                    else    // mano de obra
                                        $costos = CostosSemanaManoObra::All()
                                            ->where('codigo_semana', $codigo_semana)
                                            ->where('id_actividad_mano_obra', $act_prod->id_actividad_mano_obra)
                                            ->first();
                                    if ($costos == '') {
                                        if ($request->concepto_importar == 'I') { // insumos
                                            $model = new CostosSemana();
                                            $model->id_actividad_producto = $act_prod->id_actividad_producto;
                                        } else {    // mano de obra
                                            $model = new CostosSemanaManoObra();
                                            $model->id_actividad_mano_obra = $act_prod->id_actividad_mano_obra;
                                        }
                                        $model->codigo_semana = $codigo_semana;
                                        $model->fecha_registro = date('Y-m-d H:i:s');
                                        if ($request->criterio_importar == 'V')  // dinero
                                            $model->valor = $value;
                                        else    //
                                            $model->cantidad = $value;

                                        $model->save();
                                        if ($request->concepto_importar == 'I') { // insumos
                                            $costos = CostosSemana::All()->last();
                                            bitacora('costos_semana', $costos->id_costos_semana, 'I', 'Inserción satisfactoria de un nuevo costos_semana');
                                        } else {    // mano de obra
                                            $costos = CostosSemanaManoObra::All()->last();
                                            bitacora('costos_semana_mano_obra', $costos->id_costos_semana_mano_obra, 'I', 'Inserción satisfactoria de un nuevo costos_semana_mano_obra');
                                        }
                                    } else {
                                        if ($request->criterio_importar == 'V')  // dinero
                                            $costos->valor = $value;
                                        else    //
                                            $costos->cantidad = $value;

                                        $costos->save();
                                        if ($request->concepto_importar == 'I') // insumos
                                            bitacora('costos_semana', $costos->id_costos_semana, 'U', 'Modificación satisfactoria de un costos_semana');
                                        else    // mano de obra
                                            bitacora('costos_semana_mano_obra', $costos->id_costos_semana_mano_obra, 'U', 'Modificación satisfactoria de un costos_semana_mano_obra');
                                    }
                                }
                            }

                            $msg .= '<li class="bg-green">Se ha importado el ' . $concepto . ': "' . $producto->nombre . '" en la actividad: "' . $actividad->nombre . '</li>';
                        }
                    }
                }
            }
        } else {
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $success = false;
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success,
        ];
    }

    /* =================================== MANO OBRA ======================================= */
    public function gestion_mano_obra(Request $request)
    {
        return view('adminlte.gestion.costos.mano_obra.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'areas' => Area::All()->sortBy('nombre'),
            'actividades' => Actividad::All()->sortBy('nombre'),
            'manos_obra' => ManoObra::All()->sortBy('nombre'),
        ]);
    }

    public function store_mano_obra(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:250|unique:mano_obra',
        ], [
            'nombre.unique' => 'El nombre ya existe',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        $msg = '';
        if (!$valida->fails()) {
            $model = new ManoObra();
            $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 250);
            $model->fecha_registro = date('Y-m-d H:i:s');

            if ($model->save()) {
                $model = ManoObra::All()->last();
                $success = true;
                bitacora('mano_obra', $model->id_mano_obra, 'I', 'Inserción satisfactoria de una nueva mano de obra');
            } else {
                $success = false;
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function importar_mano_obra(Request $request)
    {
        return view('adminlte.gestion.costos.mano_obra.forms.importar_producto', [
        ]);
    }

    public function importar_file_mano_obra(Request $request)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $valida = Validator::make($request->all(), [
            'file_mano_obra' => 'required',
        ]);
        $msg = '';
        $success = true;
        if (!$valida->fails()) {

            $document = PHPExcel_IOFactory::load($request->file_mano_obra);
            $activeSheetData = $document->getActiveSheet()->toArray(null, true, true, true);

            $titles = $activeSheetData[1];

            foreach ($activeSheetData as $pos_row => $row) {
                if ($pos_row > 1) {
                    if ($row['A'] != '') {
                        $nombre = str_limit(mb_strtoupper(espacios($row['A'])), 250);
                        if (count(ManoObra::All()->where('nombre', $nombre)) == 0) {
                            $model = new ManoObra();
                            $model->nombre = $nombre;
                            $model->fecha_registro = date('Y-m-d');

                            $model->save();
                            $model = ManoObra::All()->last();
                            bitacora('mano_obra', $model->id_mano_obra, 'I', 'Inserción satisfactoria de una nueva mano de obra');
                            $msg .= '<li class="bg-green">Se ha importado la mano de obra: "' . $nombre . '."</li>';
                        }
                    }
                }
            }
        } else {
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $success = false;
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success,
        ];
    }

    public function update_mano_obra(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:250',
            'id_mano_obra' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'id_mano_obra.required' => 'La mano de obra es obligatoria',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        $msg = '';
        if (!$valida->fails()) {
            if (count(ManoObra::All()->where('nombre', '=', str_limit(mb_strtoupper(espacios($request->nombre)), 250))
                    ->where('id_mano_obra', '!=', $request->id_mano_obra)) == 0) {
                $model = ManoObra::find($request->id_mano_obra);
                $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 250);

                if ($model->save()) {
                    $success = true;
                    bitacora('mano_obra', $model->id_mano_obra, 'U', 'Actualización satisfactoria de una mano de obra');
                } else {
                    $success = false;
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> La mano de obra "' . espacios($request->nombre) . '" ya se encuentra en el sistema</p>'
                    . '</div>';
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function vincular_actividad_mano_obra(Request $request)
    {
        $actividad = Actividad::find($request->id);
        $manos_obra_vinc = [];
        foreach ($actividad->manos_obra->where('estado', 1) as $p) {
            array_push($manos_obra_vinc, $p->id_mano_obra);
        }

        return view('adminlte.gestion.costos.mano_obra.forms.vincular_actividad_mano_obra', [
            'actividad' => $actividad,
            'manos_obra_vinc' => $manos_obra_vinc,
            'manos_obra' => ManoObra::All()->where('estado', 1)->sortBy('nombre'),
        ]);
    }

    public function store_actividad_mano_obra(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'actividad' => 'required',
            'mano_obra' => 'required',
        ], [
            'actividad.required' => 'La actividad es obligatoria',
            'mano_obra.required' => 'La mano de obra es obligatorio',
        ]);
        $msg = '';
        $estado = 1;
        if (!$valida->fails()) {
            $model = ActividadManoObra::All()
                ->where('id_actividad', $request->actividad)
                ->where('id_mano_obra', $request->mano_obra)
                ->first();
            if ($model == '') {
                $model = new ActividadManoObra();
                $model->id_actividad = $request->actividad;
                $model->id_mano_obra = $request->mano_obra;
                $model->fecha_registro = date('Y-m-d H:i:s');

                if ($model->save()) {
                    $model = ActividadManoObra::All()->last();
                    $success = true;
                    bitacora('actividad_mano_obra', $model->actividad_mano_obra, 'I', 'Inserción satisfactoria de un nuevo vínculo actividad_mano_obra');
                } else {
                    $success = false;
                }
            } else {
                $model->estado = $model->estado == 1 ? 0 : 1;
                $estado = $model->estado;
                $success = true;

                $model->save();
                bitacora('mano_obra', $model->id_mano_obra, 'U', 'Modificacion satisfactoria del estado de una mano de obra');
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
            'estado' => $estado,
        ];
    }

    public function importar_file_act_mano_obra(Request $request)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $valida = Validator::make($request->all(), [
            'file_act_mano_obra' => 'required',
        ]);
        $msg = '';
        $success = true;
        $array_ids_mo = [];
        if (!$valida->fails()) {

            $document = PHPExcel_IOFactory::load($request->file_act_mano_obra);
            $activeSheetData = $document->getActiveSheet()->toArray(null, true, true, true);

            $titles = $activeSheetData[1];
            foreach ($activeSheetData as $pos_row => $row) {
                if ($pos_row > 1) {
                    if ($row['A'] != '') {
                        $nombre = str_limit(mb_strtoupper(espacios($row['B'])), 250);
                        $mano_obra = ManoObra::All()->where('nombre', $nombre)->first();

                        if ($mano_obra != '') {
                            $model = ActividadManoObra::All()
                                ->where('id_actividad', $request->id_actividad)
                                ->where('id_mano_obra', $mano_obra->id_mano_obra)
                                ->first();
                            if ($model == '') {
                                $model = new ActividadManoObra();
                                $model->id_actividad = $request->id_actividad;
                                $model->id_mano_obra = $mano_obra->id_mano_obra;
                                $model->fecha_registro = date('Y-m-d H:i:s');

                                if ($model->save()) {
                                    $model = ActividadManoObra::All()->last();
                                    $success = true;
                                    bitacora('actividad_mano_obra', $model->actividad_mano_obra, 'I', 'Inserción satisfactoria de un nuevo vínculo actividad_mano_obra');
                                } else {
                                    $success = false;
                                }
                            } else {
                                $model->estado = 1;
                                $success = true;

                                $model->save();
                                bitacora('mano_obra', $model->id_mano_obra, 'U', 'Modificación satisfactoria del estado de una mano de obra');
                            }
                            array_push($array_ids_mo, $mano_obra->id_mano_obra);
                            $msg .= '<li class="bg-green">Se ha vinculado la mano de obra: "' . $nombre . '."</li>';
                        }
                    }
                }
            }
        } else {
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $success = false;
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success,
            'ids' => $array_ids_mo,
        ];
    }

    public function delete_mano_obra(Request $request)
    {
        $model = ManoObra::find($request->id_mano_obra);
        $model->estado = $model->estado == 1 ? 0 : 1;
        $model->save();
        bitacora('mano_obra', $model->id_mano_obra, 'U', 'Modificacion satisfactoria del estado de una mano de obra');

        return [
            'success' => true,
            'mensaje' => '',
        ];
    }

    public function otros_gastos(Request $request)
    {
        $area = Area::find($request->area);
        $semana_actual = getSemanaByDate(date('Y-m-d'));
        return view('adminlte.gestion.costos.mano_obra.forms.otros_gastos', [
            'area' => $area,
            'otros_gastos' => $area->otrosGastosBySemana($semana_actual->codigo),
            'semana_actual' => $semana_actual,
        ]);
    }

    public function store_otros_gastos(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'id_area' => 'required',
            'semana' => 'required',
            'gip' => 'required',
            'ga' => 'required',
        ], [
            'semana.required' => 'La semana es obligatoria',
            'id_area.required' => 'El área es obligatoria',
            'gip.required' => 'El gip es obligatoria',
            'ga.required' => 'El ga es obligatoria',
        ]);
        if (!$valida->fails()) {
            $model = OtrosGastos::All()
                ->where('id_area', $request->id_area)
                ->where('codigo_semana', $request->semana)
                ->first();
            if ($model == '') {
                $model = new OtrosGastos();
                $model->id_area = $request->id_area;
                $model->codigo_semana = $request->semana;
            }
            $model->gip = $request->gip;
            $model->ga = $request->ga;

            if ($model->save()) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se han guardado los otros gastos satisfactoriamente</p>'
                    . '</div>';
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                    . '</div>';
            }
        } else {
            $success = false;
            $errores = '';
            foreach ($valida->errors()->all() as $mi_error) {
                if ($errores == '') {
                    $errores = '<li>' . $mi_error . '</li>';
                } else {
                    $errores .= '<li>' . $mi_error . '</li>';
                }
            }
            $msg = '<div class="alert alert-danger">' .
                '<p class="text-center">¡Por favor corrija los siguientes errores!</p>' .
                '<ul>' .
                $errores .
                '</ul>' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

}