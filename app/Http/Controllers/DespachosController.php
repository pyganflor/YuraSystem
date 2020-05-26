<?php

namespace yura\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Color;
use PHPExcel_Style_Fill;
use PHPExcel_Worksheet;
use PHPExcel_Style_Border;
use yura\Modelos\Camion;
use yura\Modelos\Conductor;
use yura\Modelos\Despacho;
use yura\Modelos\DetalleDespacho;
use yura\Modelos\Pedido;
use yura\Modelos\Submenu;
use yura\Modelos\Transportista;
use yura\Modelos\Variedad;
use Validator;
use Barryvdh\DomPDF\Facade as PDF;


class DespachosController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.postcocecha.despachos.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'annos' => DB::table('semana as s')
                ->select('s.anno')->distinct()
                ->where('s.estado', '=', 1)->orderBy('s.anno')->get(),
            'variedades' => Variedad::All()->where('estado', '=', 1),
            'clientes' => \DB::table('cliente as c')
                ->join('detalle_cliente as dc', 'c.id_cliente', '=', 'dc.id_cliente')
                ->orderBy('nombre','asc')
                ->where('dc.estado', 1)->get(),
            'unitarias' => getUnitarias(),
            'empresas' => getConfiguracionEmpresa(null,true)
        ]);
    }

    public function listar_resumen_pedidos(Request $request)
    {
        $listado = [];
        if ($request->fecha != '') {
            $listado = DB::table('pedido as p')
                ->join('cliente as c', 'c.id_cliente', '=', 'p.id_cliente')
                ->join('detalle_cliente as dc', 'dc.id_cliente', '=', 'p.id_cliente')
                ->select('p.*')->distinct()
                ->where('dc.estado', '=', 1)
                ->where('c.estado', '=', 1)
                ->where('p.estado', '=', 1)
                //->where('p.empaquetado', '=', 0)
                ->where('p.fecha_pedido', '=', $request->fecha)
                ->orderBy('dc.nombre', 'asc')
                ->where(function($query) use($request){
                    if(isset($request->id_cliente))
                        $query->where('p.id_cliente',$request->id_cliente);

                    if($request->id_configuracion_empresa != "")
                        $query->where('p.id_configuracion_empresa','=',$request->id_configuracion_empresa);
                });

            $listado = $listado->get();
            //dd($listado);
            $ids_pedidos = [];
            foreach ($listado as $item) {
                if(!getFacturaAnulada($item->id_pedido))
                    array_push($ids_pedidos, $item->id_pedido);
            }

            $ramos_x_variedad = DB::table('detalle_especificacionempaque as dee')
                ->join('especificacion_empaque as ee', 'dee.id_especificacion_empaque', '=', 'ee.id_especificacion_empaque')
                ->join('cliente_pedido_especificacion as cpe', 'ee.id_especificacion', '=', 'cpe.id_especificacion')
                ->join('detalle_pedido as dp', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
                ->select('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos', 'dee.longitud_ramo', 'dee.id_unidad_medida',
                    DB::raw('sum(dee.cantidad * ee.cantidad * dp.cantidad) as cantidad'))
                ->whereIn('dp.id_pedido', $ids_pedidos)
                ->groupBy('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos', 'dee.longitud_ramo', 'dee.id_unidad_medida')
                ->orderBy('dp.id_pedido', 'desc')
                ->get();

            $variedades = DB::table('detalle_especificacionempaque as dee')
                ->join('especificacion_empaque as ee', 'dee.id_especificacion_empaque', '=', 'ee.id_especificacion_empaque')
                ->join('cliente_pedido_especificacion as cpe', 'ee.id_especificacion', '=', 'cpe.id_especificacion')
                ->join('detalle_pedido as dp', 'cpe.id_cliente_pedido_especificacion', '=', 'dp.id_cliente_especificacion')
                ->select('dee.id_variedad')->distinct()
                ->whereIn('dp.id_pedido', $ids_pedidos)
                ->get();
        }
        $datos = [
            'listado' => $listado,
            'fecha' => $request->fecha,
            'ramos_x_variedad' => $ramos_x_variedad,
            'variedades' => $variedades,
            'opciones' => $request->opciones,
            'id_configuracion_empresa' => $request->id_configuracion_empresa

        ];
        return view('adminlte.gestion.postcocecha.despachos.partials.listado', $datos);
    }

    public function crear_despacho(Request $request){
        $arr_data_pedido = [];
        foreach ($request->pedidos as $id_pedido) {
            $arr_data_pedido[] = Pedido::where('id_pedido',$id_pedido)->first();
        }
        if(!empty($request->pedidos)){
            return view('adminlte.gestion.postcocecha.despachos.form.despacho_listado',[
                'pedidos' => $arr_data_pedido,
                'datos_responsables' => Despacho::all()->last(),
            ]);
        }else{

        }
    }

    public function list_camiones_conductores(Request $request){
        return response()->json([
            'camiones' => Camion::where([
                ['id_transportista',$request->id_transportista],
                ['estado',1]
            ])->get(),
            'conductores' => Conductor::where([
                ['id_transportista',$request->id_transportista],
                ['estado',1]
            ])->get()
        ]);
    }

    public function list_placa_camion(Request $request){
       return Camion::where([
                ['id_camion',$request->id_camion],
                ['estado',1]
            ])->select('placa')->first();
    }

    public function store_despacho(Request $request){

        $valida = Validator::make($request->all(), [
            'data_despacho.*.fecha_despacho' => 'required',
            //'data_despacho.*.firma_id_transportista' => 'required',
            'data_despacho.*.id_asist_comercial' => 'required',
            'data_despacho.*.id_camion' => 'required',
            'data_despacho.*.id_conductor' => 'required',
           //'data_despacho.*.id_cuarto_frio' => 'required',
            //'data_despacho.*.id_guardia_turno' => 'required',
            //'data_despacho.*.id_oficina_despacho' => 'required',
            'data_despacho.*.id_transportista' => 'required',
            'data_despacho.*.n_placa' => 'required',
            'data_despacho.*.n_viaje' => 'required',
            'data_despacho.*.nombre_asist_comercial' => 'required',
            //'data_despacho.*.nombre_cuarto_frio' => 'required',
            //'data_despacho.*.nombre_guardia_turno' => 'required',
            'data_despacho.*.nombre_oficina_despacho' => 'required',
            'data_despacho.*.nombre_transportista' => 'required',
            //'data_despacho.*.arr_sellos' => 'required|Array',
            'data_despacho.*.semana' => 'required',
            'data_despacho.*.correo_oficina_despacho'  => 'required',
        ],[
            'data_despacho.*.fecha_despacho.required' => 'Debe colocar la fecha de despacho para el camión',
            'data_despacho.*.id_camion.required' => 'Debe seleccionar el camión',
            'data_despacho.*.n_placa.required' =>  'Debe escribir la placa del camión',
            'data_despacho.*.semana.required' => 'Debe escribir la semana',
            'data_despacho.*.correo_oficina_despacho.required' => 'Debe escribir el correo de la persona de la oficina de despacho',
            'data_despacho.*.nombre_transportista.required' => 'Debe escribir el nombre del transportista',
            'data_despacho.*.nombre_oficina_despacho.required' => 'Debe escribir el nombre de la persona de la oficina de despacho',
            //'data_despacho.*.nombre_guardia_turno.required' => 'Debe escribir el nombre del guardia de turno',
            //'data_despacho.*.id_guardia_turno.required' => 'Debe escribir la identificación del guardia de turno',
            //'data_despacho.*.nombre_cuarto_frio.required' => 'Debe escribir el nombre de la persona del cuarto frio',
            //'data_despacho.*.id_cuarto_frio.required' => 'Debe escribir la identificación de la persona del cuarto frio',
            'data_despacho.*.id_conductor.required' => 'Debe seleccionar el conductor del camión',
            'data_despacho.*.id_transportista.required' => 'Debe seleccionar una agencia de transporte',
            'data_despacho.*.id_asist_comercial.required' => 'Debe escribir la identificación del asistente comercial'
        ]);

        if (!$valida->fails()) {
            $msg = '';
            //dd($request->all());
            foreach($request->data_despacho as $despacho){
                $s='';
                if(isset($despacho['arr_sellos'] ))
                    foreach ($despacho['arr_sellos'] as $sellos) $s .= $sellos."|";
                $distribucion = substr($despacho['distribucion'], 0, -1);
                $objDespacho = new Despacho;
                $objDespacho->id_transportista = $despacho['id_transportista'];
                $objDespacho->id_camion = $despacho['id_camion'];
                $objDespacho->id_conductor = $despacho['id_conductor'];
                $objDespacho->fecha_despacho = $despacho['fecha_despacho'];
                $objDespacho->sello_salida = $despacho['sello_salida'];
                $objDespacho->semana = $despacho['semana'];
                $objDespacho->rango_temp = $despacho['rango_temp'];
                $objDespacho->n_viaje = $despacho['n_viaje'];
                $objDespacho->hora_salida = $despacho['horas_salida'];
                $objDespacho->temp = $despacho['temperatura'];
                $objDespacho->kilometraje = $despacho['kilometraje'];
                $objDespacho->sellos = substr($s, 0, -1);
                $objDespacho->sello_adicional =$despacho['sello_adicional'];
                $objDespacho->horario = $despacho['horario'];
                $objDespacho->resp_ofi_despacho = $despacho['nombre_oficina_despacho'];
                $objDespacho->id_resp_ofi_despacho = $despacho['id_oficina_despacho'];
                $objDespacho->aux_cuarto_fri = $despacho['nombre_cuarto_frio'];
                $objDespacho->id_aux_cuarto_fri = $despacho['id_cuarto_frio'];
                $objDespacho->guardia_turno = $despacho['nombre_guardia_turno'];
                $objDespacho->id_guardia_turno = $despacho['id_guardia_turno'];
                $objDespacho->asist_comercial_ext = $despacho['nombre_asist_comercial'];
                $objDespacho->id_asist_comrecial_ext = $despacho['id_asist_comercial'];
                $objDespacho->resp_transporte = $despacho['nombre_transportista'];
                $objDespacho->mail_resp_ofi_despacho = $despacho['correo_oficina_despacho'];
                $distribucion = explode(";",$distribucion);
                $idPedido =explode("|",$distribucion[0])[0];
                $empresa = getPedido($idPedido)->empresa;
                $objDespacho->id_configuracion_empresa = $empresa->id_configuracion_empresa;
                $objDespacho->n_despacho = getSecuenciaDespacho($empresa);

                if ($objDespacho->save()) {
                    $modelDespacho = Despacho::all()->last();
                    bitacora('despacho', $modelDespacho->id_despacho, 'I', 'Inserción satisfactoria de un nuevo despacho');

                    foreach ($distribucion as $d) {
                        $objDetalleDespacho = new DetalleDespacho;
                        $objDetalleDespacho->id_despacho = $modelDespacho->id_despacho;
                        $objDetalleDespacho->id_pedido = explode("|",$d)[0];
                        $objDetalleDespacho->cantidad = explode("|",$d)[1];
                        if($objDetalleDespacho->save()){
                            $modelDetalleDespacho = DetalleDespacho::all()->last();
                            bitacora('detalle_despacho', $modelDetalleDespacho->id_detalle_despacho, 'I', 'Inserción satisfactoria de un nuevo detalle de despacho');
                            $success = true;

                        }else{
                            DetalleDespacho::where('id_despacho',$modelDespacho->id_despacho)->delete();
                            Despacho::destroy($modelDespacho->id_despacho);
                            $msg = '<div class="alert alert-warning text-center">' .
                                '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                                . '</div>';
                            $success = false;
                            return [
                                'mensaje' => $msg,
                                'success' => $success
                            ];
                        }
                    }
                    $msg .= '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado el despacho ' . str_pad($objDespacho->n_despacho, 9, "0", STR_PAD_LEFT) . '  exitosamente, 
                            <a target="_blank" href="'.url('despachos/descargar_despacho/'.$objDespacho->n_despacho.'').'">  clic aquí para ver y descargar</a></p>'
                        . '</div>';
                    $data = [
                        'empresa' => $empresa,
                        'despacho' => Despacho::where('n_despacho',(getSecuenciaDespacho($empresa)-1))
                            ->join('detalle_despacho as dd','despacho.id_despacho','dd.id_despacho')->get()
                    ];
                    PDF::loadView('adminlte.gestion.postcocecha.despachos.partials.pdf_despacho', compact('data'))->setPaper('a4', 'landscape')
                        ->save(env('PATH_PDF_DESPACHOS') . str_pad((getSecuenciaDespacho($empresa)-1), 9, "0", STR_PAD_LEFT) . ".pdf");
                }else{
                    $success = false;
                    $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
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
            'success' => $success,
        ];
    }

    public function descargar_despacho($n_despacho){
        $despacho = Despacho::where('n_despacho',$n_despacho)
            ->join('detalle_despacho as dd','despacho.id_despacho','dd.id_despacho')->get();
        
        $data = [
            'empresa' => $despacho[0]->detalles[0]->pedido->empresa,
            'despacho' => $despacho
        ];
        return PDF::loadView('adminlte.gestion.postcocecha.despachos.partials.pdf_despacho', compact('data'))
            ->setPaper('a4', 'landscape')->stream();
    }

    public function ver_despachos(Request $request){
        return view('adminlte.gestion.postcocecha.despachos.partials.despachos',[
            'listado' => Despacho::where('estado',1)->paginate(20)
        ]);
    }

    public function update_estado_despachos(Request $request){
        $despacho = Despacho::find($request->id_despacho);
        if($despacho->update(['estado'=>$request->estado == 1 ? 0 :1])){
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha actualizado el estado del despacho exitosamente</p>'
                . '</div>';
        }else{
            $success = false;
            $msg = '<div class="alert danger text-center">' .
                '<p> Hubo un error al intentar actualizar el estado del despacho exitosamente</p>'
                . '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success,
        ];
    }

    public function distribuir_despacho(Request $request){
        return view('adminlte.gestion.postcocecha.despachos.partials.distribucion',[
            'transportistas' => Transportista::where('estado',1)->get(),
            'cant_form' => $request->cant_form,
            'resp_transporte' => Despacho::select('resp_transporte')->get()->last(),
        ]);
    }

    public function add_pedido_piezas(Request $request){
        return view('adminlte.gestion.postcocecha.despachos.partials.add_pedido_piezas',[
            'sec'=> $request->secuencial,
            'arr_pedidos' => $request->arr_pedidos,
            'cant_form'=> $request->cant_form
        ]);
    }

    public function exportar_pedidos_despacho(Request $request){
        //---------------------- EXCEL --------------------------------------
        $objPHPExcel = new PHPExcel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");

        $this->excel_pedidos_despacho($objPHPExcel, $request);

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

    public function excel_pedidos_despacho($objPHPExcel, $request){
        $pedidos = Pedido::where([
            ['fecha_pedido',$request->fecha_pedido],
            ['pedido.estado',1],
            ['dc.estado',1]])
            ->join('cliente as c','pedido.id_cliente','c.id_cliente')->join('detalle_cliente as dc','c.id_cliente','dc.id_cliente')
            ->orderBy('dc.nombre','asc')->select('id_pedido');

        if(isset($request->id_configuracion_empresa))
            $pedidos->where('id_configuracion_empresa',$request->id_configuracion_empresa);

        $pedidos = $pedidos->get();
        //HOJA Tinturados
        $objSheet = new PHPExcel_Worksheet($objPHPExcel,'Tinturados');
        $objPHPExcel->addSheet($objSheet, 0);
        $objPHPExcel->setActiveSheetIndex(0);
        $objSheet->getCell('A1' )->setValue('Cliente');
        $objSheet->getCell('B1' )->setValue('Marcaciones');
        $objSheet->getCell('C1')->setValue('Flor');
        $objSheet->getCell('D1')->setValue('Empaque');
        $objSheet->getCell('E1' )->setValue('Presentación');
        $objSheet->getCell('F1')->setValue('Ramos');
        $objSheet->getCell('G1')->setValue('Color');
        $objSheet->getCell('H1')->setValue('Cuarto frio');
        $objSheet->getCell('I1')->setValue('Guía aérea');
        $objSheet->getCell('J1')->setValue('Fue');

        //HOJA No tinturados
        $objSheet1 = new PHPExcel_Worksheet($objPHPExcel,'No tinturados');
        $objPHPExcel->addSheet($objSheet1, 1);
        $objPHPExcel->setActiveSheetIndex(1);
        $objSheet1->getCell('A1' )->setValue('Cliente');
        $objSheet1->getCell('B1' )->setValue('Marcaciones');
        $objSheet1->getCell('C1')->setValue('Flor');
        $objSheet1->getCell('D1')->setValue('Empaque');
        $objSheet1->getCell('E1' )->setValue('Presentación');
        $objSheet1->getCell('F1')->setValue('piezas');
        $objSheet1->getCell('G1')->setValue('Cajas full');
        $objSheet1->getCell('H1')->setValue('Ramos');
        $objSheet1->getCell('I1')->setValue('Ramos por caja');
        $objSheet1->getCell('J1')->setValue('Cuarto frio');
        $objSheet1->getCell('K1')->setValue('Guía aérea');
        $objSheet1->getCell('L1')->setValue('Fue');
        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $style = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );
        $w = 1;
        $x = 1;
        $ids_pedidos_tinturados = [];
        $ids_pedidos_no_tinturados = [];
        $ramos_x_variedades_no_tinturados = [];
        $cajas_equivalentes_no_tinturados = [];
        $variedades_no_tinturados = [];
        $ramos_totales_no_tinturados = 0;
        $piezas_totales_no_tinturados = 0;
        $ramos_totales_estandar_no_tinturados = 0;
        $cajas_full_totales_no_tinturados = 0;
        $piezas_totales_tinturados = 0;
        $ramos_totales_tinturados = 0;
        $cajas_full_totales_tinturados = 0;
        $ramos_totales_estandar_tinturados = 0;
        $variedades_tinturados = [];
        $ramos_x_variedades_tinturados = [];
        $cajas_equivalentes_tinturados = [];

        foreach ($pedidos as $a => $pedido){
            $p = getPedido($pedido->id_pedido);
            if(!getFacturaAnulada($pedido->id_pedido)){
                if ($p->tipo_especificacion === "N") {
                        $ids_pedidos_no_tinturados[] = $p->id_pedido;
                    foreach ($p->detalles as $det => $det_ped) {
                        $datos_exportacion = '';
                        if (getDatosExportacionByDetPed($det_ped->id_detalle_pedido)->count() > 0)
                            foreach (getDatosExportacionByDetPed($det_ped->id_detalle_pedido) as $dE)
                                $datos_exportacion .= $dE->valor . "-";
                        if ($det == 0) $inicio_a = $x + 1;
                        $final_a = getCantidadDetallesEspecificacionByPedido($pedido->id_pedido) + $inicio_a - 1;
                        foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $sp => $esp_emp) {
                            $piezas_totales_no_tinturados += ($esp_emp->cantidad * $det_ped->cantidad);
                            foreach ($esp_emp->detalles as $det_sp => $det_esp_emp) {
                                $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque);
                                if ($sp == 0 && $det_sp == 0) {
                                    $inicio_b = $x + 1;
                                }
                                $final_b = getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->id_especificacion) + $inicio_b - 1;
                                if ($det_sp == 0) {
                                    $inicio_d = $x + 1;
                                    $cajas_full_totales_no_tinturados += ($esp_emp->cantidad * $det_ped->cantidad) * explode('|', $esp_emp->empaque->nombre)[1];
                                }
                                $final_d = count($esp_emp->detalles) + $inicio_d - 1;
                                $objSheet1->mergeCells('A' . $inicio_a . ':A' . $final_a);
                                $objSheet1->mergeCells('B' . $inicio_b . ':B' . $final_b);
                                $objSheet1->mergeCells('D' . $inicio_d . ':D' . $final_d);
                                $objSheet1->mergeCells('F' . $inicio_b . ':F' . $final_b);
                                $objSheet1->mergeCells('G' . $inicio_d . ':G' . $final_d);
                                $objSheet1->mergeCells('J' . $inicio_a . ':J' . $final_a);
                                $objSheet1->mergeCells('K' . $inicio_a . ':K' . $final_a);
                                $objSheet1->mergeCells('L' . $inicio_a . ':L' . $final_a);
                                $objSheet1->getCell('A' . ($x + 1))->setValue($p->cliente->detalle()->nombre);
                                $objSheet1->getCell('B' . ($x + 1))->setValue((!$datos_exportacion) ? "No posee" : substr($datos_exportacion, 0, -1));
                                $objSheet1->getCell('C' . ($x + 1))->setValue($det_esp_emp->variedad->siglas . " " . explode('|', $det_esp_emp->clasificacion_ramo->nombre)[0] . " " . $det_esp_emp->clasificacion_ramo->unidad_medida->siglas);
                                $objSheet1->getCell('D' . ($x + 1))->setValue(explode("|", $esp_emp->empaque->nombre)[0]);
                                $objSheet1->getCell('E' . ($x + 1))->setValue(explode('|', $det_esp_emp->empaque_p->nombre)[0]);
                                $objSheet1->getCell('F' . ($x + 1))->setValue($esp_emp->cantidad * $det_ped->cantidad);
                                $objSheet1->getCell('G' . ($x + 1))->setValue(($esp_emp->cantidad * $det_ped->cantidad) * explode('|', $esp_emp->empaque->nombre)[1]);

                                $ramos_totales_no_tinturados += (isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad) * $esp_emp->cantidad * $det_ped->cantidad;
                                $ramos_totales_estandar_no_tinturados += convertToEstandar((isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad) * $esp_emp->cantidad * $det_ped->cantidad, $det_esp_emp->clasificacion_ramo->nombre);
                                if (!in_array($det_esp_emp->id_variedad, $variedades_no_tinturados)) {
                                    $variedades_no_tinturados[] = $det_esp_emp->id_variedad;
                                }
                                $ramos_x_variedades_no_tinturados[] = [
                                    'id_variedad' => $det_esp_emp->id_variedad,
                                    'cantidad' => convertToEstandar((isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad) * $esp_emp->cantidad * $det_ped->cantidad, $det_esp_emp->clasificacion_ramo->nombre),
                                ];

                                $objSheet1->getStyle('A' . ($x + 1) . ':L' . ($x + 1))->applyFromArray($BStyle);
                                $objSheet1->getCell('H' . ($x + 1))->setValue((isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad) * $esp_emp->cantidad * $det_ped->cantidad);
                                $objSheet1->getCell('I' . ($x + 1))->setValue((isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad));
                                $objSheet1->getCell('J' . ($x + 1))->setValue($p->detalles[0]->agencia_carga->nombre);
                                $objSheet1->getCell('K' . ($x + 1))->setValue(isset($p->envios[0]->guia_madre) ? $p->envios[0]->guia_madre ." / ". $p->envios[0]->guia_hija : "");
                                $objSheet1->getCell('L' . ($x + 1))->setValue(isset($p->envios[0]->dae) ? $p->envios[0]->dae : "");

                                $x++;
                            }
                        }
                    }

                } else if ($p->tipo_especificacion === "T") {
                    $count_pedido_tinturado = true;
                    $ids_pedidos_tinturados[] = $p->id_pedido;

                    foreach ($p->detalles as $det_tinturado => $det_ped) {
                        $datos_exportacion = '';
                        if (getDatosExportacionByDetPed($det_ped->id_detalle_pedido)->count() > 0)
                            foreach (getDatosExportacionByDetPed($det_ped->id_detalle_pedido) as $dE)
                                $datos_exportacion .= $dE->valor . "-";

                        foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $sp_t => $esp_emp_t) {

                            foreach ($esp_emp_t->detalles as $det_sp_t => $det_esp_emp_t) {
                                $piezas_totales_tinturados += ($esp_emp_t->cantidad * $det_ped->cantidad);
                                $ramos_totales_tinturados += $det_esp_emp_t->cantidad * $esp_emp_t->cantidad * $det_ped->cantidad;
                                if ($det_sp_t == 0) $cajas_full_totales_tinturados += ($esp_emp_t->cantidad * $det_ped->cantidad) * explode('|', $esp_emp_t->empaque->nombre)[1];
                               // if ($sp_t == 0 && $det_sp_t == 0)
                                $ramos_totales_estandar_tinturados += convertToEstandar($det_esp_emp_t->cantidad * $esp_emp_t->cantidad * $det_ped->cantidad, $det_esp_emp_t->clasificacion_ramo->nombre);

                                if (!in_array($det_esp_emp_t->id_variedad, $variedades_tinturados)) {
                                    $variedades_tinturados[] = $det_esp_emp_t->id_variedad;
                                }
                                $ramos_x_variedades_tinturados[] = [
                                    'id_variedad' => $det_esp_emp_t->id_variedad,
                                    'cantidad' => convertToEstandar($det_esp_emp_t->cantidad * $esp_emp_t->cantidad * $det_ped->cantidad, $det_esp_emp_t->clasificacion_ramo->nombre),
                                ];
                            }
                        }
                        $final_tinturado_a = 0;
                        $merge_espcificacion = $w + 1;
                        foreach ($det_ped->coloraciones as $col => $coloracion) {

                            if ($count_pedido_tinturado) {
                                $inicio_tinturado_a = $w + 1;
                                $count_pedido_tinturado = false;
                            }
                            $count_coloracion = 1;
                            foreach ($coloracion->marcaciones_coloraciones as $mar_col => $m_c) {
                                $det_esp_emp_tinturado = $m_c->detalle_especificacionempaque;
                                $objSheet->getCell('A' . ($w + 1))->setValue($p->cliente->detalle()->nombre);
                                $objSheet->getCell('B' . ($w + 1))->setValue($m_c->marcacion->nombre);
                                $objSheet->getCell('C' . ($w + 1))->setValue($det_esp_emp_tinturado->variedad->siglas . " " . explode('|', $det_esp_emp_tinturado->clasificacion_ramo->nombre)[0] . " " . $det_esp_emp_tinturado->clasificacion_ramo->unidad_medida->siglas);
                                $objSheet->getCell('D' . ($w + 1))->setValue(explode("|", $det_esp_emp_tinturado->especificacion_empaque->empaque->nombre)[0]);
                                $objSheet->getCell('E' . ($w + 1))->setValue(explode('|', $det_esp_emp_tinturado->empaque_p->nombre)[0]);

                                $final_tinturado_a++;
                                if ($mar_col == 0) $inicio_tinturado_d = $w + 1;
                                $final_tinturado_d = count($coloracion->marcaciones_coloraciones);


                                $objSheet->mergeCells('C' . $inicio_tinturado_d . ':C' . ($final_tinturado_d + $inicio_tinturado_d - 1));
                                $objSheet->mergeCells('D' . $inicio_tinturado_d . ':D' . ($final_tinturado_d + $inicio_tinturado_d - 1));
                                $objSheet->mergeCells('E' . $inicio_tinturado_d . ':E' . ($final_tinturado_d + $inicio_tinturado_d - 1));
                                $objSheet->mergeCells('G' . $inicio_tinturado_d . ':G' . ($final_tinturado_d + $inicio_tinturado_d - 1));
                                $objSheet->getCell('G' . ($w + 1))->setValue($coloracion->color->nombre);
                                $objSheet->getStyle('G' . $inicio_tinturado_d . ':G' . ($final_tinturado_d + $inicio_tinturado_d - 1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($coloracion->color->fondo, 1));
                                $objSheet->getStyle('G' . ($w + 1))->getFont()->getColor()->applyFromArray(array('rgb' => substr($coloracion->color->texto, 1)));
                                $objSheet->getStyle('A' . ($w + 1) . ':J' . ($w + 1))->applyFromArray($BStyle);
                                $objSheet->getStyle('A' . ($w + 1) . ':J' . ($w + 1))->applyFromArray($style);
                                $objSheet->getCell('H' . ($w+1))->setValue($p->detalles[0]->agencia_carga->nombre);
                                $objSheet->getCell('I' . ($w+1))->setValue(isset($p->envios[0]->guia_madre) ? $p->envios[0]->guia_madre ." / ". $p->envios[0]->guia_hija : "");
                                $objSheet->getCell('J' . ($w+1))->setValue(isset($p->envios[0]->dae) ? $p->envios[0]->dae : "");
                                $w++;
                                $merge_espcificacion++;
                               $objSheet->getCell('F' . ($w))->setValue($m_c->cantidad);
                            }

                        }
                        $objSheet->mergeCells('A' . $inicio_tinturado_a . ':A' . ($final_tinturado_a + $inicio_tinturado_a-1 ));
                        /*$objSheet->mergeCells('B' . $inicio_tinturado_a . ':B' . ($final_tinturado_a + $inicio_tinturado_a-1));*/
                        $objSheet->mergeCells('H' . $inicio_tinturado_a . ':H' . ($final_tinturado_a + $inicio_tinturado_a - 1));
                        $objSheet->mergeCells('I' . $inicio_tinturado_a . ':I' . ($final_tinturado_a + $inicio_tinturado_a - 1));
                        $objSheet->mergeCells('J' . $inicio_tinturado_a . ':J' . ($final_tinturado_a + $inicio_tinturado_a - 1));
                    }
                    $objSheet->getStyle('A1:J1')->getFont()->setBold(true);
                }
            }
        }

        $objSheet->getColumnDimension('A')->setWidth(20);
        $objSheet->getColumnDimension('B')->setWidth(20);
        $objSheet->getColumnDimension('C')->setWidth(20);
        $objSheet->getColumnDimension('D')->setWidth(20);
        $objSheet->getColumnDimension('E')->setWidth(20);
        $objSheet->getColumnDimension('F')->setWidth(20);
        $objSheet->getColumnDimension('G')->setWidth(20);
        $objSheet->getColumnDimension('H')->setWidth(18);
        $objSheet->getColumnDimension('I')->setWidth(28);
        $objSheet->getColumnDimension('j')->setWidth(20);
        $objSheet1->getColumnDimension('J')->setWidth(20);
        $objSheet1->getColumnDimension('K')->setWidth(27);
        $objSheet1->getColumnDimension('L')->setWidth(22);

        //CUADRO VALORES
        if($x > 1){
            $objSheet1->mergeCells('A'. ($x + 4).':B'.($x + 4));
            $objSheet1->getCell('A' . ($x + 4))->setValue("TOTALES RAMOS POR VARIEDAD");
            $objSheet1->mergeCells('E'. ($x + 4).':G'.($x + 4));
            $objSheet1->getCell('E' . ($x + 4))->setValue("CAJAS EQUIVALENTES");
            $objSheet1->mergeCells('I'. ($x + 4).':K'.($x + 4));
            $objSheet1->mergeCells('I'. ($x + 5).':K'.($x + 5));
            $objSheet1->mergeCells('I'. ($x + 6).':K'.($x + 6));
            $objSheet1->mergeCells('I'. ($x + 7).':K'.($x + 7));
            $objSheet1->getCell('I' . ($x + 4))->setValue("Piezas Totales Pedidas: ");
            $objSheet1->getCell('L' . ($x + 4))->setValue($piezas_totales_no_tinturados);
            $objSheet1->getCell('I' . ($x + 5))->setValue("Ramos Totales Pedidos:");
            $objSheet1->getCell('L' . ($x + 5))->setValue($ramos_totales_no_tinturados);
            $objSheet1->getCell('I' . ($x + 6))->setValue("Cajas Full Totales Pedidas:" );
            $objSheet1->getCell('L' . ($x + 6))->setValue($cajas_full_totales_no_tinturados);
            $objSheet1->getCell('I' . ($x + 7))->setValue("Cajas Equivalentes Totales Pedidas: " );
            $objSheet1->getCell('L' . ($x + 7))->setValue(round($ramos_totales_estandar_no_tinturados / getConfiguracionEmpresa()->ramos_x_caja,2));
            $objSheet1->getStyle('A'. ($x + 4).':C'.($x + 4))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('A'. ($x + 4))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getStyle('E'. ($x + 4).':G'.($x + 4))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('E'. ($x + 4))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getStyle('I'. ($x + 4).':K'.($x + 4))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('I'. ($x + 5).':K'.($x + 5))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('I'. ($x + 6).':K'.($x + 6))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('I'. ($x + 7).':K'.($x + 7))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('I'. ($x + 4))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getStyle('I'. ($x + 5))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getStyle('I'. ($x + 6))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getStyle('I'. ($x + 7))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));

            $ramos_x_variedad_no_tinturados = DB::table('detalle_especificacionempaque as dee')
                ->join('especificacion_empaque as ee', 'dee.id_especificacion_empaque', 'ee.id_especificacion_empaque')
                ->join('cliente_pedido_especificacion as cpe', 'ee.id_especificacion', 'cpe.id_especificacion')
                ->join('detalle_pedido as dp', 'cpe.id_cliente_pedido_especificacion', 'dp.id_cliente_especificacion')
                ->select('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos', 'dee.longitud_ramo', 'dee.id_unidad_medida',
                    DB::raw('sum(dee.cantidad * ee.cantidad * dp.cantidad) as cantidad'))->whereIn('dp.id_pedido', $ids_pedidos_no_tinturados)
                ->groupBy('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos', 'dee.longitud_ramo', 'dee.id_unidad_medida')
                ->orderBy('dp.id_pedido', 'desc')->get();

            $variedades_no_tinturados = DB::table('detalle_especificacionempaque as dee')
                ->join('especificacion_empaque as ee', 'dee.id_especificacion_empaque', 'ee.id_especificacion_empaque')
                ->join('cliente_pedido_especificacion as cpe', 'ee.id_especificacion', 'cpe.id_especificacion')
                ->join('detalle_pedido as dp', 'cpe.id_cliente_pedido_especificacion', 'dp.id_cliente_especificacion')
                ->select('dee.id_variedad')->distinct()->whereIn('dp.id_pedido', $ids_pedidos_no_tinturados)->get();

            foreach ($variedades_no_tinturados as $variedad_no_tinturados){
                $cantidad = 0;

                foreach($ramos_x_variedades_no_tinturados as $ramos_no_tinturados){
                    if($ramos_no_tinturados['id_variedad'] == $variedad_no_tinturados->id_variedad){
                        $cantidad += $ramos_no_tinturados['cantidad'];
                    }
                }
                $cajas_equivalentes_no_tinturados[] = [
                    'id_variedad' => $variedad_no_tinturados,
                    'cantidad' => round($cantidad / getConfiguracionEmpresa()->ramos_x_caja, 2),
                ];
            }

            foreach($ramos_x_variedad_no_tinturados as $x_ramo_x_variedad_no_tinturado => $ramo_x_variedad_no_tinutrado){
               // $objSheet1->mergeCells('A'.($x + 5 + $x_ramo_x_variedad_no_tinturado).':B'.($x + 5 + $x_ramo_x_variedad_no_tinturado));
                $objSheet1->getCell('A' . ($x + 5 + $x_ramo_x_variedad_no_tinturado))->setValue(getVariedad($ramo_x_variedad_no_tinutrado->id_variedad)->siglas." ".
                    explode('|',getCalibreRamoById($ramo_x_variedad_no_tinutrado->id_clasificacion_ramo)->nombre)[0]."".
                    getCalibreRamoById($ramo_x_variedad_no_tinutrado->id_clasificacion_ramo)->unidad_medida->siglas. " ". ($ramo_x_variedad_no_tinutrado->tallos_x_ramos != '' ? $ramo_x_variedad_no_tinutrado->tallos_x_ramos." tallos " : "") .
                    ($ramo_x_variedad_no_tinutrado->longitud_ramo != '' ?  $ramo_x_variedad_no_tinutrado->longitud_ramo." ".getUnidadMedida($ramo_x_variedad_no_tinutrado->id_unidad_medida)->siglas : "" ));
                $objSheet1->getCell('B' . ($x + 5 + $x_ramo_x_variedad_no_tinturado))->setValue($ramo_x_variedad_no_tinutrado->cantidad);
               // $objSheet1->mergeCells('A'.($x + 5 + $x_ramo_x_variedad_no_tinturado+1).':B'.($x + 5 + $x_ramo_x_variedad_no_tinturado+1));
            }

            $objSheet1->getStyle('A'. ($x + 5 + $x_ramo_x_variedad_no_tinturado+1).':B'.($x + 5 + $x_ramo_x_variedad_no_tinturado+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('C' . ($x + 4))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('ffffff');
            $objSheet1->getStyle('A'. ($x + 5 + $x_ramo_x_variedad_no_tinturado+1))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getCell('A' . ($x + 5+ $x_ramo_x_variedad_no_tinturado+1))->setValue("Ramos Totales Pedidos");
            $objSheet1->getStyle('B'. ($x + 5 + $x_ramo_x_variedad_no_tinturado+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('B'. ($x + 5 + $x_ramo_x_variedad_no_tinturado+1))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getCell('B' . ($x + 5+ $x_ramo_x_variedad_no_tinturado+1))->setValue($ramos_totales_no_tinturados);

            $a = 1;
            foreach($cajas_equivalentes_no_tinturados as $caja_equivalente_no_tinturado){
                $objSheet1->mergeCells('E'.($x + 4 + $a).':F'.($x + 4 + $a));
                $objSheet1->getCell('E' . ($x + 4 + $a))->setValue(getVariedad($caja_equivalente_no_tinturado['id_variedad']->id_variedad)->nombre." (".getVariedad($caja_equivalente_no_tinturado['id_variedad']->id_variedad)->siglas.")");
                $objSheet1->getCell('G' . ($x + 4 + $a))->setValue($caja_equivalente_no_tinturado["cantidad"]);
                $a++;
            }
            $objSheet1->mergeCells('E'.($x + 4 + $a).':F'.($x + 4 + $a ));
            $objSheet1->getStyle('E'. ($x + 4 + $a))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('E'. ($x + 4 + $a))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getCell('E' . ($x + 4 + $a))->setValue("Cajas Equivalentes Totales Pedidas");
            $objSheet1->getStyle('G'. ($x + 4 + $a))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('G'. ($x + 4 + $a))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getCell('G' . ($x + 4 + $a))->setValue(round($ramos_totales_estandar_no_tinturados / getConfiguracionEmpresa()->ramos_x_caja,2));

            $objSheet1->getColumnDimension('A')->setWidth(35);
            $objSheet1->getColumnDimension('B')->setWidth(30);
            $objSheet1->getColumnDimension('C')->setWidth(10);
            $objSheet1->getColumnDimension('D')->setWidth(20);
            $objSheet1->getColumnDimension('E')->setWidth(35);
            $objSheet1->getColumnDimension('F')->setWidth(8);
            $objSheet1->getColumnDimension('G')->setWidth(10);
            $objSheet1->getColumnDimension('H')->setWidth(10);
            $objSheet1->getColumnDimension('I')->setWidth(15);
            $objSheet1->getStyle('A1:L1')->getFont()->setBold(true);
        }

        //CUADRO VALORES
        if($w > 1){
            $objSheet->mergeCells('A'. ($w + 4).':C'.($w + 4));
            $objSheet->getCell('A' . ($w + 4))->setValue("TOTALES RAMOS POR VARIEDAD");
            $objSheet->mergeCells('E'. ($w + 4).':G'.($w + 4));
            $objSheet->getCell('E' . ($w + 4))->setValue("CAJAS EQUIVALENTES");
            $objSheet->mergeCells('I'. ($w + 4).':K'.($w + 4));
            $objSheet->mergeCells('I'. ($w + 5).':K'.($w + 5));
            $objSheet->mergeCells('I'. ($w + 6).':K'.($w + 6));
            $objSheet->mergeCells('I'. ($w + 7).':K'.($w + 7));
            $objSheet->getCell('I' . ($w + 4))->setValue("Piezas Totales Pedidas: ");
            $objSheet->getCell('L' . ($w + 4))->setValue($piezas_totales_tinturados);
            $objSheet->getCell('I' . ($w + 5))->setValue("Ramos Totales Pedidos:");
            $objSheet->getCell('L' . ($w + 5))->setValue($ramos_totales_tinturados);
            $objSheet->getCell('I' . ($w + 6))->setValue("Cajas Full Totales Pedidas:" );
            $objSheet->getCell('L' . ($w + 6))->setValue($cajas_full_totales_tinturados);
            $objSheet->getCell('I' . ($w + 7))->setValue("Cajas Equivalentes Totales Pedidas: " );
            $objSheet->getCell('L' . ($w + 7))->setValue(round($ramos_totales_estandar_tinturados / getConfiguracionEmpresa()->ramos_x_caja,2));
            $objSheet->getStyle('A'. ($w + 4).':C'.($w + 4))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet->getStyle('A'. ($w + 4))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet->getStyle('E'. ($w + 4).':G'.($w + 4))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet->getStyle('E'. ($w + 4))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet->getStyle('I'. ($w + 4).':K'.($w + 4))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet->getStyle('I'. ($w + 5).':K'.($w + 5))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet->getStyle('I'. ($w + 6).':K'.($w + 6))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet->getStyle('I'. ($w + 7).':K'.($w + 7))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet->getStyle('I'. ($w + 4))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet->getStyle('I'. ($w + 5))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet->getStyle('I'. ($w + 6))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet->getStyle('I'. ($w + 7))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));

            $ramos_x_variedad_tinturados = DB::table('detalle_especificacionempaque as dee')
                ->join('especificacion_empaque as ee', 'dee.id_especificacion_empaque', 'ee.id_especificacion_empaque')
                ->join('cliente_pedido_especificacion as cpe', 'ee.id_especificacion', 'cpe.id_especificacion')
                ->join('detalle_pedido as dp', 'cpe.id_cliente_pedido_especificacion', 'dp.id_cliente_especificacion')
                ->select('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos', 'dee.longitud_ramo', 'dee.id_unidad_medida',
                    DB::raw('sum(dee.cantidad * ee.cantidad * dp.cantidad) as cantidad'))->whereIn('dp.id_pedido', $ids_pedidos_tinturados)
                ->groupBy('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos', 'dee.longitud_ramo', 'dee.id_unidad_medida')
                ->orderBy('dp.id_pedido', 'desc')->get();

            $variedades_tinturados = DB::table('detalle_especificacionempaque as dee')
                ->join('especificacion_empaque as ee', 'dee.id_especificacion_empaque', 'ee.id_especificacion_empaque')
                ->join('cliente_pedido_especificacion as cpe', 'ee.id_especificacion', 'cpe.id_especificacion')
                ->join('detalle_pedido as dp', 'cpe.id_cliente_pedido_especificacion', 'dp.id_cliente_especificacion')
                ->select('dee.id_variedad')->distinct()->whereIn('dp.id_pedido', $ids_pedidos_tinturados)->get();

            foreach ($variedades_tinturados as $variedad_tinturados){
                $cantidad_tinturados = 0;

                foreach($ramos_x_variedades_tinturados as $ramos_tinturados){
                    if($ramos_tinturados['id_variedad'] == $variedad_tinturados->id_variedad){
                        $cantidad_tinturados += $ramos_tinturados['cantidad'];
                    }
                }

                $cajas_equivalentes_tinturados[] = [
                    'id_variedad' => $variedad_tinturados,
                    'cantidad' => round($cantidad_tinturados / getConfiguracionEmpresa()->ramos_x_caja, 2),
                ];
            }

            $total_ramo_x_variedad_tinutrado =0;
            foreach($ramos_x_variedad_tinturados as $w_ramo_x_variedad_tinturado => $ramo_x_variedad_tinutrado){
               // $objSheet->mergeCells('A'.($w + 5 + $w_ramo_x_variedad_tinturado).':B'.($w + 5 + $w_ramo_x_variedad_tinturado));
                $objSheet->getCell('A' . ($w + 5 + $w_ramo_x_variedad_tinturado))->setValue(getVariedad($ramo_x_variedad_tinutrado->id_variedad)->siglas." ".
                    explode('|',getCalibreRamoById($ramo_x_variedad_tinutrado->id_clasificacion_ramo)->nombre)[0]."".
                    getCalibreRamoById($ramo_x_variedad_tinutrado->id_clasificacion_ramo)->unidad_medida->siglas. " ". ($ramo_x_variedad_tinutrado->tallos_x_ramos != '' ? $ramo_x_variedad_tinutrado->tallos_x_ramos." tallos " : "") .
                    ($ramo_x_variedad_tinutrado->longitud_ramo != '' ?  $ramo_x_variedad_tinutrado->longitud_ramo." ".getUnidadMedida($ramo_x_variedad_tinutrado->id_unidad_medida)->siglas : "" ));
                $objSheet->getCell('C' . ($w + 5 + $w_ramo_x_variedad_tinturado))->setValue($ramo_x_variedad_tinutrado->cantidad);
                //$objSheet->mergeCells('A'.($w + 5 + $w_ramo_x_variedad_tinturado+1).':B'.($w + 5 + $w_ramo_x_variedad_tinturado+1));
                $total_ramo_x_variedad_tinutrado += $ramo_x_variedad_tinutrado->cantidad;
                $objSheet->getCell('C' . ($w + 5+ $w_ramo_x_variedad_tinturado+1))->setValue($total_ramo_x_variedad_tinutrado);
            }
            $objSheet->getStyle('A'. ($w + 5 + $w_ramo_x_variedad_tinturado+1).':B'.($w + 5 + $w_ramo_x_variedad_tinturado+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet->getStyle('A'. ($w + 5 + $w_ramo_x_variedad_tinturado+1))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet->getCell('A' . ($w + 5+ $w_ramo_x_variedad_tinturado+1))->setValue("Ramos Totales Pedidos");
            $objSheet->getStyle('C'. ($w + 5 + $w_ramo_x_variedad_tinturado+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet->getStyle('C'. ($w + 5 + $w_ramo_x_variedad_tinturado+1))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));

            $b = 1;
            foreach($cajas_equivalentes_tinturados as $caja_equivalente_tinturado){
                $objSheet->mergeCells('E'.($w + 4 + $b).':F'.($w + 4 + $b));
                $objSheet->getCell('E' . ($w + 4 + $b))->setValue(getVariedad($caja_equivalente_tinturado['id_variedad']->id_variedad)->nombre." (".getVariedad($caja_equivalente_tinturado['id_variedad']->id_variedad)->siglas.")");
                $objSheet->getCell('G' . ($w + 4 + $b))->setValue($caja_equivalente_tinturado["cantidad"]);
                $b++;
            }
            $objSheet->mergeCells('E'.($w + 4 + $b).':F'.($w + 4 + $b ));
            $objSheet->getStyle('E'. ($w + 4 + $b))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet->getStyle('E'. ($w + 4 + $b))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet->getCell('E' . ($w + 4 + $b))->setValue("Cajas Equivalentes Totales Pedidas");
            $objSheet->getStyle('G'. ($w + 4 + $b))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet->getStyle('G'. ($w + 4 + $b))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet->getCell('G' . ($w + 4 + $b))->setValue(round($ramos_totales_estandar_tinturados / getConfiguracionEmpresa()->ramos_x_caja,2));

        }


        $objSheet->getDefaultStyle()->applyFromArray($style);
        $objSheet1->getDefaultStyle()->applyFromArray($style);

    }

    public function exportar_excel_listado_despacho(Request $request){
        //---------------------- EXCEL --------------------------------------
        $objPHPExcel = new PHPExcel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");

        $this->excel_listado_pedidos_despacho($objPHPExcel, $request);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="Listado despacho.xlsx"');
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

    public function excel_listado_pedidos_despacho($objPHPExcel, $request){
        $pedidos = Pedido::where([
            ['fecha_pedido',$request->fecha_pedido],
            ['pedido.estado',1],
            ['dc.estado',1]])
            ->join('cliente as c','pedido.id_cliente','c.id_cliente')->join('detalle_cliente as dc','c.id_cliente','dc.id_cliente')
            ->orderBy('dc.nombre','asc');

        if(isset($request->id_configuracion_empresa))
            $pedidos->where('id_configuracion_empresa',$request->id_configuracion_empresa);

        //HOJA Tinturados
        $objSheet = new PHPExcel_Worksheet($objPHPExcel,'Tinturados');
        $objPHPExcel->addSheet($objSheet, 0);
        $objPHPExcel->setActiveSheetIndex(0);
        $objSheet->mergeCells('A1:J1');
        $objSheet->getCell('A1')->setValue("DESPACHO DE PEDIDOS:  ". $request->fecha);
        $objSheet->getCell('A2' )->setValue('Cliente');
        $objSheet->getCell('B2' )->setValue('Factura');
        $objSheet->getCell('C2')->setValue('Marcaciones');
        $objSheet->getCell('D2')->setValue('Piezas');
        $objSheet->getCell('E2' )->setValue('Cajas full');
        $objSheet->getCell('F2')->setValue('Half');
        $objSheet->getCell('G2')->setValue('Cuartos');
        $objSheet->getCell('H2')->setValue('Octavos');
        $objSheet->getCell('I2')->setValue('Agencia de carga');
        $objSheet->getCell('J2')->setValue('Facturado por');

        $style = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );
        $total_full= 0;
        $total_half = 0;
        $total_cuarto = 0;
        $total_octavo = 0;
        $total_piezas_despacho =  0 ;
        $pedidos = $pedidos->get();
        foreach ($pedidos as $p => $pedido){
            if(!getFacturaAnulada($pedido->id_pedido)){
                $full = 0;
                $half = 0;
                $cuarto = 0;
                $sexto = 0;
                $octavo = 0;
                foreach ($pedido->detalles as $det_tinturado => $det_ped) {
                    foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp){
                        $full += explode("|", $esp_emp->empaque->nombre)[1] * $det_ped->cantidad;
                        switch (explode("|", $esp_emp->empaque->nombre)[1]) {
                            case '0.5':
                                $half += $det_ped->cantidad;
                                break;
                            case '0.25':
                                $cuarto += $det_ped->cantidad;
                                break;
                            case '0.17':
                                $sexto += $det_ped->cantidad;
                                break;
                            case '0.125':
                                $octavo += $det_ped->cantidad;
                                break;
                        }
                        $piezas_despacho = $half + $cuarto + $sexto + $octavo;
                    }
                    $datosExportacion ="";
                    if(count(getDatosExportacionCliente($det_ped->id_detalle_pedido))>0)
                        foreach(getDatosExportacionCliente($det_ped->id_detalle_pedido) as $de)
                            $datosExportacion .= " ".$de->valor;
                }

                $objSheet->getStyle('A1:J1')->getFont()->setBold(true);
                $objSheet->getStyle('A2:J2')->getFont()->setBold(true);
                $objSheet->getStyle('A' . ($p + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objSheet->getStyle('B' . ($p + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objSheet->getStyle('C' . ($p + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objSheet->getStyle('D' . ($p + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objSheet->getStyle('E' . ($p + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objSheet->getStyle('F' . ($p + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objSheet->getStyle('G' . ($p + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objSheet->getStyle('H' . ($p + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objSheet->getStyle('I' . ($p + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objSheet->getStyle('J' . ($p + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objSheet->getCell('A' . ($p + 3))->setValue($pedido->cliente->detalle()->nombre);
                $objSheet->getCell('B' . ($p + 3))->setValue(isset($pedido->envios[0]->comprobante->secuencial)? $pedido->envios[0]->comprobante->secuencial : "");
                $objSheet->getCell('C' . ($p + 3))->setValue($datosExportacion);
                $objSheet->getCell('D' . ($p + 3))->setValue($piezas_despacho);
                $objSheet->getCell('E' . ($p + 3))->setValue($full);
                $objSheet->getCell('F' . ($p + 3))->setValue($half);
                $objSheet->getCell('G' . ($p + 3))->setValue($cuarto);
                $objSheet->getCell('H' . ($p + 3))->setValue($octavo);
                $objSheet->getCell('I' . ($p + 3))->setValue($pedido->detalles[0]->agencia_carga->nombre);
                $objSheet->getCell('J' . ($p + 3))->setValue($pedido->empresa->nombre);
                $objSheet->getStyle('A' . ($p + 1) . ':J' . ($p + 1))->applyFromArray($style);
                $total_full +=$full;
                $total_half +=$half;
                $total_cuarto +=$cuarto;
                $total_octavo +=$octavo;
                $total_piezas_despacho += $piezas_despacho;
            }
        }
        $cant = $pedidos->count();
        $objSheet->getCell('C' . ($cant + 3))->setValue('TOTALES: ');
        $objSheet->getCell('D' . ($cant + 3))->setValue($total_piezas_despacho);
        $objSheet->getCell('E' . ($cant + 3))->setValue($total_full);
        $objSheet->getCell('F' . ($cant + 3))->setValue($total_half);
        $objSheet->getCell('G' . ($cant + 3))->setValue($total_cuarto);
        $objSheet->getCell('H' . ($cant + 3))->setValue($total_octavo);
        $objSheet->getStyle('C'. ($cant + 3))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
        $objSheet->getStyle('D'. ($cant + 3))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
        $objSheet->getStyle('E'. ($cant + 3))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
        $objSheet->getStyle('F'. ($cant + 3))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
        $objSheet->getStyle('G'. ($cant + 3))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
        $objSheet->getStyle('H'. ($cant + 3))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
        $objSheet->getStyle('C'. ($cant + 3).':H'.($cant + 3))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
        $objSheet->getStyle('C' . ($cant + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('C' . ($cant + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('D' . ($cant + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('E' . ($cant + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);$objSheet->getStyle('C' . ($cant + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('F' . ($cant + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('G' . ($cant + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getStyle('H' . ($cant + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objSheet->getColumnDimension('A')->setWidth(30);
        $objSheet->getColumnDimension('B')->setWidth(20);
        $objSheet->getColumnDimension('C')->setWidth(20);
        $objSheet->getColumnDimension('D')->setWidth(10);
        $objSheet->getColumnDimension('E')->setWidth(15);
        $objSheet->getColumnDimension('F')->setWidth(10);
        $objSheet->getColumnDimension('G')->setWidth(10);
        $objSheet->getColumnDimension('H')->setWidth(10);
        $objSheet->getColumnDimension('I')->setWidth(15);
        $objSheet->getColumnDimension('J')->setWidth(20);

        /*$objSheet->mergeCells('A1:J1');
        $objSheet->getCell('A1')->setValue("DESPACHO DE PEDIDOS");
        $objSheet->mergeCells('E'. ($w + 4).':G'.($w + 4));
        $objSheet->getCell('E' . ($w + 4))->setValue("CAJAS EQUIVALENTES");
        $objSheet->mergeCells('I'. ($w + 4).':K'.($w + 4));
        $objSheet->mergeCells('I'. ($w + 5).':K'.($w + 5));
        $objSheet->mergeCells('I'. ($w + 6).':K'.($w + 6));
        $objSheet->mergeCells('I'. ($w + 7).':K'.($w + 7));
        $objSheet->getCell('I' . ($w + 4))->setValue("Piezas Totales Pedidas: ");
        $objSheet->getCell('L' . ($w + 4))->setValue($piezas_totales_tinturados);
        $objSheet->getCell('I' . ($w + 5))->setValue("Ramos Totales Pedidos:");
        $objSheet->getCell('L' . ($w + 5))->setValue($ramos_totales_tinturados);
        $objSheet->getCell('I' . ($w + 6))->setValue("Cajas Full Totales Pedidas:" );
        $objSheet->getCell('L' . ($w + 6))->setValue($cajas_full_totales_tinturados);
        $objSheet->getCell('I' . ($w + 7))->setValue("Cajas Equivalentes Totales Pedidas: " );
        $objSheet->getCell('L' . ($w + 7))->setValue(round($ramos_totales_estandar_tinturados / getConfiguracionEmpresa()->ramos_x_caja,2));
        $objSheet->getStyle('A'. ($w + 4).':C'.($w + 4))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');*/

        $objSheet->getDefaultStyle()->applyFromArray($style);
    }

    public function exportar_pedidos_despacho_cuarto_frio(Request $request){
        //---------------------- EXCEL --------------------------------------
        $objPHPExcel = new PHPExcel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");

        $this->excel_pedidos_despacho_cuarto_frio($objPHPExcel, $request);

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

    public function excel_pedidos_despacho_cuarto_frio($objPHPExcel, $request){

        $pedidos = Pedido::where([['fecha_pedido',$request->fecha_pedido],['pedido.estado',1],['dc.estado',1]])
            ->join('cliente as c','pedido.id_cliente','c.id_cliente')->join('detalle_cliente as dc','c.id_cliente','dc.id_cliente')
            ->orderBy('dc.nombre','asc')->select('id_pedido')->get();

        $objSheet1 = new PHPExcel_Worksheet($objPHPExcel,'Despacho finca '. now()->toDateString());
        $objPHPExcel->addSheet($objSheet1, 1);
        $objPHPExcel->setActiveSheetIndex(1);

        $objSheet1->getCell('A1' )->setValue(strtoupper(getConfiguracionEmpresa()->razon_social));
        $objSheet1->mergeCells('A1:B3');

        $objSheet1->getCell('C1' )->setValue('SEMANA: '. getSemanaByDate($request->fecha_pedido)->codigo);
        $objSheet1->getCell('C2' )->setValue('DIA: '. getDias(TP_COMPLETO,FR_ARREGLO)[transformDiaPhp(date('w',strtotime(substr($request->fecha_pedido,0,10))))]);
        $objSheet1->getCell('C3' )->setValue('FECHA: '. Carbon::parse($request->fecha_pedido)->format('d-m-Y'));
        $objSheet1->mergeCells('C1:F1');
        $objSheet1->mergeCells('C2:F2');
        $objSheet1->mergeCells('C3:F3');


        $objSheet1->getCell('G1' )->setValue('DESPACHO DIARIOS DE CAJAS');
        $objSheet1->mergeCells('G1:L3');

        $objSheet1->getCell('A4' )->setValue('FACTURA');
        $objSheet1->getCell('B4' )->setValue('CLIENTE / CÓDIGO');
        $objSheet1->getCell('C4')->setValue('FLOR');
        $objSheet1->getCell('D4')->setValue('EMPAQUE');
        $objSheet1->getCell('E4' )->setValue('PRESENTACIÓN');
        $objSheet1->getCell('F4')->setValue('PIEZAS');
        $objSheet1->getCell('G4')->setValue('CAJAS FULL');
        $objSheet1->getCell('H4')->setValue('RAMOS');
        $objSheet1->getCell('I4')->setValue('RAMOS POR CAJA');
        $objSheet1->getCell('J4')->setValue('INGRESO DIA APERTURA');
        $objSheet1->getCell('K4')->setValue('DIAS FRIO');
        $objSheet1->getCell('L4')->setValue('HORARIO FRIO');
        $objSheet1->getCell('M4')->setValue('TEMPERATURA');
        $objSheet1->getCell('N4')->setValue('LONGITUD');
        $objSheet1->getCell('O4')->setValue('FLOR COMPRADA');
        $objSheet1->getCell('P4')->setValue('PROVEEDOR');
        $objSheet1->getCell('Q4')->setValue('CUARTO FRIO');
        $objSheet1->getCell('R4')->setValue('GUIA');
        $objSheet1->getCell('S4')->setValue('FUE');
        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );
        $style = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );

        $x = 4;
        $ids_pedidos_no_tinturados = [];
        $ramos_x_variedades_no_tinturados = [];
        $cajas_equivalentes_no_tinturados = [];
        $variedades_no_tinturados = [];
        $ramos_totales_no_tinturados = 0;
        $piezas_totales_no_tinturados = 0;
        $ramos_totales_estandar_no_tinturados = 0;
        $cajas_full_totales_no_tinturados = 0;

        foreach ($pedidos as $a => $pedido){
            $p = getPedido($pedido->id_pedido);
            if(!getFacturaAnulada($pedido->id_pedido)){
                    $ids_pedidos_no_tinturados[] = $p->id_pedido;
                    foreach ($p->detalles as $det => $det_ped) {
                        $datos_exportacion = '';
                        if (getDatosExportacionByDetPed($det_ped->id_detalle_pedido)->count() > 0)
                            foreach (getDatosExportacionByDetPed($det_ped->id_detalle_pedido) as $dE)
                                $datos_exportacion .= $dE->valor . "-";
                        if ($det == 0) $inicio_a = $x + 1;
                        $final_a = getCantidadDetallesEspecificacionByPedido($pedido->id_pedido) + $inicio_a - 1;
                        foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $sp => $esp_emp) {
                            $piezas_totales_no_tinturados += ($esp_emp->cantidad * $det_ped->cantidad);
                            foreach ($esp_emp->detalles as $det_sp => $det_esp_emp) {
                                $ramos_modificado = getRamosXCajaModificado($det_ped->id_detalle_pedido,$det_esp_emp->id_detalle_especificacionempaque);
                                if ($sp == 0 && $det_sp == 0) {
                                    $inicio_b = $x + 1;
                                }
                                $final_b = getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->id_especificacion) + $inicio_b - 1;
                                if ($det_sp == 0) {
                                    $inicio_d = $x + 1;
                                    $cajas_full_totales_no_tinturados += ($esp_emp->cantidad * $det_ped->cantidad) * explode('|', $esp_emp->empaque->nombre)[1];
                                }
                                $final_d = count($esp_emp->detalles) + $inicio_d - 1;
                                $objSheet1->mergeCells('A' . $inicio_a . ':A' . $final_a);
                                $objSheet1->mergeCells('B' . $inicio_b . ':B' . $final_b);
                                $objSheet1->mergeCells('D' . $inicio_d . ':D' . $final_d);
                                $objSheet1->mergeCells('F' . $inicio_b . ':F' . $final_b);
                                $objSheet1->mergeCells('G' . $inicio_d . ':G' . $final_d);
                                $objSheet1->getCell('A' . ($x + 1))->setValue(isset($p->envios[0]->comprobante->secuencial) ? $p->envios[0]->comprobante->secuencial : "");
                                $objSheet1->getCell('B' . ($x + 1))->setValue($p->cliente->detalle()->nombre. ((!$datos_exportacion) ? "" : " / ". substr($datos_exportacion, 0, -1)));
                                $objSheet1->getCell('C' . ($x + 1))->setValue($det_esp_emp->variedad->siglas . " " . explode('|', $det_esp_emp->clasificacion_ramo->nombre)[0] . " " . $det_esp_emp->clasificacion_ramo->unidad_medida->siglas);
                                $objSheet1->getCell('D' . ($x + 1))->setValue(explode("|", $esp_emp->empaque->nombre)[0]);
                                $objSheet1->getCell('E' . ($x + 1))->setValue(explode('|', $det_esp_emp->empaque_p->nombre)[0]);
                                $objSheet1->getCell('F' . ($x + 1))->setValue($esp_emp->cantidad * $det_ped->cantidad);
                                $objSheet1->getCell('G' . ($x + 1))->setValue(($esp_emp->cantidad * $det_ped->cantidad) * explode('|', $esp_emp->empaque->nombre)[1]);
                                $objSheet1->getCell('Q' . ($x + 1))->setValue($p->detalles[0]->agencia_carga->nombre);
                                $objSheet1->getCell('R' . ($x + 1))->setValue(isset($p->envios[0]->guia_madre) ? $p->envios[0]->guia_madre ." / ". $p->envios[0]->guia_hija : "");
                                $objSheet1->getCell('S' . ($x + 1))->setValue(isset($p->envios[0]->dae) ? $p->envios[0]->dae : "");
                                $ramos_totales_no_tinturados += (isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad) * $esp_emp->cantidad * $det_ped->cantidad;
                                $ramos_totales_estandar_no_tinturados += convertToEstandar((isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad) * $esp_emp->cantidad * $det_ped->cantidad, $det_esp_emp->clasificacion_ramo->nombre);
                                if (!in_array($det_esp_emp->id_variedad, $variedades_no_tinturados)) {
                                    $variedades_no_tinturados[] = $det_esp_emp->id_variedad;
                                }
                                $ramos_x_variedades_no_tinturados[] = [
                                    'id_variedad' => $det_esp_emp->id_variedad,
                                    'cantidad' => convertToEstandar((isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad) * $esp_emp->cantidad * $det_ped->cantidad, $det_esp_emp->clasificacion_ramo->nombre),
                                ];
                                $objSheet1->getStyle('A' . ($x - 3) . ':S' . ($x + 1))->applyFromArray($style);
                                $objSheet1->getStyle('A' . ($x - 3) . ':S' . ($x + 1))->applyFromArray($BStyle);
                                $objSheet1->getCell('H' . ($x + 1))->setValue((isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad) * $esp_emp->cantidad * $det_ped->cantidad);
                                $objSheet1->getCell('I' . ($x + 1))->setValue(isset($ramos_modificado) ? $ramos_modificado->cantidad : $det_esp_emp->cantidad);

                                $x++;
                            }
                        }
                    }



            }
        }
        //CUADRO VALORES
        if($x > 1){
            $objSheet1->mergeCells('A'. ($x + 4).':B'.($x + 4));
            $objSheet1->getCell('A' . ($x + 4))->setValue("TOTALES RAMOS POR VARIEDAD");
            $objSheet1->mergeCells('E'. ($x + 4).':G'.($x + 4));
            $objSheet1->getCell('E' . ($x + 4))->setValue("CAJAS EQUIVALENTES");
            $objSheet1->mergeCells('I'. ($x + 4).':K'.($x + 4));
            $objSheet1->mergeCells('I'. ($x + 5).':K'.($x + 5));
            $objSheet1->mergeCells('I'. ($x + 6).':K'.($x + 6));
            $objSheet1->mergeCells('I'. ($x + 7).':K'.($x + 7));
            $objSheet1->getCell('I' . ($x + 4))->setValue("Piezas Totales Pedidas: ");
            $objSheet1->getCell('L' . ($x + 4))->setValue($piezas_totales_no_tinturados);
            $objSheet1->getCell('I' . ($x + 5))->setValue("Ramos Totales Pedidos:");
            $objSheet1->getCell('L' . ($x + 5))->setValue($ramos_totales_no_tinturados);
            $objSheet1->getCell('I' . ($x + 6))->setValue("Cajas Full Totales Pedidas:" );
            $objSheet1->getCell('L' . ($x + 6))->setValue($cajas_full_totales_no_tinturados);
            $objSheet1->getCell('I' . ($x + 7))->setValue("Cajas Equivalentes Totales Pedidas: " );
            $objSheet1->getCell('L' . ($x + 7))->setValue(round($ramos_totales_estandar_no_tinturados / getConfiguracionEmpresa()->ramos_x_caja,2));
            $objSheet1->getStyle('A'. ($x + 4).':C'.($x + 4))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('A'. ($x + 4))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getStyle('E'. ($x + 4).':G'.($x + 4))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('E'. ($x + 4))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getStyle('I'. ($x + 4).':K'.($x + 4))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('I'. ($x + 5).':K'.($x + 5))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('I'. ($x + 6).':K'.($x + 6))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('I'. ($x + 7).':K'.($x + 7))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('I'. ($x + 4))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getStyle('I'. ($x + 5))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getStyle('I'. ($x + 6))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getStyle('I'. ($x + 7))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));

            $ramos_x_variedad_no_tinturados = DB::table('detalle_especificacionempaque as dee')
                ->join('especificacion_empaque as ee', 'dee.id_especificacion_empaque', 'ee.id_especificacion_empaque')
                ->join('cliente_pedido_especificacion as cpe', 'ee.id_especificacion', 'cpe.id_especificacion')
                ->join('detalle_pedido as dp', 'cpe.id_cliente_pedido_especificacion', 'dp.id_cliente_especificacion')
                ->select('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos', 'dee.longitud_ramo', 'dee.id_unidad_medida',
                    DB::raw('sum(dee.cantidad * ee.cantidad * dp.cantidad) as cantidad'))->whereIn('dp.id_pedido', $ids_pedidos_no_tinturados)
                ->groupBy('dee.id_variedad', 'dee.id_clasificacion_ramo', 'dee.tallos_x_ramos', 'dee.longitud_ramo', 'dee.id_unidad_medida')
                ->orderBy('dp.id_pedido', 'desc')->get();

            $variedades_no_tinturados = DB::table('detalle_especificacionempaque as dee')
                ->join('especificacion_empaque as ee', 'dee.id_especificacion_empaque', 'ee.id_especificacion_empaque')
                ->join('cliente_pedido_especificacion as cpe', 'ee.id_especificacion', 'cpe.id_especificacion')
                ->join('detalle_pedido as dp', 'cpe.id_cliente_pedido_especificacion', 'dp.id_cliente_especificacion')
                ->select('dee.id_variedad')->distinct()->whereIn('dp.id_pedido', $ids_pedidos_no_tinturados)->get();

            foreach ($variedades_no_tinturados as $variedad_no_tinturados){
                $cantidad = 0;

                foreach($ramos_x_variedades_no_tinturados as $ramos_no_tinturados){
                    if($ramos_no_tinturados['id_variedad'] == $variedad_no_tinturados->id_variedad){
                        $cantidad += $ramos_no_tinturados['cantidad'];
                    }
                }
                $cajas_equivalentes_no_tinturados[] = [
                    'id_variedad' => $variedad_no_tinturados,
                    'cantidad' => round($cantidad / getConfiguracionEmpresa()->ramos_x_caja, 2),
                ];
            }

            foreach($ramos_x_variedad_no_tinturados as $x_ramo_x_variedad_no_tinturado => $ramo_x_variedad_no_tinutrado){
                // $objSheet1->mergeCells('A'.($x + 5 + $x_ramo_x_variedad_no_tinturado).':B'.($x + 5 + $x_ramo_x_variedad_no_tinturado));
                $objSheet1->getCell('A' . ($x + 5 + $x_ramo_x_variedad_no_tinturado))->setValue(getVariedad($ramo_x_variedad_no_tinutrado->id_variedad)->siglas." ".
                    explode('|',getCalibreRamoById($ramo_x_variedad_no_tinutrado->id_clasificacion_ramo)->nombre)[0]."".
                    getCalibreRamoById($ramo_x_variedad_no_tinutrado->id_clasificacion_ramo)->unidad_medida->siglas. " ". ($ramo_x_variedad_no_tinutrado->tallos_x_ramos != '' ? $ramo_x_variedad_no_tinutrado->tallos_x_ramos." tallos " : "") .
                    ($ramo_x_variedad_no_tinutrado->longitud_ramo != '' ?  $ramo_x_variedad_no_tinutrado->longitud_ramo." ".getUnidadMedida($ramo_x_variedad_no_tinutrado->id_unidad_medida)->siglas : "" ));
                $objSheet1->getCell('B' . ($x + 5 + $x_ramo_x_variedad_no_tinturado))->setValue($ramo_x_variedad_no_tinutrado->cantidad);
                // $objSheet1->mergeCells('A'.($x + 5 + $x_ramo_x_variedad_no_tinturado+1).':B'.($x + 5 + $x_ramo_x_variedad_no_tinturado+1));
            }

            $objSheet1->getStyle('A'. ($x + 5 + $x_ramo_x_variedad_no_tinturado+1).':B'.($x + 5 + $x_ramo_x_variedad_no_tinturado+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('C' . ($x + 4))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('ffffff');
            $objSheet1->getStyle('A'. ($x + 5 + $x_ramo_x_variedad_no_tinturado+1))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getCell('A' . ($x + 5+ $x_ramo_x_variedad_no_tinturado+1))->setValue("Ramos Totales Pedidos");
            $objSheet1->getStyle('B'. ($x + 5 + $x_ramo_x_variedad_no_tinturado+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('B'. ($x + 5 + $x_ramo_x_variedad_no_tinturado+1))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getCell('B' . ($x + 5+ $x_ramo_x_variedad_no_tinturado+1))->setValue($ramos_totales_no_tinturados);

            $a = 1;
            foreach($cajas_equivalentes_no_tinturados as $caja_equivalente_no_tinturado){
                $objSheet1->mergeCells('E'.($x + 4 + $a).':F'.($x + 4 + $a));
                $objSheet1->getCell('E' . ($x + 4 + $a))->setValue(getVariedad($caja_equivalente_no_tinturado['id_variedad']->id_variedad)->nombre." (".getVariedad($caja_equivalente_no_tinturado['id_variedad']->id_variedad)->siglas.")");
                $objSheet1->getCell('G' . ($x + 4 + $a))->setValue($caja_equivalente_no_tinturado["cantidad"]);
                $a++;
            }
            $objSheet1->mergeCells('E'.($x + 4 + $a).':F'.($x + 4 + $a ));
            $objSheet1->getStyle('E'. ($x + 4 + $a))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('E'. ($x + 4 + $a))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getCell('E' . ($x + 4 + $a))->setValue("Cajas Equivalentes Totales Pedidas");
            $objSheet1->getStyle('G'. ($x + 4 + $a))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('357ca5');
            $objSheet1->getStyle('G'. ($x + 4 + $a))->getFont()->getColor()->applyFromArray( array('rgb' => 'ffffff'));
            $objSheet1->getCell('G' . ($x + 4 + $a))->setValue(round($ramos_totales_estandar_no_tinturados / getConfiguracionEmpresa()->ramos_x_caja,2));

            $objSheet1->getColumnDimension('A')->setWidth(20);
            $objSheet1->getColumnDimension('B')->setWidth(45);
            $objSheet1->getColumnDimension('C')->setWidth(10);
            $objSheet1->getColumnDimension('D')->setWidth(20);
            $objSheet1->getColumnDimension('E')->setWidth(35);
            $objSheet1->getColumnDimension('F')->setWidth(8);
            $objSheet1->getColumnDimension('G')->setWidth(10);
            $objSheet1->getColumnDimension('J')->setWidth(22);
            $objSheet1->getColumnDimension('H')->setWidth(10);
            $objSheet1->getColumnDimension('I')->setWidth(15);
            $objSheet1->getColumnDimension('M')->setWidth(15);
            $objSheet1->getColumnDimension('N')->setWidth(12);
            $objSheet1->getColumnDimension('L')->setWidth(15);
            $objSheet1->getColumnDimension('O')->setWidth(15);
            $objSheet1->getColumnDimension('P')->setWidth(13);
            $objSheet1->getColumnDimension('Q')->setWidth(20);
            $objSheet1->getColumnDimension('R')->setWidth(30);
            $objSheet1->getColumnDimension('S')->setWidth(25);
            $objSheet1->getStyle('A1:A2')->getFont()->setBold(true);
        }

    }
}
