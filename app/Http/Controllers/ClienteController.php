<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use yura\Jobs\ProyeccionVentaSemanalUpdate;
use yura\Modelos\ClienteConsignatario;
use yura\Modelos\Submenu;
use yura\Modelos\Pais;
use yura\Modelos\Cliente;
use yura\Modelos\Pedido;
use yura\Modelos\Contacto;
use yura\Modelos\AgenciaCarga;
use yura\Modelos\ClienteAgenciaCarga;
use yura\Modelos\DetalleCliente;
use yura\Modelos\DetalleClienteContacto;
use yura\Modelos\TipoImpuesto;
use yura\Modelos\TipoIdentificacion;
use yura\Modelos\Impuesto;
use yura\Modelos\Semana;
use yura\Modelos\Marca;
use yura\Modelos\Consignatario;
use yura\Modelos\ContactoClienteAgenciaCarga;
use DB;
use Validator;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Style_Color;
use PHPExcel_Style_Alignment;

class ClienteController extends Controller
{
    public function inicio(Request $request){
        return view('adminlte.gestion.postcocecha.clientes.inicio', [
            'url' => $request->getRequestUri(),
            'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
            'text' => ['titulo'=>'Clientes','subtitulo'=>'módulo de postcocecha']
        ]);
    }

    public function buscar_clientes(Request $request)
    {
        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);
        $mi_busqueda_toupper = mb_strtoupper($bus);
        $mi_busqueda_tolower = mb_strtolower($bus);

        $listado = DB::table('cliente as cl')
            ->where('dc.estado',1)
            ->join('detalle_cliente as dc', 'dc.id_cliente', '=', 'cl.id_cliente')
            ->join('pais as pa',  'dc.codigo_pais', '=', 'pa.codigo')
            ->select('cl.estado', 'dc.nombre','dc.direccion','dc.ruc','dc.correo','pa.nombre as pa_nombre','cl.id_cliente');

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($busqueda) {
            $q->Where('dc.nombre', 'like', '%' . $busqueda . '%')
                ->orWhere('dc.correo', 'like', '%' . $busqueda . '%')
                ->orWhere('dc.ruc', 'like', '%' . $busqueda . '%')
                ->orWhere('pa.nombre', 'like', '%' . $busqueda . '%')
                ->orWhere('dc.direccion', 'like', '%' . $busqueda . '%');
        });

        $listado = $listado->orderBy('dc.nombre', 'asc')->paginate(20);

        $datos = [
            'listado' => $listado
        ];
        //dd($datos);

        return view('adminlte.gestion.postcocecha.clientes.partials.listado', $datos);
    }

    public function add_clientes(Request $request)
    {
        !empty($request->id_cliente)
            ? $dataCliente = DetalleCliente::where([
            ['id_cliente', $request->id_cliente],
            ['estado',1]])->first()
            : $dataCliente = '';

        isset($dataCliente->codigo_porcentaje_impuesto)
            ? $tipoImpuesto = TipoImpuesto::where('codigo_impuesto',$dataCliente->codigo_porcentaje_impuesto)->get()
            : $tipoImpuesto = [];

        return view('adminlte.gestion.postcocecha.clientes.forms.add_cliente',[
            'dataPais'=>Pais::orderBy('nombre','asc')->get(),
            'dataCliente' => $dataCliente,
             'tipoImpuestos' => $tipoImpuesto,
            'dataTipoIdentificacion' => TipoIdentificacion::where('estado',1)->get(),
            'impuestos' => Impuesto::all(),
            'marcas' => Marca::all()
        ]);
    }

    public function store_clientes(Request $request){
        $valida = Validator::make($request->all(), [
            'nombre'              => 'required',
            'identificacion'      => 'required',
            'pais'                => 'required',
            'provincia'           => 'required',
            'correo'              => 'required',
            'telefono'            => 'required',
            'direccion'           => 'required',
            'codigo_impuesto'     => 'required',
            'tipo_identificacion' => 'required',
            'tipo_impuesto'       => 'required',
            'puerto_entrada'      => 'required',
            'tipo_credito'        => 'required',
            'marca'               => 'required',
        ]);

        if(!$valida->fails()) {

            if(empty($request->id_cliente)){ //Guardar

                $objCliente          = new Cliente;
                $objCliente->estado  = 1;

                if($objCliente->save()){

                    $model = Cliente::all()->last();
                    $objDetalleCliente = new DetalleCliente;
                    $objDetalleCliente->id_cliente                    = $model->id_cliente;
                    $objDetalleCliente->nombre                        = $request->nombre;
                    $objDetalleCliente->ruc                           = $request->identificacion;
                    $objDetalleCliente->codigo_pais                   = $request->pais;
                    $objDetalleCliente->provincia                     = $request->provincia;
                    $objDetalleCliente->correo                        = $request->correo;
                    $objDetalleCliente->telefono                      = $request->telefono;
                    $objDetalleCliente->direccion                     = $request->direccion;
                    $objDetalleCliente->codigo_porcentaje_impuesto    = $request->tipo_impuesto;
                    $objDetalleCliente->codigo_identificacion         = $request->tipo_identificacion;
                    $objDetalleCliente->codigo_impuesto               = $request->codigo_impuesto;
                    $objDetalleCliente->almacen                       = $request->almacen;
                    $objDetalleCliente->puerto_entrada                = $request->puerto_entrada;
                    $objDetalleCliente->tipo_credito                  = $request->tipo_credito;
                    $objDetalleCliente->id_marca                      = $request->marca;

                    $msg= '';
                    if($objDetalleCliente->save()) {
                        $model = DetalleCliente::all()->last();
                        $success = true;
                        $msg .= '<div class="alert alert-success text-center">' .
                            '<p> Se ha guardado el cliente '. $objDetalleCliente->nombre .'  exitosamente</p>'
                            . '</div>';
                        bitacora('cliente|detalle_cliente', $model->id_detalle_cliente, 'I', 'Inserción satisfactoria de un nuevo cliente con sus detalles(ID guardado tabla detalle_cliente)');

                        $semana = Semana::select(
                            DB::raw('MIN(codigo) as primera_semana'),
                            DB::raw('MAX(codigo) as ultima_semana')
                        )->first();
                        //ProyeccionVentaSemanalUpdate::dispatch($semana->primera_semana,$semana->ultima_semana,0,$model->id_cliente)->onQueue('update_venta_semanal_real');
                    } else {
                        $success = false;
                        $msg .= '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                            . '</div>';
                    }
                }else {

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
                /*return [
                    'mensaje' => $msg,
                    'success' => $success
                ];*/

            }else{ //Actualizar

                $detalleClienteActivo = DetalleCliente::where([
                    ['id_cliente',$request->id_cliente],
                    ['estado',1]
                ])->select('id_detalle_cliente','id_cliente')->first();

                $objDetalleClienteEsatdo = DetalleCliente::find($detalleClienteActivo->id_detalle_cliente);
                $objDetalleClienteEsatdo->estado = 0;

                if($objDetalleClienteEsatdo->save()){

                    $objDetalleCliente = new DetalleCliente;
                    $objDetalleCliente->nombre        = $request->nombre;
                    $objDetalleCliente->ruc           = $request->identificacion;
                    $objDetalleCliente->codigo_pais   = $request->pais;
                    $objDetalleCliente->provincia     = $request->provincia;
                    $objDetalleCliente->correo        = $request->correo;
                    $objDetalleCliente->telefono      = $request->telefono;
                    $objDetalleCliente->direccion     = $request->direccion;
                    $objDetalleCliente->id_cliente    = $detalleClienteActivo->id_cliente;
                    $objDetalleCliente->codigo_porcentaje_impuesto    = $request->tipo_impuesto;
                    $objDetalleCliente->codigo_identificacion         = $request->tipo_identificacion;
                    $objDetalleCliente->codigo_impuesto               = $request->codigo_impuesto;
                    $objDetalleCliente->almacen                       = $request->almacen;
                    $objDetalleCliente->puerto_entrada                = $request->puerto_entrada;
                    $objDetalleCliente->tipo_credito                  = $request->tipo_credito;
                    $objDetalleCliente->id_marca                      = $request->marca;
                    $msg= '';

                    if($objDetalleCliente->save()) {

                        $model = DetalleCliente::all()->last();
                        $success = true;
                        $msg .= '<div class="alert alert-success text-center">' .
                            '<p> Se ha Actualizado el cliente '. $objDetalleCliente->nombre .'  exitosamente</p>'
                            . '</div>';
                        bitacora('detalle_cliente', $model->id_detalle_cliente, 'U', 'Actualziación satisfactoria de los detalles del cliente');
                    } else {
                        $success = false;
                        $msg .= '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                            . '</div>';
                    }
                    /*return [
                        'mensaje' => $msg,
                        'success' => $success
                    ];*/
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

    public function exportar_clientes(Request $request){

        //---------------------- EXCEL --------------------------------------//
        $objPHPExcel = new PHPExcel;

        //--------------------------- GUARDAR EL EXCEL -----------------------

        $objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
        $currencyFormat = '#,#0.## \€;[Red]-#,#0.## \€';
        $numberFormat = '#,#0.##;[Red]-#,#0.##';

        $objPHPExcel->removeSheetByIndex(0); //Eliminar la hoja inicial por defecto

        $this->excel_clientes($objPHPExcel, $request);

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="Clientes.xlsx"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
    }

    public function excel_clientes($objPHPExcel, $request)
    {
        $busqueda = $request->has('busqueda') ? espacios($request->busqueda) : '';
        $bus = str_replace(' ', '%%', $busqueda);

        $listado = DB::table('detalle_cliente as dc');
        $listado->where('estado',1);
        //dd($listado);

        if ($request->busqueda != '') $listado = $listado->Where(function ($q) use ($bus) {
            $q->Where('dc.nombre', 'like', '%' . $bus . '%')
                ->orWhere('dc.correo', 'like', '%' . $bus . '%')
                ->orWhere('dc.ruc', 'like', '%' . $bus . '%')
                ->orWhere('pa.nombre', 'like', '%' . $bus . '%');
        });

        $listado = $listado->orderBy('dc.nombre', 'asc')->get();

        if (count($listado) > 0) {
            $objSheet = new PHPExcel_Worksheet($objPHPExcel, 'Clientes');
            $objPHPExcel->addSheet($objSheet, 0);

            $objSheet->mergeCells('A1:G1');
            $objSheet->getStyle('A1:G1')->getFont()->setBold(true)->setSize(12);
            $objSheet->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objSheet->getStyle('A1:G1')
                ->getFill()
                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB('CCFFCC');

            $objSheet->getCell('A1')->setValue('Listado de clientes');

            $objSheet->getCell('A3')->setValue('Nombre ');
            $objSheet->getCell('B3')->setValue('Dirección');
            $objSheet->getCell('C3')->setValue('Provincia');
            $objSheet->getCell('D3')->setValue('Pais');
            $objSheet->getCell('E3')->setValue('Teléfono');
            $objSheet->getCell('F3')->setValue('Identificación');
            $objSheet->getCell('G3')->setValue('Correo');
            $objSheet->getCell('H3')->setValue('OTROS DATOS');

            $objSheet->getStyle('A3:H3')->getFont()->setBold(true)->setSize(12);

            $objSheet->getStyle('A3:H3')
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                ->getColor()
                ->setRGB(PHPExcel_Style_Color::COLOR_BLACK);

            $objSheet->getStyle('A3:H3')
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
                $objSheet->getStyle('F' . ($i + 4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $objSheet->getStyle('G' . ($i + 4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                $objSheet->getStyle('H' . ($i + 4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                $objSheet->getCell('A' . ($i + 4))->setValue($listado[$i]->nombre);
                $objSheet->getCell('B' . ($i + 4))->setValue($listado[$i]->direccion);
                $objSheet->getCell('C' . ($i + 4))->setValue($listado[$i]->provincia);
                $pais = Pais::where('codigo',$listado[$i]->codigo_pais)->first();
                $objSheet->getCell('D' . ($i + 4))->setValue($pais->nombre);
                $objSheet->getCell('E' . ($i + 4))->setValue($listado[$i]->telefono);
                $objSheet->getCell('F' . ($i + 4))->setValue($listado[$i]->ruc);
                $objSheet->getCell('G' . ($i + 4))->setValue($listado[$i]->correo);
                $documentos = '';
                foreach (getDocumentos('detalle_cliente', $listado[$i]->id_detalle_cliente) as $doc) {
                    $documentos     .= getTextFromDocumento($doc) . "\n";
                }
                $objSheet->getCell('H' . ($i + 4))->setValue($documentos);
            }

            $objSheet->getColumnDimension('A')->setAutoSize(true);
            $objSheet->getColumnDimension('B')->setAutoSize(true);
            $objSheet->getColumnDimension('C')->setAutoSize(true);
            $objSheet->getColumnDimension('D')->setAutoSize(true);
            $objSheet->getColumnDimension('E')->setAutoSize(true);
            $objSheet->getColumnDimension('F')->setAutoSize(true);
            $objSheet->getColumnDimension('G')->setAutoSize(true);
            $objSheet->getColumnDimension('H')->setAutoSize(true);

        } else {
            return '<div>No se han encontrado coincidencias para exportar</div>';
        }
    }

    public function detalles_cliente(Request $request){

        $dataAgenciasCarga = AgenciaCarga::orderBy('id_agencia_carga','desc')->where('estado',1)->get();
        $dataCliente = DetalleCliente::where([
            ['id_cliente',$request->id_cliente],
            ['estado',1]
        ])->first();

        $dataClienteAgenciasCarga = ClienteAgenciaCarga::where('id_cliente',$request->id_cliente)->orderBy('id_cliente_agencia_carga','desc')->get();
        $pais         = Pais::where('codigo',$dataCliente->codigo_pais)->first();
        $dataContacto = DB::table('detalle_cliente as dc')
            ->where('id_cliente',$request->id_cliente)
            ->join('detalle_cliente_contacto as dcc','dc.id_detalle_cliente','=','dcc.id_detalle_cliente')
            ->join('contacto as c','dcc.id_contacto','=','c.id_contacto')
            ->get();

        return view('adminlte.gestion.postcocecha.clientes.partials.detalle',
            [
                'dataCliente'                => $dataCliente,
                'pais'                       => $pais->nombre,
                'dataAgenciaCarga'           => $dataAgenciasCarga,
                'dataClienteAgenciasCarga'   => $dataClienteAgenciasCarga,
                'dataContacto'               => $dataContacto,
            ]);
    }

    public function ver_agencia_carga(Request $request){

        $dataAgenciaCargo = AgenciaCarga::where('estado',1)->get();
        return view('adminlte.gestion.postcocecha.clientes.partials.select_agencias_carga',
            [
                'dataAgenciaCargo'=> $dataAgenciaCargo,
                'cantTr' => $request->cant_tr,
            ]);
    }

    public function store_agencia_carga(Request $request){

        $valida = Validator::make($request->all(), [
            'data_agencias_carga' => 'required|Array',
        ]);
        if (!$valida->fails()) {
            $success = false;
            $msg = '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                    . '</div>';
            $oldClienteAgenciaCarga = ClienteAgenciaCarga::where('id_cliente',$request->id_cliente)->get();
            foreach ($oldClienteAgenciaCarga as $item)
                ClienteAgenciaCarga::destroy($item->id_cliente_agencia_carga);

            foreach ($request->data_agencias_carga as $x => $agencias_carga) {

                !empty($agencias_carga[1]) ? $existAgenciaCargoCliente = ClienteAgenciaCarga::find($agencias_carga[1]) : $existAgenciaCargoCliente = '';

                if($existAgenciaCargoCliente != ''){
                    $objClienteAgenciaCarga = ClienteAgenciaCarga::find($existAgenciaCargoCliente->id_cliente_agencia_carga);
                    $accion= "U";
                    $palabra = "Actualización";
                }else{
                    $objClienteAgenciaCarga = new ClienteAgenciaCarga;
                    $accion= "I";
                    $palabra = "Inserción";
                }

                $objClienteAgenciaCarga->id_cliente = $request->id_cliente;
                $objClienteAgenciaCarga->id_agencia_carga = $agencias_carga[0];

                if($objClienteAgenciaCarga->save()) {
                    $model = ClienteAgenciaCarga::all()->last();
                    $y = 0;
                    foreach ($request->contactos[$x] as $contacto) {
                        $objContactoClienteAgenciaCarga = new ContactoClienteAgenciaCarga;
                        $objContactoClienteAgenciaCarga->id_cliente_agencia_carga = $model->id_cliente_agencia_carga;
                        $objContactoClienteAgenciaCarga->contacto = $contacto['contacto'];
                        $objContactoClienteAgenciaCarga->correo = $contacto['correo'];
                        $objContactoClienteAgenciaCarga->direccion = $contacto['direccion'];
                        if($objContactoClienteAgenciaCarga->save()) $y++;
                    }
                    if($y == count($request->contactos[$x])){
                        $success = true;
                        $msg = '<div class="alert alert-success text-center">' .
                            '<p> Se ha guardado la agencia de carga exitosamente para el cliente </p>'
                            . '</div>';
                        bitacora('cliente_agenciacarga', $model->id_cliente_agencia_carga, $accion, $palabra.' satisfactoria de la relación de una agencia de carga a un cliente');

                    }else{
                        ClienteAgenciaCarga::destroy($model->id_cliente_agencia_carga);
                    }
                }
            }
            return [
                'mensaje' => $msg,
                'success' => $success
            ];
        }
    }

    public function delete_cliente_agencia_carga(Request $request){
        $valida = Validator::make($request->all(), [
            'id_cliente_agencia_carga' => 'required',
            //'estado'                   => 'required',
        ]);

        if (!$valida->fails()) {
            $msg ='';
            if(ClienteAgenciaCarga::destroy($request->id_cliente_agencia_carga)) {
                $success = true;
                $msg .= '<div class="alert alert-success text-center">' .
                    '<p> Se ha eliminado la agencia de carga para el cliente exitosamente para el cliente</p>'
                    . '</div>';
                bitacora('cliente_agenciacarga', $request->id_cliente_agencia_carga, 'D', 'Eliminacion satisfactoria de la relación de la agencia de carga con el cliente');
            } else {
                $success = false;
                $msg .= '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                    . '</div>';
            }
            return [
                'mensaje' => $msg,
                'success' => $success
            ];
        }
    }

    public function ver_contactos_clientes(Request $request){

        return view('adminlte.gestion.postcocecha.clientes.partials.inputs_contactos_clientes',
            [
                'cantTr' => $request->cant_tr,
            ]);
    }

    public function store_contactos(Request $request){

        $valida = Validator::make($request->all(), [
            'data_contactos' => 'required|Array',
        ]);

        if (!$valida->fails()) {

            foreach ($request->data_contactos as $contactos) {

                !empty($contactos[4]) ? $existContacto = Contacto::find($contactos[4]) : $existContacto = '';

                if($existContacto != ''){
                    $objClienteContacto = Contacto::find($existContacto->id_contacto);
                    $accion= "U";
                    $palabra = "Actualización";
                }else{
                    $objClienteContacto = new Contacto;
                    $accion= "I";
                    $palabra = "Inserción";
                }

                $objClienteContacto->nombre      = $contactos[0];
                $objClienteContacto->correo      = $contactos[1];
                $objClienteContacto->telefono    = $contactos[2];
                $objClienteContacto->direccion	 = $contactos[3];
                $msg ='';

                if($objClienteContacto->save()) {
                    $model = Contacto::all()->last();
                    if($accion === 'I'){
                        $objDetalleClienteContacto = new DetalleClienteContacto;
                        $objDetalleClienteContacto->id_detalle_cliente = $request->id_detalle_cliente;
                        $objDetalleClienteContacto->id_contacto        = $model->id_contacto;
                        $objDetalleClienteContacto->save();
                    }
                    $success = true;
                    $msg .= '<div class="alert alert-success text-center">' .
                        '<p> Se ha guardado exitosamente el contacto para el cliente</p>'
                        . '</div>';
                    bitacora('contacto', $model->id_contacto, $accion, $palabra.   ' satisfactoria de la relación entre un cliente y su contacto');
                } else {
                    $success = false;
                    $msg .= '<div class="alert alert-warning text-center">' .
                        '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                }
            }
            return [
                'mensaje' => $msg,
                'success' => $success
            ];
        }
    }

    public function actualizar_estado_contacto(Request $request){

        if (!empty($request->id_contacto)) {
            $model = '';
            $model = Contacto::find($request->id_contacto);

            $request->estado == 1 ? $model->estado = 0 : $model->estado = 1;
            $msg = '';
            if ($model->save()) {
                $success = true;
                $msg .= '<div class="alert alert-success text-center">' .
                    '<p> El contacto ha sido modificado exitosamente</p>'
                    . '</div>';
                bitacora('contacto', $request->id_clasificacion, 'U', 'Actualización satisfactoria de un contacto');
            } else {
                $success = false;
                $msg .= '<div class="alert alert-warning text-center">' .
                    '<p> Ha ocurrido un problema al guardar la información al sistema</p>';
            }
            return [
                'success' => $success,
                'mensaje' => $msg
            ];
        }
    }

    public function agregar_consignatario(Request $request){
        return view('adminlte.gestion.postcocecha.clientes.forms.add_consignatario',[
            'consignatatios' => Consignatario::where('estado',1)->get(),
            'id_cliente' => $request->id_cliente,
            'cliente_consignatario' => ClienteConsignatario::where('id_cliente', $request->id_cliente)->get()
        ]);
    }

    public function store_cliente_consignatario(Request $request){

        $arrConsinatariosActuales = ClienteConsignatario::where('id_cliente',$request->id_cliente)->get();
        foreach ($request->arr_consignatarios as $consignatario) {

            $objClienteConsignatario = new ClienteConsignatario;
            $objClienteConsignatario->id_cliente = $request->id_cliente;
            $objClienteConsignatario->id_consignatario = $consignatario['id_consignatario'];
            $objClienteConsignatario->default = $consignatario['default'] == 'true';
            if($objClienteConsignatario->save()){
                $success = true;
                $msg = '<div class="alert alert-success text-center">' .
                            '<p> Se han guardado los consignatarios con éxito </p>'
                        .'</div>';
            }else{
                $success = false;
                $msg = '<div class="alert alert-warning text-center">' .
                            '<p> Ha ocurrido un problema al guardar la información al sistema</p>'
                        . '</div>';
                break;
            }
        }
       // dump($success,$arrConsinatariosActuales);
        if($success)
            foreach ($arrConsinatariosActuales as $consinatarioActual)
                $consinatarioActual->delete();

        return [
            'mensaje' => $msg,
            'success' => $success
        ];

    }
}
