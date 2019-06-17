<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\LoteRE;
use yura\Modelos\StockApertura;
use yura\Modelos\Submenu;
use yura\Modelos\Variedad;
use Validator;

class LotesController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.postcocecha.lotes.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'annos' => DB::table('semana as s')
                ->select('s.anno')->distinct()
                ->where('s.estado', '=', 1)->orderBy('s.anno')->get(),
            'variedades' => Variedad::All()->where('estado', '=', 1),
            'unitarias' => getUnitarias(),
        ]);
    }

    public function buscar_lotes(Request $request)
    {
        //dd($request->all());
        $l = DB::table('lote_re as l')
            ->select('l.id_lote_re')->distinct();

        if ($request->etapa != '')
            $l = $l->where('l.etapa', '=', $request->etapa);
        if ($request->variedad != '')
            $l = $l->where('l.id_variedad', '=', $request->variedad);
        if ($request->unitaria != '')
            $l = $l->where('l.id_clasificacion_unitaria', '=', $request->unitaria);

        $l = $l->get();

        $fecha_desde = $request->fecha_desde == '' ? '1900-01-01' : $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta == '' ? date('Y-m-d') : $request->fecha_hasta;

        $r = [];

        foreach ($l as $item) {
            $maximo_apertura = getVariedad(getLoteREById($item->id_lote_re)->id_variedad)->maximo_apertura;
            if ($request->en_tiempo == '0') {
                $compare = getLoteREById($item->id_lote_re)->getCurrentEstancia() > $maximo_apertura;
            } elseif ($request->en_tiempo == '1') {
                $compare = getLoteREById($item->id_lote_re)->getCurrentEstancia() <= $maximo_apertura;
            } else {
                $compare = true;
            }

            if (getLoteREById($item->id_lote_re)->getCurrentFecha() >= $fecha_desde &&
                getLoteREById($item->id_lote_re)->getCurrentFecha() <= $fecha_hasta &&
                $compare) {
                array_push($r, $item->id_lote_re);
            }
        }

        $listado = DB::table('lote_re')->select('id_lote_re')->distinct()->whereIn('id_lote_re', $r)
            ->orderBy('fecha_registro', 'desc')->paginate(20);

        $datos = [
            'listado' => $listado,
            'calibre' => $request->clasificacion_ramo != '' ? ClasificacionRamo::find($request->clasificacion_ramo) : ''
        ];

        return view('adminlte.gestion.postcocecha.lotes.partials.listado', $datos);
    }

    public function ver_lote(Request $request)
    {
        if ($request->has('id_lote_re')) {
            $model = LoteRE::find($request->id_lote_re);
            if ($model != '') {
                return view('adminlte.gestion.postcocecha.lotes.partials.detalles', [
                    'lote' => $model,
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado el lote en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado ningún lote</div>';
        }
    }

    public function etapas(Request $request)
    {
        if ($request->has('id_lote_re')) {
            $model = LoteRE::find($request->id_lote_re);
            if ($model != '') {
                return view('adminlte.gestion.postcocecha.lotes.partials._etapas', [
                    'lote' => $model,
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado el lote en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado ningún lote</div>';
        }
    }

    public function store_etapa(Request $request)
    {
        //dd($request->all());
        $valida = Validator::make($request->all(), [
            'fecha' => 'required|',
            'id_lote_re' => 'required|',
            'etapa' => 'required|',
        ], [
            'id_lote_re.required' => 'El lote es obligatorio',
            'fecha.required' => 'La fecha es obligatoria',
            'etapa.required' => 'La etapa actual es obligatoria',
        ]);
        if (!$valida->fails()) {
            if ($request->etapa == 'E') {
                $dias = 0;
            } else {
                if (!$request->has('dias') || $request->dias == '') {
                    return [
                        'mensaje' => '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                            . '</div>',
                        'success' => false
                    ];
                } else {
                    $dias = $request->dias;
                }
            }
            $success = true;
            $msg = '';

            if ($request->etapa == 'C') {
                $model = LoteRE::find($request->id_lote_re);
                $model->etapa = 'A';
                $model->apertura = $request->fecha;
                if ($model->save()) {
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se ha cambiado de etapa satisfactoriamente</p>'
                        . '</div>';
                    bitacora('lote_re', $model->id_grupo_menu, 'I', 'Inserción satisfactoria de un nuevo grupo de menú');

                    $stock = new StockApertura();
                    $stock->fecha_registro = date('Y-m-d H:i:s');
                    $stock->fecha_inicio = $request->fecha;
                    $stock->cantidad_tallos = $model->cantidad_tallos;
                    $stock->cantidad_disponible = $model->cantidad_tallos;
                    $stock->id_variedad = $model->id_variedad;
                    $stock->id_clasificacion_unitaria = $model->id_clasificacion_unitaria;
                    $stock->dias = $dias;
                    $stock->id_lote_re = $model->id_lote_re;

                    if ($stock->save()) {
                        $stock = StockApertura::All()->last();
                        bitacora('stock_apertura', $stock->id_stock_apertura, 'I', 'Inserción satisfactoria de un nuevo stock');

                    } else {
                        $success = false;
                        $msg .= '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                            . '</div>';
                    }
                } else {
                    $success = false;
                    $msg .= '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            }

            if ($request->etapa == 'A') {
                dd('de Apertura - a - EMpaquetado o Guarde (apertura)');
            }

            if ($request->etapa == 'G') {
                dd('de Guarde (apertura) - a - Empaquetado');
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
