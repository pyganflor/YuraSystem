<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use yura\Jobs\ProyeccionUpdateSemanal;
use yura\Jobs\ResumenSemanaCosecha;
use yura\Modelos\Apertura;
use yura\Modelos\Ciclo;
use yura\Modelos\ClasificacionVerde;
use yura\Modelos\Cosecha;
use yura\Modelos\DesgloseRecepcion;
use yura\Modelos\Modulo;
use yura\Modelos\Recepcion;
use yura\Modelos\Semana;
use yura\Modelos\Submenu;
use yura\Modelos\Variedad;
use Validator;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;

class RecepcionController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.postcocecha.recepciones.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            /*'annos' => DB::table('semana as s')
                ->select('s.anno')->distinct()
                ->where('s.estado', '=', 1)->orderBy('s.anno')->get()*/
        ]);
    }

    public function buscar_recepciones(Request $request)    /* =========== OPTIMIZR LA CONSULTA =========*/
    {
        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);
        $mi_busqueda_toupper = mb_strtoupper($bus);
        $mi_busqueda_tolower = mb_strtolower($bus);

        $listado = DB::table('recepcion as r')
            ->join('semana as s', 's.id_semana', '=', 'r.id_semana')
            ->select('r.*', 's.codigo as semana')->distinct();

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($mi_busqueda_toupper, $mi_busqueda_tolower, $bus) {
            $q->Where('s.codigo', 'like', '%' . $bus . '%')
                ->orWhere('r.fecha_ingreso', 'like', '%' . $bus . '%')
                ->orWhere('s.anno', 'like', '%' . $bus . '%');
        });
        if ($request->fecha_ingreso != '')
            $listado = $listado->where('r.fecha_ingreso', 'like', $request->fecha_ingreso . '%');
        if ($request->anno != '')
            $listado = $listado->where('s.anno', '=', $request->anno);
        if ($request->semana != '')
            $listado = $listado->where('s.codigo', '=', $request->codigo);

        $listado = $listado->orderBy('s.anno', 'desc')->orderBy('r.fecha_ingreso', 'desc')
            ->paginate(20);

        $datos = [
            'listado' => $listado
        ];

        return view('adminlte.gestion.postcocecha.recepciones.partials.listado', $datos);
    }

    public function add_recepcion(Request $request)
    {
        return view('adminlte.gestion.postcocecha.recepciones.forms.add_recepcion', [
            'modulos' => getModulos('A'),
        ]);
    }

    public function add_desglose(Request $request)
    {
        return view('adminlte.gestion.postcocecha.recepciones.forms.add_desglose', [
            'variedades' => Variedad::All()->where('estado', '=', 1),
            'recepcion' => Recepcion::find($request->id_recepcion)
        ]);
    }

    public function store_recepcion(Request $request)
    {
        $msg = '';
        $success = true;
        $valida = Validator::make($request->all(), [
            'fecha_ingreso' => 'required',
            'cantidad' => 'required',
            'personal' => 'required',
            'hora_inicio' => 'required',
        ], [
            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria',
            'cantidad.required' => 'La cantidad de tallos es obligatoria',
            'personal.required' => 'El personal es obligatorio',
            'hora_inicio.required' => 'La hora de inicio es obligatoria',
        ]);
        if (!$valida->fails()) {
            $semana = Semana::All()
                ->where('fecha_inicial', '<=', substr($request->fecha_ingreso, 0, 10))
                ->where('fecha_final', '>=', substr($request->fecha_ingreso, 0, 10))->first();
            if ($semana != '') {
                if (count($request->cantidad) > 0) {
                    /* ============= TABLA COSECHA ===============*/
                    $cosecha = Cosecha::find($request->id_cosecha);
                    if ($cosecha == '') {
                        $cosecha = new Cosecha();
                        $cosecha->personal = $request->personal;
                        $cosecha->hora_inicio = $request->hora_inicio;
                        $cosecha->fecha_ingreso = substr($request->fecha_ingreso, 0, 10);
                    }

                    if ($cosecha->save()) {
                        $cosecha = Cosecha::find($request->id_cosecha) != '' ? Cosecha::find($request->id_cosecha) : Cosecha::All()->last();
                        $accion = Cosecha::find($request->id_cosecha) != '' ? 'U' : 'I';
                        bitacora('cosecha', $cosecha->id_cosecha, $accion, 'Modificacion en la tabla cosecha');

                        /* ============= TABLA RECPCION ==============*/
                        $cantidad_total = 0;

                        $model = new Recepcion();
                        $model->id_cosecha = $cosecha->id_cosecha;
                        $model->id_semana = $semana->id_semana;
                        $model->fecha_ingreso = $request->fecha_pasada == 'true' ? $request->fecha_ingreso : date('Y-m-d H:i:s');
                        $model->fecha_registro = date('Y-m-d H:i:s');

                        if ($model->save()) {
                            $recepcion = Recepcion::All()->last();
                            bitacora('recepcion', $model->id_recepcion, 'I', 'Inserción satisfactoria de una nueva recepción');
                            /* ========= TABLA DESGLOSE_RECPCION ============*/
                            foreach ($request->cantidad as $item) {
                                $model = new DesgloseRecepcion();
                                $model->id_variedad = $item['id_variedad'];
                                $model->id_recepcion = $recepcion->id_recepcion;
                                $model->cantidad_mallas = $item['cantidad_mallas'];
                                $model->tallos_x_malla = $item['tallos_x_malla'];
                                $model->id_modulo = $item['id_modulo'];
                                $model->fecha_registro = date('Y-m-d H:i:s');

                                if ($model->save()) {
                                    $cantidad_total += ($item['cantidad_mallas'] * $item['tallos_x_malla']);
                                    bitacora('desglose_recepcion', $model->id_desglose_recepcion, 'I', 'Inserción satisfactoria de un nuevo desglose de recepción');

                                    /* ============= ACTUALIZR CICLO DEL MODULO ========== */
                                    $modulo = Modulo::find($model->id_modulo);
                                    $ciclo = $modulo->getCicloByFecha($cosecha->fecha_ingreso);
                                    if ($ciclo != '') {
                                        if ($ciclo->fecha_cosecha == '' || $cosecha->fecha_ingreso < $ciclo->fecha_cosecha) {
                                            $ciclo->fecha_cosecha = $cosecha->fecha_ingreso;
                                        }
                                        if ($ciclo->fecha_fin == '' || $cosecha->fecha_ingreso > $ciclo->fecha_fin) {
                                            $ciclo->fecha_fin = $cosecha->fecha_ingreso;
                                        }

                                        $ciclo->save();
                                        bitacora('ciclo', $ciclo->id_ciclo, 'U', 'Actualizacion satisfactoria de un ciclo desde el ingreso de la cosecha: ' . $cosecha->id_cosecha);
                                    }


                                    /* ============= ACTUALIZAR LA PROYECCION ================= */
                                    ProyeccionUpdateSemanal::dispatch($semana->codigo, $semana->codigo, $model->id_variedad, $model->id_modulo, 0)
                                        ->onQueue('resumen_cosecha_semanal');

                                    /* ======================== ACTUALIZAR LA TABLA RESUMEN_COSECHA_SEMANA FINAL ====================== */
                                    $semana_fin = getLastSemanaByVariedad($model->id_variedad);
                                    ResumenSemanaCosecha::dispatch($semana->codigo, $semana_fin->codigo, $model->id_variedad)
                                        ->onQueue('resumen_cosecha_semanal');
                                } else {
                                    $success = false;
                                    $msg .= '<div class="alert alert-warning text-center">' .
                                        '<p> Ha ocurrido un problema al guardar la cantidad de ' . $item['cantidad_mallas'] . ' mallas y ' . $item['tallos_x_malla'] . ' tallos por malla</p>'
                                        . '</div>';
                                }
                            }
                        } else {
                            $success = false;
                            $msg = '<div class="alert alert-warning text-center">' .
                                '<p> Ha ocurrido un problema al guardar el ingreso en el sistema</p>'
                                . '</div>';
                        }
                    } else {
                        $success = false;
                        $msg = '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la cosecha</p>'
                            . '</div>';
                    }

                    if ($success) {
                        $msg = '<div class="alert alert-success text-center">' .
                            '<p> Se han guardado un total de ' . $cantidad_total . ' de tallos</p>'
                            . '</div>';
                    }
                } else {
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Al menos debe ingresar una cantidad tanto de mallas como de tallos por malla</p>'
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

    public function update_desglose(Request $request)
    {
        $msg = '';
        $success = true;
        $valida = Validator::make($request->all(), [
            'id_desglose' => 'required',
            'id_modulo' => 'required',
            'id_variedad' => 'required',
            'cantidad_mallas' => 'required',
            'tallos_x_malla' => 'required',
        ], [
            'id_desglose.required' => 'El desglose es obligatorio',
            'id_modulo.required' => 'El módulo es obligatorio',
            'id_variedad.required' => 'La variedad es obligatoria',
            'cantidad_mallas.required' => 'La cantidad de mallas es obligatoria',
            'tallos_x_malla.required' => 'La cantidad de tallos por malla es obligatoria',
        ]);
        if (!$valida->fails()) {
            /* ========= TABLA DESGLOSE_RECPCION ============*/
            $model = DesgloseRecepcion::find($request->id_desglose);
            $model->id_variedad = $request->id_variedad;
            $model->cantidad_mallas = $request->cantidad_mallas;
            $model->tallos_x_malla = $request->tallos_x_malla;
            $model->id_modulo = $request->id_modulo;

            if ($model->save()) {
                bitacora('desglose_recepcion', $model->id_desglose_recepcion, 'U', 'Inserción satisfactoria de un nuevo desglose de recepción');
                $msg .= '<div class="alert alert-success text-center">' .
                    '<p> Se ha actualizado satisfactoriamente la información en el sistema</p>'
                    . '</div>';

                /* ============= ACTUALIZAR LA PROYECCION ================= */
                ProyeccionUpdateSemanal::dispatch($model->recepcion->semana->codigo, $model->recepcion->semana->codigo, $model->id_variedad, $model->id_modulo, 0)
                    ->onQueue('resumen_cosecha_semanal');

                /* ======================== ACTUALIZAR LA TABLA RESUMEN_COSECHA_SEMANA FINAL ====================== */
                $semana_fin = getLastSemanaByVariedad($model->id_variedad);
                ResumenSemanaCosecha::dispatch($model->recepcion->semana->codigo, $semana_fin->codigo, $model->id_variedad)
                    ->onQueue('resumen_cosecha_semanal');
            } else {
                $success = false;
                $msg .= '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
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

    public function store_desglose(Request $request)
    {
        $msg = '';
        $success = true;
        $valida = Validator::make($request->all(), [
            'id_recepcion' => 'required',
            'id_modulo' => 'required',
            'id_variedad' => 'required',
            'cantidad_mallas' => 'required',
            'tallos_x_malla' => 'required',
        ], [
            'id_recepcion.required' => 'La recepción es obligatorio',
            'id_modulo.required' => 'El módulo es obligatorio',
            'id_variedad.required' => 'La variedad es obligatoria',
            'cantidad_mallas.required' => 'La cantidad de mallas es obligatoria',
            'tallos_x_malla.required' => 'La cantidad de tallos por malla es obligatoria',
        ]);
        if (!$valida->fails()) {
            /* ========= TABLA DESGLOSE_RECPCION ============*/
            $model = new DesgloseRecepcion();
            $model->id_recepcion = $request->id_recepcion;
            $model->id_variedad = $request->id_variedad;
            $model->cantidad_mallas = $request->cantidad_mallas;
            $model->tallos_x_malla = $request->tallos_x_malla;
            $model->id_modulo = $request->id_modulo;

            if ($model->save()) {
                $model = DesgloseRecepcion::All()->last();
                bitacora('desglose_recepcion', $model->id_desglose_recepcion, 'U', 'Inserción satisfactoria de un nuevo desglose de recepción');
                $msg .= '<div class="alert alert-success text-center">' .
                    '<p> Se ha guardado satisfactoriamente la información en el sistema</p>'
                    . '</div>';

                /* ============= ACTUALIZR CICLO DEL MODULO ========== */
                $modulo = Modulo::find($model->id_modulo);
                $ciclo = $modulo->getCicloByFecha($model->recepcion->cosecha->fecha_ingreso);
                if ($ciclo != '') {
                    if ($ciclo->fecha_cosecha == '' || $model->recepcion->cosecha->fecha_ingreso < $ciclo->fecha_cosecha) {
                        $ciclo->fecha_cosecha = $model->recepcion->cosecha->fecha_ingreso;
                    }
                    if ($ciclo->fecha_fin == '' || $model->recepcion->cosecha->fecha_ingreso > $ciclo->fecha_fin) {
                        $ciclo->fecha_fin = $model->recepcion->cosecha->fecha_ingreso;
                    }

                    $ciclo->save();
                    bitacora('ciclo', $ciclo->id_ciclo, 'U', 'Actualizacion satisfactoria de un ciclo desde el ingreso de la cosecha: ' . $model->recepcion->id_cosecha);
                }

                /* ============= ACTUALIZAR LA PROYECCION ================= */
                ProyeccionUpdateSemanal::dispatch($model->recepcion->semana->codigo, $model->recepcion->semana->codigo, $model->id_variedad, $model->id_modulo, 0)
                    ->onQueue('resumen_cosecha_semanal');

                /* ======================== ACTUALIZAR LA TABLA RESUMEN_COSECHA_SEMANA FINAL ====================== */
                $semana_fin = getLastSemanaByVariedad($model->id_variedad);
                ResumenSemanaCosecha::dispatch($model->recepcion->semana->codigo, $semana_fin->codigo, $model->id_variedad)
                    ->onQueue('resumen_cosecha_semanal');
            } else {
                $success = false;
                $msg .= '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
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

    public function delete_desglose(Request $request)
    {
        $msg = '';
        $success = true;
        $valida = Validator::make($request->all(), [
            'id_desglose' => 'required',
        ], [
            'id_desglose.required' => 'El desglose es obligatorio',
        ]);
        if (!$valida->fails()) {
            /* ========= TABLA DESGLOSE_RECPCION ============*/
            $model = DesgloseRecepcion::find($request->id_desglose);
            $model->estado = 0;

            if ($model->save()) {
                bitacora('desglose_recepcion', $model->id_desglose_recepcion, 'U', 'Inserción satisfactoria de un nuevo desglose de recepción');
                $msg .= '<div class="alert alert-success text-center">' .
                    '<p> Se ha actualizado satisfactoriamente la información en el sistema</p>'
                    . '</div>';

                $semana_ini = $model->recepcion->semana->codigo;
                /* ============= ACTUALIZAR LA PROYECCION ================= */
                ProyeccionUpdateSemanal::dispatch($semana_ini, $semana_ini, $model->id_variedad, $model->id_modulo, 0)
                    ->onQueue('resumen_cosecha_semanal');

                /* ======================== ACTUALIZAR LA TABLA RESUMEN_COSECHA_SEMANA FINAL ====================== */
                $semana_fin = getLastSemanaByVariedad($model->id_variedad);
                ResumenSemanaCosecha::dispatch($semana_ini, $semana_fin->codigo, $model->id_variedad)
                    ->onQueue('resumen_cosecha_semanal');
            } else {
                $success = false;
                $msg .= '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
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

    public function ver_recepcion(Request $request)
    {
        if ($request->has('id_recepcion')) {
            $r = Recepcion::find($request->id_recepcion);
            if ($r != '') {
                return view('adminlte.gestion.postcocecha.recepciones.partials.detalles', [
                    'recepcion' => $r,
                ]);
            } else {
                return '<div class="alert alert-warning text-center">No se ha encontrado el usuario en el sistema</div>';
            }
        } else {
            return '<div class="alert alert-warning text-center">No se ha seleccionado ninguna recepción</div>';
        }
    }

    public function exportar_recepciones(Request $request)
    {
        //---------------------- EXCEL --------------------------------------
        $objPHPExcel = new PHPExcel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $currencyFormat = '#,#0.## \€;[Red]-#,#0.## \€';
        $numberFormat = '#,#0.##;[Red]-#,#0.##';

        $objPHPExcel->removeSheetByIndex(0); //Eliminar la hoja inicial por defecto

        $this->excel_hoja_recepciones($objPHPExcel, $request);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="Reporte ' . explode('|', getConfiguracionEmpresa()->postcocecha)[0] . '.xlsx"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }

    public function excel_hoja_recepciones($objPHPExcel, $request)
    {
        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);
        $mi_busqueda_toupper = mb_strtoupper($bus);
        $mi_busqueda_tolower = mb_strtolower($bus);

        $listado = DB::table('recepcion as r')
            ->join('semana as s', 's.id_semana', '=', 'r.id_semana')
            ->select('r.*', 's.codigo as semana')->distinct();

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($mi_busqueda_toupper, $mi_busqueda_tolower, $bus) {
            $q->Where('s.codigo', 'like', '%' . $bus . '%')
                ->orWhere('r.fecha_ingreso', 'like', '%' . $bus . '%')
                ->orWhere('s.anno', 'like', '%' . $bus . '%');
        });
        if ($request->fecha_ingreso != '')
            $listado = $listado->where('r.fecha_ingreso', '=', $request->fecha_ingreso);
        if ($request->anno != '')
            $listado = $listado->where('s.anno', '=', $request->anno);
        if ($request->semana != '')
            $listado = $listado->where('s.codigo', '=', $request->codigo);

        $listado = $listado->orderBy('s.anno', 'desc')->orderBy('r.fecha_ingreso', 'desc')
            ->get();

        if (count($listado) > 0) {
            $objSheet = new PHPExcel_Worksheet($objPHPExcel, explode('|', getConfiguracionEmpresa()->postcocecha)[0]);
            $objPHPExcel->addSheet($objSheet, 0);

            $objSheet->mergeCells('A1:F1');
            $objSheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(12);
            $objSheet->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle('A1:F1')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CCFFCC');

            $objSheet->getCell('A1')->setValue('Listado de ingresos a ' . explode('|', getConfiguracionEmpresa()->postcocecha)[0]);

            $objSheet->getCell('A3')->setValue('FECHA');
            $objSheet->getCell('B3')->setValue('SEMANA');
            $objSheet->getCell('C3')->setValue('TALLOS');
            $objSheet->getCell('D3')->setValue('CANTIDADES');
            $objSheet->getCell('E3')->setValue('ETAPA del PROCESO');
            $objSheet->getCell('F3')->setValue('OTROS DATOS');

            $objSheet->getStyle('A3:F3')->getFont()->setBold(true)->setSize(12);

            $objSheet->getStyle('A3:F3')
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                ->getColor()
                ->setRGB(PHPExcel_Style_Color::COLOR_BLACK);

            $objSheet->getStyle('A3:F3')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CCFFCC');

            //--------------------------- LLENAR LA TABLA ---------------------------------------------
            for ($i = 0; $i < sizeof($listado); $i++) {
                $objSheet->getCell('A' . ($i + 4))->setValue(substr($listado[$i]->fecha_ingreso, 0, 16));
                $objSheet->getCell('B' . ($i + 4))->setValue($listado[$i]->semana);
                $objSheet->getCell('C' . ($i + 4))->setValue(getRecepcion($listado[$i]->id_recepcion)->cantidad_tallos());
                $cantidades = '';
                foreach (getRecepcion($listado[$i]->id_recepcion)->desgloses as $recepcion) {
                    $cantidades .= $recepcion->variedad->planta->nombre . " - " . $recepcion->variedad->siglas . ": " .
                        $recepcion->cantidad_mallas . " mallas de " . $recepcion->tallos_x_malla . " tallos = " .
                        $recepcion->cantidad_mallas * $recepcion->tallos_x_malla . "\n";
                }
                $objSheet->getCell('D' . ($i + 4))->setValue($cantidades);
                $etapa = '';
                if ($listado[$i]->proceso == 'I')
                    $etapa = explode('|', getConfiguracionEmpresa()->postcocecha)[0];
                elseif ($listado[$i]->proceso == 'V')
                    $etapa = explode('|', getConfiguracionEmpresa()->postcocecha)[1];
                elseif ($listado[$i]->proceso == 'A')
                    $etapa = explode('|', getConfiguracionEmpresa()->postcocecha)[2];
                $objSheet->getCell('E' . ($i + 4))->setValue($etapa);
                $documentos = '';
                foreach (getDocumentos('recepcion', $listado[$i]->id_recepcion) as $doc) {
                    $documentos .= getTextFromDocumento($doc) . "\n";
                }
                $objSheet->getCell('F' . ($i + 4))->setValue($documentos);
            }

            $objSheet->getColumnDimension('A')->setAutoSize(true);
            $objSheet->getColumnDimension('B')->setAutoSize(true);
            $objSheet->getColumnDimension('C')->setAutoSize(true);
            $objSheet->getColumnDimension('D')->setAutoSize(true);
            $objSheet->getColumnDimension('E')->setAutoSize(true);
            $objSheet->getColumnDimension('F')->setAutoSize(true);
        } else {
            return '<div>No se han encontrado coincidencias para exportar</div>';
        }
    }

    public function getIdCosechaByFecha(Request $request)
    {
        $cosecha = Cosecha::All()->where('fecha_ingreso', '=', substr($request->fecha, 0, 10))->first();
        if ($cosecha != '')
            return [
                'id_cosecha' => $cosecha->id_cosecha,
                'personal' => $cosecha->personal,
                'hora_inicio' => $cosecha->hora_inicio,
                'rendimiento' => $cosecha->getRendimiento(),
            ];
        else
            return '';
    }

    public function store_cosecha(Request $request)
    {
        $cosecha = Cosecha::find($request->id_cosecha);
        if ($cosecha == '') {
            $cosecha = new Cosecha();
        }
        $cosecha->personal = $request->personal;
        $cosecha->hora_inicio = $request->hora_inicio;
        $cosecha->fecha_ingreso = substr($request->fecha_ingreso, 0, 10);

        if ($cosecha->save()) {
            $id = Cosecha::find($request->id_cosecha) != '' ? $cosecha->id_cosecha : Cosecha::All()->last()->id_cosecha;
            $accion = Cosecha::find($request->id_cosecha) != '' ? 'U' : 'I';
            bitacora('cosecha', $id, $accion, 'Modificacion en la tabla cosecha');

            return [
                'mensaje' => '<div class="alert alert-success text-center">Se ha guardado toda la información satisfactoriamente</div>',
                'success' => true
            ];
        } else {
            return [
                'mensaje' => '<div class="alert alert-warning text-center">No se ha podido guardar la información en el sistema</div>',
                'success' => false
            ];
        }
    }

    public function ver_rendimiento(Request $request)
    {
        $cosecha = Cosecha::find($request->id_cosecha);

        return view('adminlte.gestion.postcocecha.recepciones.partials.rendimiento', [
            'cosecha' => $cosecha,
        ]);
    }

    public function buscar_cosecha(Request $request)
    {
        $cosecha = Cosecha::All()->where('fecha_ingreso', '=', $request->fecha_ingreso)->first();
        if ($cosecha != '') {
            $arreglo = [];
            foreach ($cosecha->getVariedades() as $v) {
                array_push($arreglo, [
                    'variedad' => getVariedad($v->id_variedad)->siglas,
                    'cantidad' => $cosecha->getTotalTallosByVariedad($v->id_variedad),
                ]);
            }
            return [
                'id_cosecha' => $cosecha->id_cosecha,
                'total_cosecha' => $cosecha->getTotalTallos(),
                'listado_x_variedad' => $arreglo,
                'rendimiento' => $cosecha->getRendimiento()
            ];
        } else {
            return [
                'id_cosecha' => '',
                'total_cosecha' => 0,
                'listado_x_variedad' => [],
                'rendimiento' => 0
            ];
        }
    }

    public function editar_desglose_recepcion(Request $request)
    {
        return view('adminlte.gestion.postcocecha.recepciones.forms.editar_desglose_recepcion', [
            'desglose' => DesgloseRecepcion::find($request->id_desglose_recepcion)
        ]);
    }

    public function select_modulo_recepcion(Request $request)
    {
        $ciclo = Ciclo::where('estado', 1)
            ->where('activo', 1)
            ->where('id_modulo', $request->modulo)
            ->first();

        return [
            'id_ciclo' => $ciclo->id_ciclo,
            'id_variedad' => $ciclo->id_variedad,
            'nombre_variedad' => $ciclo->variedad->nombre,
        ];
    }
}