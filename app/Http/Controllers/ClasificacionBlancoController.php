<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Modelos\ClasificacionBlanco;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\InventarioFrio;
use yura\Modelos\Pedido;
use yura\Modelos\StockEmpaquetado;
use yura\Modelos\Submenu;
use yura\Modelos\Variedad;
use Validator;

class ClasificacionBlancoController extends Controller
{
    public function inicio(Request $request)
    {
        $blanco = ClasificacionBlanco::All()->where('fecha_ingreso', date('Y-m-d'))->first();

        return view('adminlte.gestion.postcocecha.clasificacion_blanco.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'variedades' => Variedad::All()->where('estado', '=', 1),
            'blanco' => $blanco,
        ]);
    }

    public function listar_clasificacion_blanco(Request $request)
    {
        $fecha_min = DB::table('pedido')
            ->select(DB::raw('min(fecha_pedido) as fecha'))
            ->where('estado', '=', 1)
            ->where('empaquetado', '=', 0)
            ->get();
        if (count($fecha_min) > 0)
            $fecha_min = $fecha_min[0]->fecha;
        else
            $fecha_min = date('Y-m-d');

        $fecha_fin = opDiasFecha('+', 7, $fecha_min);

        //dd($fecha_min, $fecha_fin, $fecha_min);

        $pedidos = DB::table('pedido as p')
            ->join('detalle_pedido as dp', 'dp.id_pedido', '=', 'p.id_pedido')
            ->join('cliente_pedido_especificacion as cpe', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
            ->join('especificacion_empaque as ee', 'ee.id_especificacion', '=', 'cpe.id_especificacion')
            ->join('detalle_especificacionempaque as dee', 'dee.id_especificacion_empaque', '=', 'ee.id_especificacion_empaque')
            ->select('p.id_pedido')->distinct()
            ->where('p.estado', '=', 1)
            ->where('p.empaquetado', '=', 0)
            ->where('dee.id_variedad', '=', $request->variedad)
            ->where('p.fecha_pedido', '<=', $fecha_fin)
            ->orderBy('p.fecha_pedido')
            ->get();

        $ids_pedidos = [];
        foreach ($pedidos as $id_ped) {
            if (!getFacturaAnulada($id_ped->id_pedido)) {
                if (in_array($request->variedad, explode('|', getPedido($id_ped->id_pedido)->variedad)))
                    array_push($ids_pedidos, $id_ped->id_pedido);
            }
        }

        //dd($fecha_fin);

        $fechas = DB::table('pedido')
            ->select('fecha_pedido')->distinct()
            ->whereIn('id_pedido', $ids_pedidos)
            ->orderBy('fecha_pedido')->get();

        $stock_apertura = StockEmpaquetado::All()
            ->where('id_variedad', '=', $request->variedad)
            ->where('empaquetado', '=', 0)
            ->first();
        if ($stock_apertura == '') {
            /* ========= CREAR STOCK_EMPAQUETADO ========== */
            $stock_apertura = new StockEmpaquetado();
            $stock_apertura->fecha_registro = date('Y-m-d H:i:s');
            $stock_apertura->id_variedad = $request->variedad;
            $stock_apertura->cantidad_ingresada = 0;
            $stock_apertura->cantidad_armada = 0;

            $stock_apertura->save();
            $stock_apertura = StockEmpaquetado::All()->last();
            bitacora('stock_empaquetado', $stock_apertura->id_stock_empaquetado, 'I', 'Creacion satisfactoria de un stock empaquetado');
        }

        $combinaciones = DB::table('pedido as p')
            ->join('detalle_pedido as dp', 'dp.id_pedido', '=', 'p.id_pedido')
            ->join('cliente_pedido_especificacion as cpe', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
            ->join('especificacion_empaque as ee', 'ee.id_especificacion', '=', 'cpe.id_especificacion')
            ->join('detalle_especificacionempaque as dee', 'dee.id_especificacion_empaque', '=', 'ee.id_especificacion_empaque')
            ->join('variedad as v', 'v.id_variedad', '=', 'dee.id_variedad')
            ->join('clasificacion_ramo as cr', 'dee.id_clasificacion_ramo', '=', 'cr.id_clasificacion_ramo')
            ->select(DB::raw('sum(dee.cantidad * ee.cantidad * dp.cantidad) as cantidad'),
                'dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos', 'dee.longitud_ramo', 'dee.id_unidad_medida'
                , 'dee.id_empaque_p', 'cr.nombre')
            ->whereIn('p.id_pedido', $ids_pedidos)
            ->where('p.estado', '=', 1)
            ->where('p.empaquetado', '=', 0)
            ->where('p.fecha_pedido', '<=', $fecha_fin)
            ->where('dee.id_variedad', '=', $request->variedad)
            ->groupBy('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos', 'dee.longitud_ramo', 'dee.id_unidad_medida'
                , 'dee.id_empaque_p', 'cr.nombre')
            ->orderBy('cr.nombre', 'desc')
            ->get();

        $blanco = ClasificacionBlanco::All()->where('fecha_ingreso', $request->fecha_blanco)->first();

        if ($blanco == '') {
            $blanco = new ClasificacionBlanco();
            $blanco->fecha_ingreso = $request->fecha_blanco;

            if ($blanco->save()) {
                $blanco = ClasificacionBlanco::All()->last();
                bitacora('clasificacion_blanco', $blanco->id_clasificacion_blanco, 'I', 'Creacion de una nueva clasificacion en blanco');
            } else {
                return '<div class="alert alert-danger text-center">Ha ocurrido un problema al guardar la información</div>';
            }
        }

        //dd($pedidos, $ids_pedidos, $fechas, $combinaciones, $stock_apertura);

        return view('adminlte.gestion.postcocecha.clasificacion_blanco.partials.listado', [
            'blanco' => $blanco,
            'fecha_fin' => $fecha_fin,
            'fechas' => $fechas,
            'stock_apertura' => $stock_apertura,
            'variedad' => Variedad::find($request->variedad),
            'combinaciones' => $combinaciones
        ]);
    }

    public function confirmar_pedidos(Request $request)
    {
        $success = true;
        $msg = '';

        if ($request->ramos_armados > 0) {
            $pedidos = Pedido::All()->where('fecha_pedido', '=', $request->fecha_pedidos);

            /* ============ TABLA INVENTARIO_FRIO ===============*/
            foreach ($request->arreglo as $item) {
                if ($item['armar'] > 0) {
                    $inventario = new InventarioFrio();
                    $inventario->id_variedad = $request->id_variedad;
                    $inventario->id_clasificacion_ramo = $item['clasificacion_ramo'];
                    //$inventario->id_empaque_e = $item['id_empaque_e'];
                    $inventario->id_empaque_p = $item['id_empaque_p'];
                    $inventario->tallos_x_ramo = $item['tallos_x_ramo'];
                    $inventario->longitud_ramo = $item['longitud_ramo'];
                    $inventario->id_unidad_medida = $item['id_unidad_medida'];
                    $inventario->fecha_ingreso = date('Y-m-d');
                    $inventario->cantidad = $item['armar'];
                    $inventario->disponibles = $item['armar'];
                    $inventario->descripcion = $item['texto'];

                    if ($inventario->save()) {
                        $id = InventarioFrio::All()->last()->id_inventario_frio;
                        bitacora('inventario_frio', $id, 'I', 'Insercion de un nuevo inventario en frio');
                    } else {
                        $success = false;
                        $msg .= '<div class="alert alert-warning text-center">' .
                            'Ha ocurrido un problema con los armados de "' . $item['texto'] .
                            '"</div>';
                    }
                }

                if ($request->check_maduracion == 'true') {
                    $orderBy = 'asc';
                } else {
                    $orderBy = 'desc';
                }

                $inventarios = DB::table('inventario_frio as if')
                    ->select('if.id_inventario_frio', 'if.fecha_registro', 'if.disponibles')
                    ->where('estado', '=', 1)
                    ->where('disponibilidad', '=', 1)
                    ->where('id_variedad', '=', $request->id_variedad)
                    ->where('id_clasificacion_ramo', '=', $item['clasificacion_ramo'])
                    //->where('id_empaque_e', '=', $item['id_empaque_e'])
                    ->where('id_empaque_p', '=', $item['id_empaque_p'])
                    ->where('tallos_x_ramo', '=', $item['tallos_x_ramo'])
                    ->where('longitud_ramo', '=', $item['longitud_ramo'])
                    ->where('id_unidad_medida', '=', $item['id_unidad_medida'])
                    ->orderBy('if.fecha_registro', $orderBy)
                    ->get();

                $pedido = $item['pedido'];
                foreach ($inventarios as $l) {
                    if ($pedido > 0) {
                        $disponible = $l->disponibles;
                        $disponibilidad = 1;
                        if ($pedido >= $disponible) {
                            $pedido = $pedido - $disponible;
                            $disponible = 0;
                        } else {
                            $disponible = $disponible - $pedido;
                            $pedido = 0;
                        }
                        if ($disponible == 0)
                            $disponibilidad = 0;

                        $model = InventarioFrio::find($l->id_inventario_frio);
                        $model->disponibles = $disponible;
                        $model->disponibilidad = $disponibilidad;

                        if ($model->save()) {
                            bitacora('inventario_frio', $model->id_inventario_frio, 'U', 'Actualizacion de un inventario en frio');
                        } else {
                            $success = false;
                            $msg .= '<div class="alert alert-warning text-center">' .
                                'Ha ocurrido un problema al actualizar las cantidades disponibles de los armados de "' . $item['texto'] .
                                '"</div>';
                        }
                    }
                }
            }

            /* ============ TABLA PEDIDO ===============*/
            foreach ($pedidos as $item) {
                if (!getFacturaAnulada($item->id_pedido)) {
                    $variedades = explode('|', $item->variedad);
                    if (in_array($request->id_variedad, $variedades)) {
                        $variedades = array_diff($variedades, [$request->id_variedad]);
                        if (count($variedades) == 0) {
                            $item->variedad = '';
                            $item->empaquetado = 1;
                        } else {
                            $restantes = [];
                            foreach ($variedades as $v)
                                array_push($restantes, $v);
                            $field_variedad = $restantes[0];
                            for ($i = 1; $i < count($restantes); $i++) {
                                $field_variedad .= '|' . $restantes[$i];
                            }
                            $item->variedad = $field_variedad;
                        }
                        if ($item->save()) {
                            bitacora('pedido', $item->id_pedido, 'U', 'Modificacion de un pedido');
                        } else {
                            $success = false;
                            $msg .= '<div class="alert alert-warning text-center">' .
                                'Ha ocurrido un problema con un pedido</div>';
                        }
                    }
                }
            }

            /* ============ TABLA STOCK_EMPAQUETADO =============*/
            /*$empaquetado = StockEmpaquetado::find($request->id_stock_empaquetado);
            $empaquetado->cantidad_armada = $request->ramos_armados;
            $empaquetado->empaquetado = 1;

            if ($empaquetado->save()) {
                bitacora('stock_empaquetado', $empaquetado->id_stock_empaquetado, 'U', 'Actualizacion satisfactoria de un stock_empaquetado');
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información en el sistema</p>'
                    . '</div>';
            }*/

            if ($success) {
                $msg = '<div class="alert alert-success text-center">Se ha guardado toda la información satisfactoriamente</div>';
            }
        } else {
            $success = false;
            $msg = '<div class="alert alert-warning text-center">' .
                '<p> La cantidad de ramos armados no puede ser cero</p>'
                . '</div>';
        }

        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function store_armar(Request $request)
    {
        $success = true;
        $msg = '';
        $armados = 0;
        foreach ($request->arreglo as $item) {
            if ($item['armar'] > 0) {
                $inventario = new InventarioFrio();
                $inventario->id_variedad = $request->id_variedad;
                $inventario->id_clasificacion_ramo = $item['clasificacion_ramo'];
                //$inventario->id_empaque_e = $item['id_empaque_e'];
                $inventario->id_empaque_p = $item['id_empaque_p'];
                $inventario->tallos_x_ramo = $item['tallos_x_ramo'];
                $inventario->longitud_ramo = $item['longitud_ramo'];
                $inventario->id_unidad_medida = $item['id_unidad_medida'];
                $inventario->fecha_ingreso = date('Y-m-d');
                $inventario->cantidad = $item['armar'];
                $inventario->disponibles = $item['armar'];
                $inventario->descripcion = $item['texto'];
                $inventario->id_clasificacion_blanco = $request->blanco;
                $inventario->mesa = $item['mesa'];

                if ($inventario->save()) {
                    $id = InventarioFrio::All()->last()->id_inventario_frio;
                    bitacora('inventario_frio', $id, 'I', 'Registro de un inventario en frio');

                    $variedad = getVariedad($request->id_variedad);
                    if ($variedad->tipo == 'P') {
                        $factor = ClasificacionRamo::find($item['clasificacion_ramo'])->nombre / getCalibreRamoEstandar()->nombre;
                        $armados += $item['armar'] * $factor;
                    } else {
                        $armados += round(($item['tallos_x_ramo'] * $item['armar']) / $variedad->tallos_x_ramo_estandar, 2);
                    }
                } else {
                    $success = false;
                    $msg .= '<div class="alert alert-warning text-center">' .
                        'Ha ocurrido un problema con los armados de "' . $item['texto'] .
                        '"</div>';
                }
            }
        }

        $stock_empaquetado = StockEmpaquetado::find($request->id_stock_empaquetado);
        if ($stock_empaquetado == '') { // buscar si existe en la tabla
            $stock_empaquetado = StockEmpaquetado::All()
                ->where('empaquetado', 0)
                ->where('id_variedad', $request->id_variedad)
                ->first();
            if ($stock_empaquetado == '') {
                /* ========= CREAR STOCK_EMPAQUETADO ========== */
                $stock_empaquetado = new StockEmpaquetado();
                $stock_empaquetado->fecha_registro = date('Y-m-d H:i:s');
                $stock_empaquetado->id_variedad = $request->id_variedad;
                $stock_empaquetado->cantidad_ingresada = 0;
                $stock_empaquetado->cantidad_armada = $armados;

                $stock_empaquetado->save();
                $stock_empaquetado = StockEmpaquetado::All()->last();
                bitacora('stock_empaquetado', $stock_empaquetado->id_stock_empaquetado, 'I', 'Creacion satisfactoria de un stock empaquetado');
            } else {
                $stock_empaquetado->cantidad_armada += $armados;
                if ($stock_empaquetado->save()) {
                    bitacora('stock_empaquetado', $stock_empaquetado->id_stock_empaquetado, 'U', 'Actualización de los armados de un stock_empaquetados');
                } else {
                    $success = false;
                    $msg .= '<div class="alert alert-warning text-center">' .
                        'Ha ocurrido un problema al actualizar los ramos armados' .
                        '</div>';
                }
            }
        } else {
            $stock_empaquetado->cantidad_armada += $armados;
            if ($stock_empaquetado->save()) {
                bitacora('stock_empaquetado', $stock_empaquetado->id_stock_empaquetado, 'U', 'Actualización de los armados de un stock_empaquetados');
            } else {
                $success = false;
                $msg .= '<div class="alert alert-warning text-center">' .
                    'Ha ocurrido un problema al actualizar los ramos armados' .
                    '</div>';
            }
        }

        if ($success) {
            $msg = '<div class="alert alert-success text-center">Se ha guardado toda la información satisfactoriamente</div>';
        }

        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function maduracion(Request $request)
    {
        $inventarios = DB::table('inventario_frio')
            ->select(DB::raw('sum(disponibles) as cantidad'), 'fecha_ingreso')
            ->where('estado', '=', 1)
            ->where('disponibilidad', '=', 1)
            ->where('id_variedad', '=', $request->id_variedad)
            ->where('id_clasificacion_ramo', '=', $request['clasificacion_ramo'])
            //->where('id_empaque_e', '=', $request['id_empaque_e'])
            ->where('id_empaque_p', '=', $request['id_empaque_p'])
            ->where('tallos_x_ramo', '=', $request['tallos_x_ramo'])
            ->where('longitud_ramo', '=', $request['longitud_ramo'])
            ->where('id_unidad_medida', '=', $request['id_unidad_medida'])
            ->groupBy('fecha_ingreso')
            ->orderBy('fecha_registro')
            ->get();

        $fecha_min = DB::table('pedido')
            ->select(DB::raw('min(fecha_pedido) as fecha'))
            ->where('estado', '=', 1)
            ->where('empaquetado', '=', 0)->get();
        if (count($fecha_min) > 0)
            $fecha_min = $fecha_min[0]->fecha;
        else
            $fecha_min = date('Y-m-d');

        $fecha_fin = opDiasFecha('+', 7, $fecha_min);

        $fechas = DB::table('pedido as p')
            ->join('detalle_pedido as dp', 'dp.id_pedido', '=', 'p.id_pedido')
            ->join('cliente_pedido_especificacion as cpe', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
            ->join('especificacion_empaque as ee', 'ee.id_especificacion', '=', 'cpe.id_especificacion')
            ->join('detalle_especificacionempaque as dee', 'dee.id_especificacion_empaque', '=', 'ee.id_especificacion_empaque')
            ->select('p.fecha_pedido')->distinct()
            ->where('p.estado', '=', 1)
            ->where('p.empaquetado', '=', 0)
            ->where('dee.id_variedad', '=', $request->id_variedad)
            ->where('p.fecha_pedido', '<=', $fecha_fin)
            ->orderBy('p.fecha_pedido')
            ->get();

        return view('adminlte.gestion.postcocecha.clasificacion_blanco.partials.maduracion', [
            'listado' => $inventarios,
            'fechas' => $fechas,
            'id_variedad' => $request->id_variedad,
            'texto' => $request->texto,
            'resto' => $request->arreglo != '' ? $request->arreglo : [],
            'clasificacion_ramo' => $request->clasificacion_ramo,
            'tallos_x_ramo' => $request->tallos_x_ramo,
            'longitud_ramo' => $request->longitud_ramo,
            //'id_empaque_e' => $request->id_empaque_e,
            'id_empaque_p' => $request->id_empaque_p,
            'id_unidad_medida' => $request->id_unidad_medida,
        ]);
    }

    public function update_inventario(Request $request)
    {
        $success = true;
        $msg = '';

        foreach ($request->arreglo as $item) {
            if ($item['editar'] > 0) {
                $inventario = new InventarioFrio();
                $inventario->id_variedad = $request->id_variedad;
                $inventario->id_clasificacion_blanco = $request->id_blanco;
                $inventario->id_clasificacion_ramo = $item['clasificacion_ramo'];
                //$inventario->id_empaque_e = $item['id_empaque_e'];
                $inventario->id_empaque_p = $item['id_empaque_p'];
                $inventario->tallos_x_ramo = $item['tallos_x_ramo'];
                $inventario->longitud_ramo = $item['longitud_ramo'];
                $inventario->id_unidad_medida = $item['id_unidad_medida'];
                $inventario->fecha_ingreso = date('Y-m-d'); // consultar dias de ingreso-dias_maduracion
                $inventario->cantidad = $item['editar'];
                if ($item['basura'] == 1) {
                    $inventario->disponibles = 0;
                    $inventario->disponibilidad = 0;
                    $inventario->basura = 1;
                } else
                    $inventario->disponibles = $item['editar'];
                $inventario->descripcion = $item['texto'];

                if ($inventario->save()) {
                    $id = InventarioFrio::All()->last()->id_inventario_frio;
                    bitacora('inventario_frio', $id, 'I', 'Registro de un inventario en frio');
                } else {
                    $success = false;
                    $msg .= '<div class="alert alert-warning text-center">' .
                        'Ha ocurrido un problema con los armados de "' . $item['texto'] .
                        '"</div>';
                }
            }
        }

        $models = InventarioFrio::where('disponibilidad', 1)
            ->where('estado', 1)
            ->where('basura', 0)
            ->where('id_variedad', $request->id_variedad)
            ->where('fecha_ingreso', $request->fecha_inventario_frio)
            ->where('id_clasificacion_ramo', $request->clasificacion_ramo)
            ->where('tallos_x_ramo', $request->tallos_x_ramo)
            ->where('longitud_ramo', $request->longitud_ramo)
            ->where('id_empaque_p', $request->id_empaque_p)
            ->where('id_unidad_medida', $request->id_unidad_medida)
            ->get();

        $meta = $request->editar;

        foreach ($models as $pos => $model) {
            if ($meta > 0) {
                if ($model->disponibles >= $meta) {
                    $model->disponibles = $model->disponibles - $meta;
                    $meta = 0;
                } else {
                    $meta -= $model->disponibles;
                    $model->disponibles = 0;
                }

                if ($model->disponibles == 0)
                    $model->disponibilidad = 0;

                if ($model->save()) {
                    $id = $model->id_inventario_frio;
                    bitacora('inventario_frio', $id, 'U', 'Actualizacion de un inventario en frio');
                } else {
                    $success = false;
                    $msg .= '<div class="alert alert-warning text-center">' .
                        'Ha ocurrido un problema al actualzar el inventario seleccionado' .
                        '</div>';
                }
            }
        }

        if ($success) {
            $msg = '<div class="alert alert-success text-center">Se ha guardado toda la información satisfactoriamente</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function update_stock_empaquetado(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'cantidad' => 'required|max:11',
            'id_stock_empaquetado' => 'required|',
            'blanco' => 'required|',
            'personal' => 'required|',
            'hora_inicio' => 'required|',
        ], [
            'blanco.required' => 'La clasificación en blanco es obligatoria',
            'personal.required' => 'La cantidad de trabajadores es obligatoria',
            'hora_inicio.required' => 'La hora de inicio es obligatoria',
            'cantidad.required' => 'La cantidad es obligatoria',
            'id_stock_empaquetado.required' => 'El stock es obligatorio',
            'cantidad.max' => 'La cantidad es muy grande',
        ]);
        if (!$valida->fails()) {
            $model = StockEmpaquetado::find($request->id_stock_empaquetado);
            $model->cantidad_armada = $request->cantidad;
            $model->empaquetado = $request->terminar;

            if ($model->save()) {
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado satisfactoriamente</p>'
                    . '</div>';
                bitacora('stock_empaquetado', $model->id_stock_empaquetado, 'U', 'Actualizacion satisfactoria de un stock_empaquetado');

                /* ========== CLASIFICACION_BLANCO ========== */
                $blanco = ClasificacionBlanco::find($request->blanco);
                $blanco->personal = $request->personal;
                $blanco->hora_inicio = $request->hora_inicio;

                if ($blanco->save()) {
                    $success = true;
                    bitacora('clasificacion_blanco', $blanco->id_clasificacion_blanco, 'U', 'Actualizacion satisfactoria de una clasificacion_blanco');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información de la calsificación en blanco en el sistema</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información en el sistema</p>'
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

    public function store_blanco(Request $request)
    {
        $blanco = ClasificacionBlanco::All()->where('fecha_ingreso', date('Y-m-d'))->first();
        if ($blanco == '') {
            $blanco = new ClasificacionBlanco();
            $blanco->fecha_ingreso = date('Y-m-d');

            if ($blanco->save()) {
                $blanco = ClasificacionBlanco::All()->last();
                bitacora('clasificacion_blanco', $blanco->id_clasificacion_blanco, 'I', 'Creacion de una nueva clasificacion en blanco');

                return [
                    'mensaje' => '<div class="alert alert-success text-center">Se ha creado satisfactoriamente la clasificación en blanco</div>',
                    'success' => true,
                ];
            } else {
                return [
                    'mensaje' => '<div class="alert alert-danger text-center">Ha ocurrido un problema al guardar la información</div>',
                    'success' => false,
                ];
            }
        } else {
            return [
                'mensaje' => '<div class="alert alert-info text-center">Ya existe una clasificación en blanco para hoy</div>',
                'success' => true,
            ];
        }
    }

    public function ver_rendimiento(Request $request)
    {
        $blanco = ClasificacionBlanco::find($request->blanco);
        return view('adminlte.gestion.postcocecha.clasificacion_blanco.partials.rendimiento', [
            'blanco' => $blanco
        ]);
    }

    public function rendimiento_mesas(Request $request)
    {
        $blanco = ClasificacionBlanco::All()
            ->where('estado', 1)
            ->where('fecha_ingreso', $request->fecha_verde)
            ->first();
        $ramos = DB::table('inventario_frio')
            ->select(DB::raw('sum(cantidad) as cant'))
            ->where('estado', 1)
            ->where('fecha_ingreso', 'like', $request->fecha_verde . '%')
            ->get()[0]->cant;
        $query = DB::table('inventario_frio')
            ->where('estado', 1)
            ->where('fecha_ingreso', 'like', $request->fecha_verde . '%')
            ->get();

        return view('adminlte.gestion.postcocecha.clasificacion_blanco.partials.rendimiento_mesas', [
            'blanco' => $blanco,
            'ramos' => $ramos,
            'query' => $query,
            'getCantidadHorasTrabajoBlanco' => getCantidadHorasTrabajoBlanco($request->fecha_verde),
            'fecha_blanco' => $request->fecha_blanco,
        ]);
    }
}