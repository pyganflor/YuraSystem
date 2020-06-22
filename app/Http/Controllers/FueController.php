<?php

namespace yura\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet;
use Validator;
use yura\Modelos\Cliente;
use yura\Modelos\Envio;
use yura\Modelos\FacturaClienteTercero;
use yura\Modelos\Pais;
use yura\Modelos\Submenu;
use yura\Modelos\Comprobante;

class FueController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.crm.fue.inicio',[
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'text' => ['titulo'=>'Actualización de Datos de Exportación','subtitulo'=>'módulo CRM']
        ]);
    }

    public function buscar(Request $request){
        return view('adminlte.crm.fue.partials.listado',[
            'facturas' => Comprobante::where([
                ['tipo_comprobante',01],
                ['fecha_emision' , ($request->get('busqueda') != null && !empty($request->get('busqueda')) ? $request->get('busqueda') : now()->toDateString())],
                ['comprobante.habilitado',true]
            ])->whereIn('estado',[1,5])->get()
        ]);
    }

    public function actualizar_fue(Request $request){
        /*$valida = Validator::make($request->all(), [
            'arr_datos.*.codigo_dae' => 'required',
            'arr_datos.*.guia_madre' => 'required',
            'arr_datos.*.guia_hija' => 'required',
            'arr_datos.*.manifiesto' => 'required',
            'arr_datos.*.dae' => 'required',
            'arr_datos.*.peso' => 'required',
            'arr_datos.*.id_comprobante' => 'required'
        ],[
        'arr_datos.*.codigo_dae' => 'Debe ingresar el código DAE',
        arr_datos.*.guia_madre' => 'Debe ingresar la Guía madre',
        'arr_datos.*.guia_hija' => 'Debe ingresar la Guía hija',
        'arr_datos.*.manifiesto' => 'Debe ingresar el manifiesto',
        'arr_datos.*.dae' => 'Debe ingresar la DAE completa',
        'arr_datos.*.peso' => 'Debe ingresar el peso',
        ]);*/

        //if (!$valida->fails()) {
            $success = false;
            $msg = '<div class="alert alert-warning text-center">' .
                '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                . '</div>';
            //dd($request->all());
            foreach($request->arr_datos as $item){
                $factura_tercero = getFacturaClienteTercero(getComprobante($item['id_comprobante'])->id_envio);
                $comprobante = getComprobante($item['id_comprobante']);
                $objEnvio = Envio::find($comprobante->id_envio);

                if($factura_tercero != null){
                    $objFacturatercero = FacturaClienteTercero::where('id_envio',$comprobante->id_envio);
                    $objFacturatercero->update([ 'codigo_dae'=> $item['codigo_dae'], 'dae' => $item['dae'] ]);
                }else{
                    $objEnvio->codigo_dae =  $item['codigo_dae'];
                    $objEnvio->dae = $item['dae'];
                }

                $objEnvio->guia_madre = $item['guia_madre'];
                $objEnvio->guia_hija = $item['guia_hija'];

                $objComprobante = Comprobante::find($item['id_comprobante']);
                $objComprobante->peso = $item['peso'];
                $objComprobante->manifiesto = $item['manifiesto'];

                if($objEnvio->save() && $objComprobante->save()){
                    $success = true;
                    $msg = '<div class="alert alert-success text-center">' .
                        '<p> Se han actualizado los datos exitosamente</p>'
                        . '</div>';
                }
            }
        /*}else {
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
        }*/
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function reporte_fue(){
        return view('adminlte.crm.fue.partials.listado_inicio',[
            'clientes' => Cliente::all()
        ]);
    }

    public function reporte_fue_filtrado(Request $request,$excel=false){

        $data = Comprobante::join('envio as e', 'comprobante.id_envio','e.id_envio')
                ->join('pedido as p','e.id_pedido','p.id_pedido')
                ->join('cliente as c','p.id_cliente','c.id_cliente')
                ->join('detalle_cliente as dc','c.id_cliente','dc.id_cliente')
                ->where([
                    ['dc.estado',1],
                    ['comprobante.habilitado',1],
                    ['comprobante.tipo_comprobante',01]
                ])->whereIn('comprobante.estado',[1,5])
                ->orderBy('p.fecha_pedido','asc');

        if($request->get('id_cliente') != null)
            $data->where('c.id_cliente',$request->get('id_cliente'));
        if($request->get('codigo_dae') != null)
            $data->where('e.codigo_dae',trim($request->get('codigo_dae')));
        if($request->get('guia_madre') != null)
            $data->where('e.guia_madre',trim($request->get('guia_madre')));
        if($request->get('dae') != null)
            $data->where('e.dae',trim($request->get('dae')));
        if($request->get('desde') != null && $request->get('hasta') != null)
            $data->whereBetween('comprobante.fecha_emision',[$request->get('desde'),$request->get('hasta')]);

        if($excel){
            return $data->select('comprobante.*','e.*','p.*','dc.nombre','c.id_cliente')->get();
        }else{
            return view('adminlte.crm.fue.partials.listado_filtrado',[
                'listado' =>$data->select('comprobante.*','e.*','p.*','dc.nombre','c.id_cliente')->get()
            ]);
        }

    }

    public function exportar_reporte_dae(Request $request)
    {
        //---------------------- EXCEL --------------------------------------
        $objPHPExcel = new PHPExcel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");

        $objPHPExcel->removeSheetByIndex(0); //Eliminar la hoja inicial por defecto

        $this->excel_reporte_facturas($objPHPExcel, $request);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="Reporte_Facturas_DAE.xlsx"');
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

    public function excel_reporte_facturas($objPHPExcel, $request)
    {
        if ($this->reporte_fue_filtrado($request,true)->count() > 0) {
            $datos = $this->reporte_fue_filtrado($request,true);
            $objSheet = new PHPExcel_Worksheet($objPHPExcel, 'Reporte Facturas DAE');
            $objPHPExcel->addSheet($objSheet, 0);

            $objSheet->mergeCells('A1:T1');
            $objSheet->getStyle('A1:T1')->getFont()->setBold(true)->setSize(12);
            $objSheet->getStyle('A1:T1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle('A1:T1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCFFCC');

            $objSheet->getCell('A1')->setValue('Reporte de facturas por Dae');

            $objSheet->getCell('A2')->setValue('N#');
            $objSheet->getCell('B2')->setValue('CLAVE SRI');
            $objSheet->getCell('C2')->setValue('DAE');
            $objSheet->getCell('D2')->setValue('CÓDIGO DAE');
            $objSheet->getCell('E2')->setValue('EXPORTADOR');
            $objSheet->getCell('F2')->setValue('GUÍA MADRE');
            $objSheet->getCell('G2')->setValue('GUÍA HIJA');
            $objSheet->getCell('H2')->setValue('FECHA GUÍA');
            $objSheet->getCell('I2')->setValue('FACTURA');
            $objSheet->getCell('J2')->setValue('FECHA');
            $objSheet->getCell('K2')->setValue('MANIFIESTO');
            $objSheet->getCell('L2')->setValue('AEROLÍNEA');
            $objSheet->getCell('M2')->setValue('AGENCIA CARGA');
            $objSheet->getCell('N2')->setValue('CLIENTE');
            $objSheet->getCell('O2')->setValue('RAMOS VAR');
            $objSheet->getCell('P2')->setValue('CAJAS FULL');
            $objSheet->getCell('Q2')->setValue('TALLOS');
            $objSheet->getCell('R2')->setValue('PIEZAS');
            $objSheet->getCell('S2')->setValue('NETO DOLARES');
            $objSheet->getCell('T2')->setValue('PESO GUÍA Kg.');

            $objSheet->getStyle('A2:T2')->getFont()->setBold(true)->setSize(12);

            $objSheet->getStyle('A2:T2')
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                ->getColor()
                ->setRGB(PHPExcel_Style_Color::COLOR_BLACK);

            $objSheet->getStyle('A2:T2')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CCFFCC');

            //--------------------------- LLENAR LA TABLA ---------------------------------------------
            $tp=0;
            $tr=0;
            $ttll=0;
            $tfull_eqv=0;
            $tm=0;
            foreach ($datos as $x => $d) {
                $total_piezas = 0;
                $total_ramos = 0;
                $total_tallos = 0;
                $full_equivalente_real= 0;

                foreach (getPedido($d->id_pedido)->detalles as $det_ped)
                    foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp)
                        foreach ($esp_emp->detalles as $n => $det_esp_emp){
                            $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque);
                            $total_ramos += number_format(($det_ped->cantidad*$esp_emp->cantidad*(isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad)),2,".","");
                            $full_equivalente_real += explode("|",$esp_emp->empaque->nombre)[1]*$det_ped->cantidad;
                            $total_tallos += number_format(($det_ped->cantidad*$esp_emp->cantidad*(isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad)*$det_esp_emp->tallos_x_ramos),2,".","");
                        }
                foreach (getPedido($d->id_pedido)->detalles as $det_ped)
                    $total_piezas += $det_ped->cantidad;

                $tp+=$total_piezas;
                $tr+= $total_ramos;
                $ttll+= $total_tallos;
                $tfull_eqv+= $full_equivalente_real;
                $tm+=$d->monto_total;
                $objSheet->getCell('A' . (($x+1) + 3))->setValue(($x+1));
                $objSheet->getCell('B' . (($x+1) + 3))->setValue($d->clave_acceso);
                $objSheet->getCell('C' . (($x+1) + 3))->setValue($d->dae);
                $objSheet->getCell('D' . (($x+1) + 3))->setValue($d->codigo_dae);
                $objSheet->getCell('E' . (($x+1) + 3))->setValue(getConfiguracionEmpresa($d->id_configuracion_empresa)->razon_social);
                $objSheet->getCell('F' . (($x+1) + 3))->setValue($d->guia_madre);
                $objSheet->getCell('G' . (($x+1) + 3))->setValue($d->guia_hija);
                $objSheet->getCell('H' . (($x+1) + 3))->setValue(\Carbon\Carbon::parse($d->fecha_pedido)->addDay(1)->format('d/m/Y'));
                $objSheet->getCell('I' . (($x+1) + 3))->setValue($d->numero_comprobante);
                $objSheet->getCell('J' . (($x+1) + 3))->setValue(\Carbon\Carbon::parse($d->fecha_pedido)->format('d/m/Y'));
                $objSheet->getCell('K' . (($x+1) + 3))->setValue($d->manifiesto);
                $objSheet->getCell('L' . (($x+1) + 3))->setValue(
                    isset(getPedido($d->id_pedido)->envios[0]->detalles[0]->id_aerolinea)
                        ? getAerolinea(getPedido($d->id_pedido)->envios[0]->detalles[0]->id_aerolinea)->nombre
                        : ""
                );
                $objSheet->getCell('M' . (($x+1) + 3))->setValue(getAgenciaCarga(getPedido($d->id_pedido)->detalles[0]->id_agencia_carga)->nombre);
                $objSheet->getCell('N' . (($x+1) + 3))->setValue($d->nombre);
                $objSheet->getCell('O' . (($x+1) + 3))->setValue($total_ramos);
                $objSheet->getCell('P' . (($x+1) + 3))->setValue($full_equivalente_real);
                $objSheet->getCell('Q' . (($x+1) + 3))->setValue($total_tallos);
                $objSheet->getCell('R' . (($x+1) + 3))->setValue($total_piezas);
                $objSheet->getCell('S' . (($x+1) + 3))->setValue($d->monto_total);
                $objSheet->getCell('T' . (($x+1) + 3))->setValue($d->peso);
            }
            $objSheet->getCell('N' . ($x+6))->setValue("Totales");
            $objSheet->getCell('O' . ($x+6))->setValue($tr);
            $objSheet->getCell('P' . ($x+6))->setValue($tfull_eqv);
            $objSheet->getCell('Q' . ($x+6))->setValue($ttll);
            $objSheet->getCell('R' . ($x+6))->setValue($tp);
            $objSheet->getCell('S' . ($x+6))->setValue("$".$tm);

            $objSheet->getStyle('N'.($x+6).':S'.($x+6))
            ->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB('CCFFCC');


            $objSheet->getStyle('N'.($x+6).':S'.($x+6))->getFont()->setBold(true)->setSize(12);

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
        } else {
            return '<div>No se han encontrado coincidencias</div>';
        }
    }
}
