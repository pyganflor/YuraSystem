<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\ClientePedidoEspecificacion;
use yura\Modelos\DetalleEspecificacionEmpaque;
use yura\Modelos\Empaque;
use yura\Modelos\Especificacion;
use yura\Modelos\EspecificacionEmpaque;
use yura\Modelos\UnidadMedida;
use yura\Modelos\Variedad;

class OrdenSemanalController extends Controller
{
    public function store_orden_semanal(Request $request)
    {
        dd($request->all());
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

                /* ========== TABLA DETALLE_ESPECIFICACIONEMPAQUE ============*/
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
                } else {
                    $esp_emp->delete();
                    $especificacion->delete();

                    return [
                        'success' => false,
                        'mensaje' => '<div class="alert alert-info text-center">No se ha podido crear el detalle-especificación-empaque</div>',
                    ];
                }
            } else {
                $especificacion->delete();
                return [
                    'success' => false,
                    'mensaje' => '<div class="alert alert-info text-center">No se ha podido crear la especificación-empaque</div>',
                ];
            }
        } else {
            return [
                'success' => false,
                'mensaje' => '<div class="alert alert-info text-center">No se ha podido crear la especificación</div>',
            ];
        }
    }
}