<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Modelos\ClasificacionUnitaria;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\DetalleClasificacionVerde;
use yura\Modelos\LoteRE;
use yura\Modelos\Recepcion;
use yura\Modelos\RecepcionClasificacionVerde;
use yura\Modelos\Semana;
use yura\Modelos\StockApertura;
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
        //dd($request->all());
        $listado = DB::table('clasificacion_verde as c')
            ->join('semana as s', 's.id_semana', '=', 'c.id_semana')
            ->select('c.*', 's.codigo as semana')->distinct();

        if ($request->fecha_desde != '')
            $listado = $listado->where('c.fecha_ingreso', '>=', $request->fecha_desde);
        if ($request->fecha_hasta != '')
            $listado = $listado->where('c.fecha_ingreso', '<=', $request->fecha_hasta);
        if ($request->semana_desde != '')
            $listado = $listado->where('s.codigo', '>=', $request->semana_desde);
        if ($request->semana_hasta != '')
            $listado = $listado->where('s.codigo', '<=', $request->semana_hasta);
        if ($request->anno != '')
            $listado = $listado->where('s.anno', '=', $request->anno);
        if ($request->variedad != '')
            $listado = $listado->where('c.anno', '=', $request->variedad);

        $listado = $listado->orderBy('s.anno', 'desc')->orderBy('c.fecha_ingreso', 'desc')
            ->paginate(10);

        $datos = [
            'listado' => $listado
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
            ->select('d.id_variedad', 'd.id_clasificacion_unitaria')->distinct();

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
            'unitarias' => ClasificacionUnitaria::All()->where('estado', '=', 1)
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
                if ($success) {
                    $msg = '<div class="alert alert-success text-center">' .
                        'Se ha guardado toda la información satisfactoriamente'
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
                $detalles = $verde->detalles;

                /* ================= GUARDAR TABLA LOTE_RE ===================*/
                foreach ($request->arreglo as $item) {
                    if ($item['apertura'] > 0) {    // Se trata de una cantidad para apertura

                        //dd('apertura');

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
}