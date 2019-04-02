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
use yura\Modelos\DetalleEnvio;
use yura\Modelos\DetalleEspecificacionEmpaque;
use yura\Modelos\DetallePedido;
use yura\Modelos\Empaque;
use yura\Modelos\Envio;
use yura\Modelos\Especificacion;
use yura\Modelos\EspecificacionEmpaque;
use yura\Modelos\Marcacion;
use yura\Modelos\MarcacionColoracion;
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

                                    /* =========== TABLA COLORACION ===========*/
                                    $arreglo_coloraciones = [];
                                    foreach ($request->nueva_esp['coloraciones'] as $c) {
                                        $coloracion = new Coloracion();
                                        $coloracion->id_detalle_pedido = $det_pedido->id_detalle_pedido;
                                        $coloracion->id_especificacion_empaque = $esp_emp->id_especificacion_empaque;
                                        $coloracion->id_color = $c;

                                        //dd($coloracion);

                                        if ($coloracion->save()) {
                                            $coloracion = Coloracion::All()->last();
                                            bitacora('coloracion', $coloracion->id_coloracion, 'I', 'Insercion de una nueva coloracion');
                                            array_push($arreglo_coloraciones, $coloracion);
                                        } else {
                                            foreach ($arreglo_coloraciones as $item)
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
                                                'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear la coloración "' .
                                                    getColor($c)->nombre
                                                    . '"</div>',
                                            ];
                                        }
                                    }

                                    /* =========== TABLA MARCACION ===========*/
                                    $arreglo_marcaciones = [];
                                    $arreglo_marc_col = [];
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

                                            /* =========== TABLA MARCACION_COLORACION ===========*/
                                            foreach ($m['coloraciones'] as $pos_c => $c) {
                                                $marc_col = new MarcacionColoracion();
                                                $marc_col->cantidad = $c['cantidad'] != '' ? $c['cantidad'] : 0;
                                                $marc_col->id_coloracion = $arreglo_coloraciones[$pos_c]->id_coloracion;
                                                $marc_col->id_marcacion = $marcacion->id_marcacion;
                                                $marc_col->id_detalle_especificacionempaque = $det_espemp->id_detalle_especificacionempaque;

                                                if ($marc_col->save()) {
                                                    $marc_col = MarcacionColoracion::All()->last();
                                                    bitacora('marcacion_coloracion', $marc_col->id_marcacion_coloracion, 'I', 'Insercion de una nueva marcacion-coloracion');
                                                    array_push($arreglo_marc_col, $marc_col);

                                                } else {
                                                    foreach ($arreglo_marc_col as $item)
                                                        $item->delete();
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
        if ($request->has('arreglo_esp'))
            if (count($request->arreglo_esp) > 0) {
                $arreglo_variedades = [];
                /* ============ TABLA DETALLE_PEDIDO ============ */
                $arreglo_det_pedidos = [];
                $arreglo_marcaciones = [];
                $arreglo_coloraciones = [];
                $arreglo_marc_col = [];
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

                        foreach ($esp['arreglo_esp_emp'] as $esp_emp) {
                            /* =========== TABLA COLORACION ===========*/
                            $sub_arreglo_coloraciones = [];
                            foreach ($esp_emp['coloraciones'] as $c) {
                                $coloracion = new Coloracion();
                                $coloracion->id_detalle_pedido = $det_pedido->id_detalle_pedido;
                                $coloracion->id_especificacion_empaque = $esp_emp['id_esp_emp'];
                                $coloracion->id_color = $c;

                                if ($coloracion->save()) {
                                    $coloracion = Coloracion::All()->last();
                                    bitacora('coloracion', $coloracion->id_coloracion, 'I', 'Insercion de una nueva coloracion');
                                    array_push($arreglo_coloraciones, $coloracion);
                                    array_push($sub_arreglo_coloraciones, $coloracion);
                                } else {
                                    foreach ($arreglo_coloraciones as $item)
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
                                        'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear la coloración "' .
                                            getColor($c)->nombre
                                            . '"</div>',
                                    ];
                                }
                            }

                            /* ============== TABLA MARCACION ===========*/
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

                                    /* =========== TABLA MARCACION_COLORACION =========== */
                                    foreach ($m['arreglo_colores'] as $pos_c => $c) {
                                        if (isset($c['cant_x_det_esp']))
                                            foreach ($c['cant_x_det_esp'] as $det_esp) {
                                                $marc_col = new MarcacionColoracion();
                                                $marc_col->id_marcacion = $marcacion->id_marcacion;
                                                $marc_col->id_coloracion = $sub_arreglo_coloraciones[$pos_c]->id_coloracion;
                                                $marc_col->id_detalle_especificacionempaque = $det_esp['id_det_esp'];
                                                $marc_col->cantidad = $det_esp['cantidad'] != '' ? $det_esp['cantidad'] : 0;

                                                if ($marc_col->save()) {
                                                    $marc_col = MarcacionColoracion::All()->last();
                                                    bitacora('marcaion_coloracion', $marc_col->id_marcaion_coloracion, 'I', 'Insercion de una nueva marcaion-coloracion');
                                                    array_push($arreglo_marc_col, $marc_col);

                                                    /* ======== OBTENER LAS VARIEDADES INCLUIDAS EN EL PEDIDO ======= */
                                                    $det_esp = DetalleEspecificacionEmpaque::find($marc_col->id_detalle_especificacionempaque);
                                                    if (!in_array($det_esp->id_variedad, $arreglo_variedades)) {
                                                        array_push($arreglo_variedades, $det_esp->id_variedad);
                                                    }
                                                } else {
                                                    foreach ($arreglo_marc_col as $item)
                                                        $item->delete();
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
                                    foreach ($arreglo_coloraciones as $item)
                                        $item->delete();
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
                if (!in_array($pedido->variedad, $arreglo_variedades) && $pedido->variedad != '')
                    array_push($arreglo_variedades, $pedido->variedad);
                $variedades = $arreglo_variedades[0];
                foreach ($arreglo_variedades as $i => $v) {
                    if ($i > 0)
                        $variedades .= '|' . $v;
                }
                $pedido->variedad = $variedades;
                if ($pedido->save()) {
                    bitacora('pedido', $pedido->id_pedido, 'U', 'Modificacion del campo variedad de un pedido (Flor tinturada)');

                    /* =========== ENVIO ========= */
                    $envio = new Envio();
                    $envio->id_pedido = $pedido->id_pedido;
                    $envio->fecha_envio = $request->fecha_envio;

                    if ($envio->save()) {
                        $envio = Envio::All()->last();
                        bitacora('envio', $envio->id_envio, 'I', 'Insercion de un nuevo envio');

                        foreach ($pedido->detalles as $det) {
                            $det_envio = new DetalleEnvio();
                            $det_envio->id_envio = $envio->id_envio;
                            $det_envio->id_especificacion = $det->cliente_especificacion->id_especificacion;
                            $det_envio->cantidad = $det->cantidad;

                            if ($det_envio->save()) {
                                $det_envio = DetalleEnvio::All()->last();
                                bitacora('detalle_envio', $det_envio->id_detalle_envio, 'I', 'Insercion de un nuevo detalle-envio');
                            } else {
                                $pedido->delete();
                                return [
                                    'id_pedido' => '',
                                    'success' => false,
                                    'mensaje' => '<div class="alert alert-warning text-center error">No se ha podido crear el detalle-envío</div>',
                                ];
                            }
                        }
                    } else {
                        $pedido->delete();
                        return [
                            'id_pedido' => '',
                            'success' => false,
                            'mensaje' => '<div class="alert alert-warning text-center error">No se ha podido crear el envío</div>',
                        ];
                    }
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
        if (count($pedido->detalles) > $request->pos_det_ped + 1)
            $have_next = true;
        return view('adminlte.gestion.postcocecha.pedidos_ventas.forms.orden_semanal', [
            'pedido' => $pedido,
            'pos_det_ped' => $request->pos_det_ped,
            'have_next' => $have_next,
            'have_prev' => $request->pos_det_ped > 0,
        ]);
    }

    public function update_orden_tinturada(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'id_pedido' => 'required',
            'id_detalle_pedido' => 'required',
            'fecha_pedido' => 'required',
            'cantidad_piezas' => 'required',
            'id_agencia_carga' => 'required',
            'arreglo_esp_emp' => 'required|Array',
        ], [
            'id_pedido.required' => 'El pedido es obligatorio',
            'id_detalle_pedido.required' => 'El detalle del pedido es obligatorio',
            'fecha_pedido.required' => 'La fecha del pedido es obligatoria',
            'cantidad_piezas.required' => 'La cantidad de piezas es obligatoria',
            'id_agencia_carga.required' => 'La agencia de carga es obligatoria',
            'arreglo_esp_emp.required' => 'Las especificaciones son obligatorias',
            'arreglo_esp_emp.Array' => 'Las especificaciones deben ser un listado',
        ]);
        $msg = '';
        $success = true;
        if (!$valida->fails()) {
            /* ========== PEDIDO ============ */
            $pedido = Pedido::find($request->id_pedido);
            $pedido->fecha_pedido = $request->fecha_pedido;

            if ($pedido->save()) {
                bitacora('pedido', $pedido->id_pedido, 'U', 'Actualizacion de un pedido');

                /* ========== DETALLE_PEDIDO ============ */
                $last_det_ped = DetallePedido::find($request->id_detalle_pedido);

                $det_pedido = new DetallePedido();
                $det_pedido->id_cliente_especificacion = $last_det_ped->id_cliente_especificacion;
                $det_pedido->id_pedido = $request->id_pedido;
                $det_pedido->id_agencia_carga = $request->id_agencia_carga;
                $det_pedido->cantidad = $request->cantidad_piezas;
                $det_pedido->precio = $request->arreglo_esp_emp[0]['arreglo_precios'][0]['precio'] . ';' .
                    $request->arreglo_esp_emp[0]['arreglo_precios'][0]['id_det_esp'];

                foreach ($request->arreglo_esp_emp as $pos_esp_emp => $esp_emp) {
                    foreach ($esp_emp['arreglo_precios'] as $pos_precio => $precio) {
                        if (($pos_esp_emp == 0 && $pos_precio > 0) || $pos_esp_emp > 0)
                            $det_pedido->precio .= '|' . $precio['precio'] . ';' . $precio['id_det_esp'];
                    }
                }

                if ($det_pedido->save()) {
                    $det_pedido = DetallePedido::All()->last();
                    bitacora('detalle_pedido', $det_pedido->id_detalle_pedido, 'I', 'Inserción satisfactoria de un nuevo detalle_pedido');

                    $arreglo_variedades = [];
                    foreach ($request->arreglo_esp_emp as $pos_esp_emp => $esp_emp) {
                        /* ========= COLORACIONES ========== */
                        $arreglo_coloraciones = [];
                        foreach ($esp_emp['arreglo_coloraciones'] as $col) {
                            $coloracion = new Coloracion();
                            $coloracion->id_color = $col['id_color'];
                            $coloracion->id_detalle_pedido = $det_pedido->id_detalle_pedido;
                            $coloracion->id_especificacion_empaque = $esp_emp['id_esp_emp'];

                            $precio_col = '';
                            foreach ($col['arreglo_precios_x_col'] as $p) {
                                if ($p['precio'] != '') {
                                    if ($precio_col == '') {
                                        $precio_col = $p['precio'] . ';' . $p['id_det_esp'];
                                    } else {
                                        $precio_col .= '|' . $p['precio'] . ';' . $p['id_det_esp'];
                                    }
                                }
                            }
                            $coloracion->precio = $precio_col;

                            if ($coloracion->save()) {
                                $coloracion = Coloracion::All()->last();
                                bitacora('coloracion', $coloracion->id_coloracion, 'I', 'Insercion de una nueva coloracion');
                                array_push($arreglo_coloraciones, $coloracion);
                            } else {
                                $det_pedido->delete();
                                return [
                                    'success' => false,
                                    'mensaje' => '<div class="alert alert-warning text-center">' .
                                        '<p> Ha ocurrido un problema al guardar la coloración ' . getColor($col['id_color'])->nombre . '</p>'
                                        . '</div>'
                                ];
                            }
                        }

                        /* ========= MARCACIONES ========== */
                        foreach ($esp_emp['arreglo_marcaciones'] as $marc) {
                            $marcacion = new Marcacion();
                            $marcacion->nombre = $marc['nombre'];
                            $marcacion->ramos = $marc['ramos'];
                            $marcacion->piezas = $marc['piezas'];
                            $marcacion->id_detalle_pedido = $det_pedido->id_detalle_pedido;
                            $marcacion->id_especificacion_empaque = $esp_emp['id_esp_emp'];

                            if ($marcacion->save()) {
                                $marcacion = Marcacion::All()->last();
                                bitacora('coloracion', $marcacion->id_marcacion, 'I', 'Insercion de una nueva coloracion');

                                /* ========== MARCACIONES_COLORACIONES ========= */
                                foreach ($marc['colores'] as $pos_col => $col) {
                                    foreach ($col['cant_x_det_esp'] as $mc) {
                                        $marc_col = new MarcacionColoracion();
                                        $marc_col->id_marcacion = $marcacion->id_marcacion;
                                        $marc_col->id_coloracion = $arreglo_coloraciones[$pos_col]->id_coloracion;
                                        $marc_col->cantidad = $mc['cantidad'];
                                        $marc_col->id_detalle_especificacionempaque = $mc['id_det_esp'];

                                        if ($marc_col->save()) {
                                            $marc_col = MarcacionColoracion::All()->last();
                                            bitacora('marcacion_coloracion', $marc_col->id_marcacion_coloracion, 'I', 'Insercion de una nueva marcacion-coloracion');

                                            /* =========== LLENAR ARREGLO DE VARIEDADES ============ */
                                            if (!in_array(DetalleEspecificacionEmpaque::find($mc['id_det_esp'])->id_variedad, $arreglo_variedades)) {
                                                array_push($arreglo_variedades, DetalleEspecificacionEmpaque::find($mc['id_det_esp'])->id_variedad);
                                            }
                                        } else {
                                            $det_pedido->delete();
                                            return [
                                                'success' => false,
                                                'mensaje' => '<div class="alert alert-warning text-center">' .
                                                    '<p> Ha ocurrido un problema al guardar la marcación-coloración ' . $marc['nombre'] . '-' .
                                                    $coloracion->color->nombre . '</p>'
                                                    . '</div>'
                                            ];
                                        }
                                    }
                                }
                            } else {
                                $det_pedido->delete();
                                return [
                                    'success' => false,
                                    'mensaje' => '<div class="alert alert-warning text-center">' .
                                        '<p> Ha ocurrido un problema al guardar la marcación ' . $marc['nombre'] . '</p>'
                                        . '</div>'
                                ];
                            }
                        }

                        /* ========= PRECIOS ========= */
                    }
                    $last_det_ped->delete();

                    /* =========== ENVIO ========= */
                    foreach ($pedido->envios as $item) {
                        $item->delete();
                    }

                    $envio = new Envio();
                    $envio->id_pedido = $pedido->id_pedido;
                    $envio->fecha_envio = $request->fecha_envio;

                    if ($envio->save()) {
                        $envio = Envio::All()->last();
                        bitacora('envio', $envio->id_envio, 'I', 'Insercion de un nuevo envio');

                        foreach ($pedido->detalles as $det) {
                            $det_envio = new DetalleEnvio();
                            $det_envio->id_envio = $envio->id_envio;
                            $det_envio->id_especificacion = $det->cliente_especificacion->id_especificacion;
                            $det_envio->cantidad = $det->cantidad;

                            if ($det_envio->save()) {
                                $det_envio = DetalleEnvio::All()->last();
                                bitacora('detalle_envio', $det_envio->id_detalle_envio, 'I', 'Insercion de un nuevo detalle-envio');
                            } else {
                                $pedido->delete();
                                return [
                                    'id_pedido' => '',
                                    'success' => false,
                                    'mensaje' => '<div class="alert alert-warning text-center error">No se ha podido crear el detalle-envío</div>',
                                ];
                            }
                        }
                    } else {
                        $pedido->delete();
                        return [
                            'id_pedido' => '',
                            'success' => false,
                            'mensaje' => '<div class="alert alert-warning text-center error">No se ha podido crear el envío</div>',
                        ];
                    }

                    if ($success) {
                        $msg = '<div class="alert alert-success text-center">' .
                            '<p> Se ha actualziado la información satisfactoriamente</p>'
                            . '</div>';
                    }

                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar el detalle-pedido</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar el pedido</p>'
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

    public function eliminar_detalle_pedido_tinturado(Request $request)
    {
        $success = true;
        if ($request->id_detalle_pedido != '') {
            $det_pedido = DetallePedido::find($request->id_detalle_pedido);
            $pedido = $det_pedido->pedido;
            if ($det_pedido != '' && $pedido != '') {
                if (count($pedido->detalles) > 1) {
                    $det_pedido->delete();
                } else {
                    $pedido->delete();
                }
                $msg = '<div class="alert alert-success text-center">Se ha realizado la operación satisfactoriamente</div>';
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">Faltan datos necesarios para realizar la operación</div>';
            }
        } else {
            $success = false;
            $msg = '<div class="alert alert-warning text-center">El detalle del pedido es obligatorio</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success,
        ];
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

    public function listar_especificaciones_x_cliente(Request $request)
    {
        $cliente = Cliente::find($request->id_cliente);
        return view('adminlte.gestion.postcocecha.pedidos_ventas.partials._especificacion_orden_semanal', [
            'cliente' => $cliente,
        ]);
    }

    public function distribuir_pedido_tinturado(Request $request)
    {
        $det_ped = DetallePedido::find($request->id_det_ped);
        $pedido = $det_ped->pedido;
        $last_distr = $pedido->getLastDistribucion();

        return view('adminlte.gestion.postcocecha.pedidos_ventas.forms._distribucion', [
            'data' => $request->all(),
            'last_distr' => $last_distr,
            'det_ped' => $det_ped,
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