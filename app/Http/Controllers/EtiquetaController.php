<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet;
use yura\Modelos\Comprobante;
use yura\Modelos\Submenu;

class EtiquetaController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.gestion.postcocecha.etiquetas.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Etiquetas', 'subtitulo' => 'módulo de postcocecha']
            ]);
    }

    public function listado(Request $request){
        $data = Comprobante::where([
            ['estado',5],
            ['tipo_comprobante','01'],
            ['habilitado',1]
        ])->select('id_comprobante');

        $request->hasta =! ""
            ? $data->whereBetween('fecha_emision',[$request->desde,$request->hasta])
            : $data->where('fecha_emision',$request->desde);


        return view('adminlte.gestion.postcocecha.etiquetas.partials.listado',[
            'facturas'=> $data->get()
        ]);
    }

    public function exportar_excel(Request $request)
    {
        //---------------------- EXCEL --------------------------------------
        $objPHPExcel = new PHPExcel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");

        $objPHPExcel->removeSheetByIndex(0); //Eliminar la hoja inicial por defecto

        $this->excel_facturas_etiquetas($objPHPExcel, $request);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="Etiquestas Cajas.xlsx"');
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

    public function excel_facturas_etiquetas($objPHPExcel, $request)
    {
        $objSheet = new PHPExcel_Worksheet($objPHPExcel, 'codigos DAE');
        $objPHPExcel->addSheet($objSheet, 0);
        $objSheet->mergeCells('A1:F1');
        $objSheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(12);
        //$objSheet->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //$objSheet->getStyle('A1:F1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCFFCC');

        $objSheet->getCell('A1')->setValue('Cabecera');
        $objSheet->getCell('B1')->setValue('Guía');
        $objSheet->getCell('C1')->setValue('Guía_H');
        $objSheet->getCell('D1')->setValue('Variedad');
        $objSheet->getCell('E1')->setValue('Secuencial');
        $objSheet->getCell('F1')->setValue('Total ETQ');
        $objSheet->getCell('G1')->setValue('Cliente');
        $objSheet->getCell('H1')->setValue('Cliente_b');
        $objSheet->getCell('I1')->setValue('Cod. Cliente');
        $objSheet->getCell('J1')->setValue('mis');
        $objSheet->getCell('K1')->setValue('Ramos caja');
        $objSheet->getCell('L1')->setValue('Tallos');
        $objSheet->getCell('M1')->setValue('Longitud');
        $objSheet->getCell('N1')->setValue('Peso');
        $objSheet->getCell('O1')->setValue('Cod-Finca');
        $objSheet->getCell('P1')->setValue('Registro');
        $objSheet->getCell('Q1')->setValue('País destino');
        $objSheet->getCell('R1')->setValue('Refrendo');
        $objSheet->getCell('S1')->setValue('Ruc');
        $objSheet->getCell('T1')->setValue('Color0');
        $objSheet->getCell('U1')->setValue('Length0');
        $objSheet->getCell('V1')->setValue('Bounches0');
        $objSheet->getCell('W1')->setValue('Weigth0');

        /*$objSheet->getStyle('A2:F2')->getFont()->setBold(true)->setSize(12);

        $objSheet->getStyle('A2:F2')
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
            ->getColor()
            ->setRGB(PHPExcel_Style_Color::COLOR_BLACK);

        $objSheet->getStyle('A2:F2')
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CCFFCC');*/

        if (isset($request->arr_facturas)) {
            foreach ($request->arr_facturas as $factura) {
                $comprobante = getComprobante($factura['id_comprobante']);
                foreach ($comprobante->envio->pedido->detalles as $det_ped) {
                    foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp){
                        foreach ($esp_emp->detalles as $n => $det_esp_emp){
                            for ($x=1;$x<=$det_ped->cantidad;$x++){
                                //--------------------------- LLENAR LA TABLA ---------------------------------------------
                                for ($i = 0; $i < sizeof($request['arreglo']); $i++) {
                                    $objSheet->getCell('A' . ($i + 3))->setValue(substr($request['arreglo'][$i], 0, 16));
                                    $objSheet->getCell('B' . ($i + 3))->setValue(Pais::where('codigo', $request['arreglo'][$i])->select('nombre')->first()->nombre);
                                }
                            }
                        }
                    }
                }
            }




            $objSheet->getColumnDimension('A')->setAutoSize(true);
            $objSheet->getColumnDimension('B')->setAutoSize(true);
            $objSheet->getColumnDimension('C')->setAutoSize(true);
            $objSheet->getColumnDimension('D')->setAutoSize(true);
            $objSheet->getColumnDimension('E')->setAutoSize(true);
            $objSheet->getColumnDimension('F')->setAutoSize(true);

        } else {
            $objSheet->getCell('A1')->setValue('No se han seleccionado paises');
        }
    }
}
