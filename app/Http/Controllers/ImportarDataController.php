<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use yura\Modelos\HistoricoVentas;
use yura\Modelos\ClasificacionUnitaria;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\Cosecha;
use yura\Modelos\DesgloseRecepcion;
use yura\Modelos\DetalleClasificacionVerde;
use yura\Modelos\GrupoMenu;
use yura\Modelos\Recepcion;
use yura\Modelos\RecepcionClasificacionVerde;
use yura\Modelos\Submenu;

class ImportarDataController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.importar_data.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'grupos_menu' => GrupoMenu::All()
        ]);
    }

    public function importar_cosecha(Request $request)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $valida = Validator::make($request->all(), [
            'file_postcosecha' => 'required',
            'id_modulo_postcosecha' => 'required',
            'hora_inicio_postcosecha' => 'required',
            'personal_postcosecha' => 'required',
        ]);
        $msg = '';
        $success = true;
        if (!$valida->fails()) {

            $document = PHPExcel_IOFactory::load($request->file_postcosecha);
            $activeSheetData = $document->getActiveSheet()->toArray(null, true, true, true);

            $titles = $activeSheetData[1];
            foreach ($activeSheetData as $pos_row => $row) {
                if ($pos_row > 1) {
                    //dd($titles, $row, $request->all(),$activeSheetData);
                    if ($row['A'] != '') {
                        $fecha_cruda = explode('/', $row['A']);
                        $fecha = $fecha_cruda[2];
                        $fecha .= strlen($fecha_cruda[0]) == 1 ? '-0' . $fecha_cruda[0] : '-' . $fecha_cruda[0];
                        $fecha .= strlen($fecha_cruda[1]) == 1 ? '-0' . $fecha_cruda[1] : '-' . $fecha_cruda[1];

                        /* ============ COSECHA ============== */
                        if (count(Cosecha::All()->where('fecha_ingreso', $fecha)) == 0) {
                            $cosecha = new Cosecha();
                            $cosecha->fecha_ingreso = $fecha;
                            $cosecha->personal = $request->personal_postcosecha;
                            $cosecha->hora_inicio = $request->hora_inicio_postcosecha;

                            if ($cosecha->save()) {
                                $cosecha = Cosecha::All()->last();
                                bitacora('cosecha', $cosecha->id_cosecha, 'I', 'Insercion de una nueva cosecha');

                                $semana = getSemanaByDate($fecha);
                                /* ============= RECEPCION =========== */
                                $recepcion = new Recepcion();
                                $recepcion->id_semana = $semana->id_semana;
                                $recepcion->fecha_ingreso = $fecha . ' ' . $request->hora_inicio_postcosecha;
                                $recepcion->id_cosecha = $cosecha->id_cosecha;

                                /* ============= CLASIFICACION_VERDE =========== */
                                $verde = new ClasificacionVerde();
                                $verde->id_semana = $semana->id_semana;
                                $verde->fecha_ingreso = $fecha;
                                $verde->hora_inicio = $request->hora_inicio_postcosecha;
                                $verde->personal = $request->personal_postcosecha;
                                $verde->activo = $request->has('activo_postcosecha') ? 1 : 0;

                                if ($recepcion->save() && $verde->save()) {
                                    $recepcion = Recepcion::All()->last();
                                    $verde = ClasificacionVerde::All()->last();
                                    bitacora('recepcion', $recepcion->id_recepcion, 'I', 'Insercion de una nueva recepcion');
                                    bitacora('clasificacion_verde', $verde->id_clasificacion_verde, 'I', 'Insercion de una nueva clasificacion verde');

                                    /* ========== RECEPCION_CLASIFICACION_VERDE========== */
                                    $recep_verde = new RecepcionClasificacionVerde();
                                    $recep_verde->id_recepcion = $recepcion->id_recepcion;
                                    $recep_verde->id_clasificacion_verde = $verde->id_clasificacion_verde;

                                    if ($recep_verde->save()) {
                                        $recep_verde = RecepcionClasificacionVerde::All()->last();
                                        bitacora('recepcion_clasificacion_verde', $recep_verde->id_recepcion_clasificacion_verde, 'I', 'Insercion de una nueva recepcion_clasificacion_verde');

                                        $total_tallos = 0;
                                        $rest_tallos = 0;
                                        foreach ($row as $pos_col => $col) {
                                            if (explode('|', $titles[$pos_col])[2] == 'T') { // T => Total tallos
                                                /* ========== DESGLOSE RECEPCION ========== */
                                                $det_recep = new DesgloseRecepcion();
                                                $det_recep->id_recepcion = $recepcion->id_recepcion;
                                                $det_recep->id_variedad = substr(explode('|', $titles[$pos_col])[0], 1);
                                                $det_recep->cantidad_mallas = 1;

                                                while (substr_count($col, '.') > 1) {
                                                    $col = str_replace_first('.', '', $col);
                                                }
                                                $f = substr_count($col, '.') == 0 ? 1 : 1000;
                                                $det_recep->tallos_x_malla = $col * $f;

                                                $det_recep->id_modulo = $request->id_modulo_postcosecha;

                                                if ($det_recep->save()) {
                                                    $det_recep = DesgloseRecepcion::All()->last();
                                                    bitacora('desglose_recepcion', $det_recep->id_desglose_recepcion, 'I', 'Insercion de una nueva desglose-recepcion');
                                                } else {
                                                    $success = false;
                                                    $msg .= '<li class="error">Ocurrió un problema con un desglose-recepción del día ' . $fecha .
                                                        ' con la variedad ' . getVariedad(substr(explode('|', $titles[$pos_col])[0], 1))->nombre . '</li>';
                                                }

                                                $total_tallos = $col * $f;
                                                $rest_tallos = 0;
                                            } else if (explode('|', $titles[$pos_col])[2] == 'V') { // V => tallos por calibre unitario
                                                /* ========== DETALLE CLASIFICACION VERDE ========== */
                                                $det_verde = new DetalleClasificacionVerde();
                                                $det_verde->fecha_ingreso = $fecha . ' ' . $request->hora_inicio_postcosecha;
                                                $det_verde->id_variedad = substr(explode('|', $titles[$pos_col])[0], 1);
                                                $det_verde->id_clasificacion_unitaria = substr(explode('|', $titles[$pos_col])[1], 1);
                                                $det_verde->id_clasificacion_verde = $verde->id_clasificacion_verde;
                                                $det_verde->cantidad_ramos = 1;

                                                if ($det_verde->id_clasificacion_unitaria == 7) {   // CALIBRE USA
                                                    $det_verde->tallos_x_ramos = $total_tallos - $rest_tallos;
                                                } else {
                                                    if ($request->has('cajas_postcosecha')) { // la informacion indica cajas
                                                        $estandar = $col * getConfiguracionEmpresa()->ramos_x_caja;
                                                        if ($det_verde->id_clasificacion_unitaria == 3) {    // CALIBRE CON 20 TALLOS x RAMO
                                                            $factor = 20;
                                                        } else {
                                                            $factor = explode('|', ClasificacionUnitaria::find($det_verde->id_clasificacion_unitaria)->nombre)[1];
                                                        }
                                                        $det_verde->tallos_x_ramos = round($estandar * $factor);
                                                    } else {    // la informacion indica tallos
                                                        while (substr_count($col, '.') > 1) {
                                                            $col = str_replace_first('.', '', $col);
                                                        }
                                                        $f = substr_count($col, '.') == 0 ? 1 : 1000;
                                                        $det_verde->tallos_x_ramos = $col * $f;
                                                    }
                                                    $rest_tallos += $det_verde->tallos_x_ramos;
                                                }
                                                if ($det_verde->save()) {
                                                    $det_verde = DetalleClasificacionVerde::All()->last();
                                                    bitacora('detalle_clasificacion_verde', $det_verde->id_detalle_clasificacion_verde, 'I', 'Insercion de una nuevo detalle_clasificacion_verde');
                                                } else {
                                                    $success = false;
                                                    $msg .= '<li class="error">Ocurrió un problema con un detalle_clasificacion_verde del día ' . $fecha .
                                                        ' con ' . getVariedad(substr(explode('|', $titles[$pos_col])[0], 1))->nombre . ' ' .
                                                        explode('|', ClasificacionUnitaria::find($det_verde->id_clasificacion_unitaria)->nombre)[0] . '</li>';
                                                }
                                            }
                                        }

                                    } else {
                                        $success = false;
                                        $msg .= '<li class="error">Ocurrió un problema con la recepción del día ' . $fecha . '</li>';
                                    }
                                } else {
                                    $success = false;
                                    $msg .= '<li class="error">Ocurrió un problema con la recepción del día ' . $fecha . '</li>';
                                }
                            } else {
                                $success = false;
                                $msg .= '<li class="error">Ocurrió un problema con la cosecha del día ' . $fecha . '</li>';
                            }
                        } else {
                            $success = false;
                            $msg .= '<li class="error">Ya se encuentra una cosecha del día ' . $fecha . '</li>';
                        }
                    }
                }

                if ($success) {
                    $msg = '<li class="bg-green">Se ha importado el archivo satisfactoriamente</li>';
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

    public function importar_venta(Request $request)
    {
        //dd($request->all());
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $valida = Validator::make($request->all(), [
            'file_ventas' => 'required',
            'variedad_ventas' => 'required',
            'campo_ventas' => 'required',
            'anno_ventas' => 'required',
        ]);
        $msg = '';
        $success = true;
        if (!$valida->fails()) {

            $document = PHPExcel_IOFactory::load($request->file_ventas);
            $activeSheetData = $document->getActiveSheet()->toArray(null, true, true, true);

            $titles = $activeSheetData[1];
            foreach ($activeSheetData as $pos_row => $row) {
                if ($pos_row > 1) {
                    if ($row['A'] != '') {
                        $id_cliente = intval($row['A']);
                        foreach ($row as $pos_col => $col) {
                            if ($col > 0) {
                                if ($pos_col != 'A') {
                                    $historico = HistoricoVentas::All()
                                        ->where('id_cliente', $id_cliente)
                                        ->where('id_variedad', $request->variedad_ventas)
                                        ->where('mes', $titles[$pos_col])
                                        ->where('anno', $request->anno_ventas)
                                        ->first();

                                    if ($historico != '') {
                                        dd(1);
                                        if ($request->campo_ventas == 'V')
                                            $historico->valor += str_replace(',', '', $col);
                                        if ($request->campo_ventas == 'F')
                                            $historico->cajas_fisicas += str_replace(',', '', $col);
                                        if ($request->campo_ventas == 'Q')
                                            $historico->cajas_equivalentes += str_replace(',', '', $col);
                                        if ($request->campo_ventas == 'P')
                                            $historico->precio_x_ramo = round(str_replace(',', '', $col) / $historico->precio_x_ramo, 2);
                                    } else {
                                        $historico = new HistoricoVentas();
                                        $historico->id_cliente = $id_cliente;
                                        $historico->id_variedad = $request->variedad_ventas;
                                        $historico->anno = $request->anno_ventas;
                                        $historico->mes = $titles[$pos_col];
                                        if ($request->campo_ventas == 'V')
                                            $historico->valor = str_replace(',', '', $col);
                                        if ($request->campo_ventas == 'F')
                                            $historico->cajas_fisicas = str_replace(',', '', $col);
                                        if ($request->campo_ventas == 'Q')
                                            $historico->cajas_equivalentes = str_replace(',', '', $col);
                                        if ($request->campo_ventas == 'P')
                                            $historico->precio_x_ramo = str_replace(',', '', $col) / $historico->precio_x_ramo;
                                    }

                                    $historico->save();
                                }
                            }
                        }
                    }
                }

                if ($success) {
                    $msg = '<li class="bg-green">Se ha importado el archivo satisfactoriamente</li>';
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
}