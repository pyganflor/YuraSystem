<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\ClasificacionBlanco;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\Cosecha;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;

class crmRendimientoController extends Controller
{
    public function inicio(Request $request)
    {
        /* =========== TODAY ============= */
        $cosecha = Cosecha::All()->where('fecha_ingreso', date('Y-m-d'))->first();
        $verde = ClasificacionVerde::All()->where('fecha_ingreso', date('Y-m-d'))->first();
        $blanco = ClasificacionBlanco::All()->where('fecha_ingreso', date('Y-m-d'))->first();
        $today = [
            'cosecha' => [
                'cosecha' => $cosecha,
                'rendimiento' => $cosecha != '' ? $cosecha->getRendimiento() : 0
            ],
            'verde' => [
                'verde' => $verde,
                'rendimiento' => $verde != '' ? $verde->getRendimiento() : 0,
                'desecho' => $verde != '' ? $verde->desecho() : 0,
            ],
            'blanco' => [
                'blanco' => $blanco,
                'rendimiento' => $blanco != '' ? $blanco->getRendimiento() : 0,
                'desecho' => $blanco != '' ? $blanco->getDesecho() : 0,
            ]
        ];

        /* =========== SEMANAL ============= */
        $fechas = [];
        for ($i = 1; $i <= 7; $i++) {
            array_push($fechas, opDiasFecha('-', $i, date('Y-m-d')));
        }

        $r_cos = 0;
        $count_cos = 0;
        $r_ver = 0;
        $r_ver_r = 0;
        $d_ver = 0;
        $count_ver = 0;
        $r_bla = 0;
        $d_bla = 0;
        $count_bla = 0;
        foreach ($fechas as $f) {
            $cosecha = Cosecha::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();
            $verde = ClasificacionVerde::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();
            $blanco = ClasificacionBlanco::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();

            if ($cosecha != '') {
                $r_cos += $cosecha->getRendimiento();
                $count_cos++;
            }
            if ($verde != '') {
                $r_ver += $verde->getRendimiento();
                $r_ver_r += $verde->getRendimientoRamos();
                $d_ver += $verde->desecho();
                $count_ver++;
            }
            if ($blanco != '') {
                $r_bla += $blanco->getRendimiento();
                $d_bla += $blanco->getDesecho();
                $count_bla++;
            }
        }

        $semanal = [
            'cosecha' => [
                'rendimiento' => $count_cos > 0 ? round($r_cos / $count_cos, 2) : 0
            ],
            'verde' => [
                'rendimiento' => $count_ver > 0 ? round($r_ver / $count_ver, 2) : 0,
                'rendimiento_ramos' => $count_ver > 0 ? round($r_ver_r / $count_ver, 2) : 0,
                'desecho' => $count_ver > 0 ? round($d_ver / $count_ver, 2) : 0
            ],
            'blanco' => [
                'rendimiento' => $count_bla > 0 ? round($r_bla / $count_bla, 2) : 0,
                'desecho' => $count_bla > 0 ? round($d_bla / $count_bla, 2) : 0
            ]
        ];

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
        sort($annos);
        return view('adminlte.crm.rendimiento_desecho.inicio', [
            'today' => $today,
            'semanal' => $semanal,
            'annos' => $annos,

            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
        ]);
    }

    public function filtrar_graficas(Request $request)
    {
        $desde = $request->desde;
        $hasta = $request->hasta;

        $fechas = [];
        $data = [];
        $a_cos = [];
        $a_ver = [];
        $a_bla = [];
        $s_cos = [];
        $s_ver = [];
        $s_bla = [];
        if ($request->has('annos')) {
            $view = '_annos';
            $periodo = 'semanal';

            /* ======== Obtener las semanas donde se ha trabajado para cosecha-verde-blanco ======= */
            foreach ($request->annos as $a) {
                $labels = DB::table('cosecha')
                    ->select('fecha_ingreso as dia')->distinct()
                    ->where('fecha_ingreso', '>=', $a . '-01-01')
                    ->where('fecha_ingreso', '<=', $a + 1 . '-12-31')
                    ->orderBy('fecha_ingreso')
                    ->get();

                foreach ($labels as $l)
                    if (!in_array(substr(getSemanaByDate($l->dia)->codigo, 2), $s_cos))
                        array_push($s_cos, substr(getSemanaByDate($l->dia)->codigo, 2));
            }
            foreach ($request->annos as $a) {
                $labels = DB::table('clasificacion_verde')
                    ->select('fecha_ingreso as dia')->distinct()
                    ->where('fecha_ingreso', '>=', $a . '-01-01')
                    ->where('fecha_ingreso', '<=', $a + 1 . '-12-31')
                    ->orderBy('fecha_ingreso')
                    ->get();

                foreach ($labels as $l)
                    if (!in_array(substr(getSemanaByDate($l->dia)->codigo, 2), $s_ver))
                        array_push($s_ver, substr(getSemanaByDate($l->dia)->codigo, 2));
            }
            foreach ($request->annos as $a) {
                $labels = DB::table('clasificacion_blanco')
                    ->select('fecha_ingreso as dia')->distinct()
                    ->where('fecha_ingreso', '>=', $a . '-01-01')
                    ->where('fecha_ingreso', '<=', $a + 1 . '-12-31')
                    ->orderBy('fecha_ingreso')
                    ->get();

                foreach ($labels as $l)
                    if (!in_array(substr(getSemanaByDate($l->dia)->codigo, 2), $s_bla))
                        array_push($s_bla, substr(getSemanaByDate($l->dia)->codigo, 2));
            }

            foreach ($request->annos as $a) {
                /* ====== Obtener data de cosecha ======= Rendimiento ======= */
                $arreglo_cos = [];
                foreach ($s_cos as $l) {
                    $semana = Semana::All()->where('codigo', '=', substr($a, 2) . $l)->first();
                    $objects = Cosecha::All()
                        ->where('fecha_ingreso', '>=', $semana->fecha_inicial)
                        ->where('fecha_ingreso', '<=', $semana->fecha_final);
                    $valor = 0;

                    foreach ($objects as $item) {
                        if ($request->id_variedad == '')
                            $valor += $item->getRendimiento();
                        else
                            $valor += $item->getRendimientoByVariedad($request->id_variedad);
                    }
                    $valor = count($objects) > 0 ? round($valor / count($objects), 2) : 0;

                    array_push($arreglo_cos, $valor);
                }
                /* ====== Obtener data de verde ======= Rendimiento-Desecho ======= */
                $arreglo_ver = [];
                foreach ($s_ver as $l) {
                    $semana = Semana::All()->where('codigo', '=', substr($a, 2) . $l)->first();
                    $objects = ClasificacionVerde::All()
                        ->where('fecha_ingreso', '>=', $semana->fecha_inicial)
                        ->where('fecha_ingreso', '<=', $semana->fecha_final);
                    $valor = 0;

                    foreach ($objects as $item) {
                        if ($request->criterio == 'R') {
                            if ($request->id_variedad == '')
                                $valor += $item->getRendimiento();
                            else
                                $valor += $item->getRendimientoByVariedad($request->id_variedad);
                        }
                        if ($request->criterio == 'D') {
                            if ($request->id_variedad == '')
                                $valor += $item->desecho();
                            else
                                $valor += $item->desechoByVariedad($request->id_variedad);
                        }
                    }
                    $valor = count($objects) > 0 ? round($valor / count($objects), 2) : 0;

                    array_push($arreglo_ver, $valor);
                }
                /* ====== Obtener data de verde ======= Rendimiento-Desecho ======= */
                $arreglo_bla = [];
                foreach ($s_bla as $l) {
                    $semana = Semana::All()->where('codigo', '=', substr($a, 2) . $l)->first();
                    $objects = ClasificacionBlanco::All()
                        ->where('fecha_ingreso', '>=', $semana->fecha_inicial)
                        ->where('fecha_ingreso', '<=', $semana->fecha_final);
                    $valor = 0;

                    foreach ($objects as $item) {
                        if ($request->criterio == 'R') {
                            if ($request->id_variedad == '')
                                $valor += $item->getRendimiento();
                            else
                                $valor += $item->getRendimientoByVariedad($request->id_variedad);
                        }
                        if ($request->criterio == 'D') {
                            if ($request->id_variedad == '')
                                $valor += $item->getDesecho();
                            else
                                $valor += $item->getDesechoByVariedad($request->id_variedad);
                        }
                    }
                    $valor = count($objects) > 0 ? round($valor / count($objects), 2) : 0;

                    array_push($arreglo_bla, $valor);
                }

                /* ========= GUARDAR AÑO COSECHA ========= */
                array_push($a_cos, [
                    'anno' => $a,
                    'arreglo' => $arreglo_cos,
                ]);

                /* ========= GUARDAR AÑO VERDE ========= */
                array_push($a_ver, [
                    'anno' => $a,
                    'arreglo' => $arreglo_ver,
                ]);

                /* ========= GUARDAR AÑO BLANCO ========= */
                array_push($a_bla, [
                    'anno' => $a,
                    'arreglo' => $arreglo_bla,
                ]);
            }
        } else {
            $view = 'graficas';

            if ($request->diario == 'true') {
                $periodo = 'diario';

                $array_cosecha = [];
                $array_verde = [];
                $array_blanco = [];
                if ($request->id_variedad == '') {
                    $fechas = [];
                    $dias = [];
                    for ($i = $desde; $i >= 1; $i--) {
                        array_push($dias, opDiasFecha('-', $i, date('Y-m-d')));
                    }

                    foreach ($dias as $f) {
                        $cosecha = Cosecha::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();
                        $verde = ClasificacionVerde::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();
                        $blanco = ClasificacionBlanco::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();

                        if ($cosecha != '' && $verde != '' && $blanco != '') {
                            array_push($fechas, $f);

                            array_push($array_cosecha, $cosecha != '' ? [
                                'rendimiento' => $cosecha->getRendimiento(),]
                                : [
                                    'rendimiento' => 0,
                                ]);
                            array_push($array_verde, $verde != '' ? [
                                'rendimiento' => $verde->getRendimiento(),
                                'desecho' => $verde->desecho()]
                                : [
                                    'rendimiento' => 0,
                                    'desecho' => 0
                                ]);
                            array_push($array_blanco, $blanco != '' ? [
                                'rendimiento' => $blanco->getRendimiento(),
                                'desecho' => $blanco->getDesecho()]
                                : [
                                    'rendimiento' => 0,
                                    'desecho' => 0
                                ]);
                        }
                    }
                } else {
                    $fechas = [];
                    $dias = [];
                    for ($i = $desde; $i >= 1; $i--) {
                        array_push($dias, opDiasFecha('-', $i, date('Y-m-d')));
                    }

                    foreach ($dias as $f) {
                        $cosecha = Cosecha::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();
                        $verde = ClasificacionVerde::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();
                        $blanco = ClasificacionBlanco::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();

                        if ($cosecha != '' && $verde != '' && $blanco != '') {
                            array_push($fechas, $f);
                            array_push($array_cosecha, $cosecha != '' ? [
                                'rendimiento' => $cosecha->getRendimientoByVariedad($request->id_variedad),]
                                : [
                                    'rendimiento' => 0,
                                ]);
                            array_push($array_verde, $verde != '' ? [
                                'rendimiento' => $verde->getRendimientoByVariedad($request->id_variedad),
                                'desecho' => $verde->desechoByVariedad($request->id_variedad)]
                                : [
                                    'rendimiento' => 0,
                                    'desecho' => 0
                                ]);
                            array_push($array_blanco, $blanco != '' ? [
                                'rendimiento' => $blanco->getRendimientoByVariedad($request->id_variedad),
                                'desecho' => $blanco->getDesechoByVariedad($request->id_variedad)]
                                : [
                                    'rendimiento' => 0,
                                    'desecho' => 0
                                ]);
                        }
                    }
                }

                $data = [
                    'cosecha' => $array_cosecha,
                    'verde' => $array_verde,
                    'blanco' => $array_blanco,
                ];
            } else if ($request->semanal == 'true') {
                $periodo = 'semanal';

                $array_cosecha = [];
                $array_verde = [];
                $array_blanco = [];


                $fechas = [];

                for ($i = $desde; $i >= 1; $i--) {  /* ======== Construir el arreglo de semanas [codigo1, codigo2, ..., codigoN]*/
                    $semana = Semana::All()->where('estado', 1)
                        ->where('fecha_inicial', '<=', opDiasFecha('-', $i, date('Y-m-d')))
                        ->where('fecha_final', '>=', opDiasFecha('-', $i, date('Y-m-d')))
                        ->first();

                    if (!in_array($semana->codigo, $fechas))
                        array_push($fechas, $semana->codigo);
                }

                foreach ($fechas as $f) {   /* =========== Recorro las semanas =========== */
                    $semana = Semana::All()->where('estado', 1)->where('codigo', $f)->first();  // Obtengo la semana por el codigo

                    /* ======== Obtengo los arreglos de las cosechas, verdes y blancos de cada semana ========= */
                    $cosecha = Cosecha::All()->where('estado', 1)
                        ->where('fecha_ingreso', '>=', $semana->fecha_inicial)
                        ->where('fecha_ingreso', '<=', $semana->fecha_final);
                    $verde = ClasificacionVerde::All()->where('estado', 1)
                        ->where('fecha_ingreso', '>=', $semana->fecha_inicial)
                        ->where('fecha_ingreso', '<=', $semana->fecha_final);
                    $blanco = ClasificacionBlanco::All()->where('estado', 1)
                        ->where('fecha_ingreso', '>=', $semana->fecha_inicial)
                        ->where('fecha_ingreso', '<=', $semana->fecha_final);

                    /* ========== Obtengo el rendimiento de las cosechas en la semana i ========== */
                    $r_cos = 0;
                    foreach ($cosecha as $c) {
                        if ($request->id_variedad == '')
                            $r_cos += $c->getRendimiento();
                        else
                            $r_cos += $c->getRendimientoByVariedad($request->id_variedad);
                    }
                    array_push($array_cosecha, count($cosecha) > 0 ? [
                        'rendimiento' => round($r_cos / count($cosecha), 2)]
                        : [
                            'rendimiento' => 0,
                        ]);

                    /* ========== Obtengo el rendimiento-desecho de los verdes en la semana i ========== */
                    $r_ver = 0;
                    $d_ver = 0;
                    foreach ($verde as $v) {
                        if ($request->id_variedad == '') {
                            $r_ver += $v->getRendimiento();
                            $d_ver += $v->desecho();
                        } else {
                            $r_ver += $v->getRendimientoByVariedad($request->id_variedad);
                            $d_ver += $v->desechoByVariedad($request->id_variedad);
                        }
                    }
                    array_push($array_verde, count($verde) > 0 ? [
                        'rendimiento' => round($r_ver / count($verde), 2),
                        'desecho' => round($d_ver / count($verde), 2)]
                        : [
                            'rendimiento' => 0,
                            'desecho' => 0
                        ]);

                    /* ========== Obtengo el rendimiento-desecho de los blancos en la semana i ========== */
                    $r_bla = 0;
                    $d_bla = 0;
                    foreach ($blanco as $b) {
                        if ($request->id_variedad == '') {
                            $r_bla += $b->getRendimiento();
                            $d_bla += $b->getDesecho();
                        } else {
                            $r_bla += $b->getRendimientoByVariedad($request->id_variedad);
                            $d_bla += $b->getDesechoByVariedad($request->id_variedad);
                        }
                    }
                    array_push($array_blanco, count($blanco) > 0 ? [
                        'rendimiento' => round($r_bla / count($blanco), 2),
                        'desecho' => round($d_bla / count($blanco), 2)]
                        : [
                            'rendimiento' => 0,
                            'desecho' => 0
                        ]);
                }

                $data = [
                    'cosecha' => $array_cosecha,
                    'verde' => $array_verde,
                    'blanco' => $array_blanco,
                ];
            }
        }

        return view('adminlte.crm.rendimiento_desecho.partials.' . $view, [
            'labels' => $fechas,
            's_cos' => $s_cos,
            'a_cos' => $a_cos,
            's_ver' => $s_ver,
            'a_ver' => $a_ver,
            's_bla' => $s_bla,
            'a_bla' => $a_bla,
            'data' => $data,
            'periodo' => $periodo,
            'criterio' => $request->criterio,
        ]);
    }

    public function desglose_indicador(Request $request)
    {
        //dd($request->all());

        $fechas = [];
        for ($i = 7; $i >= 1; $i--) {
            array_push($fechas, opDiasFecha('-', $i, date('Y-m-d')));
        }

        $max_min = DB::table('recepcion')
            ->select(DB::raw('min(fecha_registro) as min'), DB::raw('max(fecha_registro) as max'))
            ->where('estado', '=', 1)
            ->where('fecha_ingreso', '>=', opDiasFecha('-', 7, date('Y-m-d')))
            ->where('fecha_ingreso', '<=', opDiasFecha('-', 1, date('Y-m-d')))
            ->get();

        $arreglo_horarios = [];
        $arreglo_dias = [];
        foreach ($fechas as $f) {
            $flag = false;

            if ($request->option == 'cosecha')
                $object = Cosecha::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();
            if ($request->option == 'verde')
                $object = ClasificacionVerde::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();
            if ($request->option == 'blanco')
                $object = ClasificacionBlanco::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();

            if ($request->criterio_desglose == '' || $request->criterio_desglose == 1) {    // Mostrar por Horarios
                $horas_x_dia = [];
                foreach (getIntervalosHorasDiarias() as $int) {
                    if (1) {    // optimizar rango visible en la grafica
                        $inicio = $f . ' ' . $int['inicio'];
                        $fin = $f . ' ' . $int['fin'];
                        if ($object != '' && $object->personal != '') {
                            if ($request->id_variedad == '') {
                                if ($request->option == 'cosecha')
                                    $valor = round($object->getTotalTallosByIntervalo($inicio, $fin) / $object->personal, 2);
                                if ($request->option == 'verde')
                                    $valor = round($object->getTotalTallosByIntervalo($inicio, $fin) / $object->personal, 2);
                                if ($request->option == 'blanco')
                                    $valor = round($object->getTotalRamosByIntervaloFecha($inicio, $fin) / $object->personal, 2);
                                array_push($horas_x_dia, [
                                    'intervalo' => $int['inicio'] . '-' . $int['fin'],
                                    'valor' => $valor
                                ]);
                            } else {
                                if ($request->option == 'cosecha')
                                    $valor = round($object->getTotalTallosByIntervaloVariedad($inicio, $fin, $request->id_variedad) / $object->personal, 2);
                                if ($request->option == 'verde')
                                    $valor = round($object->getTotalTallosByVariedadIntervaloFecha($request->id_variedad, $inicio, $fin) / $object->personal, 2);
                                if ($request->option == 'blanco')
                                    $valor = round($object->getTotalRamosByVariedadIntervaloFecha($request->id_variedad, $inicio, $fin) / $object->personal, 2);
                                array_push($horas_x_dia, [
                                    'intervalo' => $int['inicio'] . '-' . $int['fin'],
                                    'valor' => $valor
                                ]);
                            }

                            $flag = true;
                        } else {
                            array_push($horas_x_dia, [
                                'intervalo' => $int['inicio'] . '-' . $int['fin'],
                                'valor' => 0
                            ]);
                        }
                    }
                }

                if ($flag)
                    array_push($arreglo_horarios, [
                        'fecha' => $f,
                        'arreglo' => $horas_x_dia]);
            }

            if ($request->criterio_desglose == 2) { // Mostrar por días
                if ($object != '' && $object->personal > 0) {
                    if ($request->id_variedad == '') {
                        if ($request->option == 'cosecha')
                            array_push($arreglo_dias, $object->getRendimiento());
                        if ($request->option == 'verde') {
                            if ($request->criterio_tipo == 'R')
                                array_push($arreglo_dias, $object->getRendimiento());
                            if ($request->criterio_tipo == 'D')
                                array_push($arreglo_dias, $object->desecho());
                        }
                        if ($request->option == 'blanco') {
                            if ($request->criterio_tipo == 'R')
                                array_push($arreglo_dias, $object->getRendimiento());
                            if ($request->criterio_tipo == 'D')
                                array_push($arreglo_dias, $object->getDesecho());
                        }
                    } else {
                        if ($request->option == 'cosecha')
                            array_push($arreglo_dias, $object->getRendimientoByVariedad($request->id_variedad));
                        if ($request->option == 'verde') {
                            if ($request->criterio_tipo == 'R')
                                array_push($arreglo_dias, $object->getRendimientoByVariedad($request->id_variedad));
                            if ($request->criterio_tipo == 'D')
                                array_push($arreglo_dias, $object->desechoByVariedad($request->id_variedad));
                        }
                        if ($request->option == 'blanco') {
                            if ($request->criterio_tipo == 'R')
                                array_push($arreglo_dias, $object->getRendimientoByVariedad($request->id_variedad));
                            if ($request->criterio_tipo == 'D')
                                array_push($arreglo_dias, $object->getDesechoByVariedad($request->id_variedad));
                        }
                    }
                } else {
                    array_push($arreglo_dias, 0);
                }
            }
        }

        //dd($request->all());
        return view('adminlte.crm.rendimiento_desecho.partials.desgloses_indicador.' . $request->option, [
            'fechas' => $fechas,
            'arreglo_horarios' => $arreglo_horarios,
            'arreglo_dias' => $arreglo_dias,
            'max_min' => $max_min,
            'option' => $request->option,
            'id_variedad' => $request->id_variedad,
            'criterio_desglose' => $request->criterio_desglose != '' ? $request->criterio_desglose : 1,
            'criterio_tipo' => $request->criterio_tipo != '' ? $request->criterio_tipo : 'R',
        ]);
    }
}