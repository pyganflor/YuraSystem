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
            /* =========================== 4 SEMANAS ======================== */
            $desde = opDiasFecha('-', 28, date('Y-m-d'));
            $hasta = opDiasFecha('-', 7, date('Y-m-d'));
            $semana_desde = getSemanaByDate($desde);
            $semana_hasta = getSemanaByDate($hasta);
            /* --------------------------- dinero --------------------------- */
            $venta = getVentaByRango($semana_desde->codigo, $semana_hasta->codigo, $var->id_variedad);
            /* --------------------------- area cerrada --------------------------- */
            $ciclos = getCiclosCerradosByRango($semana_desde->codigo, $semana_hasta->codigo, $var->id_variedad);
            $area = $ciclos['area_cerrada'];
            /* --------------------------- ciclo año --------------------------- */
            $ciclos['ciclo'] > 0 ? $ciclo_anno = round(365 / $ciclos['ciclo']) : $ciclo_anno = 0;

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

            if (($venta > 0 && $area > 0) || ($data_area_anual > 0 && $data_venta_anual > 0)) {
                array_push($array_variedades, [
                    'variedad' => $var,
                    'venta' => $venta['valor'],
                    'area_cerrada' => $area,
                    'ciclo_anno' => $ciclo_anno,
                    'area_anual' => $data_area_anual,
                    'venta_anual' => $data_venta_anual,
                ]);

                if ($area > 0)
                    $total_mensual += round(($venta['valor'] / $area) * $ciclo_anno, 2);
                if ($data_area_anual > 0)
                    $total_anual += round(($data_venta_anual / round($data_area_anual * 10000, 2)), 2);
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

    public function exportar_dashboard(Request $request)
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
        header('Content-Disposition:inline;filename="Dashboard Ventas/m2.xlsx"');
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
            /* ============== MERGE CELDAS =============*/
            $objSheet->mergeCells('A' . $pos_fila . ':D' . intval($pos_fila + 2));
            /* ============== BACKGROUND COLOR =============*/
            $objSheet->getStyle('A' . $pos_fila . ':D' . intval($pos_fila + 2))
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB(substr($var['variedad']->color, 1));
            if ($var['area_cerrada'] > 0)
                $mensual = number_format(round(($var['venta'] / $var['area_cerrada']) * $var['ciclo_anno'], 2), 2);
            else
                $mensual = 0;
            $objSheet->getCell('A' . $pos_fila)->setValue($mensual);

            $pos_fila += 4;
        }




        foreach ($columnas as $c) {
            $objSheet->getColumnDimension($c)->setAutoSize(true);
        }
    }

    public function data_dashboard(Request $request)
    {
        $array_variedades = [];
        $total_mensual = 0;
        $total_anual = 0;
        foreach (getVariedades() as $var) {
            /* =========================== 4 SEMANAS ======================== */
            $desde = opDiasFecha('-', 28, date('Y-m-d'));
            $hasta = opDiasFecha('-', 7, date('Y-m-d'));
            $semana_desde = getSemanaByDate($desde);
            $semana_hasta = getSemanaByDate($hasta);
            /* --------------------------- dinero --------------------------- */
            $venta = getVentaByRango($semana_desde->codigo, $semana_hasta->codigo, $var->id_variedad);
            /* --------------------------- area cerrada --------------------------- */
            $ciclos = getCiclosCerradosByRango($semana_desde->codigo, $semana_hasta->codigo, $var->id_variedad);
            $area = $ciclos['area_cerrada'];
            /* --------------------------- ciclo año --------------------------- */
            $ciclos['ciclo'] > 0 ? $ciclo_anno = round(365 / $ciclos['ciclo']) : $ciclo_anno = 0;

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

            if (($venta > 0 && $area > 0) || ($data_area_anual > 0 && $data_venta_anual > 0)) {
                array_push($array_variedades, [
                    'variedad' => $var,
                    'venta' => $venta['valor'],
                    'area_cerrada' => $area,
                    'ciclo_anno' => $ciclo_anno,
                    'area_anual' => $data_area_anual,
                    'venta_anual' => $data_venta_anual,
                ]);

                if ($area > 0)
                    $total_mensual += round(($venta['valor'] / $area) * $ciclo_anno, 2);
                if ($data_area_anual > 0)
                    $total_anual += round(($data_venta_anual / round($data_area_anual * 10000, 2)), 2);
            }
        }

        return [
            'variedades' => $array_variedades,
            'total_mensual' => $total_mensual,
            'total_anual' => $total_anual,
        ];
    }
}