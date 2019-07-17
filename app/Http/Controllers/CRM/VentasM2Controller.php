<?php

namespace yura\Http\Controllers\CRM;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use yura\Http\Controllers\Controller;
use yura\Modelos\Pedido;
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

class VentasM2Controller extends Controller
{
    public function inicio(Request $request)
    {
        $array_variedades = [];
        $total_mensual = 0;
        $total_anual = 0;
        foreach (getVariedades() as $var) {
            /* =========================== 4 MESES ======================== */
            $fecha_hasta = date('Y-m-d', strtotime('last month'));
            $fecha_desde = date('Y-m-d', strtotime('-4 month'));

            $data_venta_mensual = DB::table('historico_ventas')
                ->select(DB::raw('sum(valor) as cant'))
                ->where('id_variedad', '=', $var->id_variedad)
                ->where('anno', '=', substr($fecha_desde, 0, 4))
                ->where('mes', '>=', substr($fecha_desde, 5, 2))
                ->get()[0]->cant;
            if (substr($fecha_desde, 0, 4) != substr($fecha_hasta, 0, 4)) {
                $data_venta_mensual += DB::table('historico_ventas')
                    ->select(DB::raw('sum(valor) as cant'))
                    ->where('id_variedad', '=', $var->id_variedad)
                    ->where('anno', '=', substr($fecha_hasta, 0, 4))
                    ->where('mes', '<=', substr($fecha_hasta, 5, 2))
                    ->get()[0]->cant;
            }
            $semana_desde = getSemanaByDate(opDiasFecha('-', 91, date('Y-m-d')));
            $semana_hasta = getSemanaByDate(date('Y-m-d'));

            /* =========================== 1 AÑO ======================== */
            $fecha_hasta = date('Y-m-d', strtotime('last month'));
            $fecha_desde = date('Y-m-d', strtotime('last year'));

            $data_venta_anual = DB::table('historico_ventas')
                ->select(DB::raw('sum(valor) as cant'))
                ->where('id_variedad', '=', $var->id_variedad)
                ->where('anno', '=', substr($fecha_desde, 0, 4))
                ->where('mes', '>=', substr($fecha_desde, 5, 2))
                ->get()[0]->cant;
            if (substr($fecha_desde, 0, 4) != substr($fecha_hasta, 0, 4)) {
                $data_venta_anual += DB::table('historico_ventas')
                    ->select(DB::raw('sum(valor) as cant'))
                    ->where('id_variedad', '=', $var->id_variedad)
                    ->where('anno', '=', substr($fecha_hasta, 0, 4))
                    ->where('mes', '<=', substr($fecha_hasta, 5, 2))
                    ->get()[0]->cant;
            }

            $semana_desde = getSemanaByDate(opDiasFecha('-', 91, date('Y-m-d')));
            $semana_hasta = getSemanaByDate(date('Y-m-d'));
            $data = getAreaCiclosByRango($semana_desde->codigo, $semana_hasta->codigo, $var->id_variedad);
            $data_area_anual = getAreaActivaFromData($data['variedades'], $data['semanas']);

            if ($data_venta_mensual > 0 || ($data_area_anual > 0 && $data_venta_anual > 0)) {
                array_push($array_variedades, [
                    'variedad' => $var,
                    'venta_mensual' => $data_venta_mensual,
                    'area_anual' => $data_area_anual,
                    'venta_anual' => $data_venta_anual,
                ]);

                if ($data_area_anual > 0) {
                    $total_mensual += round(($data_venta_mensual / round($data_area_anual * 10000, 2)), 2) * 3;
                    $total_anual += round(($data_venta_anual / round($data_area_anual * 10000, 2)), 2);
                }
            }
        }

        return view('adminlte.crm.ventas_m2.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'variedades' => $array_variedades,
            'total_mensual' => $total_mensual,
            'total_anual' => $total_anual,
        ]);
    }

    /* ======================== EXCEL ===================== */

    public function exportar_excel(Request $request)
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
        header('Content-Disposition:inline;filename="Dashboard Ventas_m2.xlsx"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        ob_start();
        $objWriter->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        $opResult = array(
            'status' => 1,
            'data' => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData)
        );
        echo json_encode($opResult);
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

        $objSheet = new PHPExcel_Worksheet($objPHPExcel, 'Dashboard');
        $objPHPExcel->addSheet($objSheet, 0);

        $data = $this->data_dashboard($request);
        $pos_fila = 1;
        /* ================================= VARIEDADES ================================ */
        foreach ($data['variedades'] as $var) {
            /* ============== CENTRAR =============*/
            $objSheet->getStyle('A' . $pos_fila . ':D' . intval($pos_fila + 2))
                ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            /* ============== BACKGROUND COLOR =============*/
            $objSheet->getStyle('A' . $pos_fila . ':D' . intval($pos_fila + 2))
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB(substr($var['variedad']->color, 1));

            /* ============== MERGE CELDAS =============*/
            $objSheet->mergeCells('A' . $pos_fila . ':C' . $pos_fila);
            if ($var['area_anual'] > 0)
                $mensual = number_format(round(($var['venta_mensual'] / round($var['area_anual'] * 10000, 2)), 2) * 3, 2);
            else
                $mensual = 0;
            $objSheet->getCell('A' . $pos_fila)->setValue($mensual);
            $objSheet->getCell('D' . $pos_fila)->setValue('(4 meses)');
            /* ============== CENTRAR =============*/
            $objSheet->getStyle('A' . $pos_fila . ':D' . $pos_fila)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $pos_fila++;
            /* ============== MERGE CELDAS =============*/
            $objSheet->mergeCells('A' . $pos_fila . ':C' . $pos_fila);
            if ($var['area_anual'] > 0)
                $anual = number_format(round(($var['venta_anual'] / round($var['area_anual'] * 10000, 2)), 2), 2);
            else
                $anual = 0;
            $objSheet->getCell('A' . $pos_fila)->setValue($anual);
            $objSheet->getCell('D' . $pos_fila)->setValue('(1 año)');

            $pos_fila++;
            /* ============== MERGE CELDAS =============*/
            $objSheet->mergeCells('B' . $pos_fila . ':D' . $pos_fila);
            $objSheet->getCell('A' . $pos_fila)->setValue('($/m2/año)');
            $objSheet->getCell('B' . $pos_fila)->setValue($var['variedad']->siglas);

            $pos_fila += 2;
        }

        /* ================================= GRAFICAS ================================ */
        $data_img = base64_decode(explode(',', $request->src_imagen_chart_mensual)[1]);
        file_put_contents(public_path() . '/images/chart_mensual.png', $data_img);
        $data_img = base64_decode(explode(',', $request->src_imagen_chart_anual)[1]);
        file_put_contents(public_path() . '/images/chart_anual.png', $data_img);

        $img_mensual = imagecreatefrompng(public_path() . '/images/chart_mensual.png');
        $img_anual = imagecreatefrompng(public_path() . '/images/chart_anual.png');

        $background = imagecolorallocate($img_mensual, 0, 0, 0);
        // removing the black from the placeholder
        imagecolortransparent($img_mensual, $background);
        imagecolortransparent($img_anual, $background);

        /* -------------------------------- MENSUAL ------------------------------------- */
        $pos_fila = 1;
        /* ============== MERGE CELDAS =============*/
        $objSheet->mergeCells('F' . $pos_fila . ':O' . $pos_fila);
        /* ============== CENTRAR =============*/
        $objSheet->getStyle('F' . $pos_fila)
            ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        /* ============== BACKGROUND COLOR =============*/
        $objSheet->getStyle('F' . $pos_fila)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('d2d6de');
        $objSheet->getCell('F' . $pos_fila)->setValue('4 MESES (% por variedad)');

        $pos_fila++;
        /* ============== MERGE CELDAS =============*/
        $objSheet->mergeCells('F' . $pos_fila . ':M' . intval($pos_fila + 13));
        /* ============== BORDE COLOR =============*/
        $objSheet->getStyle('F' . $pos_fila)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)
            ->getColor()
            ->setRGB('000000');

        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('MENSUAL');
        $objDrawing->setDescription('MENSUAL');
        $objDrawing->setImageResource($img_mensual);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG);
        //$objDrawing->setHeight();
        $objDrawing->setCoordinates('F' . $pos_fila);
        $objDrawing->setWorksheet($objSheet);

        $pos_var = 1;
        foreach ($data['variedades'] as $var) {
            /* ============== CENTRAR =============*/
            $objSheet->getStyle('N' . intval($pos_var + 1) . ':O' . intval($pos_var + 1))
                ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            /* ============== BACKGROUND COLOR =============*/
            $objSheet->getStyle('N' . intval($pos_var + 1))
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB(substr($var['variedad']->color, 1));
            $objSheet->getCell('N' . intval($pos_var + 1))->setValue($var['variedad']->siglas);
            if ($var['area_anual'] > 0 && $data['total_mensual'] > 0)
                $objSheet->getCell('O' . intval($pos_var + 1))
                    ->setValue(round((($var['venta_mensual'] / round($var['area_anual'] * 10000, 2)) / $data['total_mensual']) * 100, 2) * 3 . '%');
            else
                $objSheet->getCell('O' . intval($pos_var + 1))
                    ->setValue(0);

            $pos_var++;
        }

        if (count($data['variedades']) > 14)
            $pos_fila += count($data['variedades']);
        else
            $pos_fila += 13;

        /* -------------------------------- ANUAL ------------------------------------- */
        $pos_fila += 2;
        /* ============== MERGE CELDAS =============*/
        $objSheet->mergeCells('F' . $pos_fila . ':O' . $pos_fila);
        /* ============== CENTRAR =============*/
        $objSheet->getStyle('F' . $pos_fila)
            ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        /* ============== BACKGROUND COLOR =============*/
        $objSheet->getStyle('F' . $pos_fila)
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('d2d6de');
        $objSheet->getCell('F' . $pos_fila)->setValue('1 AÑO (% por variedad)');

        $pos_var = $pos_fila;
        foreach ($data['variedades'] as $var) {
            /* ============== CENTRAR =============*/
            $objSheet->getStyle('N' . intval($pos_var + 1) . ':O' . intval($pos_var + 1))
                ->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            /* ============== BACKGROUND COLOR =============*/
            $objSheet->getStyle('N' . intval($pos_var + 1))
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB(substr($var['variedad']->color, 1));
            $objSheet->getCell('N' . intval($pos_var + 1))->setValue($var['variedad']->siglas);
            if ($var['area_anual'] > 0 && $data['total_anual'] > 0)
                $objSheet->getCell('O' . intval($pos_var + 1))
                    ->setValue(round((($var['venta_anual'] / round($var['area_anual'] * 10000, 2)) / $data['total_anual']) * 100, 2) . '%');
            else
                $objSheet->getCell('O' . intval($pos_var + 1))
                    ->setValue(0);

            $pos_var++;
        }

        $pos_fila++;
        /* ============== MERGE CELDAS =============*/
        $objSheet->mergeCells('F' . $pos_fila . ':M' . intval($pos_fila + 13));
        /* ============== BORDE COLOR =============*/
        $objSheet->getStyle('F' . $pos_fila)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)
            ->getColor()
            ->setRGB('000000');

        $objDrawing = new PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setName('ANUAL');
        $objDrawing->setDescription('ANUAL');
        $objDrawing->setImageResource($img_anual);
        $objDrawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
        $objDrawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG);
        //$objDrawing->setHeight();
        $objDrawing->setCoordinates('F' . $pos_fila);
        $objDrawing->setWorksheet($objSheet);

        if (count($data['variedades']) > 14)
            $pos_fila += count($data['variedades']);
        else
            $pos_fila += 13;

        /* ------------------------------ BORRAR IMAGENES ------------------------------- */

        unlink(public_path() . '/images/chart_mensual.png');
        unlink(public_path() . '/images/chart_anual.png');

        /* ============== LETRAS NEGRITAS =============*/
        $objSheet->getStyle('A1:O' . $pos_fila)->getFont()->setBold(true)->setSize(12);

        foreach ($columnas as $pos => $c) {
            if ($c != 'E' && $pos <= 14)
                $objSheet->getColumnDimension($c)->setWidth(11);
        }
    }

    public function data_dashboard(Request $request)
    {
        $array_variedades = [];
        $total_mensual = 0;
        $total_anual = 0;
        foreach (getVariedades() as $var) {
            /* =========================== 4 MESES ======================== */
            $fecha_hasta = date('Y-m-d', strtotime('last month'));
            $fecha_desde = date('Y-m-d', strtotime('-4 month'));

            $data_venta_mensual = DB::table('historico_ventas')
                ->select(DB::raw('sum(valor) as cant'))
                ->where('id_variedad', '=', $var->id_variedad)
                ->where('anno', '=', substr($fecha_desde, 0, 4))
                ->where('mes', '>=', substr($fecha_desde, 5, 2))
                ->get()[0]->cant;
            if (substr($fecha_desde, 0, 4) != substr($fecha_hasta, 0, 4)) {
                $data_venta_mensual += DB::table('historico_ventas')
                    ->select(DB::raw('sum(valor) as cant'))
                    ->where('id_variedad', '=', $var->id_variedad)
                    ->where('anno', '=', substr($fecha_hasta, 0, 4))
                    ->where('mes', '<=', substr($fecha_hasta, 5, 2))
                    ->get()[0]->cant;
            }
            $semana_desde = getSemanaByDate(opDiasFecha('-', 91, date('Y-m-d')));
            $semana_hasta = getSemanaByDate(date('Y-m-d'));

            /* =========================== 1 AÑO ======================== */
            $fecha_hasta = date('Y-m-d', strtotime('last month'));
            $fecha_desde = date('Y-m-d', strtotime('last year'));

            $data_venta_anual = DB::table('historico_ventas')
                ->select(DB::raw('sum(valor) as cant'))
                ->where('id_variedad', '=', $var->id_variedad)
                ->where('anno', '=', substr($fecha_desde, 0, 4))
                ->where('mes', '>=', substr($fecha_desde, 5, 2))
                ->get()[0]->cant;
            if (substr($fecha_desde, 0, 4) != substr($fecha_hasta, 0, 4)) {
                $data_venta_anual += DB::table('historico_ventas')
                    ->select(DB::raw('sum(valor) as cant'))
                    ->where('id_variedad', '=', $var->id_variedad)
                    ->where('anno', '=', substr($fecha_hasta, 0, 4))
                    ->where('mes', '<=', substr($fecha_hasta, 5, 2))
                    ->get()[0]->cant;
            }
            $semana_desde = getSemanaByDate(opDiasFecha('-', 91, date('Y-m-d')));
            $semana_hasta = getSemanaByDate(date('Y-m-d'));
            $data = getAreaCiclosByRango($semana_desde->codigo, $semana_hasta->codigo, $var->id_variedad);
            $data_area_anual = getAreaActivaFromData($data['variedades'], $data['semanas']);

            if ($data_venta_mensual > 0 || ($data_area_anual > 0 && $data_venta_anual > 0)) {
                array_push($array_variedades, [
                    'variedad' => $var,
                    'venta_mensual' => $data_venta_mensual,
                    'area_anual' => $data_area_anual,
                    'venta_anual' => $data_venta_anual,
                ]);

                if ($data_area_anual > 0) {
                    $total_mensual += round(($data_venta_mensual / round($data_area_anual * 10000, 2)), 2) * 3;
                    $total_anual += round(($data_venta_anual / round($data_area_anual * 10000, 2)), 2);
                }
            }
        }

        return [
            'variedades' => $array_variedades,
            'total_mensual' => $total_mensual,
            'total_anual' => $total_anual,
        ];
    }
}