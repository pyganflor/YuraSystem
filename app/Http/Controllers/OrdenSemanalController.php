<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\Cliente;
use yura\Modelos\ClientePedidoEspecificacion;
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
        $especificacion = new Especificacion();
        $descripcion = $request->cantidad_cajas . ' ' . explode('|', Empaque::find($request->id_empaque)->nombre)[0] . ' de ' .
            $request->cantidad_ramos . ' ramos ' . ClasificacionRamo::find($request->id_clasificacion_ramo)->nombre .
            ClasificacionRamo::find($request->id_clasificacion_ramo)->unidad_medida->siglas . ' ' . Variedad::find($request->id_variedad)->siglas . ' ' .
            explode('|', Empaque::find($request->id_empaque_e)->nombre)[0] . ' ' .
            explode('|', Empaque::find($request->id_empaque_p)->nombre)[0] . ' ' . $request->tallos_x_ramos . ' ' .
            $request->longitud_ramo . (UnidadMedida::find($request->id_unidad_medida) != '' ? UnidadMedida::find($request->id_unidad_medida)->siglas : '');
        $especificacion->nombre = $especificacion->descripcion = espacios($descripcion);
        $especificacion->tipo = 'O';

        if ($especificacion->save()) {
            $especificacion = Especificacion::All()->last();
            bitacora('especificacion', $especificacion->id_especificacion, 'I', 'Insercion de una nueva especificacion');

            /* =========== TABLA ESPECIFICACION_EMPAQUE ==========*/
            $esp_emp = new EspecificacionEmpaque();
            $esp_emp->id_especificacion = $especificacion->id_especificacion;
            $esp_emp->id_empaque = $request->id_empaque;
            $esp_emp->cantidad = $request->cantidad_cajas;

            if ($esp_emp->save()) {
                $esp_emp = EspecificacionEmpaque::All()->last();
                bitacora('especificacion_empaque', $esp_emp->id_especificacion_empaque, 'I', 'Insercion de una nueva especificacion-empaque');

                /* ========== TABLA DETALLE_ESPECIFICACION_EMPAQUE ============*/
                $det_espemp = new DetalleEspecificacionEmpaque();
                $det_espemp->id_especificacion_empaque = $esp_emp->id_especificacion_empaque;
                $det_espemp->id_variedad = $request->id_variedad;
                $det_espemp->id_clasificacion_ramo = $request->id_clasificacion_ramo;
                $det_espemp->cantidad = $request->cantidad_ramos;
                $det_espemp->id_empaque_e = $request->id_empaque_e;
                $det_espemp->id_empaque_p = $request->id_empaque_p;
                $det_espemp->tallos_x_ramos = $request->tallos_x_ramos;
                $det_espemp->longitud_ramo = $request->longitud_ramo;
                $det_espemp->id_unidad_medida = $request->id_unidad_medida;

                if ($det_espemp->save()) {
                    $det_espemp = DetalleEspecificacionEmpaque::All()->last();
                    bitacora('detalle_especificacionempaque', $det_espemp->id_detalle_especificacionempaque, 'I', 'Insercion de una nueva detalle-especificación-empaque');

                    /* =========== TABLA CLIENTE_PEDIDO_ESPECIFICACION ==========*/
                    $cli_ped_esp = new ClientePedidoEspecificacion();
                    $cli_ped_esp->id_especificacion = $especificacion->id_especificacion;
                    $cli_ped_esp->id_cliente = $request->id_cliente;

                    if ($cli_ped_esp->save()) {
                        $cli_ped_esp = ClientePedidoEspecificacion::All()->last();
                        bitacora('cliente_pedido_especificacion', $cli_ped_esp->id_cliente_pedido_especificacion, 'I', 'Insercion de una nueva cliente-pedido-especificación');

                        /* ========== TABLA PEDIDO ============*/
                        $pedido = new Pedido();
                        $pedido->id_cliente = $request->id_cliente;
                        $pedido->descripcion = $descripcion;
                        $pedido->variedad = $request->id_variedad;  // optimizar
                        $pedido->fecha_pedido = $request->fecha_pedido;
                        $pedido->tipo_especificacion = 'O';

                        if ($pedido->save()) {
                            $pedido = Pedido::All()->last();
                            bitacora('pedido', $pedido->id_pedido, 'I', 'Insercion de un nuevo pedido');

                            /* ========= TABLA DETALLE_PEDIDO ===========*/
                            $det_pedido = new DetallePedido();
                            $det_pedido->id_pedido = $pedido->id_pedido;
                            $det_pedido->id_cliente_especificacion = $cli_ped_esp->id_cliente_pedido_especificacion;
                            $det_pedido->id_agencia_carga = $request->id_agencia_carga;
                            $det_pedido->cantidad = 1;

                            if ($det_pedido->save()) {
                                $det_pedido = DetallePedido::All()->last();
                                bitacora('detalle_pedido', $det_pedido->id_detalle_pedido, 'I', 'Insercion de un nuevo detalle-pedido');

                                /* =========== TABLA MARCACION ===========*/
                                $cant_marcaciones = count($request->marcaciones);
                                $cant_colores = count($request->colores);
                                $marcas_creadas = [];
                                for ($i = 0; $i < $cant_marcaciones; $i++) {
                                    if ($request->marcaciones[$i] != '' && $request->matrix[$i][$cant_colores] > 0) {
                                        $marcacion = new Marcacion();
                                        $marcacion->nombre = $request->marcaciones[$i];
                                        $marcacion->cantidad = $request->matrix[$i][$cant_colores];
                                        $marcacion->id_especificacion_empaque = $esp_emp->id_especificacion_empaque;

                                        if ($marcacion->save()) {
                                            $marcacion = Marcacion::All()->last();
                                            bitacora('marcacion', $marcacion->id_marcacion, 'I', 'Insercion de una nueva marcacion');
                                            array_push($marcas_creadas, $marcacion);

                                            /* ========= TABLA COLORACION ===========*/
                                            $colores_creados = [];
                                            for ($c = 0; $c < $cant_colores; $c++) {
                                                if ($request->matrix[$i][$c] != '' && $request->colores[$c]['nombre'] != '') {
                                                    $coloracion = new Coloracion();
                                                    $coloracion->id_marcacion = $marcacion->id_marcacion;
                                                    $coloracion->nombre = $request->colores[$c]['nombre'];
                                                    $coloracion->fondo = $request->colores[$c]['fondo'];
                                                    $coloracion->texto = $request->colores[$c]['texto'];
                                                    $coloracion->cantidad = $request->matrix[$i][$c];

                                                    if ($coloracion->save()) {
                                                        $coloracion = Coloracion::All()->last();
                                                        bitacora('coloracion', $coloracion->id_coloracion, 'I', 'Insercion de una nueva coloracion');
                                                        array_push($colores_creados, $coloracion);
                                                    } else {
                                                        foreach ($colores_creados as $color_creado)
                                                            $color_creado->delete();
                                                        foreach ($marcas_creadas as $marc_creada)
                                                            $marc_creada->delete();
                                                        $pedido->delete();
                                                        $cli_ped_esp->delete();
                                                        $det_espemp->delete();
                                                        $esp_emp->delete();
                                                        $especificacion->delete();

                                                        return [
                                                            'id_pedido' => '',
                                                            'success' => false,
                                                            'mensaje' => '<div class="alert alert-warning text-center">Ha ocurrido un problema con una coloración</div>',
                                                        ];
                                                    }
                                                }
                                            }
                                        } else {
                                            foreach ($marcas_creadas as $marc_creada)
                                                $marc_creada->delete();
                                            $pedido->delete();
                                            $cli_ped_esp->delete();
                                            $det_espemp->delete();
                                            $esp_emp->delete();
                                            $especificacion->delete();

                                            return [
                                                'id_pedido' => '',
                                                'success' => false,
                                                'mensaje' => '<div class="alert alert-warning text-center">Ha ocurrido un problema con una marcación</div>',
                                            ];
                                        }
                                    }
                                }

                                return [
                                    'id_pedido' => $pedido->id_pedido,
                                    'success' => true,
                                    'mensaje' => '<div class="alert alert-success text-center">' .
                                        'Se ha guardado toda la información satisfactoriamente'
                                        . '</div>',
                                ];

                            } else {
                                $pedido->delete();
                                $cli_ped_esp->delete();
                                $det_espemp->delete();
                                $esp_emp->delete();
                                $especificacion->delete();

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
                            $especificacion->delete();

                            return [
                                'id_pedido' => '',
                                'success' => false,
                                'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear el pedido</div>',
                            ];
                        }
                    } else {
                        $det_espemp->delete();
                        $esp_emp->delete();
                        $especificacion->delete();

                        return [
                            'id_pedido' => '',
                            'success' => false,
                            'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear el cliente-pedido-especificación</div>',
                        ];
                    }
                } else {
                    $esp_emp->delete();
                    $especificacion->delete();

                    return [
                        'id_pedido' => '',
                        'success' => false,
                        'mensaje' => '<div class="alert alert-warning text-center">No se ha podido crear el detalle-especificación-empaque</div>',
                    ];
                }
            } else {
                $especificacion->delete();
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
    }

    public function buscar_agencia_carga(Request $request)
    {
        $listado = Cliente::find($request->id_cliente)->cliente_agencia_carga;
        return view('adminlte.gestion.postcocecha.pedidos_ventas.partials._select_agencias_carga', [
            'listado' => $listado
        ]);
    }

    public function distribuir_orden_semanal(Request $request)
    {
        $pedido = Pedido::find($request->id_pedido);
        return view('adminlte.gestion.postcocecha.pedidos_ventas.partials.distribucion_orden_semanal', [
            'pedido' => $pedido
        ]);
    }

    /* ================ PEDIDOS PERSONALIZDOS ================*/
    public function add_pedido_personalizado(Request $request)
    {
        return view('adminlte.gestion.postcocecha.pedidos_ventas.partials.add_pedido_personalizado', [
            'clientes' => Cliente::All()->where('estado', '=', 1),
            'cajas' => Empaque::All()->where('estado', '=', 1)->where('tipo', '=', 'C'),
            'calibres' => getCalibresRamo(),
            'variedades' => getVariedades(),
            'envolturas' => Empaque::All()->where('estado', '=', 1)->where('tipo', '=', 'E'),
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
                /* ========= TABLA ESPECIFICACION ==========*/
                $texto = $item['cantidad_piezas'] . ' ' . explode('|', Empaque::find($item['id_empaque'])->nombre)[0] . ' de ' .
                    $item['cantidad_ramos'] . ' ramos ' . ClasificacionRamo::find($item['id_clasificacion_ramo'])->nombre .
                    ClasificacionRamo::find($item['id_clasificacion_ramo'])->unidad_medida->siglas . ' ' . Variedad::find($item['id_variedad'])->siglas . ' ' .
                    explode('|', Empaque::find($item['id_empaque_e'])->nombre)[0] . ' ' .
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
                    $esp_emp->cantidad = $item['cantidad_piezas'];

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
                        $det_esp_emp->id_empaque_e = $item['id_empaque_e'];
                        $det_esp_emp->id_empaque_p = $item['id_empaque_p'];
                        $det_esp_emp->tallos_x_ramos = $item['tallos_x_ramo'];
                        $det_esp_emp->longitud_ramo = $item['longitud_ramo'];
                        $det_esp_emp->id_unidad_medida = $item['id_unidad_medida'];

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
                                $pedido->descripcion = $texto;
                                $pedido->variedad = $item['id_variedad'];  // optimizar
                                $pedido->fecha_pedido = $request->fecha_pedido;
                                if ($item['check_make_especificacion'] == 'true')
                                    $pedido->tipo_especificacion = 'N';
                                else
                                    $pedido->tipo_especificacion = 'O';

                                if ($pedido->save()) {
                                    $pedido = Pedido::All()->last();
                                    bitacora('pedido', $pedido->id_pedido, 'I', 'Insercion de un nuevo pedido');
                                    array_push($creados, $pedido);

                                    /* ========= TABLA DETALLE_PEDIDO ===========*/
                                    $det_pedido = new DetallePedido();
                                    $det_pedido->id_pedido = $pedido->id_pedido;
                                    $det_pedido->id_cliente_especificacion = $cli_ped_esp->id_cliente_pedido_especificacion;
                                    $det_pedido->id_agencia_carga = $item['id_agencia_carga'];
                                    $det_pedido->cantidad = 1;

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