<?php

namespace yura\Http\Controllers\Costos;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Jobs\ImportarCostos;
use yura\Modelos\Actividad;
use yura\Modelos\ActividadManoObra;
use yura\Modelos\ActividadProducto;
use yura\Modelos\Area;
use yura\Modelos\CostosSemana;
use yura\Modelos\CostosSemanaManoObra;
use yura\Modelos\ManoObra;
use yura\Modelos\OtrosGastos;
use yura\Modelos\ResumenCostosSemanal;
use yura\Modelos\Submenu;
use Validator;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use yura\Modelos\Producto;
use Storage as Almacenamiento;

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
            'nombre' => 'required|max:50|unique:actividad',
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
        $request->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 250);
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:250|unique:producto',
        ], [
            'nombre.unique' => 'El nombre ya existe',
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        $msg = '';
        if (!$valida->fails()) {
            if (count(Producto::All()
                    ->where('nombre', str_limit(mb_strtoupper(espacios($request->nombre)), 250))
                    ->where('estado', 1)) == 0) {
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
                $msg = '<div class="alert alert-danger text-center">El nombre ya existe</div>';
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

    public function buscar_insumosByActividad(Request $request)
    {
        $act_insumos = [];
        $actividad = Actividad::find($request->actividad);
        if ($actividad != '')
            $act_insumos = $actividad->productos;
        return view('adminlte.gestion.costos.insumo.partials.select_edit_insumo', [
            'act_insumos' => $act_insumos,
            'form' => $request->form,
        ]);
    }

    public function buscar_moByActividad(Request $request)
    {
        $act_mo = [];
        $actividad = Actividad::find($request->actividad);
        if ($actividad != '')
            $act_mo = $actividad->manos_obra;
        return view('adminlte.gestion.costos.mano_obra.partials.select_edit_mo', [
            'act_mo' => $act_mo,
            'form' => $request->form,
        ]);
    }

    public function buscar_valorByActividadInsumoSemana(Request $request)
    {
        $valor = 0;
        $act_ins = ActividadProducto::All()
            ->where('estado', 1)
            ->where('id_actividad', $request->actividad)
            ->where('id_producto', $request->insumo)
            ->first();
        if ($act_ins != '') {
            $costo_sem = CostosSemana::All()
                ->where('id_actividad_producto', $act_ins->id_actividad_producto)
                ->where('codigo_semana', $request->semana)
                ->first();
            if ($costo_sem != '')
                $valor = $costo_sem->valor;
        }
        return [
            'valor' => $valor
        ];
    }

    public function buscar_valorByActividadMOSemana(Request $request)
    {
        $valor = 0;
        $act_mo = ActividadManoObra::All()
            ->where('estado', 1)
            ->where('id_actividad', $request->actividad)
            ->where('id_mano_obra', $request->mo)
            ->first();
        if ($act_mo != '') {
            $costo_sem = CostosSemanaManoObra::All()
                ->where('id_actividad_mano_obra', $act_mo->id_actividad_mano_obra)
                ->where('codigo_semana', $request->semana)
                ->first();
            if ($costo_sem != '')
                $valor = $costo_sem->valor;
        }
        return [
            'valor' => $valor
        ];
    }

    public function save_costoInsumo(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'actividad' => 'required',
            'semana' => 'required',
            'valor' => 'required',
            'insumo' => 'required',
        ], [
            'actividad.required' => 'La actividad es obligatoria',
            'semana.required' => 'La semana es obligatoria',
            'insumo.required' => 'El insumo es obligatorio',
            'valor.required' => 'El valor es obligatorio',
        ]);
        if (!$valida->fails()) {
            $act_ins = ActividadProducto::All()
                ->where('estado', 1)
                ->where('id_actividad', $request->actividad)
                ->where('id_producto', $request->insumo)
                ->first();
            if ($act_ins != '') {
                $costo_sem = CostosSemana::All()
                    ->where('id_actividad_producto', $act_ins->id_actividad_producto)
                    ->where('codigo_semana', $request->semana)
                    ->first();
                $new = false;
                if ($costo_sem == '') {
                    $costo_sem = new CostosSemana();
                    $costo_sem->id_actividad_producto = $act_ins->id_actividad_producto;
                    $costo_sem->codigo_semana = $request->semana;
                    $costo_sem->valor = $costo_sem->cantidad = 0;
                    $new = true;
                }
                $costo_sem->valor = $request->valor;

                if ($costo_sem->save()) {
                    $success = true;
                    if ($new)
                        $id = CostosSemana::All()->last()->id_costos_semana;
                    else
                        $id = $costo_sem->id_costos_semana;
                    bitacora('costos_semana', $id, 'I', 'Inserción satisfactoria de un costo por semana');
                } else {
                    $success = false;
                }
            } else
                $success = false;
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
            'success' => $success
        ];
    }

    public function save_costoMO(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'actividad' => 'required',
            'semana' => 'required',
            'valor' => 'required',
            'mo' => 'required',
        ], [
            'actividad.required' => 'La actividad es obligatoria',
            'semana.required' => 'La semana es obligatoria',
            'mo.required' => 'La mano de obra es obligatoria',
            'valor.required' => 'El valor es obligatorio',
        ]);
        if (!$valida->fails()) {
            $act_mo = ActividadManoObra::All()
                ->where('estado', 1)
                ->where('id_actividad', $request->actividad)
                ->where('id_mano_obra', $request->mo)
                ->first();
            if ($act_mo != '') {
                $costo_sem = CostosSemanaManoObra::All()
                    ->where('id_actividad_mano_obra', $act_mo->id_actividad_mano_obra)
                    ->where('codigo_semana', $request->semana)
                    ->first();
                $new = false;
                if ($costo_sem == '') {
                    $costo_sem = new CostosSemanaManoObra();
                    $costo_sem->id_actividad_mano_obra = $act_mo->id_actividad_mano_obra;
                    $costo_sem->codigo_semana = $request->semana;
                    $costo_sem->valor = $costo_sem->cantidad = 0;
                    $new = true;
                }
                $costo_sem->valor = $request->valor;

                if ($costo_sem->save()) {
                    $success = true;
                    if ($new)
                        $id = CostosSemanaManoObra::All()->last()->id_costos_semana_mano_obra;
                    else
                        $id = $costo_sem->id_costos_semana_mano_obra;
                    bitacora('costos_semana_mano_obra', $id, 'I', 'Inserción satisfactoria de un costo por semana');
                } else {
                    $success = false;
                }
            } else
                $success = false;
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
            'success' => $success
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
        $msg = '<div class="alert alert-info text-center">Se ha importado el archivo, en menos de una hora se reflejarán los datos en el sistema</div>';
        $success = true;
        if (!$valida->fails()) {
            $archivo = $request->file_costos;
            $extension = $archivo->getClientOriginalExtension();
            $nombre_archivo = "costos_" . $request->concepto_importar . "." . $extension;
            $r1 = Almacenamiento::disk('pdf_loads')->put($nombre_archivo, \File::get($archivo));

            $url = public_path('storage\pdf_loads\\' . $nombre_archivo);
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

    /* ----------------------------------- REPORTE -------------------------------------------- */
    public function reporte_mano_obra(Request $request)
    {
        $semana_actual = getSemanaByDate(opDiasFecha('-', 7, date('Y-m-d')));
        $semana_desde = getSemanaByDate(opDiasFecha('-', 42, date('Y-m-d')));
        return view('adminlte.gestion.costos.mano_obra.reporte.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'areas' => Area::All(),
            'semana_actual' => $semana_actual,
            'semana_desde' => $semana_desde
        ]);
    }

    public function listar_reporte_mano_obra(Request $request)
    {
        $semanas = DB::table('costos_semana_mano_obra')
            ->select('codigo_semana')->distinct()
            ->where('codigo_semana', '>=', $request->desde)
            ->where('codigo_semana', '<=', $request->hasta)
            ->get();
        $area = Area::find($request->area);
        $actividad = Actividad::find($request->actividad);

        $ids = DB::table('costos_semana_mano_obra as c')
            ->select('c.id_actividad_mano_obra', 'mo.nombre')->distinct()
            ->join('actividad_mano_obra as ap', 'c.id_actividad_mano_obra', '=', 'ap.id_actividad_mano_obra')
            ->join('mano_obra as mo', 'mo.id_mano_obra', '=', 'ap.id_mano_obra');
        if ($actividad != '')   // una actividad en especifico
            $ids = $ids
                ->where('ap.id_actividad', $actividad->id_actividad);
        else if ($area != '') {
            $ids = $ids
                ->join('actividad as a', 'ap.id_actividad', '=', 'a.id_actividad')
                ->where('a.id_area', $area->id_area);
        }
        if ($request->criterio == 'V')  // dinero
            $ids = $ids->where('c.valor', '>', 0);
        else    // cantidad
            $ids = $ids->where('c.cantidad', '>', 0);
        $ids = $ids
            ->where('c.codigo_semana', '>=', $request->desde)
            ->where('c.codigo_semana', '<=', $request->hasta)
            ->orderBy('mo.nombre')
            ->get();

        $list_ids = [];
        $matriz = [];
        foreach ($ids as $item) {
            $query = CostosSemanaManoObra::where('codigo_semana', '>=', $request->desde)
                ->where('codigo_semana', '<=', $request->hasta)
                ->where('id_actividad_mano_obra', $item->id_actividad_mano_obra)
                ->get();

            array_push($matriz, $query);
            array_push($list_ids, $item->id_actividad_mano_obra);
        }

        $totales = DB::table('costos_semana_mano_obra')
            ->select(DB::raw('sum(valor) as cant'), 'codigo_semana as semana')
            ->where('codigo_semana', '>=', $request->desde)
            ->where('codigo_semana', '<=', $request->hasta)
            ->whereIn('id_actividad_mano_obra', $list_ids)
            ->groupBy('codigo_semana')
            ->get();

        return view('adminlte.gestion.costos.mano_obra.reporte.partials.listado', [
            'semanas' => $semanas,
            'area' => $area,
            'actividad' => $actividad,
            'criterio' => $request->criterio,
            'matriz' => $matriz,
            'totales' => $totales,
        ]);
    }

    public function reporte_insumos(Request $request)
    {
        $semana_actual = getSemanaByDate(opDiasFecha('-', 7, date('Y-m-d')));
        $semana_desde = getSemanaByDate(opDiasFecha('-', 42, date('Y-m-d')));
        return view('adminlte.gestion.costos.insumo.reporte.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'areas' => Area::All(),
            'semana_actual' => $semana_actual,
            'semana_desde' => $semana_desde
        ]);
    }

    public function listar_reporte_insumos(Request $request)
    {
        $semanas = DB::table('costos_semana')
            ->select('codigo_semana')->distinct()
            ->where('codigo_semana', '>=', $request->desde)
            ->where('codigo_semana', '<=', $request->hasta)
            ->get();
        $area = Area::find($request->area);
        $actividad = Actividad::find($request->actividad);

        $ids = DB::table('costos_semana as c')
            ->select('c.id_actividad_producto')->distinct();
        if ($actividad != '')   // una actividad en especifico
            $ids = $ids
                ->join('actividad_producto as ap', 'c.id_actividad_producto', '=', 'ap.id_actividad_producto')
                ->where('ap.id_actividad', $actividad->id_actividad);
        else if ($area != '') {
            $ids = $ids
                ->join('actividad_producto as ap', 'c.id_actividad_producto', '=', 'ap.id_actividad_producto')
                ->join('actividad as a', 'ap.id_actividad', '=', 'a.id_actividad')
                ->where('a.id_area', $area->id_area);
        }
        if ($request->criterio == 'V')  // dinero
            $ids = $ids->where('c.valor', '>', 0);
        else    // cantidad
            $ids = $ids->where('c.cantidad', '>', 0);
        $ids = $ids
            ->where('c.codigo_semana', '>=', $request->desde)
            ->where('c.codigo_semana', '<=', $request->hasta)
            ->get();

        $list_ids = [];
        $matriz = [];
        foreach ($ids as $item) {
            $query = CostosSemana::where('codigo_semana', '>=', $request->desde)
                ->where('codigo_semana', '<=', $request->hasta)
                ->where('id_actividad_producto', $item->id_actividad_producto)
                ->get();

            array_push($matriz, $query);
            array_push($list_ids, $item->id_actividad_producto);
        }

        $totales = DB::table('costos_semana')
            ->select(DB::raw('sum(valor) as cant'), 'codigo_semana as semana')
            ->where('codigo_semana', '>=', $request->desde)
            ->where('codigo_semana', '<=', $request->hasta)
            ->whereIn('id_actividad_producto', $list_ids)
            ->groupBy('codigo_semana')
            ->get();

        return view('adminlte.gestion.costos.insumo.reporte.partials.listado', [
            'semanas' => $semanas,
            'area' => $area,
            'actividad' => $actividad,
            'criterio' => $request->criterio,
            'matriz' => $matriz,
            'totales' => $totales,
        ]);
    }

    public function costos_generales(Request $request)
    {
        $semana_actual = getSemanaByDate(date('Y-m-d'));
        $semana_desde = getSemanaByDate(opDiasFecha('-', 35, date('Y-m-d')));
        return view('adminlte.gestion.costos.generales.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'semana_actual' => $semana_actual,
            'semana_desde' => $semana_desde
        ]);
    }

    public function listar_reporte_general(Request $request)
    {
        $semanas = DB::table('resumen_semanal_total')
            ->where('codigo_semana', '>=', $request->desde)
            ->where('codigo_semana', '<=', $request->hasta)
            ->get();

        return view('adminlte.gestion.costos.generales.partials.listado', [
            'semanas' => $semanas
        ]);
    }

    /* =================================== OTROS GASTOS ======================================= */
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
            $semana_actual = getSemanaByDate(date('Y-m-d'));
            for ($i = $request->semana; $i <= $semana_actual->codigo; $i++) {
                $model = OtrosGastos::All()
                    ->where('id_area', $request->id_area)
                    ->where('codigo_semana', $i)
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
                    return [
                        'mensaje' => $msg,
                        'success' => $success
                    ];
                }
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

    public function buscar_otros_gastos(Request $request)
    {
        $area = Area::find($request->id_area);
        $costos = $area->otrosGastosBySemana($request->semana);
        return [
            'gip' => $costos != '' ? $costos->gip : 0,
            'ga' => $costos != '' ? $costos->ga : 0,
        ];
    }
}