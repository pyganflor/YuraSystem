<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use yura\Jobs\ProyeccionUpdateSemanal;
use yura\Jobs\UpdateSaldosProyVentaSemanal;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\Cliente;
use yura\Modelos\ClientePedidoEspecificacion;
use yura\Modelos\Color;
use yura\Modelos\Coloracion;
use yura\Modelos\Comprobante;
use yura\Modelos\DetalleEnvio;
use yura\Modelos\DetalleEspecificacionEmpaque;
use yura\Modelos\DetallePedido;
use yura\Modelos\DetallePedidoDatoExportacion;
use yura\Modelos\Distribucion;
use yura\Modelos\DistribucionColoracion;
use yura\Modelos\Empaque;
use yura\Modelos\Envio;
use yura\Modelos\Especificacion;
use yura\Modelos\EspecificacionEmpaque;
use yura\Modelos\Marcacion;
use yura\Modelos\MarcacionColoracion;
use yura\Modelos\Pedido;
use yura\Modelos\UnidadMedida;
use yura\Modelos\Variedad;
use yura\Http\Controllers\ComprobanteController;

class OrdenSemanalController extends Controller
{
    public function store_orden_semanal(Request $request)
    {
        // dd($request->id_configuracion_empresa);
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
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
                            $pedido->id_configuracion_empresa = $request->id_configuracion_empresa;

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
            $pedido->id_configuracion_empresa = $request->id_configuracion_empresa;
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
                foreach ($arreglo_variedades as $i => $v)
                    if ($i > 0)
                        $variedades .= '|' . $v;

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

        $semana = getSemanaByDate($request->fecha_pedido)->codigo;
        //UpdateSaldosProyVentaSemanal::dispatch($semana, 0)->onQueue('update_saldos_proy_venta_semanal');
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
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $pedido = Pedido::find($request->id_pedido);
        $have_next = false;
        if (count($pedido->detalles) > $request->pos_det_ped + 1)
            $have_next = true;
        return view('adminlte.gestion.postcocecha.pedidos_ventas.forms.orden_semanal', [
            'pedido' => $pedido,
            'pos_det_ped' => $request->pos_det_ped,
            'have_next' => $have_next,
            'have_prev' => $request->pos_det_ped > 0,
            'listar_resumen_pedido' => $request->listar_resumen_pedido
        ]);
    }

    public function update_orden_tinturada(Request $request)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
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
            $p = getPedido($request->id_pedido);
            //dd($p->envios[0]->comprobante);
            $pedido = Pedido::find($request->id_pedido);
            $pedido->fecha_pedido = $request->fecha_pedido;

            if (isset($p->envios[0]->comprobante)) {
                $e = getEnvio($p->envios[0]->id_envio);
                $c = getComprobante($p->envios[0]->comprobante->id_comprobante);
                $id_aerolinea = $e->detalles[0]->id_aerolinea;
                $objComprobante = Comprobante::find($c->id_comprobante);
                $objComprobante->id_envio = null;
                $objComprobante->habilitado = false;
                $objComprobante->rehusar = true;
                if($objComprobante->save()){
                    $archivo_generado = env('PATH_XML_FIRMADOS') . '/facturas/' . $c->clave_acceso . ".xml";
                    $archivo_firmado = env('PATH_XML_GENERADOS') . '/facturas/' . $c->clave_acceso . ".xml";
                    if (file_exists($archivo_generado)) unlink($archivo_generado);
                    if (file_exists($archivo_firmado)) unlink($archivo_firmado);
                };
                $pedido->clave_acceso_temporal = $c->secuencial;
                $pedido->id_comprobante_temporal = $c->id_comprobante;
                $pedido->id_configuracion_empresa = $p->id_configuracion_empresa;
            }

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

                    if (isset($e)) {
                        $envio->guia_madre = $e->guia_madre;
                        $envio->guia_hija = $e->guia_hija;
                        $envio->dae = $e->dae;
                        $envio->email = $e->email;
                        $envio->telefono = $e->telefono;
                        $envio->direccion = $e->direccion;
                        $envio->codigo_pais = $e->codigo_pais;
                        $envio->almacen = $e->almacen;
                        $envio->codigo_dae = $e->codigo_dae;
                    }

                    if ($envio->save()) {
                        $envio = Envio::All()->last();

                        bitacora('envio', $envio->id_envio, 'I', 'Insercion de un nuevo envio');

                        foreach ($pedido->detalles as $det) {
                            $det_envio = new DetalleEnvio();
                            $det_envio->id_envio = $envio->id_envio;
                            $det_envio->id_especificacion = $det->cliente_especificacion->id_especificacion;
                            $det_envio->cantidad = $det->cantidad;
                            if (isset($e)) $det_envio->id_aerolinea = $id_aerolinea;

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

                        if (isset($objComprobante)) {
                            $data_actualizar_factura =[
                                'id_envio' => $envio->id_envio,
                                'codigo_pais' => $envio->codigo_pais,
                                'dae'=> $envio->dae,
                                'fecha_envio'=>$envio->fecha_envio,
                                'pais'=> getPais($envio->codigo_pais)->nombre,
                                'update'=>'true',
                                'id_comprobante'=>$objComprobante->id_comprobante,
                                'fecha_pedidos_search'=> $envio->pedido->fecha_pedido,
                                'cant_variedades'=>$envio->pedido->catntidad_det_esp_emp(),
                            ];
                            ComprobanteController::actualizar_comprobante_factura($data_actualizar_factura);
                        }
                        //LLAMAR A LA FUNCIÓN ESTÁTICA PARA ACTUALIZAR LA FACTURA

                    } else {
                        $pedido->delete();
                        return [
                            'id_pedido' => '',
                            'success' => false,
                            'mensaje' => '<div class="alert alert-warning text-center error">No se ha podido crear el envío</div>',
                        ];
                    }

                    /* ======== DATOS EXPORTACION ========= */

                    if ($request->has('arreglo_dat_exp') && count($request->arreglo_dat_exp) > 0)
                        foreach ($request->arreglo_dat_exp as $dat_exp) {
                            if ($dat_exp['valor'] != '') {
                                $det_datexp = new DetallePedidoDatoExportacion();
                                $det_datexp->id_detalle_pedido = $det_pedido->id_detalle_pedido;
                                $det_datexp->id_dato_exportacion = $dat_exp['id_dat_exp'];
                                $det_datexp->valor = $dat_exp['valor'];

                                if ($det_datexp->save()) {
                                    $det_datexp = DetallePedidoDatoExportacion::All()->last();
                                    bitacora('detallepedido_datoexportacion', $det_datexp->id_detallepedido_datoexportacion, 'I', 'Insercion de un nuevo detallepedido_datoexportacion');
                                } else {
                                    return [
                                        'success' => false,
                                        'mensaje' => '<div class="alert alert-warning text-center">' .
                                            '<p> Ha ocurrido un problema al guardar la marcación ' . $dat_exp['valor'] . '</p>'
                                            . '</div>'
                                    ];
                                }
                            }
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
        $semana = getSemanaByDate($request->fecha_pedido)->codigo;
        //UpdateSaldosProyVentaSemanal::dispatch($semana, 0)->onQueue('update_saldos_proy_venta_semanal');
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

    public function auto_distribuir_pedido_tinturado(Request $request)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        /* =================== OBTENER DISTRIBUCION ================= */
        $det_ped = DetallePedido::find($request->id_det_ped);
        $array_marc = [];
        foreach ($request->arreglo_esp_emp as $esp_emp) {
            if ($esp_emp['id_esp_emp'] == $request->id_esp_emp) {
                $esp_empaque = EspecificacionEmpaque::find($esp_emp['id_esp_emp']);
                foreach ($esp_emp['marcaciones'] as $marc) {
                    $marca = Marcacion::find($marc['id']);

                    $array_distr = [];
                    for ($x = 1; $x <= $marca->piezas; $x++) array_push($array_distr, '');

                    $array_det_esp = [];
                    foreach ($esp_empaque->detalles as $det_esp) {
                        $array_marc_col = [];
                        $ramos = 0;
                        foreach ($det_ped->coloracionesByEspEmp($esp_emp['id_esp_emp']) as $pos_col => $color) {
                            $mc = $marca->getMarcacionColoracionByDetEsp($color->id_coloracion, $det_esp->id_detalle_especificacionempaque);
                            if ($mc != '') {
                                $array_marc_col[] = [
                                    'id' => $mc->id_marcacion_coloracion,
                                    'cantidad' => $mc->cantidad,
                                ];
                                $ramos += $mc->cantidad;
                            } else {
                                $array_marc_col[] = [
                                    'id' => '',
                                    'cantidad' => 0,
                                ];
                            }
                        }

                        for ($i = 0; $i < $marca->piezas; $i++) {
                            $meta = $det_esp->cantidad; // ramos x caja
                            $array_distcol = [];
                            foreach ($array_marc_col as $pos => $item) {
                                if ($meta > 0) {
                                    if ($item['cantidad'] >= $meta) {
                                        $item['cantidad'] = $item['cantidad'] - $meta;
                                        $array_marc_col[$pos]['cantidad'] = $item['cantidad'];
                                        $array_distcol[] = [
                                            'mc' => $item['id'],
                                            'cantidad' => $meta
                                        ];
                                        $ramos -= $meta;
                                        $meta = 0;
                                    } else {
                                        $meta -= $item['cantidad'];
                                        $array_distcol[] = [
                                            'mc' => $item['id'],
                                            'cantidad' => $item['cantidad']
                                        ];
                                        $ramos -= $item['cantidad'];
                                        $item['cantidad'] = 0;
                                        $array_marc_col[$pos]['cantidad'] = $item['cantidad'];
                                    }
                                } else
                                    $array_distcol[] = [
                                        'mc' => $item['id'],
                                        'cantidad' => 0
                                    ];
                            }
                            $array_distr[$i] = $array_distcol;
                        }

                        array_push($array_det_esp, [
                            'det_esp' => $det_esp->id_detalle_especificacionempaque,
                            'arreglo' => $array_distr
                        ]);
                    }

                    array_push($array_marc, [
                        'marca' => $marca,
                        'array' => $array_det_esp,
                    ]);
                }
            }
        }

        /* =================== GUARDAR DISTRIBUCION ================= */
        $last_distr = $det_ped->pedido->getLastDistribucion();
        if ($last_distr == '') $last_distr = 0;
        else $last_distr = $last_distr->pos_pieza;
        foreach ($array_marc as $marc) {
            for ($d = 0; $d < $marc['marca']->piezas; $d++) {
                $last_distr++;
                $distr = new Distribucion();
                $distr->id_marcacion = $marc['marca']->id_marcacion;
                $distr->ramos = intval($marc['marca']->ramos / $marc['marca']->piezas);
                $distr->pos_pieza = $last_distr;
                $distr->piezas = 1;

                $distr->save();
                $distr = Distribucion::All()->last();
                bitacora('distribucion', $distr->id_distribucion, 'I', 'Creacion de una nueva distribucion');

                foreach ($marc['array'] as $array) {
                    foreach ($array['arreglo'][$d] as $item) {
                        $dc = new DistribucionColoracion();
                        $dc->id_distribucion = $distr->id_distribucion;
                        $dc->id_marcacion_coloracion = $item['mc'];
                        $dc->cantidad = $item['cantidad'];

                        $dc->save();
                        $dc = DistribucionColoracion::All()->last();
                        bitacora('distribucion_coloracion', $dc->id_distribucion_coloracion, 'I', 'Creacion de una nueva id_distribucion_coloracion');
                    }
                }
            }
        }
        return [
            'mensaje' => '<div class="alert alert-success text-center">Se han distribuido satisfactoriamente los ramos del pedido</div>',
            'success' => true,
        ];
    }

    public function guardar_distribucion(Request $request)
    {
        if (count($request->arreglo_esp_emp) > 0) {
            foreach ($request->arreglo_esp_emp as $esp_emp) {
                foreach ($esp_emp['marcaciones'] as $marc) {
                    Marcacion::find($marc['id_marcacion'])->eliminarDistribuciones();

                    /* =========== DISTRIBUCION =========== */
                    foreach ($marc['distribuciones'] as $distr) {
                        $distribucion = new Distribucion();
                        $distribucion->id_marcacion = $marc['id_marcacion'];
                        $distribucion->ramos = $distr['ramos'];
                        $distribucion->piezas = $distr['piezas'];
                        $distribucion->pos_pieza = $distr['pos_pieza'];

                        if ($distribucion->save()) {
                            $distribucion = Distribucion::All()->last();
                            bitacora('distribucion', $distribucion->id_distribucion, 'I', 'Inserción de una nueva distribucion');

                            /* =========== DISTRIBUCION_COLORACION =========== */
                            foreach ($distr['coloraciones'] as $col) {
                                foreach ($col['detalles_esp'] as $det_esp) {
                                    $marc_col = MarcacionColoracion::All()->where('id_marcacion', $marc['id_marcacion'])
                                        ->where('id_coloracion', $col['id_coloracion'])
                                        ->where('id_detalle_especificacionempaque', $det_esp['id_det_esp'])->first();

                                    $distr_col = new DistribucionColoracion();
                                    $distr_col->id_marcacion_coloracion = $marc_col->id_marcacion_coloracion;
                                    $distr_col->id_distribucion = $distribucion->id_distribucion;
                                    $distr_col->cantidad = $det_esp['cant'];

                                    if ($distr_col->save()) {
                                        $distr_col = DistribucionColoracion::All()->last();
                                        bitacora('distribucion_coloracion', $distr_col->id_distribucion_coloracion, 'I', 'Inserción de una nueva distribucion-coloracion');
                                    } else {
                                        return [
                                            'mensaje' => '<div class="alert alert-warning text-center">'
                                                . 'Ha ocurrido un problema al guardar parte de la información</div>',
                                            'success' => false,
                                        ];
                                    }
                                }
                            }
                        } else {
                            return [
                                'mensaje' => '<div class="alert alert-warning text-center">'
                                    . 'Ha ocurrido un problema al guardar la distribución</div>',
                                'success' => false,
                            ];
                        }
                    }
                }
            }

            return [
                'mensaje' => '<div class="alert alert-success text-center">'
                    . 'Se ha guardado la distribución satisfactoriamente</div>',
                'success' => true,
            ];
        } else {
            return [
                'mensaje' => '<div class="alert alert-warning text-center">'
                    . 'No se han encontrado especificaciones</div>',
                'success' => false,
            ];
        }
    }

    public function ver_distribucion(Request $request)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $det_ped = DetallePedido::find($request->id_det_ped);

        $list_esp_emp = [];
        foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $pos_esp_emp => $esp_emp) {
            $list_coloracionesByEspEmp = $det_ped->coloracionesByEspEmp($esp_emp->id_especificacion_empaque);
            $list_marcacionesByEspEmp = [];
            foreach ($det_ped->marcacionesByEspEmp($esp_emp->id_especificacion_empaque) as $pos_marc => $marc) {
                $list_distribuciones = [];
                foreach ($marc->distribuciones as $pos_distr => $distr) {
                    $coloraciones = [];
                    foreach ($list_coloracionesByEspEmp as $pos_col => $col) {
                        $detalles = [];
                        foreach ($esp_emp->detalles as $pos_det_esp => $det_esp) {
                            array_push($detalles, [
                                'det_esp' => $det_esp,
                                'distr_col' => $distr->getDistribucionMarcacionByMarcCol($marc->getMarcacionColoracionByDetEsp($col->id_coloracion, $det_esp->id_detalle_especificacionempaque)->id_marcacion_coloracion)
                            ]);
                        }
                        array_push($coloraciones, [
                            'col' => $col,
                            'detalles' => $detalles
                        ]);
                    }
                    array_push($list_distribuciones, [
                        'distr' => $distr,
                        'coloraciones' => $coloraciones
                    ]);
                }
                array_push($list_marcacionesByEspEmp, [
                    'marc' => $marc,
                    'distribuciones' => $list_distribuciones
                ]);
            }
            array_push($list_esp_emp, [
                'esp_emp' => $esp_emp,
                'coloraciones' => $list_coloracionesByEspEmp,
                'marcaciones' => $list_marcacionesByEspEmp,
            ]);
        }

        //dd($list_esp_emp);

        return view('adminlte.gestion.postcocecha.pedidos_ventas.forms._ver_distribucion', [
            'det_ped' => $det_ped,
            'list_esp_emp' => $list_esp_emp,
        ]);
    }

    public function quitar_distribuciones(Request $request)
    {
        $pedido = Pedido::find($request->id_ped);
        foreach ($pedido->detalles as $det_ped) {
            foreach ($det_ped->marcaciones as $marc) {
                foreach ($marc->distribuciones as $distr) {
                    $distr->delete();
                }
            }
        }
        return [
            'mensaje' => '<div class="alert alert-success text-center">Se han eliminado las distribuciones del pedido satisfactoriamente</div>',
            'success' => true
        ];
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
