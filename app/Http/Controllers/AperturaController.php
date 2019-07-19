<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Modelos\ClasificacionBlanco;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\Consumo;
use yura\Modelos\LoteRE;
use yura\Modelos\StockApertura;
use yura\Modelos\StockEmpaquetado;
use yura\Modelos\StockFrio;
use yura\Modelos\Submenu;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;
use PHPExcel_Style_Fill;
use yura\Modelos\Variedad;
use yura\Modelos\Pedido;

class AperturaController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.postcocecha.aperturas.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'annos' => DB::table('semana as s')
                ->select('s.anno')->distinct()
                ->where('s.estado', '=', 1)->orderBy('s.anno')->get(),
            'variedades' => Variedad::All()->where('estado', '=', 1),
            'unitarias' => getUnitarias(),
        ]);
    }

    public function buscar_aperturas(Request $request)    /* =========== OPTIMIZR LA CONSULTA =========*/
    {
        $listado = [];
        if ($request->fecha_desde != '' || $request->fecha_hasta != '' || $request->variedad != '' || $request->unitaria != '') {
            $listado = DB::table('stock_apertura as a')
                ->join('clasificacion_unitaria as u', 'u.id_clasificacion_unitaria', '=', 'a.id_clasificacion_unitaria')
                ->select('a.*')->distinct()
                ->where('a.disponibilidad', '=', 1);

            if ($request->fecha_desde != '')
                $listado = $listado->where('a.fecha_inicio', '>=', $request->fecha_desde);
            if ($request->fecha_hasta != '')
                $listado = $listado->where('a.fecha_inicio', '<=', $request->fecha_hasta);
            if ($request->variedad != '')
                $listado = $listado->where('a.id_variedad', '=', $request->variedad);
            if ($request->unitaria != '')
                $listado = $listado->where('a.id_clasificacion_unitaria', '=', $request->unitaria);

            $listado = $listado->orderBy('a.fecha_inicio', 'asc')->orderBy('u.nombre', 'asc')
                ->get();
        }

        $datos = [
            'listado' => $listado,
        ];

        return view('adminlte.gestion.postcocecha.aperturas.partials.listado', $datos);
    }

    public function exportar_aperturas(Request $request)
    {
        //---------------------- EXCEL --------------------------------------
        $objPHPExcel = new PHPExcel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $currencyFormat = '#,#0.## \€;[Red]-#,#0.## \€';
        $numberFormat = '#,#0.##;[Red]-#,#0.##';

        $objPHPExcel->removeSheetByIndex(0); //Eliminar la hoja inicial por defecto

        $this->excel_hoja_aperturas($objPHPExcel, $request);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="Reporte ' . explode('|', getConfiguracionEmpresa()->postcocecha)[2] . '.xlsx"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    } /* ======= Actualizar ========*/

    public function excel_hoja_aperturas($objPHPExcel, $request)
    {
        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);
        $mi_busqueda_toupper = mb_strtoupper($bus);
        $mi_busqueda_tolower = mb_strtolower($bus);

        $listado = DB::table('apertura as a')
            ->join('semana as s', 's.id_semana', '=', 'a.id_semana')
            ->select('a.*', 's.codigo as semana')->distinct();

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($mi_busqueda_toupper, $mi_busqueda_tolower, $bus) {
            $q->Where('s.codigo', 'like', '%' . $bus . '%')
                ->orWhere('a.fecha_ingreso', 'like', '%' . $bus . '%')
                ->orWhere('s.anno', 'like', '%' . $bus . '%');
        });
        if ($request->fecha_ingreso != '')
            $listado = $listado->where('a.fecha_ingreso', 'like', '%' . $request->fecha_ingreso . '%');
        if ($request->anno != '')
            $listado = $listado->where('s.anno', '=', $request->anno);
        if ($request->proceso != '')
            $listado = $listado->where('a.proceso', '=', $request->proceso);
        if ($request->semana != '')
            $listado = $listado->where('s.codigo', '=', $request->codigo);

        $listado = $listado->orderBy('s.anno', 'desc')->orderBy('a.fecha_ingreso', 'desc')
            ->get();

        if (count($listado) > 0) {
            $objSheet = new PHPExcel_Worksheet($objPHPExcel, explode('|', getConfiguracionEmpresa()->postcocecha)[0]);
            $objPHPExcel->addSheet($objSheet, 0);

            /* ============== MERGE CELDAS =============*/
            $objSheet->mergeCells('A1:F1');
            /* ============== LETRAS NEGRITAS =============*/
            $objSheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(12);
            /* ============== CENTRAR =============*/
            $objSheet->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            /* ============== BACKGROUND COLOR =============*/
            $objSheet->getStyle('A1:F1')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CCFFCC');

            /* ============== ENCABEZADO =============*/
            $objSheet->getCell('A1')->setValue('Listado de ingresos a ' . explode('|', getConfiguracionEmpresa()->postcocecha)[2] . ' ' . date('Y-m-d H:i'));

            $columnas = ['H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
            $cant_a = 1;   // cantidad de aperturas
            $f_a = 3;   // numero de la fila de apertura

            foreach ($listado as $apertura) {
                /* ============== LETRAS NEGRITAS =============*/
                $objSheet->getStyle('A' . $f_a . ':F' . $f_a)->getFont()->setBold(true)->setSize(12);

                /* ============== CENTRAR =============*/
                $objSheet->getStyle('A' . $f_a . ':F' . $f_a)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                /* =============== BACKGROUND COLOR ============================*/
                $objSheet->getStyle('A' . $f_a . ':F' . $f_a)
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('CCFFCC');

                /* ============== BORDER COLOR =============*/
                $objSheet->getStyle('A' . $f_a . ':F' . $f_a)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                    ->getColor()
                    ->setRGB(PHPExcel_Style_Color::COLOR_BLACK);

                /* =============== ENCABEZADOS DE LAS COLUMNAS ================*/
                $objSheet->getCell('A' . $f_a)->setValue('Nº');
                $objSheet->getCell('B' . $f_a)->setValue('FECHA');
                $objSheet->getCell('C' . $f_a)->setValue('HORA');
                $objSheet->getCell('D' . $f_a)->setValue('SEMANA');
                $objSheet->getCell('E' . $f_a)->setValue('ETAPA');
                $objSheet->getCell('F' . $f_a)->setValue('ESTANCIA');

                /* =============== LETRAS NEGRITAS ======================*/
                $objSheet->getStyle('A' . $f_a . ':F' . $f_a)->getFont()->setBold(true)->setSize(12);

                $f_v = $f_a;    // numero de la fila de las clasificaciones en verde

                $f_a++;
                /* ================== LLENAR LA FILA CON LOS DATOS DE LA APERTURA =================*/
                $objSheet->getCell('A' . $f_a)->setValue($cant_a);
                $objSheet->getCell('B' . $f_a)->setValue(substr($apertura->fecha_ingreso, 0, 10));
                $objSheet->getCell('C' . $f_a)->setValue(substr($apertura->fecha_ingreso, 11, 5));
                $objSheet->getCell('D' . $f_a)->setValue($apertura->semana);
                $objSheet->getCell('E' . $f_a)->setValue($apertura->proceso == 'I' ? 'Ingreso' : 'Completada');
                $objSheet->getCell('F' . $f_a)->setValue('calcular');

                /* ============== CENTRAR =============*/
                $objSheet->getStyle('A' . $f_a . ':F' . $f_a)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                /* ============== BORDER COLOR =============*/
                $objSheet->getStyle('A' . $f_a . ':F' . $f_a)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                    ->getColor()
                    ->setRGB(PHPExcel_Style_Color::COLOR_BLACK);

                /* ================== LLENAR LAS FILAS CON LOS DATOS DE LAS CLASIFICACIONES EN VERDE =============*/
                $col = 0;   // indice de columnas;
                $cant_clas_unitarias = count(getRecepcion($apertura->id_recepcion)->clasificacionesVerdeByVariedad(getRecepcion($apertura->id_recepcion)->variedades()[0]->id_variedad));

                /* ============== MERGE CELDAS =============*/
                $objSheet->mergeCells($columnas[$col] . '1:' . $columnas[$cant_clas_unitarias] . '1');
                /* ============== LETRAS NEGRITAS =============*/
                $objSheet->getStyle($columnas[$col] . '1:' . $columnas[$cant_clas_unitarias] . '1')->getFont()->setBold(true)->setSize(12);
                /* ============== CENTRAR =============*/
                $objSheet->getStyle($columnas[$col] . '1:' . $columnas[$cant_clas_unitarias] . '1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                /* ============== BACKGROUND COLOR =============*/
                $objSheet->getStyle($columnas[$col] . '1:' . $columnas[$cant_clas_unitarias] . '1')
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('E9ECEF');
                /* ============== ENCABEZADO =============*/
                $objSheet->getCell($columnas[$col] . '1')->setValue('Detalles de los ingresos');

                $objSheet->getCell($columnas[$col] . $f_v)->setValue('Variedad');

                /* ============== MERGE CELDAS =============*/
                $objSheet->mergeCells($columnas[$col + 1] . $f_v . ':' . $columnas[$cant_clas_unitarias] . $f_v);

                $objSheet->getCell($columnas[$col + 1] . $f_v)->setValue(getRecepcion($apertura->id_recepcion)->totalRamos_clasificacionVerde() . ' Ramos = ' . getRecepcion($apertura->id_recepcion)->totalTallos_clasificacionVerde() . ' Tallos');

                /* =============== BACKGROUND COLOR ============================*/
                $objSheet->getStyle($columnas[$col] . $f_v . ':' . $columnas[$col + 1] . $f_v)
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('E9ECEF');

                /* ============== BORDER COLOR =============*/
                $objSheet->getStyle($columnas[$col] . $f_v . ':' . $columnas[$cant_clas_unitarias] . $f_v)
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                    ->getColor()
                    ->setRGB(PHPExcel_Style_Color::COLOR_BLACK);

                /* =============== LETRAS NEGRITAS ======================*/
                $objSheet->getStyle($columnas[$col] . $f_v . ':' . $columnas[$col + 1] . $f_v)->getFont()->setBold(true)->setSize(12);

                /* ============== CENTRAR =============*/
                $objSheet->getStyle($columnas[$col] . $f_v . ':' . $columnas[$col + 1] . $f_v)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $f_v++;
                foreach (getRecepcion($apertura->id_recepcion)->variedades() as $variedad) {
                    /* ============== MERGE CELDAS =============*/
                    $objSheet->mergeCells($columnas[$col] . $f_v . ':' . $columnas[$col] . ($f_v + 1));

                    $objSheet->getCell($columnas[$col] . $f_v)->setValue($variedad->planta->nombre . " - " . $variedad->siglas . "\n" .
                        getRecepcion($apertura->id_recepcion)->ramosClasificacionVerdeByVariedad($variedad->id_variedad) . " Ramos \n" .
                        getRecepcion($apertura->id_recepcion)->tallosClasificacionVerdeByVariedad($variedad->id_variedad) . " Tallos \n");

                    /* ============== BORDER COLOR =============*/
                    $objSheet->getStyle($columnas[$col] . $f_v . ':' . $columnas[$col] . ($f_v + 1))
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                        ->getColor()
                        ->setRGB(PHPExcel_Style_Color::COLOR_BLACK);

                    /* ============== CENTRAR =============*/
                    $objSheet->getStyle($columnas[$col] . $f_v . ':' . $columnas[$col] . ($f_v + 1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objSheet->getStyle($columnas[$col] . $f_v . ':' . $columnas[$col] . ($f_v + 1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                    /* ============== SET SIZE A LA FILA ================*/
                    $objSheet->getRowDimension($f_v + 1)->setRowHeight(75);

                    /* ============== LLENAR LAS FILAS CON LOS DATOS DE LAS CLASIFICACIONES UNITARIAS ==============*/
                    $f_u = $f_v;    // cantidad de clasificaciones unitarias
                    $cant_u = 1;    // cantidad de clasificaciones unitarias
                    foreach (getRecepcion($apertura->id_recepcion)->clasificacionesVerdeByVariedad($variedad->id_variedad) as $item) {
                        $objSheet->getCell($columnas[$cant_u] . $f_u)->setValue($item->clasificacion_unitaria->nombre . "" . getConfiguracionEmpresa()->unidad_medida);

                        /* ============== BORDER COLOR =============*/
                        $objSheet->getStyle($columnas[$cant_u] . $f_u)
                            ->getBorders()
                            ->getAllBorders()
                            ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                            ->getColor()
                            ->setRGB(PHPExcel_Style_Color::COLOR_BLACK);

                        /* =============== BACKGROUND COLOR ============================*/
                        $objSheet->getStyle($columnas[$cant_u] . $f_u)
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('E9ECEF');

                        /* ============== CENTRAR =============*/
                        $objSheet->getStyle($columnas[$cant_u] . $f_u)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objSheet->getStyle($columnas[$cant_u] . $f_u)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                        /* =============== LETRAS NEGRITAS ======================*/
                        $objSheet->getStyle($columnas[$cant_u] . $f_u)->getFont()->setBold(true)->setSize(12);

                        $cant_u++;
                    }
                    $f_u++;

                    $cant_u = 1;    // cantidad de clasificaciones unitarias
                    foreach (getRecepcion($apertura->id_recepcion)->clasificacionesVerdeByVariedad($variedad->id_variedad) as $item) {
                        $objSheet->getCell($columnas[$cant_u] . $f_u)->setValue($item->cantidad_ramos . '*' . $item->tallos_x_ramo . ' = ' .
                            $item->cantidad_ramos * $item->tallos_x_ramo . '  (tallos)');

                        /* ============== BORDER COLOR =============*/
                        $objSheet->getStyle($columnas[$cant_u] . $f_u)
                            ->getBorders()
                            ->getAllBorders()
                            ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                            ->getColor()
                            ->setRGB(PHPExcel_Style_Color::COLOR_BLACK);

                        /* ============== CENTRAR =============*/
                        $objSheet->getStyle($columnas[$cant_u] . $f_u)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objSheet->getStyle($columnas[$cant_u] . $f_u)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

                        $cant_u++;
                    }

                    $f_v += 2;
                }

                $f_a = $f_v + 2;

                $cant_a++;
            }


            //--------------------------- LLENAR LA TABLA ---------------------------------------------

            foreach ($columnas as $c) {
                $objSheet->getColumnDimension($c)->setAutoSize(true);
            }

        } else {
            return '<div>No se han encontrado coincidencias para exportar</div>';
        }
    }   /* ======= Actualizar ========*/

    public function listar_pedidos(Request $request)
    {
        if ($request->fecha != '')
            $listado = DB::table('pedido as p')
                ->select('p.fecha_pedido')->distinct()
                ->where('estado', '=', 1)
                ->where('empaquetado', '=', 0)
                ->where('fecha_pedido', '<=', $request->fecha)
                ->orderBy('p.fecha_pedido', 'asc')->get();
        else
            $listado = [];
        return view('adminlte.gestion.postcocecha.aperturas.partials.listado_pedidos', [
            'listado' => $listado,
            'variedad' => Variedad::find($request->id_variedad)
        ]);
    }

    public function sacar(Request $request)
    {
        $msg = '';
        $success = true;
        if (count($request->arreglo) > 0) {
            foreach ($request->arreglo as $item) {
                $consumo = Consumo::All()->where('fecha_pedidos', '=', date('Y-m-d'))->first();
                $apertura = StockApertura::find($item['id_stock_apertura']);

                $unitaria = $apertura->clasificacion_unitaria;
                $tallos = round($item['cantidad_ramos_estandar'] * explode('|', $unitaria->nombre)[1]);
                $current = $apertura->cantidad_disponible - $tallos;

                if ($current >= 0) {
                    if ($consumo == '') {
                        /* ========= CREAR CONSUMO ========== */
                        $consumo = new Consumo();
                        $consumo->fecha_pedidos = date('Y-m-d');
                        $consumo->fecha_registro = date('Y-m-d H:i:s');

                        if ($consumo->save()) {
                            $consumo = Consumo::All()->last();
                            bitacora('consumo', $consumo->id_consumo, 'I', 'Creacion satisfactoria de un consumo');
                        } else {
                            $msg .= '<div class="alert alert-warning text-center">' .
                                'Ha ocurrido un problema al crear el nuevo consumo de la fecha de los pedidos indicada' .
                                '</div>';
                            $success = false;
                        }
                    }

                    /* ========= GUARDAR STOCK_FRIO ========== */
                    $frio = new StockFrio();
                    $frio->id_consumo = $consumo->id_consumo;
                    $frio->id_stock_apertura = $apertura->id_stock_apertura;
                    $frio->id_variedad = $apertura->id_variedad;
                    $frio->id_clasificacion_unitaria = $apertura->id_clasificacion_unitaria;
                    $frio->id_semana = getSemanaByDateVariedad($apertura->fecha_inicio, $apertura->id_variedad)->id_semana;
                    $frio->dias_maduracion = $item['dias_maduracion'];
                    $frio->cantidad_ramos_estandar = $item['cantidad_ramos_estandar'];
                    $frio->cantidad_disponible = $item['cantidad_ramos_estandar'];
                    $frio->fecha_ingreso = date('Y-m-d');
                    $frio->fecha_registro = date('Y-m-d H:i:s');

                    if ($frio->save()) {
                        $frio = StockFrio::All()->last();
                        bitacora('stock_frio', $frio->id_stock_frio, 'I', 'Creacion satisfactoria de un stock frio');

                        /* ============= CLASIFICACION_BLANCO ===============*/
                        /* PENDIENTE REVISION */

                        /* ============= ACTUALIZAR EL STOCK_EMPAQUETADO ===============*/
                        $empaquetado = StockEmpaquetado::All()
                            ->where('id_variedad', '=', $apertura->id_variedad)->where('empaquetado', '=', 0)
                            ->first();
                        if ($empaquetado == '') {
                            /* ========= CREAR STOCK_EMPAQUETADO ========== */
                            $empaquetado = new StockEmpaquetado();
                            $empaquetado->fecha_registro = date('Y-m-d H:i:s');
                            $empaquetado->id_variedad = $apertura->id_variedad;
                            $empaquetado->cantidad_ingresada = $frio->cantidad_ramos_estandar;

                            $empaquetado->save();
                            $empaquetado = StockEmpaquetado::All()->last();
                            bitacora('stock_empaquetado', $empaquetado->id_stock_empaquetado, 'I', 'Creacion satisfactoria de un stock empaquetado');
                        } else {
                            $empaquetado->cantidad_ingresada += $frio->cantidad_ramos_estandar;

                            $empaquetado->save();
                            bitacora('stock_empaquetado', $empaquetado->id_stock_empaquetado, 'U', 'Actualizacion satisfactoria de un stock empaquetado');
                        }

                        if ($empaquetado->save()) {
                            $empaquetado = StockEmpaquetado::All()->last();
                            bitacora('stock_empaquetado', $empaquetado->id_stock_empaquetado, 'I', 'Creacion satisfactoria de un stock empaquetado');
                        } else {
                            $msg .= '<div class="alert alert-warning text-center">' .
                                'Ha ocurrido un problema al guardar el stock-empaquetado de la fecha de los pedidos indicada' .
                                '</div>';
                            $success = false;
                        }

                        /* ============= ACTUALIZAR EL STOCK_APERTURA ===============*/
                        $apertura->cantidad_disponible = $current;
                        if ($apertura->cantidad_disponible == 0) {
                            $apertura->disponibilidad = 0;
                            $apertura->fecha_fin = date('Y-m-d');
                        }

                        if ($apertura->save()) {
                            $apertura = StockApertura::All()->last();
                            bitacora('stock_apertura', $apertura->id_stock_apertura, 'U', 'Actualizacion satisfactoria de un stock_apertura');
                        } else {
                            $msg .= '<div class="alert alert-warning text-center">' .
                                'Ha ocurrido un problema al actualizar el stock de apertura indicado de ' . explode('|', getStockById($item['id_stock_apertura'])->clasificacion_unitaria->nombre)[0] .
                                getStockById($item['id_stock_apertura'])->clasificacion_unitaria->unidad_medida->siglas .
                                ' con ' . $item['dias_maduracion'] . ' días de maduración' .
                                '</div>';
                            $success = false;
                        }

                        /* ============= ACTUALIZAR PEDIDOS ==============*/
                    } else {
                        $msg .= '<div class="alert alert-warning text-center">' .
                            'Ha ocurrido un problema al crear un nuevo stock en frío de ' . explode('|', getStockById($item['id_stock_apertura'])->clasificacion_unitaria->nombre)[0] .
                            getStockById($item['id_stock_apertura'])->clasificacion_unitaria->unidad_medida->siglas .
                            ' con ' . $item['dias_maduracion'] . ' días de maduración' .
                            '</div>';
                        $success = false;
                    }
                } else {
                    $msg .= '<div class="alert alert-warning text-center">' .
                        'No se puede sacar una cantidad de ramos mayor a la ingresada en apertura de ' . explode('|', getStockById($item['id_stock_apertura'])->clasificacion_unitaria->nombre)[0] .
                        getStockById($item['id_stock_apertura'])->clasificacion_unitaria->unidad_medida->siglas .
                        ' con ' . $item['dias_maduracion'] . ' días de maduración' .
                        '</div>';
                    $success = false;
                }
            }
        } else {
            $msg = '<div class="alert alert-warning text-center">' .
                'Al menos debe indicar un stock de apertura a sacar' .
                '</div>';
            $success = false;
        }
        if ($success) {
            $msg = '<div class="alert alert-success text-center">' .
                'Se ha guardado toda la información satisfactoriamente' .
                '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function mover_fecha(Request $request)
    {
        $apertura = StockApertura::find($request->id_apertura);

        return view('adminlte.gestion.postcocecha.aperturas.partials.mover_fecha', [
            'apertura' => $apertura
        ]);
    }

    public function store_mover_fecha(Request $request)
    {
        /* ------------------- VALIDAR --------------------- */
        $apertura = StockApertura::find($request->apertura);
        $lote_re = $apertura->lote_re;
        /* ======================= ANTIGUO STOCK ======================== */
        $apertura->cantidad_disponible = $request->saldo;
        $apertura->movido = 1;
        if ($request->saldo == 0) {
            $apertura->disponibilidad = 0;
            $apertura->fecha_fin = date('Y-m-d');
        }
        $apertura->save();
        bitacora('stock_apertura', $apertura->id_stock_apertura, 'U', 'Mover tallos de fecha');

        /* ======================= NUEVO STOCK ======================== */
        $new_lote = LoteRE::All()
            ->where('id_variedad', $lote_re->id_variedad)
            ->where('id_clasificacion_unitaria', $lote_re->id_clasificacion_unitaria)
            ->where('id_clasificacion_verde', $lote_re->id_clasificacion_verde)
            ->where('estado', 1)
            ->where('etapa', 'A')
            ->where('apertura', $request->fecha)
            ->first();
        if ($new_lote == '') {
            $new_lote = new LoteRE();
            $new_lote->cantidad_tallos = $request->mover;
            $new_lote->id_variedad = $lote_re->id_variedad;
            $new_lote->id_clasificacion_unitaria = $lote_re->id_clasificacion_unitaria;
            $new_lote->id_clasificacion_verde = $lote_re->id_clasificacion_verde;
            $new_lote->etapa = 'A';
            $new_lote->apertura = $request->fecha;
            $new_lote->save();
            $new_lote = LoteRE::All()->last();
            bitacora('lote_re', $new_lote->id_lote_re, 'I', 'Mover tallos de fecha');
        } else {
            $new_lote->cantidad_tallos += $request->mover;
            $new_lote->save();
            bitacora('lote_re', $new_lote->id_lote_re, 'U', 'Mover tallos de fecha');
        }

        $new_stock = StockApertura::All()
            ->where('id_variedad', $apertura->id_variedad)
            ->where('id_clasificacion_unitaria', $apertura->id_clasificacion_unitaria)
            ->where('fecha_inicio', $request->fecha)
            ->where('estado', 1)
            ->where('disponibilidad', 1)
            ->where('cantidad_disponible', '>', 0)
            ->first();
        if ($new_stock == '') {
            $new_stock = new StockApertura();
            $new_stock->cantidad_tallos = $request->mover;
            $new_stock->id_variedad = $apertura->id_variedad;
            $new_stock->id_clasificacion_unitaria = $apertura->id_clasificacion_unitaria;
            $new_stock->cantidad_disponible = $request->mover;
            $new_stock->fecha_inicio = $request->fecha;
            $new_stock->dias = $apertura->dias;
            $new_stock->id_lote_re = $new_lote->id_lote_re;
            $new_stock->save();
            $new_stock = StockApertura::All()->last();
            bitacora('lote_re', $new_stock->id_stock_apertura, 'I', 'Mover tallos de fecha');
        } else {
            $new_stock->cantidad_disponible += $request->mover;
            $new_stock->save();
            bitacora('lote_re', $new_stock->id_stock_apertura, 'U', 'Mover tallos de fecha');
        }

        return [
            'success' => true,
            'mensaje' => '<div class="alert alert-success text-center">Se ha movido de fecha satisfactoriamente la cantidad de tallos indicada</div>'
        ];
    }
}