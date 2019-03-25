<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\Cliente;
use yura\Modelos\ClientePedidoEspecificacion;
use yura\Modelos\Color;
use yura\Modelos\Coloracion;
use yura\Modelos\DetalleEspecificacionEmpaque;
use yura\Modelos\DetallePedido;
use yura\Modelos\Empaque;
use yura\Modelos\Especificacion;
use yura\Modelos\EspecificacionEmpaque;
use yura\Modelos\Marcacion;
use yura\Modelos\Pedido;
use yura\Modelos\UnidadMedida;
use yura\Modelos\Variedad;

class OrdenSemanalController extends Controller
{
    public function store_orden_semanal(Request $request)
    {
        if ($request->nueva_esp != '') {    // NUEVA ESPECIFICACION
            $esp = new Especificacion();
            $esp->tipo = 'O';

            if ($esp->save()) {
                $esp = Especificacion::All()->last();
                bitacora('especificacion', $esp->id_especificacion, 'I', 'Insercion de una nueva especificacion');

                /* =========== TABLA ESPECIFICACION_EMPAQUE ==========*/
                $esp_emp = new EspecificacionEmpaque();
                $esp_emp->id_especificacion = $esp->id_especificacion;
                $esp_emp->id_empaque = $request->nueva_esp['id_empaque'];
                $esp_emp->cantidad = 1;

                if ($esp_emp->save()) {
                    $esp_emp = EspecificacionEmpaque::All()->last();
                    bitacora('especificacion_empaque', $esp_emp->id_especificacion_empaque, 'I', 'Insercion de una nueva especificacion-empaque');

                    /* ========== TABLA DETALLE_ESPECIFICACION_EMPAQUE ============*/
                    $det_espemp = new DetalleEspecificacionEmpaque();
                    $det_espemp->id_especificacion_empaque = $esp_emp->id_especificacion_empaque;
                    $det_espemp->id_variedad = $request->nueva_esp['id_variedad'];
                    $det_espemp->id_clasificacion_ramo = $request->nueva_esp['id_clasificacion_ramo'];
                    $det_espemp->cantidad = $request->nueva_esp['cantidad_ramos'];
                    //$det_espemp->id_empaque_e = $request->id_empaque_e;
                    $det_espemp->id_empaque_p = $request->nueva_esp['id_empaque_p'];
                    $det_espemp->tallos_x_ramos = $request->nueva_esp['tallos_x_ramos'];
                    $det_espemp->longitud_ramo = $request->nueva_esp['longitud_ramo'];
                    $det_espemp->id_unidad_medida = $request->nueva_esp['id_unidad_medida'];

                    if ($det_espemp->save()) {
                        $det_espemp = DetalleEspecificacionEmpaque::All()->last();
                        bitacora('detalle_especificacionempaque', $det_espemp->id_detalle_especificacionempaque, 'I', 'Insercion de una nueva detalle-especificación-empaque');

                        /* =========== TABLA CLIENTE_PEDIDO_ESPECIFICACION ==========*/
                        $cli_ped_esp = new ClientePedidoEspecificacion();
                        $cli_ped_esp->id_especificacion = $esp->id_especificacion;
                        $cli_ped_esp->id_cliente = $request->id_cliente;

                        if ($cli_ped_esp->save()) {
                            $cli_ped_esp = ClientePedidoEspecificacion::All()->last();
                            bitacora('cliente_pedido_especificacion', $cli_ped_esp->id_cliente_pedido_especificacion, 'I', 'Insercion de una nueva cliente-pedido-especificación');

                            /* ========== TABLA PEDIDO ============*/
                            $pedido = new Pedido();
                            $pedido->id_cliente = $request->id_cliente;
                            $pedido->descripcion = 'Flor tinturada';
                            $pedido->variedad = $request->nueva_esp['id_variedad'];  // optimizar
                            $pedido->fecha_pedido = $request->fecha_pedido;
                            $pedido->tipo_especificacion = 'T'; // Flor tinturada

                            if ($pedido->save()) {
                                $pedido = Pedido::All()->last();
                                bitacora('pedido', $pedido->id_pedido, 'I', 'Insercion de un nuevo pedido');

                                /* ========= TABLA DETALLE_PEDIDO ===========*/
                                $det_pedido = new DetallePedido();
                                $det_pedido->id_pedido = $pedido->id_pedido;
                                $det_pedido->id_cliente_especificacion = $cli_ped_esp->id_cliente_pedido_especificacion;
                                $det_pedido->id_agencia_carga = $request->id_agencia_carga;
                                $det_pedido->cantidad = $request->nueva_esp['cantidad_cajas'];
                                $det_pedido->precio = $request->nueva_esp['precio'] . ';' . $det_espemp->id_detalle_especificacionempaque;

                                if ($det_pedido->save()) {
                                    $det_pedido = DetallePedido::All()->last();
                                    bitacora('detalle_pedido', $det_pedido->id_detalle_pedido, 'I', 'Insercion de un nuevo detalle-pedido');

                                    /* =========== TABLA MARCACION ===========*/
                                    $arreglo_marcaciones = [];
                                    $arreglo_coloraciones = [];
                                    foreach ($request->nueva_esp['marcaciones'] as $m) {
                                        $marcacion = new Marcacion();
                                        $marcacion->nombre = $m['nombre'];
                                        $marcacion->ramos = $m['ramos'];
                                        $marcacion->piezas = $m['piezas'];
                                        $marcacion->id_detalle_pedido = $det_pedido->id_detalle_pedido;
                                        $marcacion->id_especificacion_empaque = $esp_emp->id_especificacion_empaque;

                                        if ($marcacion->save()) {
                                            $marcacion = Marcacion::All()->last();
                                            bitacora('marcacion', $marcacion->id_marcacion, 'I', 'Insercion de una nueva marcacion');
                                            array_push($arreglo_marcaciones, $marcacion);

                                            /* =========== TABLA COLORACION ===========*/
                                            foreach ($m['coloraciones'] as $c) {
                                                $coloracion = new Coloracion();
                                                $coloracion->cantidad = $c['cantidad'] != '' ? $c['cantidad'] : 0;
                                                $coloracion->id_color = $c['id_color'];
                                                $coloracion->id_marcacion = $marcacion->id_marcacion;
                                                $coloracion->id_detalle_especificacionempaque = $det_espemp->id_detalle_especificacionempaque;

                                                if ($coloracion->save()) {
                                                    $coloracion = Coloracion::All()->last();
                                                    bitacora('coloracion', $coloracion->id_coloracion, 'I', 'Insercion de una nueva coloracion');
                                                    array_push($arreglo_coloraciones, $coloracion);

                                                } else {
                                                    foreach ($arreglo_coloraciones as $item)
                                                        $item->delete();
                                                    foreach ($arreglo_marcaciones as $item)
                                                        $item->delete();
                                                    $det_pedido->delete();
                                                    $pedido->delete();
                                                    $cli_ped_esp->delete();
                                                    $det_espemp->delete();
                                                    $esp_emp->delete();
                                                    $esp->delete();

                                                    return [
                                                        'id_pedido' => '',
                                                        'success' => false,
                                                        'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear la relación marcación-coloración "' .
                                                            $m['nombre'] . ' - ' . Color::find($c['id_color'])->nombre
                                                            . '"</div>',
                                                    ];
                                                }
                                            }
                                        } else {
                                            foreach ($arreglo_marcaciones as $item)
                                                $item->delete();
                                            $det_pedido->delete();
                                            $pedido->delete();
                                            $cli_ped_esp->delete();
                                            $det_espemp->delete();
                                            $esp_emp->delete();
                                            $esp->delete();

                                            return [
                                                'id_pedido' => '',
                                                'success' => false,
                                                'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear la marcación "' .
                                                    $m['nombre']
                                                    . '"</div>',
                                            ];
                                        }
                                    }
                                } else {
                                    $pedido->delete();
                                    $cli_ped_esp->delete();
                                    $det_espemp->delete();
                                    $esp_emp->delete();
                                    $esp->delete();

                                    return [
                                        'id_pedido' => '',
                                        'success' => false,
                                        'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear el detalle-pedido</div>',
                                    ];
                                }
                            } else {
                                $cli_ped_esp->delete();
                                $det_espemp->delete();
                                $esp_emp->delete();
                                $esp->delete();

                                return [
                                    'id_pedido' => '',
                                    'success' => false,
                                    'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear el pedido</div>',
                                ];
                            }
                        } else {
                            $det_espemp->delete();
                            $esp_emp->delete();
                            $esp->delete();

                            return [
                                'id_pedido' => '',
                                'success' => false,
                                'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear el cliente-pedido-especificación</div>',
                            ];
                        }
                    } else {
                        $esp_emp->delete();
                        $esp->delete();

                        return [
                            'id_pedido' => '',
                            'success' => false,
                            'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear el detalle-especificación-empaque</div>',
                        ];
                    }
                } else {
                    $esp->delete();
                    return [
                        'id_pedido' => '',
                        'success' => false,
                        'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear la especificación-empaque</div>',
                    ];
                }
            } else {
                return [
                    'id_pedido' => '',
                    'success' => false,
                    'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear la especificación</div>',
                ];
            }
        } else {
            /* ============ CREAR PEDIDO SI NO HAY NUEVA ESPECIFICACION ============ */
            $pedido = new Pedido();
            $pedido->id_cliente = $request->id_cliente;
            $pedido->descripcion = 'Flor tinturada';
            $pedido->variedad = ''; // se calcula más abajo
            $pedido->fecha_pedido = $request->fecha_pedido;
            $pedido->tipo_especificacion = 'T'; // Flor tinturada
            if ($pedido->save()) {
                $pedido = Pedido::All()->last();
                bitacora('pedido', $pedido->id_pedido, 'I', 'Insercion de un nuevo pedido');
            } else {
                return [
                    'id_pedido' => '',
                    'success' => false,
                    'mensaje' => '<div class="alert alert-warning text-center error">No se ha podido crear el pedido</div>',
                ];
            }
        }
        if (count($request->arreglo_esp) > 0) {
            $arreglo_variedades = [];
            /* ============ TABLA DETALLE_PEDIDO ============ */
            $arreglo_det_pedidos = [];
            $arreglo_marcaciones = [];
            $arreglo_coloraciones = [];
            foreach ($request->arreglo_esp as $pos_esp => $esp) {
                $arreglo_det_esp = [];
                $cli_ped_esp = ClientePedidoEspecificacion::where('id_cliente', $request->id_cliente)
                    ->where('id_especificacion', $esp['id_esp'])->first();
                $det_pedido = new DetallePedido();
                $det_pedido->id_pedido = $pedido->id_pedido;
                $det_pedido->id_cliente_especificacion = $cli_ped_esp->id_cliente_pedido_especificacion;
                $det_pedido->id_agencia_carga = $request->id_agencia_carga;
                $det_pedido->cantidad = $esp['cant_piezas'];
                $det_pedido->precio = $esp['arreglo_esp_emp'][0]['arreglo_det_esp'][0]['precio'] . ';' .
                    $esp['arreglo_esp_emp'][0]['arreglo_det_esp'][0]['id_det_esp'];   // se termina de calcular más abajo

                /* ========== OBTENER LOS PRECIOS POR DETALLES_ESPECIFICACION_EMPAQUE ========= */
                foreach ($esp['arreglo_esp_emp'] as $pos_esp_emp => $esp_emp) {
                    foreach ($esp_emp['arreglo_det_esp'] as $pos_precio => $precio) {
                        if (($pos_esp_emp == 0 && $pos_precio > 0) || $pos_esp_emp > 0)
                            $det_pedido->precio .= '|' . $precio['precio'] . ';' . $precio['id_det_esp'];
                    }
                }
                if ($det_pedido->save()) {
                    $det_pedido = DetallePedido::All()->last();
                    bitacora('detalle_pedido', $det_pedido->id_detalle_pedido, 'I', 'Insercion de un nuevo detalle-pedido');
                    array_push($arreglo_det_pedidos, $det_pedido);

                    /* ============== TABLA MARCACION ===========*/
                    foreach ($esp['arreglo_esp_emp'] as $esp_emp) {
                        foreach ($esp_emp['marcaciones'] as $m) {
                            $marcacion = new Marcacion();
                            $marcacion->nombre = $m['nombre'];
                            $marcacion->ramos = $m['ramos'];
                            $marcacion->piezas = $m['piezas'];
                            $marcacion->id_detalle_pedido = $det_pedido->id_detalle_pedido;
                            $marcacion->id_especificacion_empaque = $esp_emp['id_esp_emp'];

                            if ($marcacion->save()) {
                                $marcacion = Marcacion::All()->last();
                                bitacora('marcacion', $marcacion->id_marcacion, 'I', 'Insercion de una nueva marcacion');
                                array_push($arreglo_marcaciones, $marcacion);

                                /* =========== TABLA COLORACION =========== */
                                foreach ($m['arreglo_colores'] as $c) {
                                    if (isset($c['cant_x_det_esp']))
                                        foreach ($c['cant_x_det_esp'] as $det_esp) {
                                            $coloracion = new Coloracion();
                                            $coloracion->id_marcacion = $marcacion->id_marcacion;
                                            $coloracion->id_color = $c['id_color'];
                                            $coloracion->id_detalle_especificacionempaque = $det_esp['id_det_esp'];
                                            $coloracion->cantidad = $det_esp['cantidad'] != '' ? $det_esp['cantidad'] : 0;

                                            if ($coloracion->save()) {
                                                $coloracion = Coloracion::All()->last();
                                                bitacora('coloracion', $coloracion->id_coloracion, 'I', 'Insercion de una nueva coloracion');
                                                array_push($arreglo_coloraciones, $coloracion);

                                                /* ======== OBTENER LAS VARIEDADES INCLUIDAS EN EL PEDIDO ======= */
                                                $det_esp = DetalleEspecificacionEmpaque::find($coloracion->id_detalle_especificacionempaque);
                                                if (!in_array($det_esp->id_variedad, $arreglo_variedades)) {
                                                    array_push($arreglo_variedades, $det_esp->id_variedad);
                                                }
                                            } else {
                                                foreach ($arreglo_coloraciones as $item)
                                                    $item->delete();
                                                foreach ($arreglo_marcaciones as $item)
                                                    $item->delete();
                                                foreach ($arreglo_det_pedidos as $item)
                                                    $item->delete();
                                                return [
                                                    'id_pedido' => '',
                                                    'success' => false,
                                                    'mensaje' => '<div class="alert alert-warning text-center error">No se ha podido crear la coloración ' .
                                                        Color::find($c['id_color'])->nombre . ' de la marcación ' .
                                                        $m['nombre'] . ' del detalle-pedido #' .
                                                        $pos_esp . '</div>',
                                                ];
                                            }
                                        }
                                }
                            } else {
                                foreach ($arreglo_marcaciones as $item)
                                    $item->delete();
                                foreach ($arreglo_det_pedidos as $item)
                                    $item->delete();
                                return [
                                    'id_pedido' => '',
                                    'success' => false,
                                    'mensaje' => '<div class="alert alert-warning text-center error">No se ha podido crear la marcación ' .
                                        $m['nombre'] . ' del detalle-pedido #' .
                                        $pos_esp . '</div>',
                                ];
                            }
                        }
                    }
                } else {
                    foreach ($arreglo_det_pedidos as $item)
                        $item->delete();
                    return [
                        'id_pedido' => '',
                        'success' => false,
                        'mensaje' => '<div class="alert alert-warning text-center error">No se ha podido crear el detalle-pedido #' .
                            $pos_esp . '</div>',
                    ];
                }
            }

            /* ========== COMPLETAR EL CAMPO variedad DEL PEDIDO =========== */
            $variedades = $arreglo_variedades[0];
            foreach ($arreglo_variedades as $i => $v) {
                if ($i > 0)
                    $variedades .= '|' . $v;
            }
            if ($pedido->variedad == '') {
                $pedido->variedad = $variedades;
            } else {
                $pedido->variedad .= '|' . $variedades;
            }
            if ($pedido->save()) {
                bitacora('pedido', $pedido->id_pedido, 'U', 'Modificacion del campo variedad de un pedido (Flor tinturada)');
            } else {
                return [
                    'id_pedido' => '',
                    'success' => false,
                    'mensaje' => '<div class="alert alert-warning text-center error">No se ha podido terminar de guardar el pedido</div>',
                ];
            }
        }

        return [
            'id_pedido' => $pedido->id_pedido,
            'success' => true,
            'mensaje' => '<div class="alert alert-success text-center">' .
                'Se ha guardado toda la información satisfactoriamente'
                . '</div>',
        ];

    }

    public function editar_pedido_tinturado(Request $request)
    {
        $pedido = Pedido::find($request->id_pedido);
        $have_next = false;
        if (count($pedido->detalles) > $request->pos_det_ped)
            $have_next = true;
        return view('adminlte.gestion.postcocecha.pedidos_ventas.forms.orden_semanal', [
            'pedido' => $pedido,
            'pos_det_ped' => $request->pos_det_ped,
            'have_next' => $have_next
        ]);
    }

    public function buscar_agencia_carga(Request $request)
    {
        $listado = Cliente::find($request->id_cliente);
        if ($listado != '')
            $listado = $listado->cliente_agencia_carga;
        else
            $listado = [];
        return view('adminlte.gestion.postcocecha.pedidos_ventas.partials._select_agencias_carga', [
            'listado' => $listado
        ]);
    }

    public function distribuir_orden_semanal(Request $request)
    {
        $pedido = Pedido::find($request->id_pedido);
        $marcaciones = $pedido->detalles[0]->marcaciones;
        $ids_marcaciones = [];
        foreach ($marcaciones as $m) {
            array_push($ids_marcaciones, $m->id_marcacion);
        }
        $coloraciones = DB::table('coloracion as c')
            ->select('c.nombre', 'c.fondo', 'c.texto')->distinct()
            ->whereIn('c.id_marcacion', $ids_marcaciones)
            ->get();
        $esp_emp = $pedido->detalles[0]->cliente_especificacion->especificacion->especificacionesEmpaque[0];
        $det_esp = $esp_emp->detalles[0];
        return view('adminlte.gestion.postcocecha.pedidos_ventas.partials.distribucion_orden_semanal', [
            'pedido' => $pedido,
            'marcaciones' => $marcaciones,
            'coloraciones' => $coloraciones,
            'calibres' => getCalibresRamo(),
            'variedades' => getVariedades(),
            'unidades_medida' => UnidadMedida::All()->where('estado', '=', 1)->where('tipo', '=', 'L'),
            'agencias' => $pedido->cliente->cliente_agencia_carga,
            'cajas' => Empaque::All()->where('estado', '=', 1)->where('tipo', '=', 'C'),
            //'envolturas' => Empaque::All()->where('estado', '=', 1)->where('tipo', '=', 'E'),
            'presentaciones' => Empaque::All()->where('estado', '=', 1)->where('tipo', '=', 'P'),
            'esp_emp' => $esp_emp,
            'det_esp' => $det_esp,
        ]);
    }

    public function editar_coloracion(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:250',
            'color' => 'required|',
            'pedido' => 'required|',
            'fondo' => 'required|',
            'texto' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'pedido.required' => 'El pedido es obligatorio',
            'color.required' => 'El color es obligatorio',
            'fondo.required' => 'El fondo es obligatorio',
            'texto.required' => 'El texto es obligatorio',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        $success = true;
        $msg = '';
        if (!$valida->fails()) {
            $pedido = getPedido($request->pedido);
            $marcaciones = $pedido->detalles[0]->cliente_especificacion->especificacion->especificacionesEmpaque[0]->marcaciones;
            $ids_marcaciones = [];
            foreach ($marcaciones as $m) {
                array_push($ids_marcaciones, $m->id_marcacion);
            }
            $coloraciones = DB::table('coloracion as c')
                ->select('c.id_coloracion', 'c.nombre')
                ->whereIn('c.id_marcacion', $ids_marcaciones)
                ->where('c.nombre', '=', $request->color)
                ->get();

            foreach ($coloraciones as $color) {
                $model = Coloracion::find($color->id_coloracion);
                $model->nombre = str_limit(str_replace(' ', '_', espacios($request->nombre)), 250);
                $model->fondo = $request->fondo;
                $model->texto = $request->texto;

                if ($model->save()) {
                    bitacora('coloracion', $model->id_coloracion, 'U', 'Actualización satisfactoria de una coloracion');
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            }
            if ($success) {
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha actualizado el color satisfactoriamente</p>'
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

    public function editar_marcacion(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'nombre' => 'required|max:250',
            'id_marcacion' => 'required|',
            'pedido' => 'required|',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'pedido.required' => 'El pedido es obligatorio',
            'id_marcacion.required' => 'la marcación es obligatoria',
            'nombre.max' => 'El nombre es muy grande',
        ]);
        if (!$valida->fails()) {
            $model = Marcacion::find($request->id_marcacion);
            $model->nombre = str_limit(mb_strtoupper(espacios($request->nombre)), 250);

            if ($model->save()) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha actualizado la marcación satisfactoriamente</p>'
                    . '</div>';
                bitacora('marcacion', $model->id_marcacion, 'U', 'Actualización satisfactoria de una marcación');
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

    public function update_distribucion(Request $request)
    {
        //dd($request->all());
        $success = true;
        $msg = '';
        if (count($request->arreglo) > 0) {
            $pedido = Pedido::find($request->pedido);
            foreach ($request->arreglo as $item) {
                /* ========= MARCACIONES =========*/
                if ($item['tipo'] == 'M') {
                    $model = Marcacion::find($item['id_marcacion']);
                    $model->nombre = str_limit(mb_strtoupper(espacios($item['nombre'])), 250);

                    if ($model->save()) {
                        bitacora('marcacion', $model->id_marcacion, 'U', 'Actualización satisfactoria de una marcación');
                    } else {
                        $success = false;
                        $msg .= '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la marcación "' . $item['nombre'] . '" al sistema</p>'
                            . '</div>';
                    }
                }
                /* ========= COLORACIONES =========*/
                if ($item['tipo'] == 'C') {
                    $marcaciones = $pedido->detalles[0]->cliente_especificacion->especificacion->especificacionesEmpaque[0]->marcaciones;
                    $ids_marcaciones = [];
                    foreach ($marcaciones as $m) {
                        array_push($ids_marcaciones, $m->id_marcacion);
                    }
                    $coloraciones = DB::table('coloracion as c')
                        ->select('c.id_coloracion', 'c.nombre')
                        ->whereIn('c.id_marcacion', $ids_marcaciones)
                        ->where('c.nombre', '=', $item['color'])
                        ->get();

                    foreach ($coloraciones as $color) {
                        $model = Coloracion::find($color->id_coloracion);
                        $model->nombre = str_limit(str_replace(' ', '_', espacios($item['nombre'])), 250);
                        $model->fondo = $item['fondo'];
                        $model->texto = $item['texto'];

                        if ($model->save()) {
                            bitacora('coloracion', $model->id_coloracion, 'U', 'Actualización satisfactoria de una coloracion');
                        } else {
                            $success = false;
                            $msg .= '<div class="alert alert-warning text-center">' .
                                '<p> Ha ocurrido un problema al guardar la coloración "' . $item['nombre'] . '" al sistema</p>'
                                . '</div>';
                        }
                    }
                }
                /* ========= CANTIDADES =========*/
                if ($item['tipo'] == 'X') {
                    $marcacion = Marcacion::find($item['id_marcacion']);
                    $coloracion = $marcacion->getColoracionByName(str_replace(' ', '_', espacios($item['nombre'])));
                    $accion = 'U';
                    $observacion = 'Modificación';
                    if ($coloracion == '') {
                        $coloracion = new Coloracion();
                        $coloracion->id_marcacion = $marcacion->id_marcacion;
                        $coloracion->nombre = str_limit(str_replace(' ', '_', espacios($item['nombre'])), 250);
                        $coloracion->fondo = $item['fondo'];
                        $coloracion->texto = $item['texto'];
                        $accion = 'I';
                        $observacion = 'Inserción';
                    }
                    $coloracion->cantidad = $item['cantidad'];

                    if ($coloracion->save()) {
                        bitacora('coloracion', $coloracion->id_coloracion, $accion, $observacion . ' satisfactoria de una coloración');
                    } else {
                        $success = false;
                        $msg .= '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la cantidad de ramos para la marcación "' . $marcacion->nombre .
                            '" y coloración "' . espacios($item['nombre']) .
                            '" al sistema</p>'
                            . '</div>';
                    }
                }
            }
            if ($success) {
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha actualizado la información satisfactoriamente</p>'
                    . '</div>';
            }
        } else {
            $success = false;
            $msg = '<div class="alert alert-warning text-center">' .
                '<p> No hay modificaciones que realizar</p>'
                . '</div>';
        }

        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function update_pedido_orden_semanal(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'id_pedido' => 'required|',
            'id_especificacion_empaque' => 'required|',
            'id_detalle_especificacionempaque' => 'required|',
            'fecha_pedido' => 'required|',
            'cantidad_piezas' => 'required|',
            'id_empaque' => 'required|',
            'cantidad_ramos' => 'required|',
            'id_clasificacion_ramo' => 'required|',
            'id_variedad' => 'required|',
            //'id_empaque_e' => 'required|',
            'id_empaque_p' => 'required|',
            'id_agencia_carga' => 'required|',
        ], [
            'id_pedido.required' => 'El pedido es obligatorio',
            'id_especificacion_empaque.required' => 'La especificación-empaque es obligatoria',
            'id_detalle_especificacionempaque.required' => 'El detalle_especificación-empaque es obligatorio',
            'fecha_pedido.required' => 'La fecha del pedido es obligatoria',
            'cantidad_piezas.required' => 'La cantidad de piezas es obligatoria',
            'id_empaque.required' => 'La pieza es obligatoria',
            'id_clasificacion_ramo.required' => 'El calibre de los ramos es obligatorio',
            'id_variedad.required' => 'La variedad es obligatoria',
            'id_empaque_p.required' => 'La presentación es obligatoria',
            'id_agencia_carga.required' => 'La agencia de carga es obligatoria',
        ]);
        $success = true;
        $msg = '';
        if (!$valida->fails()) {
            /* ============= TABLA PEDIDO ==============*/
            $pedido = Pedido::find($request->id_pedido);
            if ($request->fecha_pedido >= date('Y-m-d')) {
                $pedido->fecha_pedido = $request->fecha_pedido;
                $descripcion = $request->cantidad_piezas . ' ' . explode('|', Empaque::find($request->id_empaque)->nombre)[0] . ' de ' .
                    $request->cantidad_ramos . ' ramos ' . ClasificacionRamo::find($request->id_clasificacion_ramo)->nombre .
                    ClasificacionRamo::find($request->id_clasificacion_ramo)->unidad_medida->siglas . ' ' . Variedad::find($request->id_variedad)->siglas . ' ' .
                    explode('|', Empaque::find($request->id_empaque_p)->nombre)[0] . ' ' . $request->tallos_x_ramos . ' ' .
                    $request->longitud_ramo . (UnidadMedida::find($request->id_unidad_medida) != '' ? UnidadMedida::find($request->id_unidad_medida)->siglas : '');
                $pedido->descripcion = $descripcion;

                if ($pedido->save()) {
                    bitacora('pedido', $pedido->id_pedido, 'U', 'Actualización satisfactoria de un pedido');

                    /* ============= TABLA ESPECIFICACION_EMPAQUE ==============*/
                    $esp_emp = EspecificacionEmpaque::find($request->id_especificacion_empaque);
                    $esp_emp->cantidad = $request->cantidad_piezas;
                    $esp_emp->id_empaque = $request->id_empaque;

                    if ($esp_emp->save()) {
                        bitacora('especificacion_empaque', $esp_emp->id_especificacion_empaque, 'U', 'Actualización satisfactoria de una especificación-empaque');

                        /* ========== TABLA ESPECIFICACION ==============*/
                        $especificacion = $esp_emp->especificacion;
                        $especificacion->nombre = $especificacion->descripcion = $descripcion;

                        if ($especificacion->save()) {
                            bitacora('especificacion', $especificacion->id_especificacion, 'U', 'Actualización satisfactoria de una especificación');

                            /* =========== TABLA DETALLE_ESPECIFICACIONEMPAQUE ==============*/
                            $det_esp = DetalleEspecificacionEmpaque::find($request->id_detalle_especificacionempaque);
                            $det_esp->id_variedad = $request->id_variedad;
                            $det_esp->id_clasificacion_ramo = $request->id_clasificacion_ramo;
                            $det_esp->cantidad = $request->cantidad_ramos;
                            //$det_esp->id_empaque_e = $request->id_empaque_e;
                            $det_esp->id_empaque_p = $request->id_empaque_p;
                            $det_esp->tallos_x_ramos = $request->tallos_x_ramos;
                            $det_esp->longitud_ramo = $request->longitud_ramo;
                            $det_esp->id_unidad_medida = $request->id_unidad_medida;

                            if ($det_esp->save()) {
                                bitacora('especificacion', $especificacion->id_especificacion, 'U', 'Actualización satisfactoria de un detalle-especificación');

                            } else {
                                $success = false;
                                $msg = '<div class="alert alert-warning text-center">' .
                                    '<p> Ha ocurrido un problema al guardar la detalle-especificación</p>'
                                    . '</div>';
                            }
                        } else {
                            $success = false;
                            $msg = '<div class="alert alert-warning text-center">' .
                                '<p> Ha ocurrido un problema al guardar la especificación</p>'
                                . '</div>';
                        }
                    } else {
                        $success = false;
                        $msg = '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la especificación-empaque</p>'
                            . '</div>';
                    }
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar el pedido</p>'
                        . '</div>';
                }

                if ($success) {
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha actualizado el pedido satisfactoriamente</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> La fecha del pedido debe ser a partir de la fecha actual</p>'
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

    public function distribuir_marcaciones(Request $request)
    {
        $pedido = Pedido::find($request->id_pedido);
        $marcaciones = $pedido->detalles[0]->marcaciones;
        $ids_marcaciones = [];
        foreach ($marcaciones as $m) {
            array_push($ids_marcaciones, $m->id_marcacion);
        }
        $marcas = [];
        foreach ($request->arreglo as $item) {
            array_push($marcas, [
                'marcacion' => Marcacion::find($item['id_marcacion']),
                'cant_distribuciones' => $item['distribuciones'],
            ]);
        }
        $coloraciones = DB::table('coloracion as c')
            ->select('c.nombre', 'c.fondo', 'c.texto')->distinct()
            ->whereIn('c.id_marcacion', $ids_marcaciones)
            ->get();
        $esp_emp = $pedido->detalles[0]->cliente_especificacion->especificacion->especificacionesEmpaque[0];
        $det_esp = $esp_emp->detalles[0];
        return view('adminlte.gestion.postcocecha.pedidos_ventas.partials._distribuir_marcaciones', [
            'pedido' => $pedido,
            'marcaciones' => $marcaciones,
            'marcas' => $marcas,
            'coloraciones' => $coloraciones,
            'esp_emp' => $esp_emp,
            'det_esp' => $det_esp,
        ]);
    }

    public function calcular_distribucion(Request $request)
    {
        $marcacion = Marcacion::find($request->id_marcacion);
        $msg = '';
        $success = true;
        $matriz = [];
        if ($request->piezas <= round($marcacion->getTotalRamos() / $request->ramos, 2)) {
            $coloraciones = [];
            foreach ($marcacion->coloraciones as $c) {
                array_push($coloraciones, [
                    'color' => str_replace(' ', '_', espacios($c->nombre)),
                    'cantidad' => $c->cantidad
                ]);
            }
            $ramos = $request->ramos;
            foreach ($request->arreglo_piezas as $piezas) {
                $current = 0;
                $meta = $piezas * $ramos;
                $arreglo = [];
                for ($i = 0; $i < count($coloraciones); $i++) {
                    if (($coloraciones[$i]['cantidad'] + $current) >= $meta) {
                        $data = [
                            'color' => str_replace(' ', '_', espacios($coloraciones[$i]['color'])),
                            'cantidad' => $meta - $current
                        ];
                        $coloraciones[$i]['cantidad'] = $coloraciones[$i]['cantidad'] - ($meta - $current);
                        $current = $meta;
                    } else {
                        $data = [
                            'color' => str_replace(' ', '_', espacios($coloraciones[$i]['color'])),
                            'cantidad' => $coloraciones[$i]['cantidad']
                        ];
                        $current += $coloraciones[$i]['cantidad'];
                        $coloraciones[$i]['cantidad'] = 0;
                    }
                    array_push($arreglo, $data);
                }
                array_push($matriz, $arreglo);
            }
        } else {
            $success = false;
            $msg = '<div class="alert alert-warning text-center">La cantidad de piezas es superior a la ingresada previamente</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
            'marcacion' => $marcacion->id_marcacion,
            'matriz' => $matriz,
        ];
    }

    public function listar_especificaciones_x_cliente(Request $request)
    {
        $cliente = Cliente::find($request->id_cliente);
        return view('adminlte.gestion.postcocecha.pedidos_ventas.partials._especificacion_orden_semanal', [
            'cliente' => $cliente,
        ]);
    }

    /* ================ PEDIDOS PERSONALIZDOS ================*/
    public function add_pedido_personalizado(Request $request)
    {
        return view('adminlte.gestion.postcocecha.pedidos_ventas.partials.add_pedido_personalizado', [
            'clientes' => Cliente::join('detalle_cliente as dc', 'cliente.id_cliente', 'dc.id_cliente')->where([
                ['cliente.estado', '=', 1],
                ['dc.estado', 1]
            ])->orderBy('dc.nombre', 'asc')->get(),
            'cajas' => Empaque::All()->where('estado', '=', 1)->where('tipo', '=', 'C'),
            'calibres' => getCalibresRamo(),
            'variedades' => getVariedades(),
            //'envolturas' => Empaque::All()->where('estado', '=', 1)->where('tipo', '=', 'E'),
            'presentaciones' => Empaque::All()->where('estado', '=', 1)->where('tipo', '=', 'P'),
            'unidades_medida' => UnidadMedida::All()->where('estado', '=', 1)->where('tipo', '=', 'L'),
        ]);
    }

    public function listar_agencias_carga(Request $request)
    {
        return view('adminlte.gestion.postcocecha.pedidos_ventas.partials._listar_agencias_carga', [
            'cliente' => Cliente::find($request->id_cliente),
            'pos' => $request->pos
        ]);
    }

    public function store_pedido_personalizado(Request $request)
    {
        $creados = [];
        if (count($request->arreglo) > 0) {
            foreach ($request->arreglo as $item) {
                //dd($item['longitud_ramo']);
                /* ========= TABLA ESPECIFICACION ==========*/
                $texto = explode('|', Empaque::find($item['id_empaque'])->nombre)[0] . ' de ' .
                    $item['cantidad_ramos'] . ' ramos ' . ClasificacionRamo::find($item['id_clasificacion_ramo'])->nombre .
                    ClasificacionRamo::find($item['id_clasificacion_ramo'])->unidad_medida->siglas . ' ' . Variedad::find($item['id_variedad'])->siglas . ' ' .
                    explode('|', Empaque::find($item['id_empaque_p'])->nombre)[0] . ' ' . $item['tallos_x_ramo'] . ' ' .
                    $item['longitud_ramo'] . (UnidadMedida::find($item['id_unidad_medida']) != '' ? UnidadMedida::find($item['id_unidad_medida'])->siglas : '');

                $especificacion = new Especificacion();
                $especificacion->nombre = $especificacion->descripcion = $texto;
                if ($item['check_make_especificacion'] == 'true')
                    $especificacion->tipo = 'N';
                else
                    $especificacion->tipo = 'O';

                if ($especificacion->save()) {
                    $especificacion = Especificacion::All()->last();
                    bitacora('especificacion', $especificacion->id_especificacion, 'I', 'Insercion de una nueva especificacion');
                    array_push($creados, $especificacion);

                    /* ========== TABLA ESPECIFIACION_EMPAQUE ==============*/
                    $esp_emp = new EspecificacionEmpaque();
                    $esp_emp->id_especificacion = $especificacion->id_especificacion;
                    $esp_emp->id_empaque = $item['id_empaque'];
                    $esp_emp->cantidad = 1;

                    if ($esp_emp->save()) {
                        $esp_emp = EspecificacionEmpaque::All()->last();
                        bitacora('especificacion_empaque', $esp_emp->id_especificacion_empaque, 'I', 'Insercion de una nueva especificacion-empaque');
                        array_push($creados, $esp_emp);

                        /* ========= TABLA DETALLE_ESPECIFICACION_EMPAQUE ===========*/
                        $det_esp_emp = new DetalleEspecificacionEmpaque();
                        $det_esp_emp->id_especificacion_empaque = $esp_emp->id_especificacion_empaque;
                        $det_esp_emp->id_variedad = $item['id_variedad'];
                        $det_esp_emp->id_clasificacion_ramo = $item['id_clasificacion_ramo'];
                        $det_esp_emp->cantidad = $item['cantidad_ramos'];
                        //$det_esp_emp->id_empaque_e = $item['id_empaque_e'];
                        $det_esp_emp->id_empaque_p = $item['id_empaque_p'];
                        $det_esp_emp->tallos_x_ramos = $item['tallos_x_ramo'];
                        $det_esp_emp->longitud_ramo = $item['longitud_ramo'];
                        $det_esp_emp->id_unidad_medida = $item['longitud_ramo'] != null ? $item['id_unidad_medida'] : null;

                        if ($det_esp_emp->save()) {
                            $det_esp_emp = DetalleEspecificacionEmpaque::All()->last();
                            bitacora('detalle_especificacionempaque', $det_esp_emp->id_detalle_especificacionempaque, 'I', 'Insercion de un nuevo detalle-especificacion-empaque');
                            array_push($creados, $det_esp_emp);

                            /* =========== TABLA CLIENTE_PEDIDO_ESPECIFICACION ==========*/
                            $cli_ped_esp = new ClientePedidoEspecificacion();
                            $cli_ped_esp->id_especificacion = $especificacion->id_especificacion;
                            $cli_ped_esp->id_cliente = $item['id_cliente'];

                            if ($cli_ped_esp->save()) {
                                $cli_ped_esp = ClientePedidoEspecificacion::All()->last();
                                bitacora('cliente_pedido_especificacion', $cli_ped_esp->id_cliente_pedido_especificacion, 'I', 'Insercion de un nuevo cliente_pedido_especificacion');
                                array_push($creados, $cli_ped_esp);

                                /* ========== TABLA PEDIDO ============*/
                                $pedido = new Pedido();
                                $pedido->id_cliente = $item['id_cliente'];
                                $pedido->descripcion = $item['cantidad_piezas'] . ' ' . $texto;
                                $pedido->variedad = $item['id_variedad'];  // optimizar
                                $pedido->fecha_pedido = $request->fecha_pedido;
                                $pedido->tipo_especificacion = 'N';

                                if ($pedido->save()) {
                                    $pedido = Pedido::All()->last();
                                    bitacora('pedido', $pedido->id_pedido, 'I', 'Insercion de un nuevo pedido');
                                    array_push($creados, $pedido);

                                    /* ========= TABLA DETALLE_PEDIDO ===========*/
                                    $det_pedido = new DetallePedido();
                                    $det_pedido->id_pedido = $pedido->id_pedido;
                                    $det_pedido->id_cliente_especificacion = $cli_ped_esp->id_cliente_pedido_especificacion;
                                    $det_pedido->id_agencia_carga = $item['id_agencia_carga'];
                                    $det_pedido->cantidad = $item['cantidad_piezas'];

                                    if ($det_pedido->save()) {
                                        $det_pedido = DetallePedido::All()->last();
                                        bitacora('detalle_pedido', $pedido->id_detalle_pedido, 'I', 'Insercion de un nuevo detalle-pedido');
                                        array_push($creados, $det_pedido);
                                    } else {
                                        foreach ($creados as $c)
                                            $c->delete();
                                        return [
                                            'success' => false,
                                            'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear la información relacionada al detalle-pedido</div>'
                                        ];
                                    }
                                } else {
                                    foreach ($creados as $c)
                                        $c->delete();
                                    return [
                                        'success' => false,
                                        'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear el pedido</div>'
                                    ];
                                }
                            } else {
                                foreach ($creados as $c)
                                    $c->delete();
                                return [
                                    'success' => false,
                                    'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear la información relacionada a CLIENTE_PEDIDO_ESPECIFICACION</div>'
                                ];
                            }
                        } else {
                            foreach ($creados as $c)
                                $c->delete();
                            return [
                                'success' => false,
                                'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear el detalle-especificación-empaque</div>'
                            ];
                        }
                    } else {
                        foreach ($creados as $c)
                            $c->delete();
                        return [
                            'success' => false,
                            'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear la especificación</div>'
                        ];
                    }
                } else {
                    foreach ($creados as $c)
                        $c->delete();
                    return [
                        'success' => false,
                        'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear la especificación</div>'
                    ];
                }
            }
            return [
                'success' => true,
                'mensaje' => '<div class="alert alert-success text-center">Se ha guardado toda la información satisfactoriamente</div>'
            ];
        } else {
            return [
                'success' => false,
                'mensaje' => '<div class="alert alert-warning text-center">Al menos ingrese un pedido</div>'
            ];
        }
    }
}
