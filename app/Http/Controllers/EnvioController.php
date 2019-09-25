<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Modelos\ClienteConsignatario;
use yura\Modelos\ConfiguracionEmpresa;
use yura\Modelos\DetallePedido;
use yura\Modelos\Aerolinea;
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
use yura\Modelos\FacturaClienteTercero;
use yura\Modelos\Impuesto;
use yura\Modelos\Marca;
use yura\Modelos\Pais;
use yura\Modelos\Pedido;
use Carbon\Carbon;
use yura\Modelos\DatosExportacion;
use yura\Modelos\Submenu;
use yura\Modelos\Rol;
use yura\Modelos\TipoIdentificacion;
use yura\Modelos\TipoImpuesto;

class EnvioController extends Controller
{
    public function add_envio(Request $request){
        $dataDetallePedido = DetallePedido::where('detalle_pedido.id_pedido',$request->id_pedido);
        return view('adminlte.gestion.postcocecha.envios.forms.form_envio',
            [
                'cantForms'           => $dataDetallePedido->count(),
                'dataDetallesPedidos' => $dataDetallePedido->join('cliente_pedido_especificacion as cpe','detalle_pedido.id_cliente_especificacion','=','cpe.id_cliente_pedido_especificacion')
                    ->join('especificacion as e','cpe.id_especificacion','=','e.id_especificacion')
                    ->select('detalle_pedido.cantidad','detalle_pedido.id_detalle_pedido','e.nombre','cpe.id_especificacion','cpe.id_cliente')->get(),
                ]);
    }

    public function add_form_envio(Request $request){

        return view('adminlte.gestion.postcocecha.envios.forms.inputs_detalles_envio',
            [
                'rows'=>$request->rows,
                'aerolineas' => Aerolinea::all(),
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
                        ['detalle_envio.id_aerolinea',$detalle_envio[1]],
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
                            $objDetalleEnvio->id_envio              = $model->id_envio;
                            $objDetalleEnvio->id_aerolinea          = $detalle_envio[1];
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
                        $objDetalleEnvio->id_aerolinea           = $detalle_envio[1];
                        $objDetalleEnvio->cantidad               = $detalle_envio[2];
                        $objDetalleEnvio->envio                  = $detalle_envio[3];
                        $objDetalleEnvio->form                   = $detalle_envio[5];

                        if ($objDetalleEnvio->save()) {
                            $success = true;
                            $msg = '<div class="alert alert-success text-center">' .
                                '<p> Se ha guardado el envio  exitosamente</p>'
                                . '</div>';
                           // bitacora('detalle_envio', $existDataEnvio->id_marca, $accion, $palabra . ' satisfactoria de un nuevo envio');
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
                          ->where('dc.estado',1)->get(),
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'roles' => Rol::All(),
            'text' => ['titulo'=>'Envíos de pedidos','subtitulo'=>'módulo de postcosecha']
        ]);
    }

    public function buscar_envio(Request $request){
        $cliente = $request->has('id_cliente') ? $request->id_cliente : '';
        $fecha   = $request->has('fecha') ? $request->fecha : '';
        $estado  = $request->has('estado') ? $request->estado : '';
        $listado = Envio::where([
            ['dc.estado',1],
            ['envio.estado',$estado != "" ? $estado : 0],
            ['envio.fecha_envio',$fecha != "" ? $fecha : Carbon::now()->toDateString()],
            ['p.estado',1]
        ])->join('pedido as p', 'envio.id_pedido','=','p.id_pedido')
            ->join('detalle_cliente as dc','p.id_cliente','=','dc.id_cliente' )
            ->join('impuesto as i','dc.codigo_impuesto','i.codigo')
            ->join('tipo_impuesto as ti','dc.codigo_porcentaje_impuesto','ti.codigo')->orderBy('envio.id_envio','Desc')
            ->select('p.*','envio.*','i.nombre as nombre_impuesto','ti.porcentaje','dc.nombre','dc.direccion as direccion_cliente','dc.almacen as almacen_cliente','dc.provincia','dc.codigo_pais as pais_cliente','dc.telefono as telefono_cliente','dc.correo');

        if ($cliente != '')
            $listado = $listado->where('dc.id_cliente',$cliente);

        return view('adminlte.gestion.postcocecha.envios.partials.listado',[
            'envios' => $listado->paginate(10),
            'paises'    => Pais::all(),
            'aerolineas' => Aerolinea::where('estado',1)->get(),
            'vista' => $request->path(),
             'consignatarios' => []/*ClienteConsignatario::where('id_cliente',$pedido->id_cliente)
            ->join('consignatario as c', 'cliente_consignatario.id_consignatario','c.id_consignatario')->get()*/
        ]);
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
            ->join('aerolineas as a','de.id_aerolinea','=','a.id_aerolinea')
            ->select('p.*','dc.nombre','e.*','de.*','es.nombre','a.nombre as a_nombre','a.tipo_agencia','dc.nombre as c_nombre')
            ->where('dc.estado',1);

        if ($busquedaAnno != '')
            $listado = $listado->where(DB::raw('YEAR(de.fecha_envio)'), $busquedaAnno );
        if ($busquedacliente != '')
            $listado = $listado->where('c.id_cliente',$busquedacliente);
        if ($busquedaDesde != '' && $request->hasta != '')
            $listado = $listado->whereBetween('de.fecha_envio', [$busquedaDesde,$busquedaHasta]);
        if($busquedaEsatdo != '')
            $listado = $listado->where('e.estado',$request->estado);

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
            $objSheet->getCell('D3')->setValue('Aerolinea');
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
            'aerolineas' => Aerolinea::all(),
            'id_detalle_envio' => $request->id_detalle_envio,
            'cant_detalles' =>  $detalleEnvio->distinct()->count(),
            'fechaEnvio' => $detalleEnvio->select('fecha_envio')->get(),
            'cant_rows' => $cant_rows
        ]);
    }

    public function ver_envios(Request $request){
        $dataPedido = Pedido::where('pedido.id_pedido',$request->id_pedido)->select('id_cliente','id_pedido')->first();
        return view('adminlte.gestion.postcocecha.pedidos.partials.desglose_envios_pedido',[
            'id_pedido' => $dataPedido->id_pedido,
            'datos_exportacion' => DatosExportacion::join('cliente_datoexportacion as cde','dato_exportacion.id_dato_exportacion','cde.id_dato_exportacion')
            ->where('id_cliente',$dataPedido->id_cliente)->get(),
            'agenciasCarga' => DB::table('cliente_agenciacarga as cac')
                ->join('agencia_carga as ac', 'cac.id_agencia_carga', 'ac.id_agencia_carga')
                ->where([
                    ['cac.id_cliente', $dataPedido->id_cliente],
                    ['cac.estado', 1]
                ])->get(),
        ]);
    }

    public function actualizar_envio(Request $request)
    {
        $valida = Validator::make($request->all(), [
            'codigo_pais' => 'required',
            'email' => 'required',
            'telefono' => 'required',
            'direccion' => 'required',
            'fecha_envio' => 'required',
        ],[
            'email.required' => 'Debe colocar el email del cliente',
            'fecha_envio.required' => 'Debe colocar la fecha del pedido',
        ]);

        if (!$valida->fails()) {

            $objEnvio = Envio::find($request->id_envio);
            $objEnvio->dae = $request->dae;
            $objEnvio->guia_madre = $request->guia_madre;
            $objEnvio->guia_hija = $request->guia_hija;
            $objEnvio->codigo_pais = $request->codigo_pais;
            $objEnvio->email = $request->email;
            $objEnvio->telefono = $request->telefono;
            $objEnvio->direccion = $request->direccion;
            $objEnvio->fecha_envio = $request->fecha_envio;
            $objEnvio->almacen = $request->almacen;
            $objEnvio->codigo_dae = $request->codigo_dae;
            $objEnvio->id_consignatario = $request->consignatario;

            if($objEnvio->save()){
                bitacora('envio', $request->id_envio, 'U', 'Actualización satisfactoria del envío');
                $save_det_env = DetalleEnvio::where('id_envio',$request->id_envio)->update(['id_aerolinea' => $request->aerolinea]);

                if($save_det_env) {
                    if ($request->tipo_pedido === "N") { //PEDIDO NO TINTURADO
                        /*$dataDetallePedido = Envio::where('id_envio',$request->id_envio)->select('id_pedido')
                             ->join('detalle_pedido as dp','envio.id_pedido','dp.id_pedido')->select('id_detalle_pedido')->get();

                         foreach ($dataDetallePedido as $key => $detallePedido) {
                             $objDetallePedido = DetallePedido::where('id_detalle_pedido', $detallePedido->id_detalle_pedido);
                             $objDetallePedido->update([
                                 'precio' => substr($request->precios[$key]['precios'], 0, -1),
                                 'cantidad' => $request->precios[$key]['piezas'],
                             ]);
                             if ($objDetallePedido) {
                                 $modelDetallePedido = DetallePedido::all()->last();
                                 bitacora('detalle_pedido', $modelDetallePedido->id_detalle_pedido, 'U', 'Actualización del precio del detalle del pedido ' . $detallePedido->id_pedido . '');
                             }
                         }*/
                    }
                }
                $confirmar = Pedido::find(getPedido(getEnvio($request->id_envio)->pedido->id_pedido)->id_pedido);
                $confirmar->update(['confirmado'=> 1]);
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                    '<p> Se han actualizado exitosamente los datos del envío</p>'
                    . '</div>';
            }else{
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al actualizar la información del envío</p>'
                    . '</div>';
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

    public function factura_cliente_tercero(Request $request){
        return view('adminlte.gestion.postcocecha.envios.forms.factura_cliente_tercero',[
            'dataCliente' => FacturaClienteTercero::where('id_envio',$request->id_envio)->first(),
            'dataTipoIdentificacion' => TipoIdentificacion::all(),
            'impuestos' => Impuesto::all(),
            'tipoImpuestos' => TipoImpuesto::all(),
            'dataPais' => Pais::all(),
            'facturado' =>  getFacturado($request->id_envio,5),
            'marcas' => Marca::all()
        ]);
    }

    public function store_datos_factura_cliente_tercero(Request $request){

        $valida = Validator::make($request->all(), [
            'id_envio'            => 'required',
            'nombre'              => 'required',
            'tipo_identificacion' => 'required',
            'codigo_pais'         => 'required',
            'provincia'           => 'required',
            'correo'              => 'required',
            'telefono'            => 'required',
            'direccion'           => 'required',
            'codigo_impuesto'     => 'required',
            'tipo_identificacion' => 'required',
            'codigo_impuesto'     => 'required',
            'codigo_impuesto_porcentaje' => 'required',
            'puerto_entrada'      => 'required',
            'tipo_credito'        => 'required',
            'marca'               => 'required',
        ]);

        $msg= '';
        if(!$valida->fails()) {
            $objFacturaClienteTercero = empty($request->id_factura_cliente_tercero)
                ? new FacturaClienteTercero
                : FacturaClienteTercero::find($request->id_factura_cliente_tercero);

            $objFacturaClienteTercero->id_envio                   = $request->id_envio;
            $objFacturaClienteTercero->nombre_cliente_tercero     = $request->nombre;
            $objFacturaClienteTercero->identificacion             = $request->identificacion;
            $objFacturaClienteTercero->codigo_pais                = $request->codigo_pais;
            $objFacturaClienteTercero->provincia                  = $request->provincia;
            $objFacturaClienteTercero->correo                     = $request->correo;
            $objFacturaClienteTercero->telefono                   = $request->telefono;
            $objFacturaClienteTercero->direccion                  = $request->direccion;
            $objFacturaClienteTercero->codigo_impuesto            = $request->codigo_impuesto;
            $objFacturaClienteTercero->codigo_identificacion      = $request->tipo_identificacion;
            $objFacturaClienteTercero->codigo_impuesto_porcentaje = $request->codigo_impuesto_porcentaje;
            $objFacturaClienteTercero->almacen                    = $request->almacen;
            $objFacturaClienteTercero->dae                        = $request->dae;
            $objFacturaClienteTercero->puerto_entrada             = $request->puerto_entrada;
            $objFacturaClienteTercero->tipo_credito               = $request->tipo_credito;
            $objFacturaClienteTercero->id_marca                   = $request->marca;
            $objFacturaClienteTercero->codigo_dae                 = $request->codigo_dae;

            if($objFacturaClienteTercero->save()){
                $model= FacturaClienteTercero::all()->last();
                $success = true;
                $msg .= '<div class="alert alert-success text-center">' .
                    '<p> Se han guardado los datos de la persona a facturar '. $objFacturaClienteTercero->nombre .'  exitosamente</p>'
                    . '</div>';
                bitacora('factura_cliente_tercero', $model->id_factura_cliente_tercero, 'I', 'Inserción satisfactoria de una persona a facturar');
            }else {
                $success = false;
                $msg .= '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información al sistema</pyura_db@servidor_amazon>'
                    . '</div>';
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

    public function delete_datos_factura_cliente_tercero(Request $request){
        $msg = "";
        $objFacturaClienteTercero = FacturaClienteTercero::where('id_envio',$request->id_envio)->delete();
        if($objFacturaClienteTercero){
            $success = true;
            $msg .= '<div class="alert alert-success text-center">' .
                '<p> Se han eliminado los datos de la persona a facturar exitosamente</p>'
                . '</div>';
        }else{
            $success = false;
            $msg .= '<div class="alert alert-warning text-center">' .
                '<p> Ha ocurrido un problema al elimninar la información al sistema</p>'
                . '</div>';
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function agregar_correo(Request $request){
        return view('adminlte.gestion.postcocecha.envios.partials.input_correo',[
            'cant_inputs' => $request->cant_input
        ]);
    }

    public function buscar_codigo_dae(Request $request){

        $mes =Carbon::parse($request->fecha_envio)->format('m');
        $anno = Carbon::parse($request->fecha_envio)->format('Y');
        $ultimo_dia_mes = Carbon::parse($request->fecha_envio)->endOfMonth()->toDateString();
        if($ultimo_dia_mes == Carbon::parse($request->fecha_envio)->toDateString()) {
            $mes = Carbon::parse($request->fecha_envio)->addMonth()->format('m');
            $anno = Carbon::parse($request->fecha_envio)->addMonth()->format('Y');
        }
    dd($request->id_envio);
    $dae = getCodigoDae($request->codigo_pais,$mes,$anno,getEnvio($request->id_envio)->pedido->id_configuracion_empresa);
    return response()->json([
        'dae' => isset($dae->dae) ? $dae->dae : "",
        'codigo_dae' => isset($dae->codigo_dae) ? $dae->codigo_dae : "",
        'codigo_empresa' => ConfiguracionEmpresa::select('codigo_pais')->first()->codigo_pais
    ]);
}

}
