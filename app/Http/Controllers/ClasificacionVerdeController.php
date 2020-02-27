<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Jobs\ResumenSemanaCosecha;
use yura\Modelos\ClasificacionUnitaria;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\Cosecha;
use yura\Modelos\DetalleClasificacionVerde;
use yura\Modelos\LoteRE;
use yura\Modelos\Recepcion;
use yura\Modelos\RecepcionClasificacionVerde;
use yura\Modelos\Semana;
use yura\Modelos\StockApertura;
use yura\Modelos\StockEmpaquetado;
use yura\Modelos\StockGuarde;
use yura\Modelos\Submenu;
use yura\Modelos\Variedad;
use Validator;

class ClasificacionVerdeController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.postcocecha.clasificacion_verde.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'annos' => DB::table('semana as s')
                ->select('s.anno')->distinct()
                ->where('s.estado', '=', 1)->orderBy('s.anno')->get(),
            'variedades' => Variedad::All()->where('estado', '=', 1),
            'unitarias' => getUnitarias(),
        ]);
    }

    public function buscar_clasificaciones(Request $request)
    {
        $datos = [
            'verde' => ClasificacionVerde::All()
                ->where('estado', 1)
                ->where('fecha_ingreso', $request->fecha_verde)
                ->first()
        ];

        return view('adminlte.gestion.postcocecha.clasificacion_verde.partials.listado', $datos);
    }

    public function buscar_detalles_reales(Request $request)
    {
        $listado = DB::table('detalle_clasificacion_verde as d')
            ->join('variedad as v', 'v.id_variedad', '=', 'd.id_variedad')
            ->join('clasificacion_unitaria as u', 'u.id_clasificacion_unitaria', '=', 'd.id_clasificacion_unitaria')
            ->join('unidad_medida as um', 'um.id_unidad_medida', '=', 'u.id_unidad_medida')
            ->select('d.*', 'v.nombre as nombre_variedad', 'v.siglas as siglas_variedad', 'um.siglas as unidad_medida', 'u.nombre as nombre_unitaria')->distinct();

        if ($request->id_variedad != '')
            $listado = $listado->where('d.id_variedad', '=', $request->id_variedad);

        $listado = $listado->orderBy('d.fecha_registro', 'asc')->orderBy('v.nombre', 'asc')->paginate(10);

        $datos = [
            'listado' => $listado
        ];

        return view('adminlte.gestion.postcocecha.clasificacion_verde.partials._listado_detalles_reales', $datos);
    }

    public function buscar_detalles_estandar(Request $request)
    {
        $listado = DB::table('detalle_clasificacion_verde as d')
            ->join('variedad as v', 'v.id_variedad', '=', 'd.id_variedad')
            ->select('d.id_variedad', 'd.id_clasificacion_unitaria')->distinct()
            ->where('d.id_clasificacion_verde', '=', $request->id_clasificacion_verde);

        if ($request->id_variedad != '')
            $listado = $listado->where('d.id_variedad', '=', $request->id_variedad);

        $listado = $listado->orderBy('v.nombre', 'asc')->simplePaginate(10);

        $datos = [
            'listado' => $listado,
            'clasificacion' => ClasificacionVerde::find($request->id_clasificacion_verde),
        ];

        return view('adminlte.gestion.postcocecha.clasificacion_verde.partials._listado_detalles_estandar', $datos);
    }

    public function add_verde(Request $request)
    {
        return view('adminlte.gestion.postcocecha.clasificacion_verde.forms.add', [
            'fecha' => $request->fecha
        ]);
    }

    public function buscar_recepciones_byFecha(Request $request)
    {
        $r = [];
        $variedades = [];
        $clasificacion_verde = '';
        if ($request->fecha != '') {
            $l = DB::table('recepcion')
                ->select('id_recepcion')->distinct()
                ->where('fecha_ingreso', 'like', '%' . $request->fecha . '%')
                ->where('estado', '=', 1)->get();

            $ids_recepcion = [];

            foreach ($l as $item) {
                $recepcion = Recepcion::find($item->id_recepcion);
                array_push($r, $recepcion);
                array_push($ids_recepcion, $item->id_recepcion);

                if (count(Recepcion::find($item->id_recepcion)->clasificaciones_verdes) > 0)
                    $clasificacion_verde = Recepcion::find($item->id_recepcion)->clasificaciones_verdes[0]->clasificacion_verde;
            }
            $v = DB::table('desglose_recepcion as d')
                ->select('d.id_variedad')->distinct()
                ->whereIn('d.id_recepcion', $ids_recepcion)
                ->where('d.estado', '=', 1)->get();
            foreach ($v as $i) {
                $tallos = 0;
                foreach ($r as $recepcion) {
                    $tallos += $recepcion->total_x_variedad($i->id_variedad);
                }
                array_push($variedades, [
                    'variedad' => Variedad::find($i->id_variedad),
                    'tallos' => $tallos]);
            }
        }
        return view('adminlte.gestion.postcocecha.clasificacion_verde.forms._add', [
            'clasificacion_verde' => $clasificacion_verde,
            'recepciones' => $r,
            'variedades' => $variedades,
        ]);
    }

    public function cargar_tabla_variedad(Request $request)
    {
        return view('adminlte.gestion.postcocecha.clasificacion_verde.forms.partials.tabla_variedad', [
            'variedad' => Variedad::find($request->id_variedad),
            'unitarias' => ClasificacionUnitaria::All()->where('estado', '=', 1),
            'clasificacion_verde' => ClasificacionVerde::find($request->id_clasificacion_verde)
        ]);
    }

    public function store(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'recepciones' => 'required',
            'id_variedad' => 'required',
            'fecha_ingreso' => 'required',
        ], [
            'recepciones.required' => 'Las recepciones son obligatorias',
            'id_variedad.required' => 'La variedad es obligatoria',
            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria',
        ]);
        $msg = '';
        $success = true;

        if (!$valida->fails()) {
            if (count($request->detalles) > 0) {
                $semana = Semana::All()
                    ->where('estado', 1)
                    ->where('id_variedad', $request->id_variedad)
                    ->where('fecha_inicial', '<=', $request->fecha_ingreso)
                    ->where('fecha_final', '>=', $request->fecha_ingreso)->first();
                if ($semana != '') {
                    $verde = new ClasificacionVerde();
                    $verde->id_semana = $semana->id_semana;
                    $verde->fecha_ingreso = $request->fecha_ingreso;
                    $verde->fecha_registro = date('Y-m-d H:i:s');

                    if ($verde->save()) {
                        $verde = ClasificacionVerde::All()->last();
                        $msg = '<div class="alert alert-success text-center">' .
                            '<p> Se ha guardado una nueva clasificación satisfactoriamente</p>'
                            . '</div>';
                        bitacora('clasificacion_verde', $verde->id_clasificacion_verde, 'I', 'Inserción satisfactoria de una nueva clasificación en verde');

                        /* ================= GUARDAR TABLA RECEPCION_CLASIFICACION_VERDE ===================*/
                        foreach (explode('|', $request->recepciones) as $id) {
                            $relacion = new RecepcionClasificacionVerde();
                            $relacion->id_recepcion = $id;
                            $relacion->id_clasificacion_verde = $verde->id_clasificacion_verde;
                            $relacion->fecha_registro = date('Y-m-d H:i:s');

                            if ($relacion->save()) {
                                $relacion = ClasificacionVerde::All()->last();
                                bitacora('recepcion_clasificacion_verde', $relacion->id_recepcion_clasificacion_verde, 'I', 'Inserción satisfactoria de una nueva relacion recepcion-clasificación en verde');
                            } else {
                                $success = false;
                                $msg = '<div class="alert alert-warning text-center">' .
                                    '<p> Ha ocurrido un problema al guardar la recepción del día ' . Recepcion::find($id)->fecha_ingreso . '</p>'
                                    . '</div>';
                            }
                        }

                        /* ================= GUARDAR TABLA DETALLE_CLASIFICACION_VERDE ===================*/
                        foreach ($request->detalles as $item) {
                            if (($item['cantidad_ramos'] * $item['tallos_x_ramos']) > 0) {
                                $detalle = new DetalleClasificacionVerde();
                                $detalle->id_variedad = $request->id_variedad;
                                $detalle->id_clasificacion_unitaria = $item['id_clasificacion_unitaria'];
                                $detalle->id_clasificacion_verde = $verde->id_clasificacion_verde;
                                $detalle->cantidad_ramos = $item['cantidad_ramos'];
                                $detalle->tallos_x_ramos = $item['tallos_x_ramos'];
                                $detalle->fecha_registro = date('Y-m-d H:i:s');
                                $detalle->fecha_ingreso = date('Y-m-d H:i');    // ojo

                                if ($detalle->save()) {
                                    $detalle = DetalleClasificacionVerde::All()->last();
                                    bitacora('detalle_clasificacion_verde', $detalle->id_detalle_clasificacion_verde, 'I', 'Inserción satisfactoria de un nuevo detalle de la clasificación en verde');
                                } else {
                                    $success = false;
                                    $msg = '<div class="alert alert-warning text-center">' .
                                        '<p> Ha ocurrido un problema al guardar el detalle de ' . $item['cantidad_ramos'] . ' ramos de ' .
                                        $item['tallos_x_ramos'] . ' tallos por ramo de ' . ClasificacionUnitaria::find($item['id_clasificacion_unitaria'])->nombre . '</p>'
                                        . '</div>';
                                }
                            }
                        }

                        /* ======================== ACTUALIZAR LA TABLA RESUMEN_COSECHA_SEMANA FINAL ====================== */
                        $semana_fin = getLastSemanaByVariedad($request->id_variedad);
                        ResumenSemanaCosecha::dispatch($verde->semana->codigo, $semana_fin->codigo, $request->id_variedad)
                            ->onQueue('resumen_cosecha_semanal');
                    } else {
                        $success = false;
                        $msg = '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la clasificación al sistema</p>'
                            . '</div>';
                    }
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> La fecha seleccionada no pertenece a ninguna semana programada anteriormente</p>'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Al menos ingrese un detalle de la clasificación</p>'
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

    public function store_detalles(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'id_clasificacion_verde' => 'required',
            'id_variedad' => 'required',
            'detalles' => 'required',
        ], [
            'id_clasificacion_verde.required' => 'La clasificación es obligatoria',
            'id_variedad.required' => 'La variedad es obligatoria',
            'detalles.required' => 'Los detalles son obligatorios',
        ]);
        $msg = '';
        $success = true;

        if (!$valida->fails()) {
            if (count($request->detalles) > 0) {
                $verde = ClasificacionVerde::find($request->id_clasificacion_verde);

                /* ================= GUARDAR TABLA DETALLE_CLASIFICACION_VERDE ===================*/
                foreach ($request->detalles as $item) {
                    if (($item['cantidad_ramos'] * $item['tallos_x_ramos']) > 0) {
                        $detalle = new DetalleClasificacionVerde();
                        $detalle->id_variedad = $request->id_variedad;
                        $detalle->id_clasificacion_unitaria = $item['id_clasificacion_unitaria'];
                        $detalle->id_clasificacion_verde = $verde->id_clasificacion_verde;
                        $detalle->cantidad_ramos = $item['cantidad_ramos'];
                        $detalle->tallos_x_ramos = $item['tallos_x_ramos'];
                        $detalle->fecha_registro = date('Y-m-d H:i:s');
                        $detalle->fecha_ingreso = date('Y-m-d H:i');    // ojo

                        if ($detalle->save()) {
                            $detalle = DetalleClasificacionVerde::All()->last();
                            bitacora('detalle_clasificacion_verde', $detalle->id_detalle_clasificacion_verde, 'I', 'Inserción satisfactoria de un nuevo detalle de la clasificación en verde');
                        } else {
                            $success = false;
                            $msg .= '<div class="alert alert-warning text-center">' .
                                '<p> Ha ocurrido un problema al guardar el detalle de ' . $item['cantidad_ramos'] . ' ramos de ' .
                                $item['tallos_x_ramos'] . ' tallos por ramo de ' . ClasificacionUnitaria::find($item['id_clasificacion_unitaria'])->nombre . '</p>'
                                . '</div>';
                        }
                    }
                }

                /* ================= GUARDAR TABLA RECEPCION_CLASIFICACION_VERDE ===================*/
                foreach (explode('|', $request->recepciones) as $item) {
                    $relacion = RecepcionClasificacionVerde::where('id_recepcion', '=', $item)->where('id_clasificacion_verde', '=', $verde->id_clasificacion_verde)->first();
                    if ($relacion == '') {
                        $relacion = new RecepcionClasificacionVerde();
                        $relacion->id_recepcion = $item;
                        $relacion->id_clasificacion_verde = $verde->id_clasificacion_verde;
                        $relacion->fecha_registro = date('Y-m-d H:i:s');
                        if ($relacion->save()) {
                            $relacion = ClasificacionVerde::All()->last();
                            bitacora('recepcion_clasificacion_verde', $relacion->id_recepcion_clasificacion_verde, 'I', 'Inserción satisfactoria de una nueva relacion recepcion-clasificación en verde');
                        } else {
                            $success = false;
                            $msg = '<div class="alert alert-warning text-center">' .
                                '<p> Ha ocurrido un problema al guardar la recepción del día ' . Recepcion::find($item)->fecha_ingreso . '</p>'
                                . '</div>';
                        }
                    }
                }

                if ($success) {
                    $msg = '<div class="alert alert-success text-center">' .
                        'Se ha guardado toda la información satisfactoriamente'
                        . '</div>';
                }

                /* ======================== ACTUALIZAR LA TABLA RESUMEN_COSECHA_SEMANA FINAL ====================== */
                $semana_fin = getLastSemanaByVariedad($request->id_variedad);
                ResumenSemanaCosecha::dispatch($verde->semana->codigo, $semana_fin->codigo, $request->id_variedad)
                    ->onQueue('resumen_cosecha_semanal');
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Al menos ingrese un detalle de la clasificación</p>'
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

    public function ver_clasificacion(Request $request)
    {
        if ($request->has('id_clasificacion_verde')) {
            $model = ClasificacionVerde::find($request->id_clasificacion_verde);
            if ($model != '') {
                return view('adminlte.gestion.postcocecha.clasificacion_verde.partials.detalles', [
                    'clasificacion' => $model,
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado la clasificación en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado ninguna clasificación</div>';
        }
    }

    public function detalles_reales(Request $request)
    {
        if ($request->has('id_clasificacion_verde')) {
            $model = ClasificacionVerde::find($request->id_clasificacion_verde);
            if ($model != '') {
                return view('adminlte.gestion.postcocecha.clasificacion_verde.partials._detalles_reales', [
                    'clasificacion' => $model,
                    'variedades' => Variedad::All()->where('estado', '=', 1),
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado la clasificación en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado ninguna clasificación</div>';
        }
    }

    public function detalles_estandar(Request $request)
    {
        if ($request->has('id_clasificacion_verde')) {
            $model = ClasificacionVerde::find($request->id_clasificacion_verde);
            if ($model != '') {
                return view('adminlte.gestion.postcocecha.clasificacion_verde.partials._detalles_estandar', [
                    'clasificacion' => $model,
                    'variedades' => Variedad::All()->where('estado', '=', 1),
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado la clasificación en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado ninguna clasificación</div>';
        }
    }

    public function clasificaciones_x_fecha(Request $request)
    {
        if ($request->has('id_clasificacion_verde')) {
            $model = ClasificacionVerde::find($request->id_clasificacion_verde);
            if ($model != '') {
                $fechas = [];
                foreach ($model->detalles as $det) {
                    if (!in_array(substr($det->fecha_ingreso, 0, 10), $fechas))
                        array_push($fechas, substr($det->fecha_ingreso, 0, 10));
                }

                $listado = [];
                foreach ($fechas as $f) {
                    $variedades = [];
                    foreach (getVariedades() as $var) {
                        $calibres = DB::table('detalle_clasificacion_verde')
                            ->select(DB::raw('sum(cantidad_ramos * tallos_x_ramos) as cant'), 'id_clasificacion_unitaria')
                            ->where('estado', '=', 1)
                            ->where('id_variedad', '=', $var->id_variedad)
                            ->where('id_clasificacion_verde', '=', $model->id_clasificacion_verde)
                            ->where('fecha_ingreso', 'like', $f . '%')
                            ->groupBy('id_clasificacion_unitaria')
                            ->orderBy('id_clasificacion_unitaria')
                            ->get();

                        if (count($calibres) > 0)
                            array_push($variedades, [
                                'variedad' => $var,
                                'calibres' => $calibres,
                            ]);
                    }
                    array_push($listado, [
                        'fecha' => $f,
                        'variedades' => $variedades,
                    ]);
                }

                return view('adminlte.gestion.postcocecha.clasificacion_verde.partials._clasificaciones_x_fecha', [
                    'clasificacion' => $model,
                    'listado' => $listado,
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado la clasificación en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado ninguna clasificación</div>';
        }
    }

    public function detalles_x_variedad(Request $request)
    {
        if ($request->has('id_clasificacion_verde')) {
            $model = ClasificacionVerde::find($request->id_clasificacion_verde);
            if ($model != '') {
                return view('adminlte.gestion.postcocecha.clasificacion_verde.partials._detalles_x_variedad', [
                    'clasificacion' => $model,
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado la clasificación en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado ninguna clasificación</div>';
        }
    }

    public function destinar_lotes(Request $request)
    {
        if ($request->has('id_clasificacion_verde')) {
            $model = ClasificacionVerde::find($request->id_clasificacion_verde);
            if ($model != '') {
                return view('adminlte.gestion.postcocecha.clasificacion_verde.partials.destinar_lotes', [
                    'clasificacion' => $model,
                    'variedad' => Variedad::find($request->id_variedad),
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado la clasificación en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado ninguna clasificación</div>';
        }
    }

    public function destinar_lotes_form(Request $request)
    {
        if ($request->has('id_clasificacion_verde')) {
            $model = ClasificacionVerde::find($request->id_clasificacion_verde);
            if ($model != '') {
                return view('adminlte.gestion.postcocecha.clasificacion_verde.partials.destinar_lotes_form', [
                    'clasificacion' => $model,
                    'variedad' => Variedad::find($request->id_variedad),
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado la clasificación en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado ninguna clasificación</div>';
        }
    }

    public function ver_lotes(Request $request)
    {
        if ($request->has('id_clasificacion_verde')) {
            $model = ClasificacionVerde::find($request->id_clasificacion_verde);
            if ($model != '') {
                //dd($model->lotes_reByVariedad($request->id_variedad)[3]->id_lote_re);
                return view('adminlte.gestion.postcocecha.clasificacion_verde.partials.ver_lotes', [
                    'clasificacion' => $model,
                    'variedad' => Variedad::find($request->id_variedad),
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado la clasificación en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado ninguna clasificación</div>';
        }
    }

    public function calcular_stock(Request $request)
    {
        $stock = getStock($request->id_variedad, $request->id_clasificacion_unitaria);
        $disponible = getStockToFecha($request->id_variedad, $request->id_clasificacion_unitaria, $request->fecha_ingreso, $request->dias);

        $fecha = strtotime('+' . $request->dias . ' day', strtotime($request->fecha_ingreso));
        $fecha = date('Y-m-d', $fecha);

        return [
            'stock' => $stock + $request->cantidad_tallos,
            'disponible' => $disponible + $request->cantidad_tallos,
            'fecha' => $fecha,
        ];
    }

    public function store_lote_re(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'id_clasificacion_verde' => 'required',
            'id_variedad' => 'required',
            'fecha' => 'required',
            'arreglo' => 'required',
        ], [
            'id_clasificacion_verde.required' => 'La clasificación es obligatoria',
            'id_variedad.required' => 'La variedad es obligatoria',
            'fecha.required' => 'La fecha es obligatoria',
            'arreglo.required' => 'Los lotes son obligatorios',
        ]);
        $msg = '';
        $success = true;

        if (!$valida->fails()) {
            if (count($request->arreglo) > 0) {
                $verde = ClasificacionVerde::find($request->id_clasificacion_verde);

                /* ================= ACTUALIZAR CLASIFICACION_VERDE =================*/
                if ($request->terminar == 0) {
                    $verde->activo = 0;

                    if ($verde->save()) {
                        bitacora('clasificacion_verde', $verde->id_clasificacion_verde, 'U', 'Actualizacion satisfactoria del campo activo de una clasificacion en verde');
                    } else {
                        $success = false;
                        $msg .= '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al terminar la clasificación en verde'
                            . '</div>';
                    }
                }

                $variedad = getVariedad($request->id_variedad);
                if ($variedad->estandar_apertura > 0) {
                    /* ================= GUARDAR TABLA LOTE_RE ===================*/
                    foreach ($request->arreglo as $item) {
                        if ($item['apertura'] > 0) {    // Se trata de una cantidad para apertura
                            $lote = new LoteRE();
                            $lote->id_variedad = $request->id_variedad;
                            $lote->id_clasificacion_unitaria = $item['id_clasificacion_unitaria'];
                            $lote->id_clasificacion_verde = $verde->id_clasificacion_verde;
                            $lote->fecha_registro = date('Y-m-d H:i:s');
                            $lote->cantidad_tallos = $item['apertura'];
                            $lote->etapa = 'A';
                            $lote->apertura = $request->fecha;

                            if ($lote->save()) {
                                $lote = LoteRE::All()->last();
                                bitacora('lote_re', $lote->id_lote_re, 'I', 'Inserción satisfactoria de un nuevo lote RE');

                                /* ================ GUARDAR EN TABLA STOCK_APERTURA ===============*/
                                $stock = new StockApertura();
                                $stock->fecha_registro = date('Y-m-d H:i:s');
                                $stock->fecha_inicio = $request->fecha;
                                $stock->cantidad_tallos = $lote->cantidad_tallos;
                                $stock->cantidad_disponible = $lote->cantidad_tallos;
                                $stock->id_variedad = $lote->id_variedad;
                                $stock->id_clasificacion_unitaria = $lote->id_clasificacion_unitaria;
                                $stock->dias = $item['dias'];
                                $stock->id_lote_re = $lote->id_lote_re;

                                if ($stock->save()) {
                                    $stock = StockApertura::All()->last();
                                    bitacora('stock_apertura', $stock->id_stock_apertura, 'I', 'Inserción satisfactoria de un nuevo lote RE a Stock');
                                } else {
                                    $success = false;
                                    $msg .= '<div class="alert alert-warning text-center">' .
                                        '<p> Ha ocurrido un problema al guardar en <strong>stock</strong> el lote de ' .
                                        ClasificacionUnitaria::find($item['id_clasificacion_unitaria'])->nombre .
                                        Variedad::find($request->id_variedad)->unidad_de_medida . '</p>'
                                        . '</div>';
                                }
                            } else {
                                $success = false;
                                $msg .= '<div class="alert alert-warning text-center">' .
                                    '<p> Ha ocurrido un problema al guardar el lote de ' . ClasificacionUnitaria::find($item['id_clasificacion_unitaria'])->nombre .
                                    Variedad::find($request->id_variedad)->unidad_de_medida . '</p>'
                                    . '</div>';
                            }
                        }
                        if ($item['guarde'] > 0) {    // Se trata de una cantidad para guarde

                            //dd('guarde');

                            $lote = new LoteRE();
                            $lote->id_variedad = $request->id_variedad;
                            $lote->id_clasificacion_unitaria = $item['id_clasificacion_unitaria'];
                            $lote->id_clasificacion_verde = $verde->id_clasificacion_verde;
                            $lote->fecha_registro = date('Y-m-d H:i:s');
                            $lote->cantidad_tallos = $item['guarde'];
                            $lote->etapa = 'C';
                            $lote->guarde_clasificacion = $request->fecha;
                            $lote->dias_guarde_clasificacion = $item['dias'];

                            if ($lote->save()) {
                                $lote = LoteRE::All()->last();
                                bitacora('lote_re', $lote->id_lote_re, 'I', 'Inserción satisfactoria de un nuevo lote RE');

                                /* ================ GUARDAR EN TABLA STOCK_GUARDE ===============*/
                                $stock = new StockGuarde();
                                $stock->fecha_registro = date('Y-m-d H:i:s');
                                $stock->fecha_inicio = $request->fecha;
                                $stock->cantidad_tallos = $lote->cantidad_tallos;
                                $stock->cantidad_disponible = $lote->cantidad_tallos;
                                $stock->id_variedad = $lote->id_variedad;
                                $stock->id_clasificacion_unitaria = $lote->id_clasificacion_unitaria;
                                $stock->dias = $item['dias'];
                                $stock->id_lote_re = $lote->id_lote_re;

                                if ($stock->save()) {
                                    $stock = StockApertura::All()->last();
                                    bitacora('stock_apertura', $stock->id_stock_apertura, 'I', 'Inserción satisfactoria de un nuevo lote RE a Stock');
                                } else {
                                    $success = false;
                                    $msg .= '<div class="alert alert-warning text-center">' .
                                        '<p> Ha ocurrido un problema al guardar en <strong>stock</strong> el lote de ' .
                                        ClasificacionUnitaria::find($item['id_clasificacion_unitaria'])->nombre .
                                        Variedad::find($request->id_variedad)->unidad_de_medida . '</p>'
                                        . '</div>';
                                }
                            } else {
                                $success = false;
                                $msg .= '<div class="alert alert-warning text-center">' .
                                    '<p> Ha ocurrido un problema al guardar el lote de ' . ClasificacionUnitaria::find($item['id_clasificacion_unitaria'])->nombre .
                                    Variedad::find($request->id_variedad)->unidad_de_medida . '</p>'
                                    . '</div>';
                            }
                        }
                    }
                } else {
                    /* ======================== MISMA OPERACION QUE SACAR DE APERTURA ======================== */
                    $ramos_estandar = 0;
                    foreach ($verde->detalles->where('id_variedad', $request->id_variedad) as $detalle) {
                        $ramos_estandar += $detalle->cantidad_tallos_estandar();
                    }
                    /* ------------------------------- ACTUALIZAR EL STOCK_EMPAQUETADO ------------------------ */
                    $empaquetado = StockEmpaquetado::All()
                        ->where('id_variedad', '=', $request->id_variedad)
                        ->where('empaquetado', '=', 0)
                        ->first();
                    if ($empaquetado == '') {
                        /* ========= CREAR STOCK_EMPAQUETADO ========== */
                        $empaquetado = new StockEmpaquetado();
                        $empaquetado->fecha_registro = date('Y-m-d H:i:s');
                        $empaquetado->id_variedad = $request->id_variedad;
                        $empaquetado->cantidad_ingresada = $ramos_estandar;
                        $empaquetado->save();
                        $empaquetado = StockEmpaquetado::All()->last();
                        bitacora('stock_empaquetado', $empaquetado->id_stock_empaquetado, 'I', 'Creacion satisfactoria de un stock empaquetado');
                    } else {
                        $empaquetado->cantidad_ingresada += $ramos_estandar;
                        $empaquetado->save();
                        bitacora('stock_empaquetado', $empaquetado->id_stock_empaquetado, 'U', 'Actualizacion satisfactoria de un stock empaquetado');
                    }
                }
                if ($success) {
                    $msg = '<div class="alert alert-success text-center">' .
                        'Se ha guardado toda la información satisfactoriamente'
                        . '</div>';
                }
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Al menos ingrese un lote de la clasificación</p>'
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

    public function store_lote_re_from(Request $request)
    {
        $msg = '';
        $success = true;
        foreach ($request->arreglo as $object) {
            $valida = Validator::make($object, [
                'id_clasificacion_verde' => 'required',
                'id_variedad' => 'required',
                'fecha' => 'required',
                'arreglo' => 'required',
            ], [
                'id_clasificacion_verde.required' => 'La clasificación es obligatoria',
                'id_variedad.required' => 'La variedad es obligatoria',
                'fecha.required' => 'La fecha es obligatoria',
                'arreglo.required' => 'Los lotes son obligatorios',
            ]);
            if (!$valida->fails()) {
                if (count($object['arreglo']) > 0) {
                    $verde = ClasificacionVerde::find($object['id_clasificacion_verde']);

                    /* ================= ACTUALIZR CLASIFICACION_VERDE =================*/
                    if ($object['terminar'] == 0) {
                        $verde->activo = 0;

                        if ($verde->save()) {
                            bitacora('clasificacion_verde', $verde->id_clasificacion_verde, 'U', 'Actualizacion satisfactoria del campo activo de una clasificacion en verde');
                        } else {
                            $success = false;
                            $msg .= '<div class="alert alert-warning text-center">' .
                                '<p> Ha ocurrido un problema al terminar la clasificación en verde'
                                . '</div>';
                        }
                    }

                    $variedad = getVariedad($object['id_variedad']);
                    if ($variedad->estandar_apertura > 0) {
                        /* ================= GUARDAR TABLA LOTE_RE ===================*/
                        foreach ($object['arreglo'] as $item) {
                            if ($item['apertura'] > 0) {    // Se trata de una cantidad para apertura

                                //dd('apertura');

                                $lote = new LoteRE();
                                $lote->id_variedad = $object['id_variedad'];
                                $lote->id_clasificacion_unitaria = $item['id_clasificacion_unitaria'];
                                $lote->id_clasificacion_verde = $verde->id_clasificacion_verde;
                                $lote->fecha_registro = date('Y-m-d H:i:s');
                                $lote->cantidad_tallos = $item['apertura'];
                                $lote->etapa = 'A';
                                $lote->apertura = $item['fecha'];

                                if ($lote->save()) {
                                    $lote = LoteRE::All()->last();
                                    bitacora('lote_re', $lote->id_lote_re, 'I', 'Inserción satisfactoria de un nuevo lote RE');

                                    /* ================ GUARDAR EN TABLA STOCK_APERTURA ===============*/
                                    $stock = StockApertura::All()
                                        ->where('estado', 1)
                                        ->where('fecha_inicio', $item['fecha'])
                                        ->where('id_clasificacion_unitaria', $lote->id_clasificacion_unitaria)
                                        ->where('id_variedad', $lote->id_variedad)
                                        ->first();
                                    if ($stock == '') {
                                        $stock = new StockApertura();
                                        $stock->fecha_registro = date('Y-m-d H:i:s');
                                        $stock->fecha_inicio = $item['fecha'];
                                        $stock->cantidad_tallos = $lote->cantidad_tallos;
                                        $stock->cantidad_disponible = $lote->cantidad_tallos;
                                        $stock->id_variedad = $lote->id_variedad;
                                        $stock->id_clasificacion_unitaria = $lote->id_clasificacion_unitaria;
                                        $stock->dias = $item['dias'];
                                        $stock->id_lote_re = $lote->id_lote_re;
                                    } else {
                                        $stock->cantidad_tallos += $lote->cantidad_tallos;
                                        $stock->cantidad_disponible += $lote->cantidad_tallos;
                                    }
                                    if ($stock->save()) {
                                        $stock = StockApertura::All()->last();
                                        bitacora('stock_apertura', $stock->id_stock_apertura, 'I', 'Inserción satisfactoria de un nuevo lote RE a Stock');
                                    } else {
                                        $success = false;
                                        $msg .= '<div class="alert alert-warning text-center">' .
                                            '<p> Ha ocurrido un problema al guardar en <strong>stock</strong> el lote de ' .
                                            ClasificacionUnitaria::find($item['id_clasificacion_unitaria'])->nombre .
                                            Variedad::find($object['id_variedad'])->unidad_de_medida . '</p>'
                                            . '</div>';
                                    }
                                } else {
                                    $success = false;
                                    $msg .= '<div class="alert alert-warning text-center">' .
                                        '<p> Ha ocurrido un problema al guardar el lote de ' . ClasificacionUnitaria::find($item['id_clasificacion_unitaria'])->nombre .
                                        Variedad::find($object['id_variedad'])->unidad_de_medida . '</p>'
                                        . '</div>';
                                }
                            }
                            if ($item['guarde'] > 0) {    // Se trata de una cantidad para guarde

                                //dd('guarde');

                                $lote = new LoteRE();
                                $lote->id_variedad = $object['id_variedad'];
                                $lote->id_clasificacion_unitaria = $item['id_clasificacion_unitaria'];
                                $lote->id_clasificacion_verde = $verde->id_clasificacion_verde;
                                $lote->fecha_registro = date('Y-m-d H:i:s');
                                $lote->cantidad_tallos = $item['guarde'];
                                $lote->etapa = 'C';
                                $lote->guarde_clasificacion = $item['fecha'];
                                $lote->dias_guarde_clasificacion = $item['dias'];

                                if ($lote->save()) {
                                    $lote = LoteRE::All()->last();
                                    bitacora('lote_re', $lote->id_lote_re, 'I', 'Inserción satisfactoria de un nuevo lote RE');

                                    /* ================ GUARDAR EN TABLA STOCK_GUARDE ===============*/
                                    $stock = new StockGuarde();
                                    $stock->fecha_registro = date('Y-m-d H:i:s');
                                    $stock->fecha_inicio = $item['fecha'];
                                    $stock->cantidad_tallos = $lote->cantidad_tallos;
                                    $stock->cantidad_disponible = $lote->cantidad_tallos;
                                    $stock->id_variedad = $lote->id_variedad;
                                    $stock->id_clasificacion_unitaria = $lote->id_clasificacion_unitaria;
                                    $stock->dias = $item['dias'];
                                    $stock->id_lote_re = $lote->id_lote_re;

                                    if ($stock->save()) {
                                        $stock = StockApertura::All()->last();
                                        bitacora('stock_apertura', $stock->id_stock_apertura, 'I', 'Inserción satisfactoria de un nuevo lote RE a Stock');
                                    } else {
                                        $success = false;
                                        $msg .= '<div class="alert alert-warning text-center">' .
                                            '<p> Ha ocurrido un problema al guardar en <strong>stock</strong> el lote de ' .
                                            ClasificacionUnitaria::find($item['id_clasificacion_unitaria'])->nombre .
                                            Variedad::find($object['id_variedad'])->unidad_de_medida . '</p>'
                                            . '</div>';
                                    }
                                } else {
                                    $success = false;
                                    $msg .= '<div class="alert alert-warning text-center">' .
                                        '<p> Ha ocurrido un problema al guardar el lote de ' . ClasificacionUnitaria::find($item['id_clasificacion_unitaria'])->nombre .
                                        Variedad::find($object['id_variedad'])->unidad_de_medida . '</p>'
                                        . '</div>';
                                }
                            }
                        }
                    } else {
                        /* ======================== MISMA OPERACION QUE SACAR DE APERTURA ======================== */
                        $ramos_estandar = 0;
                        foreach ($verde->detalles->where('id_variedad', $variedad->id_variedad) as $detalle) {
                            $ramos_estandar += $detalle->cantidad_tallos_estandar();
                        }
                        /* ------------------------------- ACTUALIZAR EL STOCK_EMPAQUETADO ------------------------ */
                        $empaquetado = StockEmpaquetado::All()
                            ->where('id_variedad', '=', $variedad->id_variedad)
                            ->where('empaquetado', '=', 0)
                            ->first();
                        if ($empaquetado == '') {
                            /* ========= CREAR STOCK_EMPAQUETADO ========== */
                            $empaquetado = new StockEmpaquetado();
                            $empaquetado->fecha_registro = date('Y-m-d H:i:s');
                            $empaquetado->id_variedad = $variedad->id_variedad;
                            $empaquetado->cantidad_ingresada = $ramos_estandar;
                            $empaquetado->save();
                            $empaquetado = StockEmpaquetado::All()->last();
                            bitacora('stock_empaquetado', $empaquetado->id_stock_empaquetado, 'I', 'Creacion satisfactoria de un stock empaquetado');
                        } else {
                            $empaquetado->cantidad_ingresada += $ramos_estandar;
                            $empaquetado->save();
                            bitacora('stock_empaquetado', $empaquetado->id_stock_empaquetado, 'U', 'Actualizacion satisfactoria de un stock empaquetado');
                        }
                    }
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Al menos ingrese un lote de la clasificación</p>'
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
        }
        if ($success) {
            $msg = '<div class="alert alert-success text-center">' .
                'Se han enviado todos los ramos clasificados a las aperturas'
                . '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function destinar_a(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'id_lote_re' => 'required',
            'etapa' => 'required',
        ], [
            'id_lote_re.required' => 'El lote es obligatorio',
            'etapa.required' => 'La etapa es obligatoria',
        ]);
        $msg = '';
        $success = true;

        if (!$valida->fails()) {
            $lote = LoteRE::find($request->id_lote_re);
            $lote->etapa = $request->etapa;
            if ($request->etapa == 'A')
                $lote->apertura = date('Y-m-d');
            elseif ($request->etapa == 'G')
                $lote->guarde_apertura = date('Y-m-d');
            elseif ($request->etapa == 'E')
                $lote->empaquetado = date('Y-m-d');

            if ($lote->save()) {
                $msg = '<div class="alert alert-success text-center">' .
                    'Se ha actualizado la información del lote satisfactoriamente'
                    . '</div>';
                bitacora('lote_re', $lote->id_lote_re, 'U', 'Actualización satisfactoria de lote RE');

                if ($request->etapa == 'A') {
                    $stock = new StockApertura();
                    $stock->fecha_inicio = $lote->apertura;
                    $stock->cantidad_tallos = $lote->cantidad_tallos;
                    $stock->cantidad_disponible = $lote->cantidad_tallos;
                    $stock->id_variedad = $lote->id_variedad;
                    $stock->id_clasificacion_unitaria = $lote->id_clasificacion_unitaria;
                    $stock->dias = Variedad::find($lote->id_variedad)->estandar_apertura;
                    $stock->id_lote_re = $lote->id_lote_re;

                    if ($stock->save()) {
                        $stock = StockApertura::All()->last();
                        bitacora('stock_apertura', $stock->id_stock_apertura, 'I', 'Inserción satisfactoria de un nuevo lote RE a Stock');
                    } else {
                        $success = false;
                        $msg .= '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar en <strong>stock</strong> el lote</p>'
                            . '</div>';
                    }
                }
            } else {
                $success = false;
                $msg .= '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información</p>'
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

    public function terminar(Request $request)
    {
        $model = ClasificacionVerde::find($request->id_clasificacion_verde);
        $model->activo = 0;

        if ($model->save()) {
            bitacora('clasificacion_verde', $model->id_clasificacion_verde, 'U', 'Terminacion satisfactia de una clasificacion en verde');

            return [
                'success' => true,
                'mensaje' => '<div class="alert alert-success text-center">Se ha terminado satisfactoriamente la clasificación</div>'
            ];
        } else {
            return [
                'success' => false,
                'mensaje' => '<div class="alert alert-warning text-center">No se pudo terminar la clasificación</div>'
            ];
        }
    }

    public function store_personal(Request $request)
    {
        $verde = ClasificacionVerde::find($request->id_clasificacion_verde);
        if ($verde == '')
            $verde = new ClasificacionVerde();
        $semana = Semana::All()
            ->where('fecha_inicial', '<=', $request->fecha_ingreso)
            ->where('fecha_final', '>=', $request->fecha_ingreso)->first();
        if ($semana != '') {
            $verde->id_semana = $semana->id_semana;
            $verde->personal = $request->personal;
            $verde->hora_inicio = $request->hora_inicio;
            $verde->fecha_ingreso = $request->fecha_ingreso;
            $verde->fecha_registro = date('Y-m-d H:i:s');

            if ($verde->save()) {
                if (ClasificacionVerde::find($request->id_clasificacion_verde) == '')
                    $verde = ClasificacionVerde::All()->last();
                bitacora('clasificacion_verde', $verde->id_clasificacion_verde, 'U', 'Actualización satisfactia de una clasificacion en verde');

                /* ================= GUARDAR TABLA RECEPCION_CLASIFICACION_VERDE ===================*/
                foreach (explode('|', $request->recepciones) as $item) {
                    $relacion = RecepcionClasificacionVerde::where('id_recepcion', '=', $item)->where('id_clasificacion_verde', '=', $verde->id_clasificacion_verde)->first();
                    if ($relacion == '') {
                        $relacion = new RecepcionClasificacionVerde();
                        $relacion->id_recepcion = $item;
                        $relacion->id_clasificacion_verde = $verde->id_clasificacion_verde;
                        $relacion->fecha_registro = date('Y-m-d H:i:s');
                        if ($relacion->save()) {
                            $relacion = ClasificacionVerde::All()->last();
                            bitacora('recepcion_clasificacion_verde', $relacion->id_recepcion_clasificacion_verde, 'I', 'Inserción satisfactoria de una nueva relacion recepcion-clasificación en verde');
                        } else {
                            return [
                                'success' => false,
                                'mensaje' => '<div class="alert alert-warning text-center">' .
                                    '<p> Ha ocurrido un problema al guardar la recepción del día ' . Recepcion::find($item)->fecha_ingreso . '</p>'
                                    . '</div>'
                            ];
                        }
                    }
                }

                /* ======================== ACTUALIZAR LA TABLA RESUMEN_COSECHA_SEMANA FINAL ====================== */
                $semana_fin = getLastSemanaByVariedad(getVariedades()[0]->id_variedad);
                ResumenSemanaCosecha::dispatch($verde->semana->codigo, $semana_fin->codigo, 0)
                    ->onQueue('resumen_cosecha_semanal');

                return [
                    'success' => true,
                    'mensaje' => '<div class="alert alert-success text-center">Se ha guardado satisfactoriamente el personal</div>'
                ];
            } else {
                return [
                    'success' => false,
                    'mensaje' => '<div class="alert alert-warning text-center">No se pudo guardar la información</div>'
                ];
            }
        } else {
            return [
                'success' => false,
                'mensaje' => '<div class="alert alert-warning text-center">' .
                    '<p> La fecha seleccionada no pertenece a ninguna semana programada anteriormente</p>'
                    . '</div>',
            ];
        }
    }

    public function ver_rendimiento(Request $request)
    {
        $clasificacion_verde = ClasificacionVerde::find($request->id_clasificacion_verde);
        $listado = DB::table('detalle_clasificacion_verde')
            ->select(DB::raw('sum(tallos_x_ramos * cantidad_ramos) as cantidad'), 'fecha_ingreso as fecha')
            ->where('estado', '=', 1)
            ->where('fecha_ingreso', 'like', $clasificacion_verde->fecha_ingreso . '%')
            ->groupBy('fecha_ingreso')
            ->orderBy('fecha_ingreso')
            ->get();
        return view('adminlte.gestion.postcocecha.clasificacion_verde.partials.rendimiento', [
            'clasificacion_verde' => $clasificacion_verde,
            'listado' => $listado,
        ]);
    }

    public function rendimiento_mesas(Request $request)
    {
        $verde = ClasificacionVerde::All()
            ->where('estado', 1)
            ->where('fecha_ingreso', $request->fecha_verde)
            ->first();
        $tallos = DB::table('detalle_clasificacion_verde')
            ->select(DB::raw('sum(cantidad_ramos * tallos_x_ramos) as cant'))
            ->where('estado', 1)
            ->where('fecha_ingreso', 'like', $request->fecha_verde . '%')
            ->get()[0]->cant;
        $query = DB::table('detalle_clasificacion_verde')
            ->where('estado', 1)
            ->where('fecha_ingreso', 'like', $request->fecha_verde . '%')
            ->get();

        return view('adminlte.gestion.postcocecha.clasificacion_verde.partials.rendimiento_mesas', [
            'verde' => $verde,
            'tallos' => $tallos,
            'query' => $query,
            'getCantidadHorasTrabajoVerde' => getCantidadHorasTrabajoVerde($request->fecha_verde),
            'fecha_verde' => $request->fecha_verde,
        ]);
    }

    /* ---------------------------------- MOBIL ---------------------------------------- */
    public function add_verde_mobil(Request $request)
    {
        $fecha = $request->fecha == '' ? date('Y-m-d') : $request->fecha;
        $verde = ClasificacionVerde::All()
            ->where('estado', 1)
            ->where('fecha_ingreso', $fecha)
            ->first();

        return view('adminlte.gestion.postcocecha.clasificacion_verde.forms.mobil.add', [
            'fecha' => $fecha,
            'verde' => $verde,
        ]);
    }

    public function store_form_verde(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'personal' => 'required',
            'hora_inicio' => 'required',
            'fecha' => 'required',
        ], [
            'personal.required' => 'El personal es obligatorio',
            'hora_inicio.required' => 'La hora de inicio es obligatoria',
            'fecha.required' => 'La fecha es obligatoria',
        ]);
        if (!$valida->fails()) {
            $model = ClasificacionVerde::All()->where('fecha_ingreso', $request->fecha)->first();
            if ($model == '') {
                $model = new ClasificacionVerde();
                $model->fecha_ingreso = $request->fecha;
                $model->id_semana = getSemanaByDate($request->fecha)->id_semana;
            }
            $model->personal = $request->personal;
            $model->hora_inicio = $request->hora_inicio;

            if ($model->save()) {
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado la clasificación verde satisfactoriamente</p>'
                    . '</div>';
            } else {
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                    . '</div>';
            }

            /* ================= GUARDAR TABLA RECEPCION_CLASIFICACION_VERDE ===================*/
            $verde = ClasificacionVerde::All()->where('fecha_ingreso', $request->fecha)->first();
            $recepciones = DB::table('recepcion')
                ->select('id_recepcion')->distinct()
                ->where('estado', 1)
                ->where('fecha_ingreso', 'like', '%' . $request->fecha_recepciones . '%')
                ->get();
            foreach ($recepciones as $item) {
                $relacion = RecepcionClasificacionVerde::where('id_recepcion', '=', $item->id_recepcion)->where('id_clasificacion_verde', '=', $verde->id_clasificacion_verde)->first();
                if ($relacion == '') {
                    $relacion = new RecepcionClasificacionVerde();
                    $relacion->id_recepcion = $item->id_recepcion;
                    $relacion->id_clasificacion_verde = $verde->id_clasificacion_verde;
                    $relacion->fecha_registro = date('Y-m-d H:i:s');
                    if ($relacion->save()) {
                        $relacion = ClasificacionVerde::All()->last();
                        bitacora('recepcion_clasificacion_verde', $relacion->id_recepcion_clasificacion_verde, 'I', 'Inserción satisfactoria de una nueva relacion recepcion-clasificación en verde');
                    } else {
                        $success = false;
                        $msg = '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la recepción del día ' . Recepcion::find($item->id_recepcion)->fecha_ingreso . '</p>'
                            . '</div>';
                    }
                }
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

    public function select_fecha_recepciones(Request $request)
    {
        $fecha_recepcion = $request->fecha_recepcion == '' ? date('Y-m-d') : $request->fecha_recepcion;
        $fecha_verde = $request->fecha_verde == '' ? date('Y-m-d') : $request->fecha_verde;
        $verde = ClasificacionVerde::All()
            ->where('estado', 1)
            ->where('fecha_ingreso', $fecha_verde)
            ->first();
        $cosecha = Cosecha::All()
            ->where('estado', 1)
            ->where('fecha_ingreso', $fecha_recepcion)
            ->first();

        return view('adminlte.gestion.postcocecha.clasificacion_verde.forms.mobil._formulario', [
            'fecha_recepcion' => $fecha_recepcion,
            'fecha_verde' => $fecha_verde,
            'verde' => $verde,
            'cosecha' => $cosecha,
        ]);
    }

    public function construir_tabla(Request $request)
    {
        $variedad = Variedad::find($request->variedad);
        return view('adminlte.gestion.postcocecha.clasificacion_verde.forms.mobil._tabla', [
            'variedad' => $variedad,
            'clasificaciones' => $variedad->clasificaciones,
        ]);
    }

    public function store_detalle_verde(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'verde' => 'required',
            'variedad' => 'required',
            'array_data' => 'required',
        ], [
            'verde.required' => 'La clasificación verde es obligatoria',
            'variedad.required' => 'La variedad es obligatoria',
            'array_data.required' => 'El listado de datos es obligatorio',
        ]);
        $success = true;
        $msg = '<div class="alert alert-success text-center">Se ha guardado la información satisfactoriamente</div>';
        if (!$valida->fails()) {
            $verde = ClasificacionVerde::find($request->verde);

            /* ================= GUARDAR TABLA DETALLE_CLASIFICACION_VERDE ===================*/
            foreach ($request->array_data as $data) {
                if ($data['mesa'] != '' && $data['ramos'] != '' && $data['tallos_x_ramo'] != '') {
                    $model = new DetalleClasificacionVerde();
                    $model->id_clasificacion_verde = $request->verde;
                    $model->id_variedad = $request->variedad;
                    $model->id_clasificacion_unitaria = $data['unitaria'];
                    $model->mesa = $data['mesa'];
                    $model->cantidad_ramos = $data['ramos'];
                    $model->tallos_x_ramos = $data['tallos_x_ramo'];
                    $model->fecha_registro = date('Y-m-d H:i:s');
                    $model->fecha_ingreso = date('Y-m-d H:i');    // ojo

                    $model->save();
                }
            }

            /* ================= GUARDAR TABLA RECEPCION_CLASIFICACION_VERDE ===================*/
            $recepciones = DB::table('recepcion')
                ->select('id_recepcion')->distinct()
                ->where('estado', 1)
                ->where('fecha_ingreso', 'like', '%' . $request->fecha_recepciones . '%')
                ->get();
            foreach ($recepciones as $item) {
                $relacion = RecepcionClasificacionVerde::where('id_recepcion', '=', $item->id_recepcion)->where('id_clasificacion_verde', '=', $verde->id_clasificacion_verde)->first();
                if ($relacion == '') {
                    $relacion = new RecepcionClasificacionVerde();
                    $relacion->id_recepcion = $item->id_recepcion;
                    $relacion->id_clasificacion_verde = $verde->id_clasificacion_verde;
                    $relacion->fecha_registro = date('Y-m-d H:i:s');
                    if ($relacion->save()) {
                        $relacion = ClasificacionVerde::All()->last();
                        bitacora('recepcion_clasificacion_verde', $relacion->id_recepcion_clasificacion_verde, 'I', 'Inserción satisfactoria de una nueva relacion recepcion-clasificación en verde');
                    } else {
                        $success = false;
                        $msg = '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la recepción del día ' . Recepcion::find($item->id_recepcion)->fecha_ingreso . '</p>'
                            . '</div>';
                    }
                }
            }

            /* ======================== ACTUALIZAR LA TABLA RESUMEN_COSECHA_SEMANA FINAL ====================== */
            $semana_fin = getLastSemanaByVariedad($request->variedad);
            ResumenSemanaCosecha::dispatch($verde->semana->codigo, $semana_fin->codigo, $request->variedad)
                ->onQueue('resumen_cosecha_semanal');
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
}