<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\Cosecha;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use PHPExcel_Worksheet_MemoryDrawing;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;
use PHPExcel_Style_Alignment;
use yura\Modelos\Variedad;

class tblPostcosechaController extends Controller
{
    public function inicio(Request $request)
    {
        $annos = DB::table('clasificacion_verde as v')
            ->select(DB::raw('year(v.fecha_ingreso) as anno'))->distinct()
            ->orderBy(DB::raw('year(v.fecha_ingreso)'))
            ->get();

        return view('adminlte.crm.tbl_postcosecha.inicio', [
            'annos' => $annos,

            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
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
                $data = $this->getTablasByRangoAnual($request->desde, $request->hasta, $request->variedad, $annos, $request->criterio);
            } else if ($request->rango == 'M') {
                if ($request->desde >= 1 && $request->desde <= 12 && $request->hasta >= 1 && $request->hasta <= 12 && $request->desde <= $request->hasta) {
                    $view = 'mensual';
                    $data = $this->getTablasByRangoMensual($request->desde, $request->hasta, $request->variedad, $annos, $request->criterio, $request->acumulado);
                } else {
                    return '<div class="alert alert-warning text-center">Los meses ingresados están incorrectos</div>';
                }
            } else if ($request->rango == 'S') {
                if ($request->desde >= 1 && $request->desde <= 53 && $request->hasta >= 1 && $request->hasta <= 53 && $request->desde <= $request->hasta) {
                    $view = 'semanal';
                    $data = $this->getTablasByRangoSemanal($request->desde, $request->hasta, $request->variedad, $annos, $request->criterio, $request->acumulado);
                } else {
                    return '<div class="alert alert-warning text-center">Las semanas ingresadas están incorrectas</div>';
                }
            } else {
                $view = 'diario';
                $data = $this->getTablasByRangoDiario($request->desde, $request->hasta, $request->variedad, $annos, $request->criterio);
            }

            return view('adminlte.crm.tbl_postcosecha.partials.' . $view, [
                'data' => $data,
                'acumulado' => $request->acumulado,
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

            $data = $this->getTablasByRangoSemanal($request->desde, $request->hasta, $request->filtro_variedad, [$request->anno], $request->criterio, $request->acumulado);
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

            $data = $this->getTablasByRangoDiario($desde, $hasta, $request->filtro_variedad, [$request->anno], $request->criterio);
        }

        return view('adminlte.crm.tbl_postcosecha.partials.' . $view, [
            'data' => $data,
            'acumulado' => $request->acumulado,
            'criterio' => $request->criterio,
            'variedad' => $request->variedad,
        ]);
    }

    public function getTablasByRangoAnual($semana_inicial, $semana_final, $variedad, $annos, $criterio)
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

                $verdes = [];
                if ($s_inicial != '' && $s_final != '') {
                    if ($criterio == 'K')    // tallos (cosecha)
                        $verdes = DB::table('cosecha')
                            ->select('id_cosecha as id')->distinct();
                    else
                        $verdes = DB::table('clasificacion_verde')
                            ->select('id_clasificacion_verde as id')->distinct();

                    $verdes = $verdes->where('estado', '=', 1)
                        ->where(DB::raw('year(fecha_ingreso)'), '=', $a)
                        ->where(DB::raw('fecha_ingreso'), '>=', $s_inicial->fecha_inicial)
                        ->where(DB::raw('fecha_ingreso'), '<=', $s_final->fecha_final)
                        ->get();
                    foreach ($verdes as $obj) {
                        if ($criterio == 'K') {
                            $cosecha = Cosecha::find($obj->id);
                            $valor += $cosecha->getTotalTallos();
                        } else {
                            $verde = getClasificacionVerde($obj->id);

                            if ($criterio == 'C')
                                $valor += round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2);
                            if ($criterio == 'T')
                                $valor += $verde->total_tallos();
                            if ($criterio == 'E')
                                $valor += $verde->getTotalRamosEstandar();
                            if ($criterio == 'D')
                                $valor += $verde->desecho();
                            if ($criterio == 'R')
                                $valor += $verde->getRendimiento();
                            if ($criterio == 'Q')
                                $valor += $verde->getCalibre();
                        }
                    }
                }

                if ($criterio == 'K')
                    array_push($valores, $valor);
                if ($criterio == 'C')
                    array_push($valores, $valor);
                if ($criterio == 'T')
                    array_push($valores, $valor);
                if ($criterio == 'E')
                    array_push($valores, $valor);
                if ($criterio == 'D')
                    array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
                if ($criterio == 'R')
                    array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
                if ($criterio == 'Q')
                    array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
            }

            array_push($filas, [
                'encabezado' => '',
                'valores' => $valores,
            ]);
        } else if ($variedad == 'T') {
            foreach (Variedad::All() as $var) {
                $valores = [];
                foreach ($labels as $a) {
                    $s_inicial = Semana::All()->where('estado', 1)->where('codigo', substr($a, 2) . $semana_inicial)->first();
                    $s_final = Semana::All()->where('estado', 1)->where('codigo', substr($a, 2) . $semana_final)->first();

                    $valor = 0;
                    $cant_verdes = 0;
                    $value = 0;

                    $verdes = [];
                    if ($s_inicial != '' && $s_final != '') {
                        if ($criterio == 'K')    // tallos (cosecha)
                            $verdes = DB::table('cosecha')
                                ->select('id_cosecha as id')->distinct();
                        else
                            $verdes = DB::table('clasificacion_verde')
                                ->select('id_clasificacion_verde as id')->distinct();

                        $verdes = $verdes->where('estado', '=', 1)
                            ->where(DB::raw('year(fecha_ingreso)'), '=', $a)
                            ->where(DB::raw('fecha_ingreso'), '>=', $s_inicial->fecha_inicial)
                            ->where(DB::raw('fecha_ingreso'), '<=', $s_final->fecha_final)
                            ->get();
                        foreach ($verdes as $obj) {
                            if ($criterio == 'K') {
                                $cosecha = Cosecha::find($obj->id);
                                $valor += $cosecha->getTotalTallosByVariedad($var->id_variedad);
                            } else {
                                $verde = getClasificacionVerde($obj->id);

                                if ($criterio == 'C')
                                    $valor += round($verde->getTotalRamosEstandarByVariedad($var->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                                if ($criterio == 'T')
                                    $valor += $verde->tallos_x_variedad($var->id_variedad);
                                if ($criterio == 'E')
                                    $valor += $verde->getTotalRamosEstandarByVariedad($var->id_variedad);
                                if ($criterio == 'D') {
                                    $value = $verde->desechoByVariedad($var->id_variedad);
                                    $valor += $value;
                                }
                                if ($criterio == 'R') {
                                    $value = $verde->getRendimientoByVariedad($var->id_variedad);
                                    $valor += $value;
                                }
                                if ($criterio == 'Q') {
                                    $value = $verde->calibreByVariedad($var->id_variedad);
                                    $valor += $value;
                                }

                                if ($value > 0)
                                    $cant_verdes++;
                            }
                        }
                    }

                    if ($criterio == 'K')
                        array_push($valores, $valor);
                    if ($criterio == 'C')
                        array_push($valores, $valor);
                    if ($criterio == 'T')
                        array_push($valores, $valor);
                    if ($criterio == 'E')
                        array_push($valores, $valor);
                    if ($criterio == 'D')
                        array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
                    if ($criterio == 'R')
                        array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
                    if ($criterio == 'Q')
                        array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
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
                $cant_verdes = 0;
                $value = 0;

                $verdes = [];
                if ($s_inicial != '' && $s_final != '') {
                    if ($criterio == 'K')    // tallos (cosecha)
                        $verdes = DB::table('cosecha')
                            ->select('id_cosecha as id')->distinct();
                    else
                        $verdes = DB::table('clasificacion_verde')
                            ->select('id_clasificacion_verde as id')->distinct();

                    $verdes = $verdes->where('estado', '=', 1)
                        ->where(DB::raw('year(fecha_ingreso)'), '=', $a)
                        ->where(DB::raw('fecha_ingreso'), '>=', $s_inicial->fecha_inicial)
                        ->where(DB::raw('fecha_ingreso'), '<=', $s_final->fecha_final)
                        ->get();
                    foreach ($verdes as $obj) {
                        if ($criterio == 'K') {
                            $cosecha = Cosecha::find($obj->id);
                            $valor += $cosecha->getTotalTallosByVariedad($variedad);
                        } else {
                            $verde = getClasificacionVerde($obj->id);

                            if ($criterio == 'C')
                                $valor += round($verde->getTotalRamosEstandarByVariedad($variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                            if ($criterio == 'T')
                                $valor += $verde->tallos_x_variedad($variedad);
                            if ($criterio == 'E')
                                $valor += $verde->getTotalRamosEstandarByVariedad($variedad);
                            if ($criterio == 'D') {
                                $value = $verde->desechoByVariedad($variedad);
                                $valor += $value;
                            }
                            if ($criterio == 'R') {
                                $value = $verde->getRendimientoByVariedad($variedad);
                                $valor += $value;
                            }
                            if ($criterio == 'Q') {
                                $value = $verde->calibreByVariedad($variedad);
                                $valor += $value;
                            }

                            if ($value > 0)
                                $cant_verdes++;
                        }
                    }
                }

                if ($criterio == 'K')
                    array_push($valores, $valor);
                if ($criterio == 'C')
                    array_push($valores, $valor);
                if ($criterio == 'T')
                    array_push($valores, $valor);
                if ($criterio == 'E')
                    array_push($valores, $valor);
                if ($criterio == 'D')
                    array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
                if ($criterio == 'R')
                    array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
                if ($criterio == 'Q')
                    array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
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

    public function getTablasByRangoMensual($mes_inicial, $mes_final, $variedad, $annos, $criterio, $acumulado)
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

                    if ($criterio == 'K') // tallos (cosecha)
                        $verdes = DB::table('cosecha')
                            ->select('id_cosecha as id')->distinct();
                    else
                        $verdes = DB::table('clasificacion_verde')
                            ->select('id_clasificacion_verde as id')->distinct();

                    $verdes = $verdes->where('estado', '=', 1)
                        ->where(DB::raw('year(fecha_ingreso)'), '=', $l);

                    if ($acumulado == 'true') {
                        $verdes = $verdes->where(DB::raw('month(fecha_ingreso)'), '>=', 1)
                            ->where(DB::raw('month(fecha_ingreso)'), '<=', $m);
                    } else {
                        $verdes = $verdes->where(DB::raw('month(fecha_ingreso)'), '=', $m);
                    }

                    $verdes = $verdes->get();

                    $valor = 0;

                    foreach ($verdes as $obj) {
                        if ($criterio == 'K') {
                            $cosecha = Cosecha::find($obj->id);
                            $valor += $cosecha->getTotalTallos();
                        } else {
                            $verde = getClasificacionVerde($obj->id);

                            if ($criterio == 'C') {
                                $valor += round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2);
                            }
                            if ($criterio == 'T') {
                                $valor += $verde->total_tallos();
                            }
                            if ($criterio == 'E') {
                                $valor += $verde->getTotalRamosEstandar();
                            }
                            if ($criterio == 'D') {
                                $valor += $verde->desecho();
                            }
                            if ($criterio == 'R') {
                                $valor += $verde->getRendimiento();
                            }
                            if ($criterio == 'Q') {
                                $valor += $verde->getCalibre();
                            }
                        }
                    }

                    if ($criterio == 'K')
                        array_push($valores, $valor);
                    if ($criterio == 'C')
                        array_push($valores, $valor);
                    if ($criterio == 'T')
                        array_push($valores, $valor);
                    if ($criterio == 'E')
                        array_push($valores, $valor);
                    if ($criterio == 'D')
                        array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
                    if ($criterio == 'R')
                        array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
                    if ($criterio == 'Q')
                        array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
                }
            }

            array_push($filas, [
                'encabezado' => '',
                'valores' => $valores,
            ]);
        } else if ($variedad == 'T') {  // Todas las variedades
            foreach (Variedad::All() as $pos_var => $var) {
                $valores = [];

                foreach ($labels as $pos => $l) {
                    for ($m = $mes_inicial; $m <= $mes_final; $m++) {
                        if ($pos == 0 && $pos_var == 0) {
                            array_push($meses, getMeses()[$m - 1]);
                        }

                        if (strlen($m) != 2)
                            $m = '0' . $m;

                        if ($criterio == 'K') // tallos (cosecha)
                            $verdes = DB::table('cosecha')
                                ->select('id_cosecha as id')->distinct();
                        else
                            $verdes = DB::table('clasificacion_verde')
                                ->select('id_clasificacion_verde as id')->distinct();

                        $verdes = $verdes->where('estado', '=', 1)
                            ->where(DB::raw('year(fecha_ingreso)'), '=', $l);

                        if ($acumulado == 'true') {
                            $verdes = $verdes->where(DB::raw('month(fecha_ingreso)'), '>=', 1)
                                ->where(DB::raw('month(fecha_ingreso)'), '<=', $m);
                        } else {
                            $verdes = $verdes->where(DB::raw('month(fecha_ingreso)'), '=', $m);
                        }

                        $verdes = $verdes->get();

                        $valor = 0;
                        $cant_verdes = 0;
                        $value = 0;

                        foreach ($verdes as $obj) {
                            if ($criterio == 'K') {
                                $cosecha = Cosecha::find($obj->id);
                                $valor += $cosecha->getTotalTallosByVariedad($var->id_variedad);
                            } else {
                                $verde = getClasificacionVerde($obj->id);

                                if ($criterio == 'C') {
                                    $valor += round($verde->getTotalRamosEstandarByVariedad($var->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                                }
                                if ($criterio == 'T') {
                                    $valor += $verde->tallos_x_variedad($var->id_variedad);
                                }
                                if ($criterio == 'E') {
                                    $valor += $verde->getTotalRamosEstandarByVariedad($var->id_variedad);
                                }
                                if ($criterio == 'D') {
                                    $value = $verde->desechoByVariedad($var->id_variedad);
                                    $valor += $value;
                                }
                                if ($criterio == 'R') {
                                    $value = $verde->getRendimientoByVariedad($var->id_variedad);
                                    $valor += $value;
                                }
                                if ($criterio == 'Q') {
                                    $value = $verde->calibreByVariedad($var->id_variedad);
                                    $valor += $value;
                                }

                                if ($value > 0)
                                    $cant_verdes++;
                            }
                        }

                        if ($criterio == 'K')
                            array_push($valores, $valor);
                        if ($criterio == 'C')
                            array_push($valores, $valor);
                        if ($criterio == 'T')
                            array_push($valores, $valor);
                        if ($criterio == 'E')
                            array_push($valores, $valor);
                        if ($criterio == 'D')
                            array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
                        if ($criterio == 'R')
                            array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
                        if ($criterio == 'Q')
                            array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
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

                    if ($criterio == 'K') // tallos (cosecha)
                        $verdes = DB::table('cosecha')
                            ->select('id_cosecha as id')->distinct();
                    else
                        $verdes = DB::table('clasificacion_verde')
                            ->select('id_clasificacion_verde as id')->distinct();

                    $verdes = $verdes->where('estado', '=', 1)
                        ->where(DB::raw('year(fecha_ingreso)'), '=', $l);

                    if ($acumulado == 'true') {
                        $verdes = $verdes->where(DB::raw('month(fecha_ingreso)'), '>=', 1)
                            ->where(DB::raw('month(fecha_ingreso)'), '<=', $m);
                    } else {
                        $verdes = $verdes->where(DB::raw('month(fecha_ingreso)'), '=', $m);
                    }

                    $verdes = $verdes->get();

                    $valor = 0;
                    $cant_verdes = 0;
                    $value = 0;

                    foreach ($verdes as $obj) {
                        if ($criterio == 'K') {
                            $cosecha = Cosecha::find($obj->id);
                            $valor += $cosecha->getTotalTallosByVariedad($variedad);
                        } else {
                            $verde = getClasificacionVerde($obj->id);

                            if ($criterio == 'C')
                                $valor += round($verde->getTotalRamosEstandarByVariedad($variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                            if ($criterio == 'T')
                                $valor += $verde->tallos_x_variedad($variedad);
                            if ($criterio == 'E')
                                $valor += $verde->getTotalRamosEstandarByVariedad($variedad);
                            if ($criterio == 'D') {
                                $value = $verde->desechoByVariedad($variedad);
                                $valor += $value;
                            }
                            if ($criterio == 'R') {
                                $value = $verde->getRendimientoByVariedad($variedad);
                                $valor += $value;
                            }
                            if ($criterio == 'Q') {
                                $value = $verde->calibreByVariedad($variedad);
                                $valor += $value;
                            }

                            if ($value > 0)
                                $cant_verdes++;
                        }
                    }

                    if ($criterio == 'K')
                        array_push($valores, $valor);
                    if ($criterio == 'C')
                        array_push($valores, $valor);
                    if ($criterio == 'T')
                        array_push($valores, $valor);
                    if ($criterio == 'E')
                        array_push($valores, $valor);
                    if ($criterio == 'D')
                        array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
                    if ($criterio == 'R')
                        array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
                    if ($criterio == 'Q')
                        array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
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

    public function getTablasByRangoSemanal($semana_inicial, $semana_final, $variedad, $annos, $criterio, $acumulado)
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
                        if ($criterio == 'K') { // tallos (cosecha)
                            $verdes = DB::table('cosecha')
                                ->select('id_cosecha as id')->distinct();
                        } else {
                            $verdes = DB::table('clasificacion_verde')
                                ->select('id_clasificacion_verde as id')->distinct();
                        }
                        $verdes = $verdes->where('estado', '=', 1)
                            ->where(DB::raw('fecha_ingreso'), '<=', $semana->fecha_final);

                        if ($acumulado == 'true') {
                            $semana_1 = Semana::All()->where('estado', 1)->where('codigo', substr($l, 2) . '01')->first();

                            $verdes = $verdes->where(DB::raw('fecha_ingreso'), '>=', $semana_1->fecha_inicial);
                        } else {
                            $verdes = $verdes->where(DB::raw('fecha_ingreso'), '>=', $semana->fecha_inicial);
                        }

                        $verdes = $verdes->get();
                    } else
                        $verdes = [];

                    $valor = 0;

                    foreach ($verdes as $obj) {
                        if ($criterio == 'K') { // tallos (cosecha)
                            $cosecha = Cosecha::find($obj->id);
                            $valor += $cosecha->getTotalTallos();
                        } else {    // clasificacion verde
                            $verde = getClasificacionVerde($obj->id);

                            if ($criterio == 'C') {
                                $valor += round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2);
                            }
                            if ($criterio == 'T') {
                                $valor += $verde->total_tallos();
                            }
                            if ($criterio == 'E') {
                                $valor += $verde->getTotalRamosEstandar();
                            }
                            if ($criterio == 'D') {
                                $valor += $verde->desecho();
                            }
                            if ($criterio == 'R') {
                                $valor += $verde->getRendimiento();
                            }
                            if ($criterio == 'Q') {
                                $valor += $verde->getCalibre();
                            }
                        }
                    }

                    if ($criterio == 'K')
                        array_push($valores, $valor);
                    if ($criterio == 'C')
                        array_push($valores, $valor);
                    if ($criterio == 'T')
                        array_push($valores, $valor);
                    if ($criterio == 'E')
                        array_push($valores, $valor);
                    if ($criterio == 'D')
                        array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
                    if ($criterio == 'R')
                        array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
                    if ($criterio == 'Q')
                        array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
                }
            }

            array_push($filas, [
                'encabezado' => '',
                'valores' => $valores,
            ]);
        } else if ($variedad == 'T') {  // Todas las variedades
            foreach (Variedad::All() as $pos_var => $var) {
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
                            if ($criterio == 'K') { // tallos (cosecha)
                                $verdes = DB::table('cosecha')
                                    ->select('id_cosecha as id')->distinct();
                            } else {
                                $verdes = DB::table('clasificacion_verde')
                                    ->select('id_clasificacion_verde as id')->distinct();
                            }
                            $verdes = $verdes->where('estado', '=', 1)
                                ->where(DB::raw('fecha_ingreso'), '<=', $semana->fecha_final);

                            if ($acumulado == 'true') {
                                $semana_1 = Semana::All()->where('estado', 1)->where('codigo', substr($l, 2) . '01')->first();

                                $verdes = $verdes->where(DB::raw('fecha_ingreso'), '>=', $semana_1->fecha_inicial);
                            } else {
                                $verdes = $verdes->where(DB::raw('fecha_ingreso'), '>=', $semana->fecha_inicial);
                            }

                            $verdes = $verdes->get();
                        } else
                            $verdes = [];

                        $valor = 0;
                        $cant_verdes = 0;
                        $value = 0;

                        foreach ($verdes as $obj) {
                            if ($criterio == 'K') { // tallos (cosecha)
                                $cosecha = Cosecha::find($obj->id);
                                $valor += $cosecha->getTotalTallosByVariedad($var->id_variedad);
                            } else {    // clasificacion verde
                                $verde = getClasificacionVerde($obj->id);

                                if ($criterio == 'C') {
                                    $valor += round($verde->getTotalRamosEstandarByVariedad($var->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                                }
                                if ($criterio == 'T') {
                                    $valor += $verde->tallos_x_variedad($var->id_variedad);
                                }
                                if ($criterio == 'E') {
                                    $valor += $verde->getTotalRamosEstandarByVariedad($var->id_variedad);
                                }
                                if ($criterio == 'D') {
                                    $value = $verde->desechoByVariedad($var->id_variedad);
                                    $valor += $value;
                                }
                                if ($criterio == 'R') {
                                    $value = $verde->getRendimientoByVariedad($var->id_variedad);
                                    $valor += $value;
                                }
                                if ($criterio == 'Q') {
                                    $value = $verde->calibreByVariedad($var->id_variedad);
                                    $valor += $value;
                                }

                                if ($value > 0)
                                    $cant_verdes++;
                            }
                        }

                        if ($criterio == 'K')
                            array_push($valores, $valor);
                        if ($criterio == 'C')
                            array_push($valores, $valor);
                        if ($criterio == 'T')
                            array_push($valores, $valor);
                        if ($criterio == 'E')
                            array_push($valores, $valor);
                        if ($criterio == 'D')
                            array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
                        if ($criterio == 'R')
                            array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
                        if ($criterio == 'Q')
                            array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
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
                        if ($criterio == 'K') { // tallos (cosecha)
                            $verdes = DB::table('cosecha')
                                ->select('id_cosecha as id')->distinct();
                        } else {
                            $verdes = DB::table('clasificacion_verde')
                                ->select('id_clasificacion_verde as id')->distinct();
                        }
                        $verdes = $verdes->where('estado', '=', 1)
                            //->where(DB::raw('year(fecha_ingreso)'), '=', $l)
                            ->where(DB::raw('fecha_ingreso'), '<=', $semana->fecha_final);

                        if ($acumulado == 'true') {
                            $semana_1 = Semana::All()->where('estado', 1)->where('codigo', substr($l, 2) . '01')->first();

                            $verdes = $verdes->where(DB::raw('fecha_ingreso'), '>=', $semana_1->fecha_inicial);
                        } else {
                            $verdes = $verdes->where(DB::raw('fecha_ingreso'), '>=', $semana->fecha_inicial);
                        }

                        $verdes = $verdes->get();
                    } else
                        $verdes = [];

                    $valor = 0;
                    $cant_verdes = 0;
                    $value = 0;

                    foreach ($verdes as $obj) {
                        if ($criterio == 'K') { // tallos (cosecha)
                            $cosecha = Cosecha::find($obj->id);
                            $valor += $cosecha->getTotalTallosByVariedad($variedad);
                        } else {    // clasificacion verde
                            $verde = getClasificacionVerde($obj->id);

                            if ($criterio == 'C')
                                $valor += round($verde->getTotalRamosEstandarByVariedad($variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                            if ($criterio == 'T')
                                $valor += $verde->tallos_x_variedad($variedad);
                            if ($criterio == 'E')
                                $valor += $verde->getTotalRamosEstandarByVariedad($variedad);
                            if ($criterio == 'D') {
                                $value = $verde->desechoByVariedad($variedad);
                                $valor += $value;
                            }
                            if ($criterio == 'R') {
                                $value = $verde->getRendimientoByVariedad($variedad);
                                $valor += $value;
                            }
                            if ($criterio == 'Q') {
                                $value = $verde->calibreByVariedad($variedad);
                                $valor += $value;
                            }

                            if ($value > 0)
                                $cant_verdes++;
                        }
                    }

                    if ($criterio == 'K')
                        array_push($valores, $valor);
                    if ($criterio == 'C')
                        array_push($valores, $valor);
                    if ($criterio == 'T')
                        array_push($valores, $valor);
                    if ($criterio == 'E')
                        array_push($valores, $valor);
                    if ($criterio == 'D')
                        array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
                    if ($criterio == 'R')
                        array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
                    if ($criterio == 'Q')
                        array_push($valores, $cant_verdes > 0 ? round($valor / $cant_verdes, 2) : 0);
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

    public function getTablasByRangoDiario($desde, $hasta, $variedad, $annos, $criterio)
    {
        sort($annos);
        $labels = $annos;
        $dias = [];
        $filas = [];
        $valores = [];

        if ($variedad == 'A') { // Acumulado
            foreach ($labels as $pos => $l) {
                for ($d = 0; $d <= difFechas($hasta, $desde)->days; $d++) {
                    if ($pos == 0) {
                        array_push($dias, opDiasFecha('+', $d, $desde));
                    }

                    if ($criterio == 'K')    // tallos (cosecha)
                        $verdes = DB::table('cosecha')
                            ->select('id_cosecha as id')->distinct();
                    else
                        $verdes = DB::table('clasificacion_verde')
                            ->select('id_clasificacion_verde as id')->distinct();

                    $verdes = $verdes->where('estado', '=', 1)
                        ->where(DB::raw('fecha_ingreso'), '=', opDiasFecha('+', $d, $desde))
                        ->get();

                    $valor = 0;

                    foreach ($verdes as $obj) {
                        if ($criterio == 'K') {
                            $cosecha = Cosecha::find($obj->id);
                            $valor += $cosecha->getTotalTallos();
                        } else {
                            $verde = getClasificacionVerde($obj->id);

                            if ($criterio == 'C') {
                                $valor += round($verde->getTotalRamosEstandar() / getConfiguracionEmpresa()->ramos_x_caja, 2);
                            }
                            if ($criterio == 'T') {
                                $valor += $verde->total_tallos();
                            }
                            if ($criterio == 'E') {
                                $valor += $verde->getTotalRamosEstandar();
                            }
                            if ($criterio == 'D') {
                                $valor += $verde->desecho();
                            }
                            if ($criterio == 'R') {
                                $valor += $verde->getRendimiento();
                            }
                            if ($criterio == 'Q') {
                                $valor += $verde->getCalibre();
                            }
                        }
                    }

                    if ($criterio == 'K')
                        array_push($valores, $valor);
                    if ($criterio == 'C')
                        array_push($valores, $valor);
                    if ($criterio == 'T')
                        array_push($valores, $valor);
                    if ($criterio == 'E')
                        array_push($valores, $valor);
                    if ($criterio == 'D')
                        array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
                    if ($criterio == 'R')
                        array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
                    if ($criterio == 'Q')
                        array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
                }
            }
            array_push($filas, [
                'encabezado' => '',
                'valores' => $valores,
            ]);
        } else if ($variedad == 'T') {  // Todas las variedades
            foreach (Variedad::All() as $pos_var => $var) {
                $valores = [];

                foreach ($labels as $pos => $l) {
                    for ($d = 0; $d <= difFechas($hasta, $desde)->days; $d++) {

                        if ($pos == 0 && $pos_var == 0) {
                            array_push($dias, opDiasFecha('+', $d, $desde));
                        }

                        if ($criterio == 'K')    // tallos (cosecha)
                            $verdes = DB::table('cosecha')
                                ->select('id_cosecha as id')->distinct();
                        else
                            $verdes = DB::table('clasificacion_verde')
                                ->select('id_clasificacion_verde as id')->distinct();

                        $verdes = $verdes->where('estado', '=', 1)
                            ->where(DB::raw('fecha_ingreso'), '=', opDiasFecha('+', $d, $desde))
                            ->get();

                        $valor = 0;

                        foreach ($verdes as $obj) {
                            if ($criterio == 'K') {
                                $cosecha = Cosecha::find($obj->id);
                                $valor += $cosecha->getTotalTallosByVariedad($var->id_variedad);
                            } else {
                                $verde = getClasificacionVerde($obj->id);

                                if ($criterio == 'C') {
                                    $valor += round($verde->getTotalRamosEstandarByVariedad($var->id_variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                                }
                                if ($criterio == 'T') {
                                    $valor += $verde->tallos_x_variedad($var->id_variedad);
                                }
                                if ($criterio == 'E') {
                                    $valor += $verde->getTotalRamosEstandarByVariedad($var->id_variedad);
                                }
                                if ($criterio == 'D') {
                                    $valor += $verde->desechoByVariedad($var->id_variedad);
                                }
                                if ($criterio == 'R') {
                                    $valor += $verde->getRendimientoByVariedad($var->id_variedad);
                                }
                                if ($criterio == 'Q') {
                                    $valor += $verde->calibreByVariedad($var->id_variedad);
                                }
                            }
                        }

                        if ($criterio == 'K')
                            array_push($valores, $valor);
                        if ($criterio == 'C')
                            array_push($valores, $valor);
                        if ($criterio == 'T')
                            array_push($valores, $valor);
                        if ($criterio == 'E')
                            array_push($valores, $valor);
                        if ($criterio == 'D')
                            array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
                        if ($criterio == 'R')
                            array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
                        if ($criterio == 'Q')
                            array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
                    }
                }

                array_push($filas, [
                    'encabezado' => $var,
                    'valores' => $valores,
                ]);
            }
        } else {    // Una variedad
            foreach ($labels as $pos => $l) {
                for ($d = 0; $d <= difFechas($hasta, $desde)->days; $d++) {
                    if ($pos == 0) {
                        array_push($dias, opDiasFecha('+', $d, $desde));
                    }

                    if ($criterio == 'K')    // tallos (cosecha)
                        $verdes = DB::table('cosecha')
                            ->select('id_cosecha as id')->distinct();
                    else
                        $verdes = DB::table('clasificacion_verde')
                            ->select('id_clasificacion_verde as id')->distinct();

                    $verdes = $verdes->where('estado', '=', 1)
                        ->where(DB::raw('fecha_ingreso'), '=', opDiasFecha('+', $d, $desde))
                        ->get();

                    $valor = 0;

                    foreach ($verdes as $obj) {
                        if ($criterio == 'K') {
                            $cosecha = Cosecha::find($obj->id);
                            $valor += $cosecha->getTotalTallosByVariedad($variedad);
                        } else {
                            $verde = getClasificacionVerde($obj->id);

                            if ($criterio == 'C')
                                $valor += round($verde->getTotalRamosEstandarByVariedad($variedad) / getConfiguracionEmpresa()->ramos_x_caja, 2);
                            if ($criterio == 'T')
                                $valor += $verde->tallos_x_variedad($variedad);
                            if ($criterio == 'E')
                                $valor += $verde->getTotalRamosEstandarByVariedad($variedad);
                            if ($criterio == 'D')
                                $valor += $verde->desechoByVariedad($variedad);
                            if ($criterio == 'R')
                                $valor += $verde->getRendimientoByVariedad($variedad);
                            if ($criterio == 'Q')
                                $valor += $verde->calibreByVariedad($variedad);
                        }
                    }

                    if ($criterio == 'K')
                        array_push($valores, $valor);
                    if ($criterio == 'C')
                        array_push($valores, $valor);
                    if ($criterio == 'T')
                        array_push($valores, $valor);
                    if ($criterio == 'E')
                        array_push($valores, $valor);
                    if ($criterio == 'D')
                        array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
                    if ($criterio == 'R')
                        array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
                    if ($criterio == 'Q')
                        array_push($valores, count($verdes) > 0 ? round($valor / count($verdes), 2) : 0);
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

    /* ================= EXCEL ================= */

    public function exportar_tabla(Request $request)
    {
        //---------------------- EXCEL --------------------------------------
        $objPHPExcel = new PHPExcel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $currencyFormat = '#,#0.## \€;[Red]-#,#0.## \€';
        $numberFormat = '#,#0.##;[Red]-#,#0.##';

        $objPHPExcel->removeSheetByIndex(0); //Eliminar la hoja inicial por defecto

        $this->excel_hoja($objPHPExcel, $request);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="Reporte tabla-postcosecha.xlsx"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }

    public function excel_hoja($objPHPExcel, $request)
    {
        $columnas = [0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G', 7 => 'H', 8 => 'I', 9 => 'J', 10 => 'K', 11 => 'L',
            12 => 'M', 13 => 'N', 14 => 'O', 15 => 'P', 16 => 'Q', 17 => 'R', 18 => 'S', 19 => 'T', 20 => 'U', 21 => 'V', 22 => 'W', 23 => 'X',
            24 => 'Y', 25 => 'Z', 26 => 'AA', 27 => 'AB', 28 => 'AC', 29 => 'AD', 30 => 'AE', 31 => 'AF', 32 => 'AG', 33 => 'AH', 34 => 'AI',
            35 => 'AJ', 36 => 'AK', 37 => 'AL', 38 => 'AM', 39 => 'AN', 40 => 'AO', 41 => 'AP', 42 => 'AQ', 43 => 'AR', 44 => 'AS', 45 => 'AT',
            46 => 'AU', 47 => 'AV', 48 => 'AW', 49 => 'AX', 50 => 'AY', 51 => 'AZ', 52 => 'BA', 53 => 'BB', 54 => 'BC', 55 => 'BD', 56 => 'BE',
            57 => 'BF', 58 => 'BG', 59 => 'BH', 60 => 'BI', 61 => 'BJ', 62 => 'BK', 63 => 'BL', 64 => 'BM', 65 => 'BN', 66 => 'BO', 67 => 'BP',
            68 => 'BQ', 69 => 'BR', 70 => 'BS', 71 => 'BT', 72 => 'BU', 73 => 'BV', 74 => 'BW', 75 => 'BX', 76 => 'BY', 77 => 'BZ'];

        if ($request->annos == '')
            $annos = [date('Y')];
        else
            $annos = explode(' - ', $request->annos);

        if ($request->desde != '' && $request->hasta != '') {
            if ($request->rango == 'A') {
                $view = 'anual';
                $data = $this->getTablasByRangoAnual($request->desde, $request->hasta, $request->variedad, $annos, $request->criterio);
            } else if ($request->rango == 'M') {
                if ($request->desde >= 1 && $request->desde <= 12 && $request->hasta >= 1 && $request->hasta <= 12 && $request->desde <= $request->hasta) {
                    $view = 'meses';
                    $data = $this->getTablasByRangoMensual($request->desde, $request->hasta, $request->variedad, $annos, $request->criterio, $request->acumulado);
                } else {
                    return '<div class="alert alert-warning text-center">Los meses ingresados están incorrectos</div>';
                }
            } else if ($request->rango == 'S') {
                if ($request->desde >= 1 && $request->desde <= 53 && $request->hasta >= 1 && $request->hasta <= 53 && $request->desde <= $request->hasta) {
                    $view = 'semanas';
                    $data = $this->getTablasByRangoSemanal($request->desde, $request->hasta, $request->variedad, $annos, $request->criterio, $request->acumulado);
                } else {
                    return '<div class="alert alert-warning text-center">Las semanas ingresadas están incorrectas</div>';
                }
            } else {
                $view = 'diario';
                $data = $this->getTablasByRangoDiario($request->desde, $request->hasta, $request->variedad, $annos, $request->criterio);
            }

            ([
                'data' => $data,
                'acumulado' => $request->acumulado,
                'criterio' => $request->criterio,
                'variedad' => $request->variedad,
                'desde' => $request->desde,
                'hasta' => $request->hasta,
            ]);

            $criterios = ['C' => 'Cajas', 'T' => 'Tallos', 'E' => 'Ramos', 'D' => 'Desecho', 'R' => 'Rendimiento', 'Q' => 'Calibre'];
            $title_variedad = 'Todas';
            if ($request->variedad != 'T')
                $title_variedad = getVariedad($request->variedad)->siglas;
            $objSheet = new PHPExcel_Worksheet($objPHPExcel, $criterios[$request->criterio] . ' - ' . $title_variedad);
            $objPHPExcel->addSheet($objSheet, 0);

            if ($view == 'meses' || $view == 'semanas') {
                /* ============== MERGE CELDAS =============*/
                $objSheet->mergeCells('A1:A2');

                /* ============== ENCABEZADO =============*/
                $objSheet->getCell('A1')->setValue('Variedad');

                $array_totales = [];
                $array_subtotales = [];
                $pos_col = 1;
                foreach ($data['labels'] as $anno) {
                    $inicio = $pos_col;
                    array_push($array_subtotales, [
                        'valor' => 0,
                        'positivos' => 0,
                    ]);
                    foreach ($data[$view] as $mes) {
                        array_push($array_totales, [
                            'valor' => 0,
                            'positivos' => 0,
                        ]);
                        /* ============== BACKGROUND COLOR =============*/
                        $objSheet->getStyle($columnas[$pos_col] . '2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('e9ecef');
                        $objSheet->getCell($columnas[$pos_col] . '2')->setValue($mes);      // <th> mes
                        $pos_col++;
                    }

                    if ($request->acumulado == 'false') {
                        /* ============== BACKGROUND COLOR =============*/
                        $objSheet->getStyle($columnas[$pos_col] . '2')
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('d2d6de');
                        $objSheet->getCell($columnas[$pos_col] . '2')->setValue('Subtotal');        // <th> subtotal
                        $pos_col++;
                    }

                    $objSheet->mergeCells($columnas[$inicio] . '1:' . $columnas[$pos_col - 1] . '1');

                    $objSheet->getCell($columnas[$inicio] . '1')->setValue($anno);      // <th> año
                }

                /* ============== MERGE CELDAS =============*/
                $objSheet->mergeCells($columnas[$pos_col] . '1:' . $columnas[$pos_col] . '2');
                /* ============== LETRAS NEGRITAS =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . '2')->getFont()->setBold(true)->setSize(12);
                /* ============== CENTRAR =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . intval(2 + count($data['filas']) + 1))
                    ->getAlignment()
                    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . '1')
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('357ca5');
                /* ============== TEXT COLOR =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . '1')
                    ->getFont()
                    ->getColor()
                    ->setRGB('ffffff');
                /* ============== BORDE COLOR =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . intval(2 + count($data['filas']) + 1))
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)
                    ->getColor()
                    ->setRGB('000000');

                if ($request->acumulado == 'false') {
                    $objSheet->getCell($columnas[$pos_col] . '1')
                        ->setValue($request->criterio == 'C' || $request->criterio == 'T' || $request->criterio == 'E' ? 'Total' : 'Promedio');
                }

                //--------------------------- LLENAR LA TABLA ---------------------------------------------
                $pos_fila = 3;
                foreach ($data['filas'] as $fila) {
                    if ($fila['encabezado'] != '')
                        $objSheet->getCell('A' . $pos_fila)
                            ->setValue($fila['encabezado']->siglas);
                    else
                        $objSheet->getCell('A' . $pos_fila)->setValue('Todas');

                    $col_fil = 0;
                    $col = 1;
                    $total_fila = 0;
                    $total_positivos = 0;
                    for ($a = 1; $a <= count($data['labels']); $a++) {  // for años
                        $subtotal = 0;
                        $positivos = 0;
                        for ($m = 1; $m <= count($data[$view]); $m++) {  // for meses
                            $objSheet->getCell($columnas[$col] . $pos_fila)->setValue(number_format($fila['valores'][$col_fil], 2));
                            $subtotal += $fila['valores'][$col_fil];
                            $array_totales[$col_fil]['valor'] += $fila['valores'][$col_fil];
                            if ($fila['valores'][$col_fil] > 0) {
                                $array_totales[$col_fil]['positivos']++;
                                $positivos++;
                                $total_positivos++;
                                $array_subtotales[$a - 1]['positivos']++;
                            }
                            $col_fil++;
                            $col++;
                        }
                        if ($request->acumulado == 'false') {
                            /* ============== LETRAS NEGRITAS =============*/
                            $objSheet->getStyle($columnas[$col] . $pos_fila)->getFont()->setBold(true)->setSize(12);
                            /* ============== BACKGROUND COLOR =============*/
                            $objSheet->getStyle($columnas[$col] . $pos_fila)
                                ->getFill()
                                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setRGB('d2d6de');

                            if ($request->criterio == 'C' || $request->criterio == 'T' || $request->criterio == 'E') {
                                $objSheet->getCell($columnas[$col] . $pos_fila)->setValue(number_format($subtotal, 2)); // subtotal
                            } else {
                                $objSheet->getCell($columnas[$col] . $pos_fila)
                                    ->setValue(number_format($positivos > 0 ? round($subtotal / $positivos, 2) : 0, 2)); // subtotal
                            }
                            $array_subtotales[$a - 1]['valor'] += $subtotal;

                            $col++;
                        }
                        $total_fila += $subtotal;
                    }
                    if ($request->acumulado == 'false') {
                        /* ============== LETRAS NEGRITAS =============*/
                        $objSheet->getStyle($columnas[$col] . $pos_fila)->getFont()->setBold(true)->setSize(12);

                        if ($request->criterio == 'C' || $request->criterio == 'T' || $request->criterio == 'E')
                            $objSheet->getCell($columnas[$col] . $pos_fila)->setValue(number_format($total_fila, 2)); // total fila
                        else
                            $objSheet->getCell($columnas[$col] . $pos_fila)
                                ->setValue(number_format($total_positivos > 0 ? round($total_fila / $total_positivos, 2) : 0, 2)); // total fila

                    }
                    $pos_fila++;
                }

                /* ---------------------------- FILA TOTALES ---------------------------- */
                /* ============== LETRAS NEGRITAS =============*/
                $objSheet->getStyle('A' . $pos_fila . ':' . $columnas[$pos_col] . $pos_fila)->getFont()->setBold(true)->setSize(12);
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle('A' . $pos_fila . ':' . $columnas[$pos_col] . $pos_fila)->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
                /* ============== TEXT COLOR =============*/
                $objSheet->getStyle('A' . $pos_fila . ':' . $columnas[$pos_col] . $pos_fila)->getFont()->getColor()->setRGB('ffffff');
                /* ============================================================================================================================ */
                $objSheet->getCell('A' . $pos_fila)
                    ->setValue($request->criterio == 'C' || $request->criterio == 'T' || $request->criterio == 'E' ? 'Total' : 'Promedio');
                $col_fil = 0;
                $col = 1;
                $total_fila = 0;
                $total_positivos = 0;
                for ($a = 1; $a <= count($data['labels']); $a++) {  // for años
                    for ($m = 1; $m <= count($data[$view]); $m++) {  // for meses
                        if ($request->criterio == 'C' || $request->criterio == 'T' || $request->criterio == 'E')
                            $objSheet->getCell($columnas[$col] . $pos_fila)
                                ->setValue(number_format($array_totales[$col_fil]['valor'], 2));
                        else
                            $objSheet->getCell($columnas[$col] . $pos_fila)
                                ->setValue(number_format($array_totales[$col_fil]['positivos'] > 0 ? round($array_totales[$col_fil]['valor'] / $array_totales[$col_fil]['positivos'], 2) : 0, 2));
                        $total_fila += $array_totales[$col_fil]['valor'];
                        $total_positivos += $array_totales[$col_fil]['positivos'];
                        $col_fil++;
                        $col++;
                    }
                    if ($request->acumulado == 'false') {
                        /* ============== LETRAS NEGRITAS =============*/
                        $objSheet->getStyle($columnas[$col] . $pos_fila)->getFont()->setBold(true)->setSize(12);
                        /* ============== BACKGROUND COLOR =============*/
                        $objSheet->getStyle($columnas[$col] . $pos_fila)->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('d2d6de');
                        /* ============== TEXT COLOR =============*/
                        $objSheet->getStyle($columnas[$col] . $pos_fila)->getFont()->getColor()->setRGB('000000');

                        if ($request->criterio == 'C' || $request->criterio == 'T' || $request->criterio == 'E')
                            $objSheet->getCell($columnas[$col] . $pos_fila)->setValue(number_format($array_subtotales[$a - 1]['valor'], 2)); // subtotal
                        else
                            $objSheet->getCell($columnas[$col] . $pos_fila)
                                ->setValue(number_format($array_subtotales[$a - 1]['positivos'] > 0 ? round($array_subtotales[$a - 1]['valor'] / $array_subtotales[$a - 1]['positivos'], 2) : 0, 2)); // subtotal

                        $col++;
                    }
                }

                if ($request->acumulado == 'false') {
                    /* ============== LETRAS NEGRITAS =============*/
                    $objSheet->getStyle($columnas[$col] . $pos_fila)->getFont()->setBold(true)->setSize(12);

                    if ($request->criterio == 'C' || $request->criterio == 'T' || $request->criterio == 'E')
                        $objSheet->getCell($columnas[$col] . $pos_fila)->setValue(number_format($total_fila, 2)); // total fila
                    else
                        $objSheet->getCell($columnas[$col] . $pos_fila)
                            ->setValue(number_format($total_positivos > 0 ? round($total_fila / $total_positivos, 2) : 0, 2)); // total fila
                }

                /* ============== LETRAS NEGRITAS =============*/
                $objSheet->getStyle('A3:A' . intval(2 + count($data['filas'])))->getFont()->setBold(true)->setSize(12);
                /* ============== CENTRAR =============*/
                $objSheet->getStyle('A3:A' . intval(2 + count($data['filas'])))->getAlignment()
                    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle('A3:A' . intval(2 + count($data['filas'])))->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('e9ecef');

                /* ============== LETRAS NEGRITAS =============*/
                $objSheet->getStyle($columnas[$pos_col] . '3:' . $columnas[$pos_col] . intval(2 + count($data['filas'])))->getFont()->setBold(true)->setSize(12);
                /* ============== CENTRAR =============*/
                $objSheet->getStyle($columnas[$pos_col] . '3:' . $columnas[$pos_col] . intval(2 + count($data['filas'])))
                    ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle($columnas[$pos_col] . '3:' . $columnas[$pos_col] . intval(2 + count($data['filas'])))
                    ->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('e9ecef');

                foreach ($columnas as $c) {
                    $objSheet->getColumnDimension($c)->setAutoSize(true);
                }

            } else {
                /* ============== ENCABEZADO =============*/
                $objSheet->getCell('A1')->setValue($request->cliente == 'P' ? 'País' : 'Cliente');

                $pos_col = 1;
                $array_totales = [];
                foreach ($data['labels'] as $anno) {
                    array_push($array_totales, [
                        'valor' => 0,
                        'positivos' => 0,
                    ]);
                    $objSheet->getCell($columnas[$pos_col] . '1')->setValue($anno);      // <th> año
                    $pos_col++;
                }
                $objSheet->getCell($columnas[$pos_col] . '1')
                    ->setValue($request->criterio == 'C' || $request->criterio == 'T' || $request->criterio == 'E' ? 'Total' : 'Promedio');

                /* ============== LETRAS NEGRITAS =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . '1')->getFont()->setBold(true)->setSize(12);
                /* ============== CENTRAR =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . intval(2 + count($data['filas'])))
                    ->getAlignment()
                    ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . '1')
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('357ca5');
                /* ============== TEXT COLOR =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . '1')
                    ->getFont()
                    ->getColor()
                    ->setRGB('ffffff');
                /* ============== BORDE COLOR =============*/
                $objSheet->getStyle('A1:' . $columnas[$pos_col] . intval(2 + count($data['filas'])))
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)
                    ->getColor()
                    ->setRGB('000000');// <th> Total/Promedio

                //--------------------------- LLENAR LA TABLA ---------------------------------------------
                $pos_fila = 2;
                $positivos = 0;
                foreach ($data['filas'] as $fila) {
                    if ($fila['encabezado'] != '')
                        $objSheet->getCell('A' . $pos_fila)
                            ->setValue($fila['encabezado']->siglas);
                    else
                        $objSheet->getCell('A' . $pos_fila)->setValue('Todos');

                    $col = 1;
                    $total_fila = 0;
                    $total_positivos = 0;

                    foreach ($fila['valores'] as $pos_val => $valor) {
                        $objSheet->getCell($columnas[$col] . $pos_fila)->setValue($valor);
                        $total_fila += $valor;
                        $array_totales[$pos_val]['valor'] += $valor;
                        if ($valor > 0) {
                            $total_positivos++;
                            $array_totales[$pos_val]['positivos']++;
                            $positivos++;
                        }
                        $col++;
                    }

                    /* ============== LETRAS NEGRITAS =============*/
                    $objSheet->getStyle($columnas[$col] . $pos_fila)->getFont()->setBold(true)->setSize(12);
                    $objSheet->getStyle('A' . $pos_fila)->getFont()->setBold(true)->setSize(12);

                    if ($request->criterio == 'C' || $request->criterio == 'T' || $request->criterio == 'E')
                        $objSheet->getCell($columnas[$col] . $pos_fila)->setValue(number_format($total_fila, 2)); // total fila
                    else
                        $objSheet->getCell($columnas[$col] . $pos_fila)
                            ->setValue(number_format($total_positivos > 0 ? round($total_fila / $total_positivos, 2) : 0, 2)); // total fila

                    $pos_fila++;
                }

                /* ============== LETRAS NEGRITAS =============*/
                $objSheet->getStyle($columnas[$col] . $pos_fila)->getFont()->setBold(true)->setSize(12);
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle('A2:A' . intval(count($data['filas']) + 1))
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('e9ecef');
                $objSheet->getStyle($columnas[$pos_col] . '2:' . $columnas[$pos_col] . intval(count($data['filas']) + 1))
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('e9ecef');

                /* ---------------------------- FILA TOTALES ---------------------------- */
                /* ============== LETRAS NEGRITAS =============*/
                $objSheet->getStyle('A' . $pos_fila . ':' . $columnas[$pos_col] . $pos_fila)->getFont()->setBold(true)->setSize(12);
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle('A' . $pos_fila . ':' . $columnas[$pos_col] . $pos_fila)->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
                /* ============== TEXT COLOR =============*/
                $objSheet->getStyle('A' . $pos_fila . ':' . $columnas[$pos_col] . $pos_fila)->getFont()->getColor()->setRGB('ffffff');
                /* ============================================================================================================================ */
                $objSheet->getCell('A' . $pos_fila)
                    ->setValue($request->criterio == 'C' || $request->criterio == 'T' || $request->criterio == 'E' ? 'Total' : 'Promedio');

                $col = 1;
                $total = 0;
                foreach ($array_totales as $valor) {
                    if ($request->criterio == 'C' || $request->criterio == 'T' || $request->criterio == 'E')
                        $objSheet->getCell($columnas[$col] . $pos_fila)->setValue(number_format($valor['valor'], 2));
                    else
                        $objSheet->getCell($columnas[$col] . $pos_fila)
                            ->setValue(number_format($valor['positivos'] > 0 ? round($valor['valor'] / $valor['positivos'], 2) : 0, 2));
                    $total += $valor['valor'];
                    $col++;
                }

                if ($request->criterio == 'C' || $request->criterio == 'T' || $request->criterio == 'E')
                    $objSheet->getCell($columnas[$pos_col] . $pos_fila)->setValue(number_format($total, 2)); // total fila
                else
                    $objSheet->getCell($columnas[$pos_col] . $pos_fila)
                        ->setValue(number_format($positivos > 0 ? round($total / $positivos, 2) : 0, 2)); // total fila
            }

        } else {
            return '<div class="alert alert-warning text-center" > Debes ingresar desde - hasta </div > ';
        }
    }
}