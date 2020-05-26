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
use yura\Modelos\Pedido;

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
            'pedidos' => Pedido::where([
                ['pedido.fecha_pedido',$request->desde],
                ['pedido.estado',true],
                ['dc.estado',true]
            ])->join('envio as e','pedido.id_pedido','e.id_pedido')
                ->join('cliente as cl','pedido.id_cliente','cl.id_cliente')
                ->join('detalle_cliente as dc','cl.id_cliente','dc.id_cliente')
                ->leftJoin('comprobante as c','e.id_envio','c.id_envio')
                ->select('c.secuencial','dc.nombre as cli_nombre','pedido.id_pedido','c.estado as estado_comprobante')
                ->where('pedido.id_configuracion_empresa', $request->id_configuracion_empresa)->get()
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

        //dd("hola");
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

    /**
     * @param $objPHPExcel
     * @param $request
     * @throws \PHPExcel_Exception
     */
    public function excel_facturas_etiquetas($objPHPExcel, $request)
    {
        $objSheet = new PHPExcel_Worksheet($objPHPExcel, 'Etiquetas');
        $objPHPExcel->addSheet($objSheet, 0);

        $objSheet->getCell('A1')->setValue('Guía');
        $objSheet->getCell('B1')->setValue('Guía_H');
        $objSheet->getCell('C1')->setValue('Secuencial');
        $objSheet->getCell('D1')->setValue('Total ETQ');
        $objSheet->getCell('E1')->setValue('Cliente');
        $objSheet->getCell('F1')->setValue('Cliente_b');
        $objSheet->getCell('G1')->setValue('Cod. Cliente');
        $objSheet->getCell('H1')->setValue('Ramos caja');
        $objSheet->getCell('I1')->setValue('Cod-Finca');
        $objSheet->getCell('J1')->setValue('Registro');
        $objSheet->getCell('K1')->setValue('País destino');
        $objSheet->getCell('L1')->setValue('Refrendo');
        $objSheet->getCell('M1')->setValue('Ruc');
        $objSheet->getCell('N1')->setValue('Variedad0');
        $objSheet->getCell('O1')->setValue('Color0');
        $objSheet->getCell('P1')->setValue('Length0');
        $objSheet->getCell('Q1')->setValue('Bounches0');
        $objSheet->getCell('R1')->setValue('Weigth0');
        $objSheet->getCell('S1')->setValue('Variedad1');
        $objSheet->getCell('T1')->setValue('Color1');
        $objSheet->getCell('U1')->setValue('Length1');
        $objSheet->getCell('V1')->setValue('Bounches1');
        $objSheet->getCell('W1')->setValue('Weigth1');
        $objSheet->getCell('X1')->setValue('Variedad2');
        $objSheet->getCell('Y1')->setValue('Color2');
        $objSheet->getCell('Z1')->setValue('Length2');
        $objSheet->getCell('AA1')->setValue('Bounches2');
        $objSheet->getCell('AB1')->setValue('Weigth2');
        $objSheet->getCell('AC1')->setValue('Variedad3');
        $objSheet->getCell('AD1')->setValue('Color3');
        $objSheet->getCell('AE1')->setValue('Length3');
        $objSheet->getCell('AF1')->setValue('Bounches3');
        $objSheet->getCell('AG1')->setValue('Weigth3');
        $objSheet->getCell('AH1')->setValue('Variedad4');
        $objSheet->getCell('AI1')->setValue('Color4');
        $objSheet->getCell('AJ1')->setValue('Length4');
        $objSheet->getCell('AK1')->setValue('Bounches4');
        $objSheet->getCell('AL1')->setValue('Weigth4');
        $objSheet->getCell('AM1')->setValue('Variedad5');
        $objSheet->getCell('AN1')->setValue('Color5');
        $objSheet->getCell('AO1')->setValue('Length5');
        $objSheet->getCell('AP1')->setValue('Bounches5');
        $objSheet->getCell('AQ1')->setValue('Weigth5');
        $objSheet->getCell('AR1')->setValue('Variedad6');
        $objSheet->getCell('AS1')->setValue('Color6');
        $objSheet->getCell('AT1')->setValue('Length6');
        $objSheet->getCell('AU1')->setValue('Bounches6');
        $objSheet->getCell('AV1')->setValue('Weigth6');
        $objSheet->getCell('AW1')->setValue('Variedad7');
        $objSheet->getCell('AX1')->setValue('Color7');
        $objSheet->getCell('AY1')->setValue('Length7');
        $objSheet->getCell('AZ1')->setValue('Bounches7');
        $objSheet->getCell('BA1')->setValue('Weigth7');
        $objSheet->getCell('BB1')->setValue('Variedad8');
        $objSheet->getCell('BC1')->setValue('Color8');
        $objSheet->getCell('BD1')->setValue('Length8');
        $objSheet->getCell('BE1')->setValue('Bounches8');
        $objSheet->getCell('BF1')->setValue('Weigth8');
        $objSheet->getCell('BG1')->setValue('Variedad9');
        $objSheet->getCell('BH1')->setValue('Color9');
        $objSheet->getCell('BI1')->setValue('Length9');
        $objSheet->getCell('BJ1')->setValue('Bounches9');
        $objSheet->getCell('BK1')->setValue('Weigth9');
        $objSheet->getCell('BL1')->setValue('Variedad10');
        $objSheet->getCell('BM1')->setValue('Color10');
        $objSheet->getCell('BN1')->setValue('Length10');
        $objSheet->getCell('BO1')->setValue('Bounches10');
        $objSheet->getCell('BP1')->setValue('Weigth10');
        $objSheet->getCell('BQ1')->setValue('Variedad11');
        $objSheet->getCell('BR1')->setValue('Color11');
        $objSheet->getCell('BS1')->setValue('Length11');
        $objSheet->getCell('BT1')->setValue('Bounches11');
        $objSheet->getCell('BU1')->setValue('Weigth11');
        $objSheet->getCell('BV1')->setValue('Variedad12');
        $objSheet->getCell('BW1')->setValue('Color12');
        $objSheet->getCell('BX1')->setValue('Length12');
        $objSheet->getCell('BY1')->setValue('Bounches12');
        $objSheet->getCell('BZ1')->setValue('Weigth12');

        if (sizeof($request->arr_facturas) > 0) {

            $w = 1;
            $empresa = getPedido($request->arr_facturas[0]['id_pedido'])->empresa;
            $prefijo = explode("-",$empresa->codigo_etiqueta_empresa)[0];
            $nombre_empresa = str_split(isset(explode("-",$empresa->codigo_etiqueta_empresa)[1]) ? explode("-",$empresa->codigo_etiqueta_empresa)[1] : explode("-",$empresa->codigo_etiqueta_empresa)[0]);
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

            $arr_pedidos = [];
            foreach ($request->arr_facturas as $factura){
                $arr_pedidos[$factura['id_pedido']][] = [
                    'caja'=>$factura['caja'],
                    'doble'=>$factura['doble']
                ];
            }
            foreach ($arr_pedidos as $id_pedido => $arr_ped) {
                $z=1;
                $pedido = getPedido($id_pedido);
                if($pedido->tipo_especificacion == "T")
                    $total_cajas = $pedido->cant_rows_etiqueta($arr_ped);
                foreach($arr_ped as $ped){

                    $ped['doble'] == "true"  ? $doble = 2  : $doble = 1;
                    $pais_destino = getPais($pedido->cliente->detalle()->codigo_pais)->nombre;
                    $dae = $pedido->envios[0]->dae;
                    $factura_tercero = getFacturaClienteTercero($pedido->envios[0]->id_envio);
                    if($factura_tercero!= "" && isset($factura_tercero->codigo_pais)){
                        $pais_destino = getPais($factura_tercero->codigo_pais)->nombre;
                        $dae = $factura_tercero->dae;
                    }
                    $ruc = "RUC: ". $pedido->empresa->ruc;

                    $arr_posiciones = $this->posiciones_excel();
                    for($y=1;$y<=$doble;$y++) {
                        if($pedido->tipo_especificacion == "N"){
                            foreach ($pedido->detalles as $det_ped) {
                                $datos_exportacion ='';
                                if(getDatosExportacionByDetPed($det_ped->id_detalle_pedido)->count() > 0){
                                    foreach (getDatosExportacionByDetPed($det_ped->id_detalle_pedido) as $dE)
                                        $datos_exportacion .= $dE->valor."-";
                                }
                                foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp) {
                                    if (explode("|", $esp_emp->empaque->nombre)[1] === $ped['caja']) {
                                        $x = 1;
                                        for ($z = 1; $z <= $det_ped->cantidad; $z++) {
                                            $posicion = 14;
                                            //--------------------------- LLENAR LA TABLA ---------------------------------------------
                                            $objSheet->getCell('A' . ($w + 1))->setValue("AWB. " . $pedido->envios[0]->guia_madre);
                                            $objSheet->getCell('B' . ($w + 1))->setValue("HAWB. " . $pedido->envios[0]->guia_hija);
                                            $objSheet->getCell('C' . ($w + 1))->setValue($x);
                                            $objSheet->getCell('D' . ($w + 1))->setValue($det_ped->cantidad);
                                            $objSheet->getCell('E' . ($w + 1))->setValue($pedido->envios[0]->id_consignatario!= "" ? $pedido->envios[0]->consignatario->nombre : $pedido->cliente->detalle()->nombre);
                                            $objSheet->getCell('F' . ($w + 1))->setValue($pedido->envios[0]->id_consignatario!= "" ? $pedido->cliente->detalle()->nombre : "");
                                            $objSheet->getCell('G' . ($w + 1))->setValue($datos_exportacion != '' ? substr($datos_exportacion,0,-1): "");
                                            $objSheet->getCell('H' . ($w + 1))->setValue($esp_emp->ramos_x_caja($det_ped->detalle_pedido));
                                            $objSheet->getCell('I' . ($w + 1))->setValue($prefijo."-".$codigo_finca);
                                            $objSheet->getCell('J' . ($w + 1))->setValue(getConfiguracionEmpresa($pedido->id_configuracion_empresa)->permiso_agrocalidad);
                                            $objSheet->getCell('K' . ($w + 1))->setValue('País destino. '.$pais_destino);
                                            $objSheet->getCell('L' . ($w + 1))->setValue($dae);
                                            $objSheet->getCell('M' . ($w + 1))->setValue($ruc);
                                            foreach ($esp_emp->detalles as $det_esp_emp) {
                                                $objSheet->getCell($arr_posiciones[$posicion] . ($w + 1))->setValue($det_esp_emp->variedad->nombre);
                                                $objSheet->getCell($arr_posiciones[$posicion + 1] . ($w + 1))->setValue("White");
                                                $objSheet->getCell($arr_posiciones[$posicion + 2] . ($w + 1))->setValue($det_esp_emp->longitud_ramo . " " . $det_esp_emp->unidad_medida->siglas);
                                                $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque);
                                                $objSheet->getCell($arr_posiciones[$posicion + 3] . ($w + 1))->setValue(isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad);
                                                $objSheet->getCell($arr_posiciones[$posicion + 4] . ($w + 1))->setValue($det_esp_emp->clasificacion_ramo->nombre . " " . $det_esp_emp->clasificacion_ramo->unidad_medida->siglas . ".");
                                                $posicion += 5;
                                            }
                                            $w++;
                                            $x++;
                                        }
                                    }
                                }
                            }
                        }else if($pedido->tipo_especificacion == "T"){
                            foreach ($pedido->detalles as $det_ped) {
                                $datos_exportacion = '';
                                if (getDatosExportacionByDetPed($det_ped->id_detalle_pedido)->count() > 0)
                                    foreach (getDatosExportacionByDetPed($det_ped->id_detalle_pedido) as $dE)
                                        $datos_exportacion .= $dE->valor . "-";

                                    foreach ($det_ped->marcaciones as $mc) {
                                        if (explode("|", $mc->especificacion_empaque->empaque->nombre)[1] === $ped['caja']) {
                                            foreach ($mc->distribuciones as $d => $dist) {
                                                $posicion = 14;
                                                $distribucion_coloracion = json_decode($dist->dist_col);

                                                $objSheet->getCell('A' . ($w + 1))->setValue("AWB. " . $pedido->envios[0]->guia_madre);
                                                $objSheet->getCell('B' . ($w + 1))->setValue("HAWB. " . $pedido->envios[0]->guia_hija);
                                                $objSheet->getCell('C' . ($w + 1))->setValue($z);
                                                $objSheet->getCell('D' . ($w + 1))->setValue($total_cajas);
                                                $objSheet->getCell('E' . ($w + 1))->setValue($pedido->envios[0]->id_consignatario!= "" ? $pedido->envios[0]->consignatario->nombre : $pedido->cliente->detalle()->nombre);
                                                $objSheet->getCell('F' . ($w + 1))->setValue($pedido->envios[0]->id_consignatario!= "" ? $pedido->cliente->detalle()->nombre : "");
                                                $objSheet->getCell('G' . ($w + 1))->setValue($mc->nombre);
                                                $objSheet->getCell('H' . ($w + 1))->setValue($mc->marcaciones_coloraciones[0]->detalle_especificacionempaque->cantidad);
                                                $objSheet->getCell('I' . ($w + 1))->setValue("DS-" . $codigo_finca);
                                                $objSheet->getCell('J' . ($w + 1))->setValue(getConfiguracionEmpresa($pedido->id_configuracion_empresa)->permiso_agrocalidad);
                                                $objSheet->getCell('K' . ($w + 1))->setValue('País destino. '.$pais_destino);
                                                $objSheet->getCell('L' . ($w + 1))->setValue($dae);
                                                $objSheet->getCell('M' . ($w + 1))->setValue($ruc);
                                                foreach($distribucion_coloracion as $dist_col){
                                                    foreach($dist_col as $dc){
                                                        if($dc->cantidad>0){
                                                            $objSheet->getCell($arr_posiciones[$posicion] . ($w + 1))->setValue($dc->planta . " - " . $dc->variedad);
                                                            $objSheet->getCell($arr_posiciones[$posicion + 1] . ($w + 1))->setValue($dc->color);
                                                            $objSheet->getCell($arr_posiciones[$posicion + 2] . ($w + 1))->setValue($dc->longitud_ramo . " " . $dc->det_esp_u_m);
                                                            $objSheet->getCell($arr_posiciones[$posicion + 3] . ($w + 1))->setValue($dc->cantidad);
                                                            $objSheet->getCell($arr_posiciones[$posicion + 4] . ($w + 1))->setValue($dc->ramo . " " . $dc->ramo_u_m . ".");
                                                            $posicion += 5;
                                                        }
                                                    }
                                                }
                                                $w++;
                                                $z++;
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
            foreach($this->posiciones_excel() as $posicion) $objSheet->getColumnDimension($posicion)->setAutoSize(true);

        } else {
            $objSheet->getCell('A1')->setValue('No se han seleccionado facturas');
        }
    }

    public function posiciones_excel(){
        return [
            14=>'N',15=>'O',16=>'P',17=>'Q',18=>'R',19=>'S',20=>'T',21=>'U',22=>'V',23=>'W',24=>'X',25=>'Y',26=>'Z',
            27=>'AA',28=>'AB',29=>'AC',30=>'AD',31=>'AE',32=>'AF',33=>'AG',34=>'AH',35=>'AI',36=>'AJ',37=>'AK',38=>'AL',39=>'AM',
            40=>'AN',41=>'AO',42=>'AP',43=>'AQ',44=>'AR',45=>'AS',46=>'AT',47=>'AU',48=>'AV',49=>'AW',50=>'AX',51=>'AY',52=>'AZ',
            53=>'BA',54=>'BB',55=>'BC',56=>'BD',57=>'BE',58=>'BF',59=>'BG',60=>'BH',61=>'BI',62=>'BJ',63=>'BK',64=>'BL',65=>'BM',
            66=>'BN',67=>'BO',68=>'BP',69=>'BQ',70=>'BR',71=>'BS',72=>'BS',73=>'BT',74=>'BU',75=>'BU',76=>'BV',77=>'BW',78=>'BX',
            79=>'BY',80=>'BZ',81=>'CA'

        ];
    }
}
