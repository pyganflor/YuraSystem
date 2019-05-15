<?php

namespace yura\Http\Controllers\CRM;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\ClasificacionBlanco;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\Cosecha;
use yura\Modelos\Semana;

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
        $annos = DB::table('historico_ventas')
            ->select('anno')->distinct()
            ->get();

        return view('adminlte.crm.rendimiento_desecho.inicio', [
            'today' => $today,
            'semanal' => $semanal,
            'annos' => $annos,
        ]);
    }

    public function filtrar_graficas(Request $request)
    {
        $desde = $request->desde;
        $hasta = $request->hasta;

        $arreglo_annos = [];
        if ($request->has('annos')) {
            $view = '_annos';

            $fechas = [];

            $data = [];
            $periodo = 'mensual';

            foreach ($request->annos as $anno) {
                $arreglo_valores = [];
                $arreglo_fisicas = [];
                $arreglo_cajas = [];
                $arreglo_precios = [];

                foreach (getMeses(TP_NUMERO) as $mes) {
                    $query = DB::table('historico_ventas')
                        ->select(DB::raw('sum(valor) as valor'), DB::raw('sum(cajas_fisicas) as cajas_fisicas'),
                            DB::raw('sum(cajas_equivalentes) as cajas_equivalentes'),
                            DB::raw('sum(precio_x_ramo) as precio_x_ramo'))
                        ->where('anno', '=', $anno)
                        ->where('mes', '=', $mes);
                    $count_query = DB::table('historico_ventas')
                        ->select(DB::raw('count(*) as count'))
                        ->where('anno', '=', $anno)
                        ->where('mes', '=', $mes);

                    if ($request->id_variedad != '') {
                        $query = $query->where('id_variedad', '=', $request->id_variedad);
                        $count_query = $count_query->where('id_variedad', '=', $request->id_variedad);
                    }
                    if ($request->x_cliente == 'true' && $request->id_cliente != '') {
                        $query = $query->where('id_cliente', '=', $request->id_cliente);
                        $count_query = $count_query->where('id_cliente', '=', $request->id_cliente);
                    }
                    $query = $query->get();
                    $count_query = $count_query->get();


                    array_push($arreglo_valores, count($query) > 0 ? round($query[0]->valor, 2) : 0);
                    array_push($arreglo_fisicas, count($query) > 0 ? round($query[0]->cajas_fisicas, 2) : 0);
                    array_push($arreglo_cajas, count($query) > 0 ? round($query[0]->cajas_equivalentes, 2) : 0);
                    array_push($arreglo_precios, (count($query) > 0 && $count_query[0]->count > 0) ? round($query[0]->precio_x_ramo / $count_query[0]->count, 2) : 0);
                }
                array_push($arreglo_annos, [
                    'anno' => $anno,
                    'valores' => $arreglo_valores,
                    'fisicas' => $arreglo_fisicas,
                    'equivalentes' => $arreglo_cajas,
                    'precios' => $arreglo_precios,
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
                    for ($i = $desde; $i >= 1; $i--) {
                        array_push($fechas, opDiasFecha('-', $i, date('Y-m-d')));
                    }

                    foreach ($fechas as $f) {
                        $cosecha = Cosecha::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();
                        $verde = ClasificacionVerde::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();
                        $blanco = ClasificacionBlanco::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();

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
                } else {
                    $fechas = [];
                    for ($i = $desde; $i >= 1; $i--) {
                        array_push($fechas, opDiasFecha('-', $i, date('Y-m-d')));
                    }

                    foreach ($fechas as $f) {
                        $cosecha = Cosecha::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();
                        $verde = ClasificacionVerde::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();
                        $blanco = ClasificacionBlanco::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();

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

                if ($request->id_variedad == '') {
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
                            $r_cos += $c->getRendimiento();
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
                            $r_ver += $v->getRendimiento();
                            $d_ver += $v->desecho();
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
                            $r_bla += $b->getRendimiento();
                            $d_bla += $b->getDesecho();
                        }
                        array_push($array_blanco, count($blanco) > 0 ? [
                            'rendimiento' => round($r_bla / count($blanco), 2),
                            'desecho' => round($d_bla / count($blanco), 2)]
                            : [
                                'rendimiento' => 0,
                                'desecho' => 0
                            ]);
                    }
                } else {
                    dd(55);
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
            'arreglo_annos' => $arreglo_annos,
            'data' => $data,
            'periodo' => $periodo,
            'criterio' => $request->criterio,
        ]);
    }

    public function desglose_indicador(Request $request)
    {
        $fechas = [];
        for ($i = 1; $i <= 7; $i++) {
            array_push($fechas, opDiasFecha('-', $i, date('Y-m-d')));
        }

        $arreglo_dias = [];
        foreach ($fechas as $f) {
            $cosecha = Cosecha::All()->where('estado', 1)->where('fecha_ingreso', $f)->first();

            $horas_x_dia = [];
            foreach (getIntervalosHorasDiarias() as $int) {
                $inicio = $f . ' ' . $int['inicio'];
                $fin = $f . ' ' . $int['fin'];
                if ($cosecha != '' && $cosecha->personal != '')
                    array_push($horas_x_dia, [
                        'intervalo' => $int['inicio'] . '-' . $int['fin'],
                        'valor' => round($cosecha->getTotalTallosByIntervalo($inicio, $fin) / $cosecha->personal, 2)
                    ]);
                else
                    array_push($horas_x_dia, [
                        'intervalo' => $int['inicio'] . '-' . $int['fin'],
                        'valor' => 0
                    ]);
            }

            array_push($arreglo_dias, [
                'fecha' => $f,
                'arreglo' => $horas_x_dia]);
        }

        return view('adminlte.crm.rendimiento_desecho.partials.desgloses_indicador.' . $request->option, [
            'fechas' => $fechas,
            'arreglo_dias' => $arreglo_dias,
        ]);
    }
}