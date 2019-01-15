<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\DetallePedido;
use yura\Modelos\AgenciaTransporte;
use yura\Modelos\Envio;
use yura\Modelos\DetalleEnvio;
use Validator;
use DB;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;
use PHPExcel_Style_Alignment;

class EnvioController extends Controller
{
    public function add_envio(Request $request){
        $dataDetallePedido = DetallePedido::where('detalle_pedido.id_pedido',$request->id_pedido);
        return view('adminlte.gestion.postcocecha.envios.forms.form_envio',
            [
                'cantForms'           => $dataDetallePedido->count(),
                'dataDetallesPedidos' => $dataDetallePedido->join('cliente_pedido_especificacion as cpe','detalle_pedido.id_cliente_especificacion','=','cpe.id_cliente_pedido_especificacion')
                                                           ->join('especificacion as e','cpe.id_especificacion','=','e.id_especificacion')
                                                           ->select('detalle_pedido.cantidad','detalle_pedido.id_detalle_pedido','e.nombre','cpe.id_especificacion','cpe.id_cliente')->get()

                ]);
    }

    public function add_form_envio(Request $request){

        return view('adminlte.gestion.postcocecha.envios.forms.inputs_detalles_envio',
            [
                'rows'=>$request->rows,
                'agencia_transporte' => AgenciaTransporte::all(),
                'cantidad'           => $request->cant_pedidos,
                'form'               => $request->id_form
            ]);
    }

    public function store_envio(Request $request){

        $valida = Validator::make($request->all(), [
            'arrData' => 'required|Array',
            'id_pedido' => 'required',
        ]);
        if (!$valida->fails()) {

            $existDataEnvio = Envio::where('id_pedido',$request->id_pedido);

            if($existDataEnvio->count() > 0){
                foreach($existDataEnvio->get() as $dataEnvio){
                    if(DetalleEnvio::where('id_envio',$dataEnvio->id_envio)->delete()){
                        Envio::destroy($dataEnvio->id_envio);
                    }
                }
            }
            foreach ($request->arrData as $key => $detalle_envio) {
                $existDataEnvio = DetalleEnvio::join('envio as e','detalle_envio.id_envio','=','e.id_envio')
                    ->join('pedido as p','e.id_pedido','=','p.id_pedido')
                    ->where([
                        //['id_especificaion',$detalle_envio[0]],
                        ['detalle_envio.id_agencia_transporte',$detalle_envio[1]],
                        ['e.fecha_envio',$detalle_envio[4]],
                        ['e.id_pedido',$request->id_pedido]
                    ])->first();

                    if($existDataEnvio == null){
                        $objEnvio = new Envio;
                        $objEnvio->id_pedido   = $request->id_pedido;
                        $objEnvio->fecha_envio = $detalle_envio[4];
                        if($objEnvio->save()){

                            $model = Envio::all()->last();
                            bitacora('envio', $model->id_envio, 'I', 'Inserción satisfactoria de un nuevo envio');
                            //foreach ($request->arrData as $detalle_envio) {
                            if (empty($request->id_detalle_envio)) {
                                $objDetalleEnvio = new DetalleEnvio;
                                $palabra = 'Inserción';
                                $accion = 'I';
                            } else {
                                $objDetalleEnvio = DetalleEnvio::find($model->id_envio);
                                $palabra = 'Actualización';
                                $accion = 'U';
                            }
                            $objDetalleEnvio->id_especificacion     = $detalle_envio[0];
                            $objDetalleEnvio->id_envio              =   $model->id_envio;
                            $objDetalleEnvio->id_agencia_transporte = $detalle_envio[1];
                            $objDetalleEnvio->cantidad              = $detalle_envio[2];
                            $objDetalleEnvio->envio                 = $detalle_envio[3];
                            $objDetalleEnvio->form                  = $detalle_envio[5];

                            if ($objDetalleEnvio->save()) {
                                $success = true;
                                $msg = '<div class="alert alert-success text-center">' .
                                    '<p> Se ha guardado el envio  exitosamente</p>'
                                    . '</div>';
                                bitacora('Detalle_envio', $model->id_marca, $accion, $palabra . ' satisfactoria de un nuevo envio');
                            } else {
                                $success = false;
                                $msg = '<div class="alert alert-warning text-center">' .
                                    '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                                    . '</div>';
                            }
                        }
                    }else{
                        if (empty($request->id_detalle_envio)) {
                            $objDetalleEnvio = new DetalleEnvio;
                            $palabra = 'Inserción';
                            $accion = 'I';
                        } else {
                            $objDetalleEnvio = DetalleEnvio::find($existDataEnvio->id_envio);
                            $palabra = 'Actualización';
                            $accion = 'U';
                        }
                        $objDetalleEnvio->id_especificacion      = $detalle_envio[0];
                        $objDetalleEnvio->id_envio               = $existDataEnvio->id_envio;
                        $objDetalleEnvio->id_agencia_transporte  = $detalle_envio[1];
                        $objDetalleEnvio->cantidad               = $detalle_envio[2];
                        $objDetalleEnvio->envio                  = $detalle_envio[3];
                        $objDetalleEnvio->form                   = $detalle_envio[5];

                        if ($objDetalleEnvio->save()) {
                            $success = true;
                            $msg = '<div class="alert alert-success text-center">' .
                                '<p> Se ha guardado el envio  exitosamente</p>'
                                . '</div>';
                           // bitacora('Detalle_envio', $existDataEnvio->id_marca, $accion, $palabra . ' satisfactoria de un nuevo envio');
                        } else {
                            $success = false;
                            $msg = '<div class="alert alert-warning text-center">' .
                                '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                                . '</div>';
                        }
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
            'success' => $success
        ];
    }

    public function ver_envio(Request $request){
        return view('adminlte.gestion.postcocecha.envios.inicio',
        [
            'annos'    => DB::table('envio as e')->select(DB::raw('YEAR(e.fecha_envio) as anno'))->distinct()->get(),
            'clientes' => DB::table('cliente as c')->join('detalle_cliente as dc', 'c.id_cliente','=','dc.id_cliente')
                          ->where('dc.estado',1)->get()
        ]);
    }

    public function buscar_envio(Request $request){

        $busquedaAnno    = $request->has('anno') ? $request->anno : '';
        $busquedacliente = $request->has('id_cliente') ? $request->id_cliente : '';
        $busquedaDesde   = $request->has('desde') ? $request->desde : '';
        $busquedaHasta   = $request->has('hasta') ? $request->hasta : '';
        $busquedaEsatdo  = $request->has('estado') ? $request->estado : '';

        $listado = DB::table('envio as e')
            ->join('detalle_envio as de','e.id_envio','=','de.id_envio')
            ->join('pedido as p', 'e.id_pedido','=','p.id_pedido')
            ->join('cliente as c','p.id_cliente','=','c.id_cliente')
            ->join('detalle_cliente as dc','c.id_cliente','=','dc.id_cliente' )
            ->join('especificacion as es','de.id_especificacion','es.id_especificacion')
            ->join('agencia_transporte as at','de.id_agencia_transporte','=','at.id_agencia_transporte')
            ->select('p.*','dc.nombre','e.*','de.*','es.nombre','at.nombre as at_nombre','at.tipo_agencia','dc.nombre as c_nombre')
            ->where('dc.estado',1);

        if ($busquedaAnno != '')
            $listado = $listado->where(DB::raw('YEAR(de.fecha_envio)'), $busquedaAnno );
        if ($busquedacliente != '')
            $listado = $listado->where('c.id_cliente',$busquedacliente);
        if ($busquedaDesde != '' && $request->hasta != '')
            $listado = $listado->whereBetween('e.fecha_envio', [$busquedaDesde,$busquedaHasta]);
        $busquedaEsatdo != '' ?  $listado = $listado->where('de.estado',$request->estado) : $listado = $listado->where('de.estado',0);

        $listado = $listado->orderBy('e.fecha_envio', 'desc')->distinct()->paginate(20);

        $datos = [
            'listado' => $listado,
            'idCliente' => $request->id_cliente,
        ];
       // dd($datos);
        return view('adminlte.gestion.postcocecha.envios.partials.listado',$datos);
    }

    public function generar_excel_envios(Request $request)
    {
        //---------------------- EXCEL --------------------------------------
        $objPHPExcel = new PHPExcel;
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $currencyFormat = '#,#0.## \€;[Red]-#,#0.## \€';
        $numberFormat = '#,#0.##;[Red]-#,#0.##';

        $objPHPExcel->removeSheetByIndex(0); //Eliminar la hoja inicial por defecto

        $this->excel_envios($objPHPExcel, $request);

        //--------------------------- GUARDAR EL EXCEL -----------------------

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="Reporte de envios.xlsx"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }

    public function excel_envios($objPHPExcel, $request)
    {
        $busquedaAnno    = $request->has('anno') ? $request->anno : '';
        $busquedacliente = $request->has('id_cliente') ? $request->id_cliente : '';
        $busquedaDesde   = $request->has('desde') ? $request->desde : '';
        $busquedaHasta   = $request->has('hasta') ? $request->hasta : '';
        $busquedaEsatdo  = $request->has('estado') ? $request->estado : '';

        $listado = DB::table('envio as e')
            ->join('detalle_envio as de','e.id_envio','=','de.id_envio')
            ->join('pedido as p', 'e.id_pedido','=','p.id_pedido')
            ->join('cliente as c','p.id_cliente','=','c.id_cliente')
            ->join('detalle_cliente as dc','c.id_cliente','=','dc.id_cliente' )
            ->join('especificacion as es','de.id_especificacion','es.id_especificacion')
            ->join('agencia_transporte as at','de.id_agencia_transporte','=','at.id_agencia_transporte')
            ->select('p.*','dc.nombre','e.*','de.*','es.nombre','at.nombre as at_nombre','at.tipo_agencia','dc.nombre as c_nombre')
            ->where('dc.estado',1);

        if ($busquedaAnno != '')
            $listado = $listado->where(DB::raw('YEAR(de.fecha_envio)'), $busquedaAnno );
        if ($busquedacliente != '')
            $listado = $listado->where('c.id_cliente',$busquedacliente);
        if ($busquedaDesde != '' && $request->hasta != '')
            $listado = $listado->whereBetween('de.fecha_envio', [$busquedaDesde,$busquedaHasta]);
        if($busquedaEsatdo != '')
            $listado = $listado->where('de.estado',$request->estado);

        $listado = $listado->orderBy('e.fecha_envio', 'desc')->distinct()->get(20);
      //  dd($listado);

        if (count($listado) > 0) {
            $objSheet = new PHPExcel_Worksheet($objPHPExcel, 'Envío');
            $objPHPExcel->addSheet($objSheet, 0);

            $objSheet->mergeCells('A1:E1');
            $objSheet->getStyle('A1:E1')->getFont()->setBold(true)->setSize(12);
            $objSheet->getStyle('A1:E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle('A1:E1')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CCFFCC');

            $objSheet->getCell('A1')->setValue('Listado Envíos');

            $objSheet->getCell('A3')->setValue('Fecha de envío ');
            $objSheet->getCell('B3')->setValue('Cantidad / Especificaciones');
            $objSheet->getCell('C3')->setValue('Cliente');
            $objSheet->getCell('D3')->setValue('Agencia de Transporte');
            $objSheet->getCell('E3')->setValue('Tipo de agencia');


            $objSheet->getStyle('A3:E3')->getFont()->setBold(true)->setSize(12);

            $objSheet->getStyle('A3:E3')
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                ->getColor()
                ->setRGB(PHPExcel_Style_Color::COLOR_BLACK);

            $objSheet->getStyle('A3:E3')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CCFFCC');

            //--------------------------- LLENAR LA TABLA ---------------------------------------------
            for ($i = 0; $i < sizeof($listado); $i++) {

                $objSheet->getStyle('A' . ($i + 4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $objSheet->getStyle('B' . ($i + 4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $objSheet->getStyle('C' . ($i + 4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $objSheet->getStyle('D' . ($i + 4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $objSheet->getStyle('E' . ($i + 4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $objSheet->getCell('A' . ($i + 4))->setValue($listado[$i]->fecha_envio);
                $objSheet->getCell('B' . ($i + 4))->setValue($listado[$i]->cantidad." ".$listado[$i]->nombre);
                $objSheet->getCell('C' . ($i + 4))->setValue($listado[$i]->c_nombre);
                $objSheet->getCell('D' . ($i + 4))->setValue($listado[$i]->at_nombre);
                if($listado[$i]->tipo_agencia == 'T')
                    $agencia = "TERRESTRE";
                if($listado[$i]->tipo_agencia == 'A')
                    $agencia = "AÉREA";
                if($listado[$i]->tipo_agencia == 'M')
                    $agencia = "MARÍTIMA";
                $objSheet->getCell('E' . ($i + 4))->setValue($agencia);
            }

            $objSheet->getColumnDimension('A')->setAutoSize(true);
            $objSheet->getColumnDimension('B')->setAutoSize(true);
            $objSheet->getColumnDimension('C')->setAutoSize(true);
            $objSheet->getColumnDimension('D')->setAutoSize(true);
            $objSheet->getColumnDimension('E')->setAutoSize(true);

        } else {
            return '<div>No se han encontrado coincidencias para exportar</div>';
        }
    }

    public function editar_envio(Request $request){

        $dataDetallePedido = DetallePedido::where('detalle_pedido.id_pedido',$request->id_pedido);
        $detalleEnvio = Envio::where('id_pedido',$request->id_pedido)->join('detalle_envio as de','envio.id_envio','=','de.id_envio');
        $cant_rows = $detalleEnvio->count();

        return view('adminlte.gestion.postcocecha.envios.forms.form_edit_envio',
        [
            'cantForms'          => $dataDetallePedido->count(),
            'dataDetallesPedidos'=> $dataDetallePedido->join('cliente_pedido_especificacion as cpe','detalle_pedido.id_cliente_especificacion','=','cpe.id_cliente_pedido_especificacion')
                                                      ->join('especificacion as e','cpe.id_especificacion','=','e.id_especificacion')
                                                      ->select('detalle_pedido.cantidad','detalle_pedido.id_detalle_pedido','e.nombre','cpe.id_especificacion','cpe.id_cliente')->get(),
            'dataDetalleEnvio' => $detalleEnvio->get(),
            'agencia_transporte' => AgenciaTransporte::all(),
            'id_detalle_envio' => $request->id_detalle_envio,
            'cant_detalles' =>  $detalleEnvio->distinct()->count(),
            'fechaEnvio' => $detalleEnvio->select('fecha_envio')->get(),
            'cant_rows' => $cant_rows
        ]);
    }
}
