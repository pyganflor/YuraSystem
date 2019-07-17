<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;
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
            'unitarias' => getUnitarias(),
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
                ->get();

            $ids_pedidos = [];
            foreach ($listado as $item) {
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
                'empresa' => getConfiguracionEmpresa(),
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
            'data_despacho.*.id_cuarto_frio' => 'required',
            'data_despacho.*.id_guardia_turno' => 'required',
            'data_despacho.*.id_oficina_despacho' => 'required',
            'data_despacho.*.id_transportista' => 'required',
            'data_despacho.*.n_placa' => 'required',
            'data_despacho.*.n_viaje' => 'required',
            'data_despacho.*.nombre_asist_comercial' => 'required',
            'data_despacho.*.nombre_cuarto_frio' => 'required',
            'data_despacho.*.nombre_guardia_turno' => 'required',
            'data_despacho.*.nombre_oficina_despacho' => 'required',
            'data_despacho.*.nombre_transportista' => 'required',
            //'data_despacho.*.arr_sellos' => 'required|Array',
            'data_despacho.*.semana' => 'required',
            'data_despacho.*.correo_oficina_despacho'  => 'required'
        ],[
            'data_despacho.*.fecha_despacho.required' => 'Debe colocar la fecha de despacho para el camión',
            'data_despacho.*.id_camion.required' => 'Debe seleccionar el camión',
            'data_despacho.*.n_placa.required' =>  'Debe escribir la placa del camión',
            'data_despacho.*.semana.required' => 'Debe escribir la semana',
            'data_despacho.*.correo_oficina_despacho.required' => 'Debe escribir el correo de la persona de la oficina de despacho',
            'data_despacho.*.nombre_transportista.required' => 'Debe escribir el nombre del transportista',
            'data_despacho.*.nombre_oficina_despacho.required' => 'Debe escribir el nombre de la persona de la oficina de despacho',
            'data_despacho.*.nombre_guardia_turno.required' => 'Debe escribir el nombre del guardia de turno',
            'data_despacho.*.id_guardia_turno.required' => 'Debe escribir la identificación del guardia de turno',
            'data_despacho.*.nombre_cuarto_frio.required' => 'Debe escribir el nombre de la persona del cuarto frio',
            'data_despacho.*.id_cuarto_frio.required' => 'Debe escribir la identificación de la persona del cuarto frio',
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
               // $objDespacho->id_resp_transporte = $despacho['firma_id_transportista'];
                $objDespacho->mail_resp_ofi_despacho = $despacho['correo_oficina_despacho'];
                $objDespacho->n_despacho = getSecuenciaDespacho();

                if ($objDespacho->save()) {
                    $modelDespacho = Despacho::all()->last();
                    bitacora('despacho', $modelDespacho->id_despacho, 'I', 'Inserción satisfactoria de un nuevo despacho');
                    $distribucion = explode(";",$distribucion);

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
                        'empresa' => getConfiguracionEmpresa(),
                        'despacho' => Despacho::where('n_despacho',(getSecuenciaDespacho()-1))
                            ->join('detalle_despacho as dd','despacho.id_despacho','dd.id_despacho')->get()
                    ];
                    PDF::loadView('adminlte.gestion.postcocecha.despachos.partials.pdf_despacho', compact('data'))->setPaper('a4', 'landscape')
                        ->save(env('PATH_PDF_DESPACHOS') . str_pad((getSecuenciaDespacho()-1), 9, "0", STR_PAD_LEFT) . ".pdf");
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
        $data = [
            'empresa' => getConfiguracionEmpresa(),
            'despacho' => Despacho::where('n_despacho',$n_despacho)
                ->join('detalle_despacho as dd','despacho.id_despacho','dd.id_despacho')->get()
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
        $pedidos = Pedido::where([['fecha_pedido',$request->fecha_pedido],['pedido.estado',1],['dc.estado',1]])
            ->join('cliente as c','pedido.id_cliente','c.id_cliente')->join('detalle_cliente as dc','c.id_cliente','dc.id_cliente')
            ->orderBy('dc.nombre','asc')->select('id_pedido')->get();

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
        $estilo = array(
            'borders' => array(
                'outline' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            )
        );

        $objSheet1->getActiveSheet()->getStyle('A19:I19')->applyFromArray($estilo);
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

            if($p->tipo_especificacion === "N"){

                $count_pedido_tinturado =false;
                $ids_pedidos_no_tinturados[] = $p->id_pedido;
                foreach ($p->detalles as $det => $det_ped) {
                    $datos_exportacion = '';
                    if (getDatosExportacionByDetPed($det_ped->id_detalle_pedido)->count() > 0)
                        foreach (getDatosExportacionByDetPed($det_ped->id_detalle_pedido) as $dE)
                            $datos_exportacion .= $dE->valor . "-";
                    if($det == 0) $inicio_a = $x+1;
                    $final_a = getCantidadDetallesEspecificacionByPedido($pedido->id_pedido) + $inicio_a - 1;
                    foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $sp => $esp_emp) {
                        foreach ($esp_emp->detalles as $det_sp => $det_esp_emp) {
                            if($sp == 0 && $det_sp == 0) {
                                $inicio_b =$x+1;
                                $piezas_totales_no_tinturados += ($esp_emp->cantidad * $det_ped->cantidad);
                            }
                            $final_b =getCantidadDetallesByEspecificacion($det_ped->cliente_especificacion->id_especificacion) + $inicio_b - 1 ;
                            if($det_sp == 0){
                                $inicio_d =$x+1;
                                $cajas_full_totales_no_tinturados += ($esp_emp->cantidad * $det_ped->cantidad) * explode('|',$esp_emp->empaque->nombre)[1];
                            }
                            $final_d = count($esp_emp->detalles) + $inicio_d -1;
                            $objSheet1->mergeCells('A'.$inicio_a.':A'.$final_a);
                            $objSheet1->mergeCells('B'.$inicio_b.':B'.$final_b);
                            $objSheet1->mergeCells('D'.$inicio_d.':D'.$final_d);
                            $objSheet1->mergeCells('F'.$inicio_b.':F'.$final_b);
                            $objSheet1->mergeCells('G'.$inicio_d.':G'.$final_d);
                            $objSheet1->getCell('A' . ($x + 1))->setValue($p->cliente->detalle()->nombre);
                            $objSheet1->getCell('B' . ($x + 1))->setValue((!$datos_exportacion) ? "No posee" : substr($datos_exportacion,0,-1));
                            $objSheet1->getCell('C' . ($x + 1))->setValue($det_esp_emp->variedad->siglas." ".explode('|',$det_esp_emp->clasificacion_ramo->nombre)[0]." ".$det_esp_emp->clasificacion_ramo->unidad_medida->siglas);
                            $objSheet1->getCell('D' . ($x + 1))->setValue(explode("|",$esp_emp->empaque->nombre)[0]);
                            $objSheet1->getCell('E' . ($x + 1))->setValue(explode('|',$det_esp_emp->empaque_p->nombre)[0]);
                            $objSheet1->getCell('F' . ($x + 1))->setValue($esp_emp->cantidad * $det_ped->cantidad);
                            $objSheet1->getCell('G' . ($x + 1))->setValue(($esp_emp->cantidad * $det_ped->cantidad) * explode('|',$esp_emp->empaque->nombre)[1]);
                            $ramos_totales_no_tinturados += $det_esp_emp->cantidad * $esp_emp->cantidad * $det_ped->cantidad;
                            $ramos_totales_estandar_no_tinturados += convertToEstandar($det_esp_emp->cantidad * $esp_emp->cantidad * $det_ped->cantidad, $det_esp_emp->clasificacion_ramo->nombre);
                            if (!in_array($det_esp_emp->id_variedad, $variedades_no_tinturados)){
                                $variedades_no_tinturados[] = $det_esp_emp->id_variedad;
                            }
                            $ramos_x_variedades_no_tinturados[] = [
                                'id_variedad' => $det_esp_emp->id_variedad,
                                'cantidad' => convertToEstandar($det_esp_emp->cantidad * $esp_emp->cantidad * $det_ped->cantidad, $det_esp_emp->clasificacion_ramo->nombre),
                            ];

                            $objSheet1->getCell('H' . ($x + 1))->setValue($det_esp_emp->cantidad * $esp_emp->cantidad * $det_ped->cantidad);
                            $objSheet1->getCell('I' . ($x + 1))->setValue($det_esp_emp->cantidad);
                            $x++;
                        }
                    }
                }

            }else if($p->tipo_especificacion === "T") {
                $count_pedido_tinturado =true;
                $ids_pedidos_tinturados[] = $p->id_pedido;
                foreach ($p->detalles as $det_tinturado => $det_ped) {

                    $datos_exportacion = '';
                    if (getDatosExportacionByDetPed($det_ped->id_detalle_pedido)->count() > 0)
                        foreach (getDatosExportacionByDetPed($det_ped->id_detalle_pedido) as $dE)
                            $datos_exportacion .= $dE->valor . "-";

                        foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $sp_t => $esp_emp_t) {
                            foreach ($esp_emp_t->detalles as $det_sp_t => $det_esp_emp_t) {
                                $ramos_totales_tinturados += $det_esp_emp_t->cantidad * $esp_emp_t->cantidad * $det_ped->cantidad;
                                if($det_sp_t == 0) $cajas_full_totales_tinturados += ($esp_emp_t->cantidad * $det_ped->cantidad) * explode('|',$esp_emp_t->empaque->nombre)[1];
                                if ($sp_t == 0 && $det_sp_t == 0) $piezas_totales_tinturados += ($esp_emp_t->cantidad * $det_ped->cantidad);
                                $ramos_totales_estandar_tinturados += convertToEstandar($det_esp_emp_t->cantidad * $esp_emp_t->cantidad * $det_ped->cantidad, $det_esp_emp_t->clasificacion_ramo->nombre);

                                if (!in_array($det_esp_emp_t->id_variedad, $variedades_tinturados)){
                                    $variedades_tinturados[] = $det_esp_emp_t->id_variedad;
                                }
                                $ramos_x_variedades_tinturados[] = [
                                    'id_variedad' => $det_esp_emp_t->id_variedad,
                                    'cantidad' => convertToEstandar($det_esp_emp_t->cantidad * $esp_emp_t->cantidad * $det_ped->cantidad, $det_esp_emp_t->clasificacion_ramo->nombre),
                                ];
                            }
                        }


                    $final_tinturado_a = 0;
                    foreach ($det_ped->coloraciones as $col =>$coloracion) {
                        $total_cantidad_mc = 0;
                        foreach($coloracion->marcaciones_coloraciones as $mar_col => $m_c){
                            $det_esp_emp_tinturado = $m_c->detalle_especificacionempaque;
                            $objSheet->getCell('A' . ($w + 1))->setValue($p->cliente->detalle()->nombre);
                            $objSheet->getCell('B' . ($w + 1))->setValue($datos_exportacion == "" ? "No posee " : substr($datos_exportacion,0,-1));
                            $objSheet->getCell('C' . ($w + 1))->setValue($det_esp_emp_tinturado->variedad->siglas." ".explode('|',$det_esp_emp_tinturado->clasificacion_ramo->nombre)[0]." ".$det_esp_emp_tinturado->clasificacion_ramo->unidad_medida->siglas);
                            $objSheet->getCell('D' . ($w + 1))->setValue(explode("|",$det_esp_emp_tinturado->especificacion_empaque->empaque->nombre)[0]);
                            $objSheet->getCell('E' . ($w + 1))->setValue(explode('|',$det_esp_emp_tinturado->empaque_p->nombre)[0]);

                            $final_tinturado_a++;
                            if($count_pedido_tinturado) {
                                $inicio_tinturado_a = $w+1;
                                $count_pedido_tinturado = false;
                            }
                            if($mar_col == 0) $inicio_tinturado_d = $w+1;
                            $final_tinturado_d = count($coloracion->marcaciones_coloraciones);
                            foreach($coloracion->marcaciones_coloraciones as $mar_col => $m_c) $total_cantidad_mc +=  $m_c->cantidad;
                            $objSheet->mergeCells('F'.$inicio_tinturado_d.':F'.($final_tinturado_d + $inicio_tinturado_d-1));
                            $objSheet->getCell('F' . ($w + 1))->setValue($total_cantidad_mc);
                            $objSheet->mergeCells('C'.$inicio_tinturado_d.':C'.($final_tinturado_d + $inicio_tinturado_d-1));
                            $objSheet->mergeCells('D'.$inicio_tinturado_d.':D'.($final_tinturado_d + $inicio_tinturado_d-1));
                            $objSheet->mergeCells('E'.$inicio_tinturado_d.':E'.($final_tinturado_d + $inicio_tinturado_d-1));
                            $objSheet->mergeCells('G'.$inicio_tinturado_d.':G'.($final_tinturado_d + $inicio_tinturado_d-1));
                            $objSheet->getCell('G' . ($w + 1))->setValue($coloracion->color->nombre);
                            $objSheet->getStyle('G'.$inicio_tinturado_d.':G'.($final_tinturado_d + $inicio_tinturado_d-1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($coloracion->color->fondo,1));
                            $objSheet->getStyle('G'. ($w + 1))->getFont()->getColor()->applyFromArray( array('rgb' => substr($coloracion->color->texto,1)));
                            $w++;
                            $objSheet->getColumnDimension('A')->setWidth(20);
                            $objSheet->getColumnDimension('B')->setWidth(20);
                            $objSheet->getColumnDimension('C')->setWidth(20);
                            $objSheet->getColumnDimension('D')->setWidth(20);
                            $objSheet->getColumnDimension('E')->setWidth(20);
                            $objSheet->getColumnDimension('F')->setWidth(20);
                            $objSheet->getColumnDimension('G')->setWidth(20);


                        }
                    }
                    $objSheet->mergeCells('A'.$inicio_tinturado_a.':A'.($final_tinturado_a + $inicio_tinturado_a-1));
                    $objSheet->mergeCells('B'.$inicio_tinturado_a.':B'.($final_tinturado_a + $inicio_tinturado_a-1));


                }
                $objSheet->getStyle('A1:I1')->getFont()->setBold(true);
            }
        }


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
            $objSheet1->getStyle('A1:I1')->getFont()->setBold(true);
        }

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
                $objSheet->mergeCells('A'.($w + 5 + $w_ramo_x_variedad_tinturado).':B'.($w + 5 + $w_ramo_x_variedad_tinturado));
                $objSheet->getCell('A' . ($w + 5 + $w_ramo_x_variedad_tinturado))->setValue(getVariedad($ramo_x_variedad_tinutrado->id_variedad)->siglas." ".
                    explode('|',getCalibreRamoById($ramo_x_variedad_tinutrado->id_clasificacion_ramo)->nombre)[0]."".
                    getCalibreRamoById($ramo_x_variedad_tinutrado->id_clasificacion_ramo)->unidad_medida->siglas. " ". ($ramo_x_variedad_tinutrado->tallos_x_ramos != '' ? $ramo_x_variedad_tinutrado->tallos_x_ramos." tallos " : "") .
                    ($ramo_x_variedad_tinutrado->longitud_ramo != '' ?  $ramo_x_variedad_tinutrado->longitud_ramo." ".getUnidadMedida($ramo_x_variedad_tinutrado->id_unidad_medida)->siglas : "" ));
                $objSheet->getCell('C' . ($w + 5 + $w_ramo_x_variedad_tinturado))->setValue($ramo_x_variedad_tinutrado->cantidad);
                $objSheet->mergeCells('A'.($w + 5 + $w_ramo_x_variedad_tinturado+1).':B'.($w + 5 + $w_ramo_x_variedad_tinturado+1));
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
        $style = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
        );
        $objSheet->getDefaultStyle()->applyFromArray($style);
        $objSheet1->getDefaultStyle()->applyFromArray($style);

    }
}
