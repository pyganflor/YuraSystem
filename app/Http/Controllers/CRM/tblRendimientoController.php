<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\ClasificacionBlanco;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\Cosecha;
use yura\Modelos\Semana;

class tblRendimientoController extends Controller
{
    public function inicio(Request $request)
    {
        /* ======= AÑOS ======= */
        $annos_cosecha = DB::table('cosecha')
            ->select(DB::raw('year(fecha_ingreso) as anno'))->distinct()
            ->get();
        $annos_verde = DB::table('clasificacion_verde')
            ->select(DB::raw('year(fecha_ingreso) as anno'))->distinct()
            ->get();
        $annos_blanco = DB::table('clasificacion_blanco')
            ->select(DB::raw('year(fecha_ingreso) as anno'))->distinct()
            ->get();

        $annos = [];
        foreach ($annos_cosecha as $item) {
            array_push($annos, $item->anno);
        }
        foreach ($annos_verde as $item) {
            if (!in_array($item->anno, $annos))
                array_push($annos, $item->anno);
        }
        foreach ($annos_blanco as $item) {
            if (!in_array($item->anno, $annos))
                array_push($annos, $item->anno);
        }

        return view('adminlte.crm.tbl_rendimiento.inicio', [
            'annos' => $annos
        ]);
    }

    public function filtrar_tablas(Request $request)
    {
        if ($request->annos == '')
            $annos = [date('Y')];
        else
            $annos = explode(' - ', $request->annos);

        if ($request->desde != '' && $request->hasta != '') {
            if ($request->rango == 'A') {
                $view = 'anual';
                $data = $this->getTablasByRangoAnual($request->desde, $request->hasta, $request->variedad, $annos, $request->criterio, $request->area);
            } else if ($request->rango == 'M') {
                if ($request->desde >= 1 && $request->desde <= 12 && $request->hasta >= 1 && $request->hasta <= 12 && $request->desde <= $request->hasta) {
                    $view = 'mensual';
                    $data = $this->getTablasByRangoMensual($request->desde, $request->hasta, $request->variedad, $annos, $request->criterio, $request->area);
                } else {
                    return '<div class="alert alert-warning text-center">Los meses ingresados están incorrectos</div>';
                }
            } else if ($request->rango == 'S') {
                if ($request->desde >= 1 && $request->desde <= 53 && $request->hasta >= 1 && $request->hasta <= 53 && $request->desde <= $request->hasta) {
                    $view = 'semanal';
                    $data = $this->getTablasByRangoSemanal($request->desde, $request->hasta, $request->variedad, $annos, $request->criterio, $request->area);
                } else {
                    return '<div class="alert alert-warning text-center">Las semanas ingresadas están incorrectas</div>';
                }
            } else {
                $view = 'diario';
                $data = $this->getTablasByRangoDiario($request->desde, $request->hasta, $request->variedad, $annos, $request->criterio, $request->area);
            }

            return view('adminlte.crm.tbl_rendimiento.partials.' . $view, [
                'data' => $data,
                //'acumulado' => $request->acumulado,
                'area' => $request->area,
                'criterio' => $request->criterio,
                'variedad' => $request->variedad,
                'desde' => $request->desde,
                'hasta' => $request->hasta,
            ]);
        } else {
            return '<div class="alert alert-warning text-center">Debes ingresar desde - hasta</div>';
        }
    }

    public function navegar_tabla(Request $request)
    {
        if ($request->rango == 'S') {
            $view = 'semanal';

            $variedad = $request->variedad;
            if ($variedad == '')
                $variedad = 'A';

            $data = $this->getTablasByRangoSemanal($request->desde, $request->hasta, $request->filtro_variedad, [$request->anno], $request->criterio, $request->filtro_area);
        } else {
            $view = 'diario';

            if ($request->tipo == 'M') {    // se trata de meses
                $mes = '01';
                foreach (getMeses() as $pos => $m)
                    if ($m == $request->periodo)
                        $mes = $pos + 1;

                if (strlen($mes) != 2)
                    $mes = '0' . $mes;

                $desde = $request->anno . '-' . $mes . '-' . '01';
                if (in_array($mes, ['01', '03', '05', '07', '08', '10', '12']))
                    $hasta = $request->anno . '-' . $mes . '-' . '31';
                else {
                    if ($mes == '02') {
                        if ($request->anno % 4 == 0)
                            $hasta = $request->anno . '-' . $mes . '-' . '29';
                        else
                            $hasta = $request->anno . '-' . $mes . '-' . '28';
                    } else
                        $hasta = $request->anno . '-' . $mes . '-' . '30';
                }

            } else {    // se trata de semanas
                $semana = Semana::All()->where('estado', 1)->where('codigo', substr($request->anno, 2) . $request->periodo)->first();
                $desde = $semana->fecha_inicial;
                $hasta = $semana->fecha_final;
            }

            $data = $this->getTablasByRangoDiario($desde, $hasta, $request->filtro_variedad, [$request->anno], $request->criterio, $request->filtro_area);
        }

        return view('adminlte.crm.tbl_rendimiento.partials.' . $view, [
            'data' => $data,
            //'acumulado' => $request->acumulado,
            'area' => $request->filtro_area,
            'criterio' => $request->criterio,
            'variedad' => $request->variedad,
        ]);
    }

    public function getTablasByRangoAnual($semana_inicial, $semana_final, $variedad, $annos, $criterio, $area)
    {
        sort($annos);
        $labels = $annos;
        $filas = [];
        $valores = [];

        if (strlen($semana_inicial) != 2)
            $semana_inicial = '0' . $semana_inicial;
        if (strlen($semana_final) != 2)
            $semana_final = '0' . $semana_final;

        if ($variedad == 'A') {
            foreach ($labels as $a) {
                $s_inicial = Semana::All()->where('estado', 1)->where('codigo', substr($a, 2) . $semana_inicial)->first();
                $s_final = Semana::All()->where('estado', 1)->where('codigo', substr($a, 2) . $semana_final)->first();

                $valor = 0;

                $objets = [];
                if ($s_inicial != '' && $s_final != '') {
                    /* ========= OBTENER LOS MODELOS DEL AREA INDICADA ========= */
                    if ($area == 'V') {
                        $objets = DB::table('clasificacion_verde')
                            ->select('id_clasificacion_verde as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('year(fecha_ingreso)'), '=', $a)
                            ->where(DB::raw('fecha_ingreso'), '>=', $s_inicial->fecha_inicial)
                            ->where(DB::raw('fecha_ingreso'), '<=', $s_final->fecha_final)
                            ->get();
                    } else if ($area == 'C') {
                        $objets = DB::table('cosecha')
                            ->select('id_cosecha as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('year(fecha_ingreso)'), '=', $a)
                            ->where(DB::raw('fecha_ingreso'), '>=', $s_inicial->fecha_inicial)
                            ->where(DB::raw('fecha_ingreso'), '<=', $s_final->fecha_final)
                            ->get();
                    } else {    // Blanco
                        $objets = DB::table('clasificacion_blanco')
                            ->select('id_clasificacion_blanco as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('year(fecha_ingreso)'), '=', $a)
                            ->where(DB::raw('fecha_ingreso'), '>=', $s_inicial->fecha_inicial)
                            ->where(DB::raw('fecha_ingreso'), '<=', $s_final->fecha_final)
                            ->get();
                    }
                    foreach ($objets as $obj) {
                        if ($area == 'C')
                            $model = Cosecha::find($obj->id);
                        else if ($area == 'V')
                            $model = ClasificacionVerde::find($obj->id);
                        else
                            $model = ClasificacionBlanco::find($obj->id);

                        if ($criterio == 'D') { // DESECHO
                            if ($area == 'V')
                                $valor += $model->desecho();
                            else if ($area == 'B')
                                $valor += $model->getDesecho();
                        }
                        if ($criterio == 'R') { // RENDIMIENTO
                            $valor += $model->getRendimiento();
                        }
                    }
                }

                array_push($valores, count($objets) > 0 ? round($valor / count($objets), 2) : 0);
            }

            array_push($filas, [
                'encabezado' => '',
                'valores' => $valores,
            ]);
        } else if ($variedad == 'T') {
            foreach (getVariedades() as $var) {
                $valores = [];
                foreach ($labels as $a) {
                    $s_inicial = Semana::All()->where('estado', 1)->where('codigo', substr($a, 2) . $semana_inicial)->first();
                    $s_final = Semana::All()->where('estado', 1)->where('codigo', substr($a, 2) . $semana_final)->first();

                    $valor = 0;

                    $objets = [];
                    if ($s_inicial != '' && $s_final != '') {
                        /* ========= OBTENER LOS MODELOS DEL AREA INDICADA ========= */
                        if ($area == 'V') {
                            $objets = DB::table('clasificacion_verde')
                                ->select('id_clasificacion_verde as id')->distinct()
                                ->where('estado', '=', 1)
                                ->where(DB::raw('year(fecha_ingreso)'), '=', $a)
                                ->where(DB::raw('fecha_ingreso'), '>=', $s_inicial->fecha_inicial)
                                ->where(DB::raw('fecha_ingreso'), '<=', $s_final->fecha_final)
                                ->get();
                        } else if ($area == 'C') {
                            $objets = DB::table('cosecha')
                                ->select('id_cosecha as id')->distinct()
                                ->where('estado', '=', 1)
                                ->where(DB::raw('year(fecha_ingreso)'), '=', $a)
                                ->where(DB::raw('fecha_ingreso'), '>=', $s_inicial->fecha_inicial)
                                ->where(DB::raw('fecha_ingreso'), '<=', $s_final->fecha_final)
                                ->get();
                        } else {    // Blanco
                            $objets = DB::table('clasificacion_blanco')
                                ->select('id_clasificacion_blanco as id')->distinct()
                                ->where('estado', '=', 1)
                                ->where(DB::raw('year(fecha_ingreso)'), '=', $a)
                                ->where(DB::raw('fecha_ingreso'), '>=', $s_inicial->fecha_inicial)
                                ->where(DB::raw('fecha_ingreso'), '<=', $s_final->fecha_final)
                                ->get();
                        }
                        foreach ($objets as $obj) {
                            if ($area == 'C')
                                $model = Cosecha::find($obj->id);
                            else if ($area == 'V')
                                $model = ClasificacionVerde::find($obj->id);
                            else
                                $model = ClasificacionBlanco::find($obj->id);

                            if ($criterio == 'D') { // DESECHO
                                if ($area == 'V')
                                    $valor += $model->desechoByVariedad($var->id_variedad);
                                else if ($area == 'B')
                                    $valor += $model->getDesechoByVariedad($var->id_variedad);
                            }
                            if ($criterio == 'R') { // RENDIMIENTO
                                $valor += $model->getRendimientoByVariedad($var->id_variedad);
                            }
                        }
                    }

                    array_push($valores, count($objets) > 0 ? round($valor / count($objets), 2) : 0);
                }

                array_push($filas, [
                    'encabezado' => $var,
                    'valores' => $valores,
                ]);
            }
        } else {
            foreach ($labels as $a) {
                $s_inicial = Semana::All()->where('estado', 1)->where('codigo', substr($a, 2) . $semana_inicial)->first();
                $s_final = Semana::All()->where('estado', 1)->where('codigo', substr($a, 2) . $semana_final)->first();

                $valor = 0;

                $objets = [];
                if ($s_inicial != '' && $s_final != '') {
                    /* ========= OBTENER LOS MODELOS DEL AREA INDICADA ========= */
                    if ($area == 'V') {
                        $objets = DB::table('clasificacion_verde')
                            ->select('id_clasificacion_verde as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('year(fecha_ingreso)'), '=', $a)
                            ->where(DB::raw('fecha_ingreso'), '>=', $s_inicial->fecha_inicial)
                            ->where(DB::raw('fecha_ingreso'), '<=', $s_final->fecha_final)
                            ->get();
                    } else if ($area == 'C') {
                        $objets = DB::table('cosecha')
                            ->select('id_cosecha as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('year(fecha_ingreso)'), '=', $a)
                            ->where(DB::raw('fecha_ingreso'), '>=', $s_inicial->fecha_inicial)
                            ->where(DB::raw('fecha_ingreso'), '<=', $s_final->fecha_final)
                            ->get();
                    } else {    // Blanco
                        $objets = DB::table('clasificacion_blanco')
                            ->select('id_clasificacion_blanco as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('year(fecha_ingreso)'), '=', $a)
                            ->where(DB::raw('fecha_ingreso'), '>=', $s_inicial->fecha_inicial)
                            ->where(DB::raw('fecha_ingreso'), '<=', $s_final->fecha_final)
                            ->get();
                    }
                    foreach ($objets as $obj) {
                        if ($area == 'C')
                            $model = Cosecha::find($obj->id);
                        else if ($area == 'V')
                            $model = ClasificacionVerde::find($obj->id);
                        else
                            $model = ClasificacionBlanco::find($obj->id);

                        if ($criterio == 'D') { // DESECHO
                            if ($area == 'V')
                                $valor += $model->desechoByVariedad($variedad);
                            else if ($area == 'B')
                                $valor += $model->getDesechoByVariedad($variedad);
                        }
                        if ($criterio == 'R') { // RENDIMIENTO
                            $valor += $model->getRendimientoByVariedad($variedad);
                        }
                    }
                }

                array_push($valores, count($objets) > 0 ? round($valor / count($objets), 2) : 0);
            }

            array_push($filas, [
                'encabezado' => getVariedad($variedad),
                'valores' => $valores,
            ]);
        }

        return [
            'labels' => $labels,
            'filas' => $filas,
        ];
    }

    public function getTablasByRangoMensual($mes_inicial, $mes_final, $variedad, $annos, $criterio, $area)
    {
        sort($annos);
        $labels = $annos;
        $meses = [];
        $filas = [];
        $valores = [];

        if ($variedad == 'A') { // Acumulado
            foreach ($labels as $pos => $l) {
                for ($m = $mes_inicial; $m <= $mes_final; $m++) {
                    if ($pos == 0) {
                        array_push($meses, getMeses()[$m - 1]);
                    }

                    if (strlen($m) != 2)
                        $m = '0' . $m;

                    if ($area == 'V') {
                        $objects = DB::table('clasificacion_verde')
                            ->select('id_clasificacion_verde as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                            ->where(DB::raw('month(fecha_ingreso)'), '=', $m)
                            ->get();
                    } else if ($area == 'C') {
                        $objects = DB::table('cosecha')
                            ->select('id_cosecha as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                            ->where(DB::raw('month(fecha_ingreso)'), '=', $m)
                            ->get();
                    } else {
                        $objects = DB::table('clasificacion_blanco')
                            ->select('id_clasificacion_blanco as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                            ->where(DB::raw('month(fecha_ingreso)'), '=', $m)
                            ->get();
                    }

                    $valor = 0;

                    foreach ($objects as $obj) {
                        if ($area == 'C')
                            $model = Cosecha::find($obj->id);
                        else if ($area == 'V')
                            $model = ClasificacionVerde::find($obj->id);
                        else
                            $model = ClasificacionBlanco::find($obj->id);

                        if ($criterio == 'D') { // DESECHO
                            if ($area == 'V')
                                $valor += $model->desecho();
                            else if ($area == 'B')
                                $valor += $model->getDesecho();
                        }
                        if ($criterio == 'R') { // RENDIMIENTO
                            $valor += $model->getRendimiento();
                        }
                    }

                    array_push($valores, count($objects) > 0 ? round($valor / count($objects), 2) : 0);
                }
            }

            array_push($filas, [
                'encabezado' => '',
                'valores' => $valores,
            ]);
        } else if ($variedad == 'T') {  // Todas las variedades
            foreach (getVariedades() as $pos_var => $var) {
                $valores = [];

                foreach ($labels as $pos => $l) {
                    for ($m = $mes_inicial; $m <= $mes_final; $m++) {
                        if ($pos == 0 && $pos_var == 0) {
                            array_push($meses, getMeses()[$m - 1]);
                        }

                        if (strlen($m) != 2)
                            $m = '0' . $m;

                        if ($area == 'V') {
                            $objects = DB::table('clasificacion_verde')
                                ->select('id_clasificacion_verde as id')->distinct()
                                ->where('estado', '=', 1)
                                ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                                ->where(DB::raw('month(fecha_ingreso)'), '=', $m)
                                ->get();
                        } else if ($area == 'C') {
                            $objects = DB::table('cosecha')
                                ->select('id_cosecha as id')->distinct()
                                ->where('estado', '=', 1)
                                ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                                ->where(DB::raw('month(fecha_ingreso)'), '=', $m)
                                ->get();
                        } else {
                            $objects = DB::table('clasificacion_blanco')
                                ->select('id_clasificacion_blanco as id')->distinct()
                                ->where('estado', '=', 1)
                                ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                                ->where(DB::raw('month(fecha_ingreso)'), '=', $m)
                                ->get();
                        }

                        $valor = 0;

                        foreach ($objects as $obj) {
                            if ($area == 'C')
                                $model = Cosecha::find($obj->id);
                            else if ($area == 'V')
                                $model = ClasificacionVerde::find($obj->id);
                            else
                                $model = ClasificacionBlanco::find($obj->id);

                            if ($criterio == 'D') { // DESECHO
                                if ($area == 'V')
                                    $valor += $model->desechoByVariedad($var->id_variedad);
                                else if ($area == 'B')
                                    $valor += $model->getDesechoByVariedad($var->id_variedad);
                            }
                            if ($criterio == 'R') { // RENDIMIENTO
                                $valor += $model->getRendimientoByVariedad($var->id_variedad);
                            }
                        }

                        array_push($valores, count($objects) > 0 ? round($valor / count($objects), 2) : 0);
                    }
                }

                array_push($filas, [
                    'encabezado' => $var,
                    'valores' => $valores,
                ]);
            }
        } else {    // Una variedad
            foreach ($labels as $pos => $l) {
                for ($m = $mes_inicial; $m <= $mes_final; $m++) {
                    if ($pos == 0) {
                        array_push($meses, getMeses()[$m - 1]);
                    }

                    if (strlen($m) != 2)
                        $m = '0' . $m;

                    if ($area == 'V') {
                        $objects = DB::table('clasificacion_verde')
                            ->select('id_clasificacion_verde as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                            ->where(DB::raw('month(fecha_ingreso)'), '=', $m)
                            ->get();
                    } else if ($area == 'C') {
                        $objects = DB::table('cosecha')
                            ->select('id_cosecha as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                            ->where(DB::raw('month(fecha_ingreso)'), '=', $m)
                            ->get();
                    } else {
                        $objects = DB::table('clasificacion_blanco')
                            ->select('id_clasificacion_blanco as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                            ->where(DB::raw('month(fecha_ingreso)'), '=', $m)
                            ->get();
                    }

                    $valor = 0;

                    foreach ($objects as $obj) {
                        if ($area == 'C')
                            $model = Cosecha::find($obj->id);
                        else if ($area == 'V')
                            $model = ClasificacionVerde::find($obj->id);
                        else
                            $model = ClasificacionBlanco::find($obj->id);

                        if ($criterio == 'D') { // DESECHO
                            if ($area == 'V')
                                $valor += $model->desechoByVariedad($variedad);
                            else if ($area == 'B')
                                $valor += $model->getDesechoByVariedad($variedad);
                        }
                        if ($criterio == 'R') { // RENDIMIENTO
                            $valor += $model->getRendimientoByVariedad($variedad);
                        }
                    }

                    array_push($valores, count($objects) > 0 ? round($valor / count($objects), 2) : 0);
                }
            }

            array_push($filas, [
                'encabezado' => getVariedad($variedad),
                'valores' => $valores,
            ]);
        }

        return [
            'labels' => $labels,
            'meses' => $meses,
            'filas' => $filas,
        ];
    }

    public function getTablasByRangoSemanal($semana_inicial, $semana_final, $variedad, $annos, $criterio, $area)
    {
        sort($annos);
        $labels = $annos;
        $semanas = [];
        $filas = [];
        $valores = [];

        if ($variedad == 'A') { // Acumulado
            foreach ($labels as $pos => $l) {
                for ($s = $semana_inicial; $s <= $semana_final; $s++) {
                    if (strlen($s) != 2)
                        $s = '0' . $s;

                    if ($pos == 0) {
                        array_push($semanas, $s);
                    }
                    $semana = Semana::All()->where('estado', 1)->where('codigo', substr($l, 2) . $s)->first();

                    if ($semana != '') {
                        if ($area == 'V') {
                            $objects = DB::table('clasificacion_verde')
                                ->select('id_clasificacion_verde as id')->distinct()
                                ->where('estado', '=', 1)
                                ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                                ->where(DB::raw('fecha_ingreso'), '<=', $semana->fecha_final)
                                ->where(DB::raw('fecha_ingreso'), '>=', $semana->fecha_inicial)
                                ->get();
                        } else if ($area == 'C') {
                            $objects = DB::table('cosecha')
                                ->select('id_cosecha as id')->distinct()
                                ->where('estado', '=', 1)
                                ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                                ->where(DB::raw('fecha_ingreso'), '<=', $semana->fecha_final)
                                ->where(DB::raw('fecha_ingreso'), '>=', $semana->fecha_inicial)
                                ->get();
                        } else {
                            $objects = DB::table('clasificacion_blanco')
                                ->select('id_clasificacion_blanco as id')->distinct()
                                ->where('estado', '=', 1)
                                ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                                ->where(DB::raw('fecha_ingreso'), '<=', $semana->fecha_final)
                                ->where(DB::raw('fecha_ingreso'), '>=', $semana->fecha_inicial)
                                ->get();
                        }
                    } else
                        $objects = [];

                    $valor = 0;

                    foreach ($objects as $obj) {
                        if ($area == 'C')
                            $model = Cosecha::find($obj->id);
                        else if ($area == 'V')
                            $model = ClasificacionVerde::find($obj->id);
                        else
                            $model = ClasificacionBlanco::find($obj->id);

                        if ($criterio == 'D') { // DESECHO
                            if ($area == 'V')
                                $valor += $model->desecho();
                            else if ($area == 'B')
                                $valor += $model->getDesecho();
                        }
                        if ($criterio == 'R') { // RENDIMIENTO
                            $valor += $model->getRendimiento();
                        }
                    }

                    array_push($valores, count($objects) > 0 ? round($valor / count($objects), 2) : 0);
                }
            }

            array_push($filas, [
                'encabezado' => '',
                'valores' => $valores,
            ]);
        } else if ($variedad == 'T') {  // Todas las variedades
            foreach (getVariedades() as $pos_var => $var) {
                $valores = [];

                foreach ($labels as $pos => $l) {
                    for ($s = $semana_inicial; $s <= $semana_final; $s++) {
                        if (strlen($s) != 2)
                            $s = '0' . $s;

                        if ($pos == 0 && $pos_var == 0) {
                            array_push($semanas, $s);
                        }
                        $semana = Semana::All()->where('estado', 1)->where('codigo', substr($l, 2) . $s)->first();

                        if ($semana != '') {
                            if ($area == 'V') {
                                $objects = DB::table('clasificacion_verde')
                                    ->select('id_clasificacion_verde as id')->distinct()
                                    ->where('estado', '=', 1)
                                    ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                                    ->where(DB::raw('fecha_ingreso'), '<=', $semana->fecha_final)
                                    ->where(DB::raw('fecha_ingreso'), '>=', $semana->fecha_inicial)
                                    ->get();
                            } else if ($area == 'C') {
                                $objects = DB::table('cosecha')
                                    ->select('id_cosecha as id')->distinct()
                                    ->where('estado', '=', 1)
                                    ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                                    ->where(DB::raw('fecha_ingreso'), '<=', $semana->fecha_final)
                                    ->where(DB::raw('fecha_ingreso'), '>=', $semana->fecha_inicial)
                                    ->get();
                            } else {
                                $objects = DB::table('clasificacion_blanco')
                                    ->select('id_clasificacion_blanco as id')->distinct()
                                    ->where('estado', '=', 1)
                                    ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                                    ->where(DB::raw('fecha_ingreso'), '<=', $semana->fecha_final)
                                    ->where(DB::raw('fecha_ingreso'), '>=', $semana->fecha_inicial)
                                    ->get();
                            }
                        } else
                            $objects = [];

                        $valor = 0;

                        foreach ($objects as $obj) {
                            if ($area == 'C')
                                $model = Cosecha::find($obj->id);
                            else if ($area == 'V')
                                $model = ClasificacionVerde::find($obj->id);
                            else
                                $model = ClasificacionBlanco::find($obj->id);

                            if ($criterio == 'D') { // DESECHO
                                if ($area == 'V')
                                    $valor += $model->desechoByVariedad($var->id_variedad);
                                else if ($area == 'B')
                                    $valor += $model->getDesechoByVariedad($var->id_variedad);
                            }
                            if ($criterio == 'R') { // RENDIMIENTO
                                $valor += $model->getRendimientoByVariedad($var->id_variedad);
                            }
                        }

                        array_push($valores, count($objects) > 0 ? round($valor / count($objects), 2) : 0);
                    }
                }

                array_push($filas, [
                    'encabezado' => $var,
                    'valores' => $valores,
                ]);
            }
        } else {    // Una variedad
            foreach ($labels as $pos => $l) {
                for ($s = $semana_inicial; $s <= $semana_final; $s++) {
                    if (strlen($s) != 2)
                        $s = '0' . $s;

                    if ($pos == 0) {
                        array_push($semanas, $s);
                    }
                    $semana = Semana::All()->where('estado', 1)->where('codigo', substr($l, 2) . $s)->first();

                    if ($semana != '') {
                        if ($area == 'V') {
                            $objects = DB::table('clasificacion_verde')
                                ->select('id_clasificacion_verde as id')->distinct()
                                ->where('estado', '=', 1)
                                ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                                ->where(DB::raw('fecha_ingreso'), '<=', $semana->fecha_final)
                                ->where(DB::raw('fecha_ingreso'), '>=', $semana->fecha_inicial)
                                ->get();
                        } else if ($area == 'C') {
                            $objects = DB::table('cosecha')
                                ->select('id_cosecha as id')->distinct()
                                ->where('estado', '=', 1)
                                ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                                ->where(DB::raw('fecha_ingreso'), '<=', $semana->fecha_final)
                                ->where(DB::raw('fecha_ingreso'), '>=', $semana->fecha_inicial)
                                ->get();
                        } else {
                            $objects = DB::table('clasificacion_blanco')
                                ->select('id_clasificacion_blanco as id')->distinct()
                                ->where('estado', '=', 1)
                                ->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                                ->where(DB::raw('fecha_ingreso'), '<=', $semana->fecha_final)
                                ->where(DB::raw('fecha_ingreso'), '>=', $semana->fecha_inicial)
                                ->get();
                        }
                    } else
                        $objects = [];

                    $valor = 0;

                    foreach ($objects as $obj) {
                        if ($area == 'C')
                            $model = Cosecha::find($obj->id);
                        else if ($area == 'V')
                            $model = ClasificacionVerde::find($obj->id);
                        else
                            $model = ClasificacionBlanco::find($obj->id);

                        if ($criterio == 'D') { // DESECHO
                            if ($area == 'V')
                                $valor += $model->desechoByVariedad($variedad);
                            else if ($area == 'B')
                                $valor += $model->getDesechoByVariedad($variedad);
                        }
                        if ($criterio == 'R') { // RENDIMIENTO
                            $valor += $model->getRendimientoByVariedad($variedad);
                        }
                    }

                    array_push($valores, count($objects) > 0 ? round($valor / count($objects), 2) : 0);
                }
            }

            array_push($filas, [
                'encabezado' => getVariedad($variedad),
                'valores' => $valores,
            ]);
        }

        return [
            'labels' => $labels,
            'semanas' => $semanas,
            'filas' => $filas,
        ];
    }

    public function getTablasByRangoDiario($desde, $hasta, $variedad, $annos, $criterio, $area)
    {
        sort($annos);
        $labels = $annos;
        $dias = [];
        $filas = [];
        $valores = [];

        if ($variedad == 'A') { // Acumulado

            foreach ($labels as $pos => $l) {
                for ($d = 0; $d < difFechas($hasta, $desde)->days; $d++) {
                    if ($pos == 0) {
                        array_push($dias, opDiasFecha('+', $d, $desde));
                    }

                    if ($area == 'V') {
                        $objects = DB::table('clasificacion_verde')
                            ->select('id_clasificacion_verde as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('fecha_ingreso'), '=', opDiasFecha('+', $d, $desde))
                            ->get();
                    } else if ($area == 'C') {
                        $objects = DB::table('cosecha')
                            ->select('id_cosecha as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('fecha_ingreso'), '=', opDiasFecha('+', $d, $desde))
                            ->get();
                    } else {
                        $objects = DB::table('clasificacion_blanco')
                            ->select('id_clasificacion_blanco as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('fecha_ingreso'), '=', opDiasFecha('+', $d, $desde))
                            ->get();
                    }

                    $valor = 0;

                    foreach ($objects as $obj) {
                        if ($area == 'V')
                            $model = ClasificacionVerde::find($obj->id);
                        else if ($area == 'C')
                            $model = Cosecha::find($obj->id);
                        else
                            $model = ClasificacionBlanco::find($obj->id);

                        if ($criterio == 'D') { // DESECHO
                            if ($area == 'V')
                                $valor += $model->desecho();
                            else if ($area == 'B')
                                $valor += $model->getDesecho();
                        }
                        if ($criterio == 'R') { // RENDIMIENTO
                            $valor += $model->getRendimiento();
                        }
                    }

                    array_push($valores, count($objects) > 0 ? round($valor / count($objects), 2) : 0);
                }
            }
            array_push($filas, [
                'encabezado' => '',
                'valores' => $valores,
            ]);
        } else if ($variedad == 'T') {  // Todas las variedades
            foreach (getVariedades() as $pos_var => $var) {
                $valores = [];

                foreach ($labels as $pos => $l) {
                    for ($d = 0; $d < difFechas($hasta, $desde)->days; $d++) {
                        if ($pos == 0 && $pos_var == 0) {
                            array_push($dias, opDiasFecha('+', $d, $desde));
                        }

                        if ($area == 'V') {
                            $objects = DB::table('clasificacion_verde')
                                ->select('id_clasificacion_verde as id')->distinct()
                                ->where('estado', '=', 1)
                                ->where(DB::raw('fecha_ingreso'), '=', opDiasFecha('+', $d, $desde))
                                ->get();
                        } else if ($area == 'C') {
                            $objects = DB::table('cosecha')
                                ->select('id_cosecha as id')->distinct()
                                ->where('estado', '=', 1)
                                ->where(DB::raw('fecha_ingreso'), '=', opDiasFecha('+', $d, $desde))
                                ->get();
                        } else {
                            $objects = DB::table('clasificacion_blanco')
                                ->select('id_clasificacion_blanco as id')->distinct()
                                ->where('estado', '=', 1)
                                ->where(DB::raw('fecha_ingreso'), '=', opDiasFecha('+', $d, $desde))
                                ->get();
                        }

                        $valor = 0;

                        foreach ($objects as $obj) {
                            if ($area == 'V')
                                $model = ClasificacionVerde::find($obj->id);
                            else if ($area == 'C')
                                $model = Cosecha::find($obj->id);
                            else
                                $model = ClasificacionBlanco::find($obj->id);

                            if ($criterio == 'D') { // DESECHO
                                if ($area == 'V')
                                    $valor += $model->desechoByVariedad($var->id_variedad);
                                else if ($area == 'B')
                                    $valor += $model->getDesechoByVariedad($var->id_variedad);
                            }
                            if ($criterio == 'R') { // RENDIMIENTO
                                $valor += $model->getRendimientoByVariedad($var->id_variedad);
                            }
                        }

                        array_push($valores, count($objects) > 0 ? round($valor / count($objects), 2) : 0);
                    }
                }

                array_push($filas, [
                    'encabezado' => $var,
                    'valores' => $valores,
                ]);
            }
        } else {    // Una variedad
            foreach ($labels as $pos => $l) {
                for ($d = 0; $d < difFechas($hasta, $desde)->days; $d++) {
                    if ($pos == 0) {
                        array_push($dias, opDiasFecha('+', $d, $desde));
                    }

                    if ($area == 'V') {
                        $objects = DB::table('clasificacion_verde')
                            ->select('id_clasificacion_verde as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('fecha_ingreso'), '=', opDiasFecha('+', $d, $desde))
                            ->get();
                    } else if ($area == 'C') {
                        $objects = DB::table('cosecha')
                            ->select('id_cosecha as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('fecha_ingreso'), '=', opDiasFecha('+', $d, $desde))
                            ->get();
                    } else {
                        $objects = DB::table('clasificacion_blanco')
                            ->select('id_clasificacion_blanco as id')->distinct()
                            ->where('estado', '=', 1)
                            ->where(DB::raw('fecha_ingreso'), '=', opDiasFecha('+', $d, $desde))
                            ->get();
                    }

                    $valor = 0;

                    foreach ($objects as $obj) {
                        if ($area == 'V')
                            $model = ClasificacionVerde::find($obj->id);
                        else if ($area == 'C')
                            $model = Cosecha::find($obj->id);
                        else
                            $model = ClasificacionBlanco::find($obj->id);

                        if ($criterio == 'D') { // DESECHO
                            if ($area == 'V')
                                $valor += $model->desechoByVariedad($variedad);
                            else if ($area == 'B')
                                $valor += $model->getDesechoByVariedad($variedad);
                        }
                        if ($criterio == 'R') { // RENDIMIENTO
                            $valor += $model->getRendimientoByVariedad($variedad);
                        }
                    }

                    array_push($valores, count($objects) > 0 ? round($valor / count($objects), 2) : 0);
                }
            }

            array_push($filas, [
                'encabezado' => getVariedad($variedad),
                'valores' => $valores,
            ]);
        }

        return [
            'labels' => $labels,
            'dias' => $dias,
            'filas' => $filas,
        ];
    }
}
