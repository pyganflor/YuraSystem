<?php

namespace yura\Http\Controllers;

use Carbon\Carbon;
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
        return view('adminlte.gestion.postcocecha.etiquetas.partials.listado',[
            'facturas'=> Comprobante::where([
                ['estado',5],
                ['tipo_comprobante','01'],
                ['habilitado',1],
                //['fecha_emision',$request->desde]
            ])->select('id_comprobante')->get()
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

        $objSheet->getCell('A1')->setValue('Guía');
        $objSheet->getCell('B1')->setValue('Guía_H');
        $objSheet->getCell('C1')->setValue('Secuencial');
        $objSheet->getCell('D1')->setValue('Total ETQ');
        $objSheet->getCell('E1')->setValue('Cliente');
        $objSheet->getCell('F1')->setValue('Cliente_b');
        $objSheet->getCell('G1')->setValue('Cod. Cliente');
        $objSheet->getCell('H1')->setValue('Cod-Finca');
        $objSheet->getCell('I1')->setValue('Registro');
        $objSheet->getCell('J1')->setValue('País destino');
        $objSheet->getCell('K1')->setValue('Refrendo');
        $objSheet->getCell('L1')->setValue('Ruc');
        $objSheet->getCell('M1')->setValue('Variedad0');


        $objSheet->getCell('N1')->setValue('Color0');
        $objSheet->getCell('O1')->setValue('Length0');
        $objSheet->getCell('P1')->setValue('Bounches0');
        $objSheet->getCell('Q1')->setValue('Weigth0');
        $objSheet->getCell('R1')->setValue('Variedad1');
        $objSheet->getCell('S1')->setValue('Color1');
        $objSheet->getCell('T1')->setValue('Length1');
        $objSheet->getCell('U1')->setValue('Bounches1');
        $objSheet->getCell('V1')->setValue('Weigth1');
        $objSheet->getCell('W1')->setValue('Variedad2');
        $objSheet->getCell('X1')->setValue('Color2');
        $objSheet->getCell('Y1')->setValue('Length2');
        $objSheet->getCell('Z1')->setValue('Bounches2');
        $objSheet->getCell('AA1')->setValue('Weigth2');
        $objSheet->getCell('AB1')->setValue('Variedad3');
        $objSheet->getCell('AC1')->setValue('Color3');
        $objSheet->getCell('AD1')->setValue('Length3');
        $objSheet->getCell('AE1')->setValue('Bounches3');
        $objSheet->getCell('AF1')->setValue('Weigth3');
        $objSheet->getCell('AG1')->setValue('Variedad4');
        $objSheet->getCell('AH1')->setValue('Color4');
        $objSheet->getCell('AI1')->setValue('Length4');
        $objSheet->getCell('AJ1')->setValue('Bounches4');
        $objSheet->getCell('AK1')->setValue('Weigth4');
        $objSheet->getCell('AL1')->setValue('Variedad5');
        $objSheet->getCell('AM1')->setValue('Color5');
        $objSheet->getCell('AN1')->setValue('Length5');
        $objSheet->getCell('AO1')->setValue('Bounches5');
        $objSheet->getCell('AP1')->setValue('Weigth5');
        $objSheet->getCell('AQ1')->setValue('Variedad6');
        $objSheet->getCell('AR1')->setValue('Color6');
        $objSheet->getCell('AS1')->setValue('Length6');
        $objSheet->getCell('AT1')->setValue('Bounches6');
        $objSheet->getCell('AU1')->setValue('Weigth6');
        $objSheet->getCell('AV1')->setValue('Variedad7');
        $objSheet->getCell('AW1')->setValue('Color7');
        $objSheet->getCell('AX1')->setValue('Length7');
        $objSheet->getCell('AY1')->setValue('Bounches7');
        $objSheet->getCell('AZ1')->setValue('Weigth7');

        if (sizeof($request->arr_facturas) > 0) {
            $w = 1;
            $nombre_empresa = ['E','D','A','S','A','L','F','L','O','R'];
            $semana = substr(getSemanaByDate(now()->toDateString())->codigo,2,2);
            $anno = Carbon::parse(now()->toDateString())->format('y');
            $dia_semana = Carbon::parse(now()->toDateString())->dayOfWeek;
            $numeracion = $anno.$semana.$dia_semana;
            $numeracion=str_split($numeracion);
            $codigo_finca = '';

            foreach ($numeracion as $n){
                if(isset(getSemanaByDate(now()->toDateString())->codigo)){
                    for ($x=0;$x<count($nombre_empresa);$x++){
                        if($x == $n){
                            $codigo_finca .= $nombre_empresa[$x];
                            break;
                        }
                    }
                }
            }

            foreach ($request->arr_facturas as $a => $factura) {
                $comprobante = getComprobante($factura['id_comprobante']);
                $factura['doble'] == "true"  ? $doble = 2  : $doble = 1;

                if($a == 0){
                    for($y=1;$y<=$doble;$y++) {
                        if($comprobante->envio->pedido->tipo_especificacion == "N"){
                            foreach ($comprobante->envio->pedido->detalles as $det_ped) {

                                $datos_exportacion ='';
                                if(getDatosExportacionByDetPed($det_ped->id_detalle_pedido)->count() > 0)
                                    foreach (getDatosExportacionByDetPed($det_ped->id_detalle_pedido) as $dE)
                                        $datos_exportacion .= $dE->valor."-";

                                foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp) {
                                    if (explode("|", $esp_emp->empaque->nombre)[1] === $factura['caja']) {
                                        foreach ($esp_emp->detalles as $det_esp_emp) {
                                            $x = 1;
                                            for ($z = 1; $z <= $det_ped->cantidad; $z++) {
                                                //--------------------------- LLENAR LA TABLA ---------------------------------------------
                                                $planta = substr(getVariedad($det_esp_emp->id_variedad)->planta->nombre, 0, 3);
                                                $objSheet->getCell('A' . ($w + 1))->setValue($planta);
                                                $objSheet->getCell('B' . ($w + 1))->setValue("AWB. " . $comprobante->envio->guia_madre);
                                                $objSheet->getCell('C' . ($w + 1))->setValue("HAWB. " . $comprobante->envio->guia_hija);
                                                $objSheet->getCell('D' . ($w + 1))->setValue($planta . " - " . $det_esp_emp->variedad->nombre);
                                                $objSheet->getCell('E' . ($w + 1))->setValue($x);
                                                $objSheet->getCell('F' . ($w + 1))->setValue($det_ped->cantidad);
                                                $objSheet->getCell('G' . ($w + 1))->setValue(strtoupper($comprobante->envio->pedido->cliente->detalle()->nombre));
                                                $objSheet->getCell('H' . ($w + 1))->setValue();
                                                $objSheet->getCell('I' . ($w + 1))->setValue($datos_exportacion != '' ? substr($datos_exportacion,0,-1): "");
                                                $objSheet->getCell('J' . ($w + 1))->setValue();
                                                $objSheet->getCell('K' . ($w + 1))->setValue($det_esp_emp->cantidad);
                                                $objSheet->getCell('L' . ($w + 1))->setValue($det_esp_emp->tallos_x_ramos*$esp_emp->cantidad*$det_ped->cantidad*($det_esp_emp->cantidad*$det_ped->cantidad));
                                                $objSheet->getCell('M' . ($w + 1))->setValue($det_esp_emp->longitud_ramo ." ". $det_esp_emp->unidad_medida->siglas);
                                                $objSheet->getCell('N' . ($w + 1))->setValue($det_esp_emp->clasificacion_ramo->nombre. " ". $det_esp_emp->clasificacion_ramo->unidad_medida->siglas.".");
                                                $objSheet->getCell('O' . ($w + 1))->setValue("DS-".$codigo_finca);
                                                $objSheet->getCell('P' . ($w + 1))->setValue(getConfiguracionEmpresa()->permiso_agrocalidad);
                                                $pais_destino = getPais($comprobante->envio->pedido->cliente->detalle()->codigo_pais)->nombre;
                                                $dae = $comprobante->envio->dae;
                                                $factura_tercero = getFacturaClienteTercero($comprobante->envio->id_envio);
                                                if($factura_tercero!= "" && isset($factura_tercero->codigo_pais)){
                                                    $pais_destino = getPais($factura_tercero->codigo_pais)->nombre;
                                                    $dae = $factura_tercero->dae;
                                                }
                                                $objSheet->getCell('Q' . ($w + 1))->setValue("País destino. ".$pais_destino);
                                                $objSheet->getCell('R' . ($w + 1))->setValue($dae);
                                                $objSheet->getCell('S' . ($w + 1))->setValue("RUC: ".env('RUC'));
                                                $color = getColoracionByDetPed($det_ped->id_detalle_pedido);
                                                $cantidad_color0 = 0;
                                                if(isset($color[0])) {
                                                    foreach ($color[0]->marcaciones_coloraciones as $mC) {
                                                        $cantidad_color0 += $mC->cantidad;
                                                    }
                                                }
                                                $objSheet->getCell('T' . ($w + 1))->setValue(isset($color[0]->color->nombre) ? $color[0]->color->nombre : "White");
                                                $objSheet->getCell('U' . ($w + 1))->setValue( $det_esp_emp->longitud_ramo ." ". $det_esp_emp->unidad_medida->siglas);
                                                $objSheet->getCell('V' . ($w + 1))->setValue(isset($color[0]->color->nombre) ?  $cantidad_color0 : $det_esp_emp->cantidad );
                                                $objSheet->getCell('W' . ($w + 1))->setValue($det_esp_emp->clasificacion_ramo->nombre." ". $det_esp_emp->clasificacion_ramo->unidad_medida->siglas.".");

                                                /*$objSheet->getCell('X' . ($w + 1))->setValue(isset($color[1]->color->nombre) ? $color[1]->color->nombre : "");
                                                $objSheet->getCell('Y' . ($w + 1))->setValue(isset($color[1]->color->nombre) ? $det_esp_emp->longitud_ramo ." ". $det_esp_emp->unidad_medida->siglas : "");
                                                $objSheet->getCell('Z' . ($w + 1))->setValue();
                                                $objSheet->getCell('AA' . ($w + 1))->setValue(isset($color[1]->color->nombre)  ? $det_esp_emp->clasificacion_ramo->nombre." ". $det_esp_emp->clasificacion_ramo->unidad_medida->siglas."." : "");


                                                $objSheet->getCell('AB' . ($w + 1))->setValue(isset($color[2]->color->nombre)  ? $color[2]->color->nombre : "");
                                                $objSheet->getCell('AC' . ($w + 1))->setValue(isset($color[2]->color->nombre)  ? $det_esp_emp->longitud_ramo ." ". $det_esp_emp->unidad_medida->siglas : "");
                                                $objSheet->getCell('AD' . ($w + 1))->setValue();
                                                $objSheet->getCell('AE' . ($w + 1))->setValue(isset($color[2]->color->nombre)  ? $det_esp_emp->clasificacion_ramo->nombre." ". $det_esp_emp->clasificacion_ramo->unidad_medida->siglas."." : "");

                                                $objSheet->getCell('AF' . ($w + 1))->setValue(isset($color[3]->color->nombre)  ? $color[3]->color->nombre : "");
                                                $objSheet->getCell('AG' . ($w + 1))->setValue(isset($color[3]->color->nombre)  ? $det_esp_emp->longitud_ramo ." ". $det_esp_emp->unidad_medida->siglas : "");
                                                $objSheet->getCell('AH' . ($w + 1))->setValue();
                                                $objSheet->getCell('AI' . ($w + 1))->setValue();



                                                $objSheet->getCell('AJ' . ($w + 1))->setValue(isset($color[4]->color->nombre)  ? $color[4]->color->nombre : "");
                                                $objSheet->getCell('AK' . ($w + 1))->setValue(isset($color[4]->color->nombre)  ? $det_esp_emp->longitud_ramo ." ". $det_esp_emp->unidad_medida->siglas : "");
                                                $objSheet->getCell('AL' . ($w + 1))->setValue();
                                                $objSheet->getCell('AM' . ($w + 1))->setValue(isset($color[4]->color->nombre)  ? $det_esp_emp->clasificacion_ramo->nombre." ". $det_esp_emp->clasificacion_ramo->unidad_medida->siglas."." : "");



                                                $objSheet->getCell('AN' . ($w + 1))->setValue(isset($color[5]->color->nombre)  ? $color[5]->color->nombre : "");
                                                $objSheet->getCell('AO' . ($w + 1))->setValue(isset($color[5]->color->nombre)  ? $det_esp_emp->longitud_ramo ." ". $det_esp_emp->unidad_medida->siglas : "");
                                                $objSheet->getCell('AP' . ($w + 1))->setValue();
                                                $objSheet->getCell('AQ' . ($w + 1))->setValue(isset($color[5]->color->nombre)  ? $det_esp_emp->clasificacion_ramo->nombre." ". $det_esp_emp->clasificacion_ramo->unidad_medida->siglas."." : "");



                                                $objSheet->getCell('AR' . ($w + 1))->setValue(isset($color[6]->color->nombre)  ? $color[6]->color->nombre : "");
                                                $objSheet->getCell('AS' . ($w + 1))->setValue(isset($color[6]->color->nombre)  ? $det_esp_emp->longitud_ramo ." ". $det_esp_emp->unidad_medida->siglas : "");
                                                $objSheet->getCell('AT' . ($w + 1))->setValue();
                                                $objSheet->getCell('AU' . ($w + 1))->setValue(isset($color[6]->color->nombre)  ? $det_esp_emp->clasificacion_ramo->nombre." ". $det_esp_emp->clasificacion_ramo->unidad_medida->siglas."." : "");



                                                $objSheet->getCell('AV' . ($w + 1))->setValue(isset($color[7]->color->nombre)  ? $color[7]->color->nombre : "");
                                                $objSheet->getCell('AW' . ($w + 1))->setValue(isset($color[7]->color->nombre)  ? $det_esp_emp->longitud_ramo ." ". $det_esp_emp->unidad_medida->siglas : "");
                                                $objSheet->getCell('AX' . ($w + 1))->setValue();
                                                $objSheet->getCell('AY' . ($w + 1))->setValue(isset($color[7]->color->nombre)  ? $det_esp_emp->clasificacion_ramo->nombre." ". $det_esp_emp->clasificacion_ramo->unidad_medida->siglas."." : "");*/
                                                $w++;
                                                $x++;
                                            }
                                        }
                                    }
                                }
                            }
                        }else if($comprobante->envio->pedido->tipo_especificacion == "T"){
                            foreach ($comprobante->envio->pedido->detalles as $det_ped) {

                                $datos_exportacion ='';
                                if(getDatosExportacionByDetPed($det_ped->id_detalle_pedido)->count() > 0){
                                    foreach (getDatosExportacionByDetPed($det_ped->id_detalle_pedido) as $dE)
                                        $datos_exportacion .= $dE->valor."-";
                                }

                                /*if (explode("|", $esp_emp->empaque->nombre)[1] === $factura['caja']) {

                                }*/
                                foreach($det_ped->marcaciones as $mc){ //4
                                    foreach ($mc->distribuciones as $dist){
                                        for($z=1;$z<=$dist->piezas;$z++){
                                            // $color = $dist_col->marcacion_coloracion->coloracion->color->nombre;
                                            // $cantidad_ramos = $dist_col->cantidad/$dist->piezas;
                                            // $det_esp_emp = $dist->marcacion;//->marcacion_coloracion->detalle_especificacionempaque;
                                            // $esp_emp = $dist_col->marcacion_coloracion->detalle_especificacionempaque->especificacion_empaque;
                                            // $planta = substr(getVariedad($det_esp_emp->id_variedad)->planta->nombre, 0, 3);
                                            // $objSheet->getCell('A' . ($w + 1))->setValue($planta);
                                            $objSheet->getCell('A' . ($w + 1))->setValue("AWB. " . $comprobante->envio->guia_madre);
                                            $objSheet->getCell('B' . ($w + 1))->setValue("HAWB. " . $comprobante->envio->guia_hija);
                                            //$objSheet->getCell('D' . ($w + 1))->setValue($planta . " - " . $det_esp_emp->variedad->nombre);
                                            $objSheet->getCell('E' . ($w + 1))->setValue($z);
                                            $objSheet->getCell('D' . ($w + 1))->setValue($dist->piezas);
                                            $w++;
                                            /*foreach ($dist->distribuciones_coloraciones as $dist_col){
                                           }*/
                                        }
                                    }
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
            $objSheet->getColumnDimension('G')->setAutoSize(true);
            $objSheet->getColumnDimension('H')->setAutoSize(true);
            $objSheet->getColumnDimension('I')->setAutoSize(true);
            $objSheet->getColumnDimension('J')->setAutoSize(true);
            $objSheet->getColumnDimension('K')->setAutoSize(true);
            $objSheet->getColumnDimension('L')->setAutoSize(true);
            $objSheet->getColumnDimension('M')->setAutoSize(true);
            $objSheet->getColumnDimension('N')->setAutoSize(true);
            $objSheet->getColumnDimension('O')->setAutoSize(true);
            $objSheet->getColumnDimension('P')->setAutoSize(true);
            $objSheet->getColumnDimension('Q')->setAutoSize(true);
            $objSheet->getColumnDimension('R')->setAutoSize(true);
            $objSheet->getColumnDimension('S')->setAutoSize(true);
            $objSheet->getColumnDimension('T')->setAutoSize(true);
            $objSheet->getColumnDimension('U')->setAutoSize(true);
            $objSheet->getColumnDimension('V')->setAutoSize(true);
            $objSheet->getColumnDimension('W')->setAutoSize(true);
            $objSheet->getColumnDimension('X')->setAutoSize(true);
            $objSheet->getColumnDimension('Y')->setAutoSize(true);
            $objSheet->getColumnDimension('Z')->setAutoSize(true);
            $objSheet->getColumnDimension('AA')->setAutoSize(true);
            $objSheet->getColumnDimension('AB')->setAutoSize(true);
            $objSheet->getColumnDimension('AC')->setAutoSize(true);
            $objSheet->getColumnDimension('AD')->setAutoSize(true);
            $objSheet->getColumnDimension('AE')->setAutoSize(true);
            $objSheet->getColumnDimension('AF')->setAutoSize(true);
            $objSheet->getColumnDimension('AG')->setAutoSize(true);
            $objSheet->getColumnDimension('AH')->setAutoSize(true);
            $objSheet->getColumnDimension('AI')->setAutoSize(true);
            $objSheet->getColumnDimension('AJ')->setAutoSize(true);
            $objSheet->getColumnDimension('AK')->setAutoSize(true);
            $objSheet->getColumnDimension('AL')->setAutoSize(true);
            $objSheet->getColumnDimension('AM')->setAutoSize(true);
            $objSheet->getColumnDimension('AN')->setAutoSize(true);
            $objSheet->getColumnDimension('AO')->setAutoSize(true);
            $objSheet->getColumnDimension('AP')->setAutoSize(true);
            $objSheet->getColumnDimension('AQ')->setAutoSize(true);
            $objSheet->getColumnDimension('AR')->setAutoSize(true);
            $objSheet->getColumnDimension('AS')->setAutoSize(true);
            $objSheet->getColumnDimension('AT')->setAutoSize(true);
            $objSheet->getColumnDimension('AU')->setAutoSize(true);
            $objSheet->getColumnDimension('AV')->setAutoSize(true);
            $objSheet->getColumnDimension('AW')->setAutoSize(true);
            $objSheet->getColumnDimension('AX')->setAutoSize(true);
            $objSheet->getColumnDimension('AY')->setAutoSize(true);
        } else {
            $objSheet->getCell('A1')->setValue('No se han seleccionado facturas');
        }
    }
}
