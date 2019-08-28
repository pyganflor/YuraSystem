<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DomDocument;
use PHPExcel;
use PHPExcel_IOFactory;
use yura\Modelos\Comprobante;
use yura\Modelos\DetalleCliente;
use yura\Modelos\FacturaClienteTercero;
use yura\Modelos\ImpuestoDetalleFactura;
use yura\Modelos\DetalleFactura;
use yura\Modelos\DesgloseEnvioFactura;
use yura\Modelos\ImpuestoDesgloseEnvioFactura;
use yura\Modelos\Pedido;
use yura\Modelos\Usuario;
use yura\Modelos\Submenu;
use yura\Modelos\TipoComprobante;
use yura\Modelos\Cliente;
use yura\Modelos\DetalleGuiaRemision;
use Validator;
use DB;
use SoapClient;
use Barryvdh\DomPDF\Facade as PDF;
use yura\Mail\CorreoFacturaVenture;
use Illuminate\Support\Facades\Mail;
use DateTime;

class ComprobanteController extends Controller
{
    public function inicio(Request $request)
    {
        return view('adminlte.gestion.comprobante.inicio',
            [
                'url' => $request->getRequestUri(),
                'submenu' => Submenu::Where('url', '=', substr($request->getRequestUri(), 1))->get()[0],
                'text' => ['titulo' => 'Comprobantes', 'subtitulo' => 'módulo de facturación'],
                'tiposCompbantes' => TipoComprobante::all(),
                'annos' => DB::table('comprobante as c')->select(DB::raw('YEAR(c.fecha_emision) as anno'))->distinct()->get(),
                'clientes' => Cliente::join('detalle_cliente as dc', 'cliente.id_cliente', 'dc.id_cliente')->where('dc.estado', 1)->select('nombre', 'cliente.id_cliente')
                    ->orderBy('dc.nombre','asc')->get(),
                'empresas' =>  getConfiguracionEmpresa(null, true)
            ]);
    }

    public function buscar_comprobante(Request $request)
    {
        $busquedaAnno = $request->has('anno') ? $request->anno : '';
        $busquedacliente = $request->has('id_cliente') ? $request->id_cliente : '';
        $desde = $request->has('desde') ? $request->desde : '';
        $hasta = $request->has('hasta') ? $request->hasta : '';
        $busquedaComprobante = $request->has('codigo_comprobante') ? $request->codigo_comprobante : '';
        $listado = Comprobante::where([
            ['comprobante.estado', isset($request->estado) ? $request->estado : 1],
            ['tipo_comprobante', '!=', "00"],
            ['comprobante.habilitado',true]
        ])->join('tipo_comprobante as tc', 'comprobante.tipo_comprobante', 'tc.codigo');

        if($desde != "" && $hasta == "")
            $listado->where('comprobante.fecha_emision', $desde);

        if($desde != "" && $hasta != "")
            $listado->whereBetween('comprobante.fecha_emision', [$desde,$hasta]);

        if($busquedaComprobante == "" || $busquedaComprobante== "01"){
            $listado->join('envio as e', 'comprobante.id_envio', 'e.id_envio')
                ->join('pedido as p', 'e.id_pedido', 'p.id_pedido')
                ->join('detalle_cliente as dc', 'p.id_cliente', 'dc.id_cliente')
            ->where([
                ['dc.estado', 1],
                ['p.estado',1],
                ['p.id_configuracion_empresa',$request->id_configuracion_empresa]
            ]);
        }

        /*if ($busquedaAnno != '')
            $listado = $listado->where(DB::raw('YEAR(de.fecha_emision)'), $busquedaAnno);*/
        if ($busquedacliente != '')
            $listado = $listado->where('dc.id_cliente', $busquedacliente);
        if ($busquedaComprobante != '' && $request->codigo_comprobante != '')
            $listado = $listado->where('comprobante.tipo_comprobante', $busquedaComprobante);

        $listado = $listado->orderBy('id_comprobante', 'Desc')
            ->select('comprobante.*', 'tc.nombre as nombre_comprobante', ($busquedaComprobante == "01" ||$busquedaComprobante == "" ) ? 'dc.nombre as nombre_cliente' : 'comprobante.*')->get();

        $datos = [
            'listado' => $listado,
            'columna_causa' => ($request->estado == 3 || $request->estado == 4) ? true : false,
            'firmar_comprobante' => isset($request->estado) ? true : false,
            'tipo_comprobante' =>$busquedaComprobante == "" ? "01" : $busquedaComprobante
        ];

        return view('adminlte.gestion.comprobante.partials.listado', $datos);
    }

    public function generar_comprobante_factura(Request $request){

        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $valida = Validator::make($request->all(), [
            'id_envio' => 'required',
            'codigo_pais' => 'required',
            'destino' => 'required',
            'email' => 'required',
            'telefono' => 'required',
        ],[
            'id_envio.required' => 'No existe el envío para crear el comprobante',
            'codigo_pais.required' => 'Debe seleccionar un pais para la facturación',
        ]);

        $msg = "";
        if (!$valida->fails()) {
            $precio_total_sin_impuestos = 0.00;
            $peso_neto = 0;
            //$peso_bruto = 0;
            $peso_caja=0;
            $envio = getEnvio($request->id_envio);

            if($envio->pedido->tipo_especificacion === "N") {
                foreach ($envio->pedido->detalles as $x => $det_ped) {
                    $precio = explode("|", $det_ped->precio);
                    $i = 0;
                    foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp) {
                        foreach ($esp_emp->detalles as $n => $det_esp_emp) {
                            $peso_neto += (int)$det_esp_emp->clasificacion_ramo->nombre * number_format(($det_ped->cantidad*$det_esp_emp->cantidad),2,".","");
                            $peso_caja += isset(explode("|",$det_esp_emp->especificacion_empaque->empaque->nombre)[2]) ? explode("|",$det_esp_emp->especificacion_empaque->empaque->nombre)[2] : 0;
                            $precio_x_variedad = ($det_esp_emp->cantidad * ((float)explode(";", $precio[$i])[0]) * $esp_emp->cantidad * $det_ped->cantidad);
                            $precio_total_sin_impuestos += $precio_x_variedad;
                            $i++;
                        }
                    }
                }
            }else if($envio->pedido->tipo_especificacion === "T"){
                foreach ($envio->pedido->detalles as $x => $det_ped) {
                    foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp){
                        foreach ($esp_emp->detalles as $n => $det_esp_emp){
                            $peso_neto += (int)$det_esp_emp->clasificacion_ramo->nombre * number_format(($det_ped->cantidad*$det_esp_emp->cantidad),2,".","");
                            $peso_caja += isset(explode("|",$det_esp_emp->especificacion_empaque->empaque->nombre)[2]) ? (explode("|",$det_esp_emp->especificacion_empaque->empaque->nombre)[2]*$det_ped->cantidad) : 0;
                        }
                    }
                    foreach($det_ped->coloraciones as $y => $coloracion){
                        $cant_esp_emp = $coloracion->especificacion_empaque->cantidad;
                        $i=0;
                        foreach($coloracion->marcaciones_coloraciones as $m_c){
                            if($m_c->cantidad >0){
                                if($coloracion->precio==""){
                                    foreach (explode("|", $det_ped->precio) as $p)
                                        if($m_c->id_detalle_especificacionempaque == explode(";",$p)[1])
                                            $precio = explode(";",$p)[0];
                                }else{
                                    foreach(explode("|",$coloracion->precio) as $p)
                                        if($m_c->id_detalle_especificacionempaque == explode(";",$p)[1])
                                            $precio = explode(";",$p)[0];
                                }
                                $precio_x_variedad = $m_c->cantidad * $precio * $cant_esp_emp;
                                $precio_total_sin_impuestos += $precio_x_variedad;
                                $i++;
                            }
                        }
                    }
                }
            }

            $facturaClienTeTercero = getFacturaClienteTercero($envio->id_envio);
            $dataCliente = DetalleCliente::where([
                ['id_cliente', $envio->pedido->cliente->id_cliente],
                ['estado', 1]
            ])->first();

            if($facturaClienTeTercero != null ){
                $codigo_pais = $facturaClienTeTercero->codigo_pais;
                $codigo_impuesto = $facturaClienTeTercero->codigo_impuesto;
                $codigo_porcentaje_impuesto =  $facturaClienTeTercero->codigo_impuesto_porcentaje;
                $codigo_identificacion = $facturaClienTeTercero->codigo_identificacion;
                $identificacion =  $facturaClienTeTercero->identificacion;
                $nombre_cliente =  $facturaClienTeTercero->nombre_cliente_tercero;
                $provincia = $facturaClienTeTercero->provincia;
                //*******COMENTADO PARA QUE LA FACTURACIÓN FUNCIONE CON EL VENTURE*********//
                /*$direccion = $facturaClienTeTercero->direccion;
                $correo = $facturaClienTeTercero->correo;
                $telefono = $facturaClienTeTercero->telefono;
                $almacen = $facturaClienTeTercero->almacen;*/
                $dae = $facturaClienTeTercero->dae;

            }else{
                $codigo_pais = $request->codigo_pais;
                $codigo_impuesto = $dataCliente->codigo_impuesto;
                $codigo_porcentaje_impuesto = $dataCliente->codigo_porcentaje_impuesto;
                $codigo_identificacion = $dataCliente->codigo_identificacion;
                $identificacion = $dataCliente->ruc;
                $nombre_cliente = $dataCliente->nombre;
                //*******COMENTADO PARA QUE LA FACTURACIÓN FUNCIONE CON EL VENTURE*********//
                /*$provincia = $dataCliente->provincia;
                $direccion = $dataCliente->direccion;
                $correo = $dataCliente->correo;
                $telefono = $dataCliente->telefono;
                $almacen = $request->almacen;*/
                $dae = $request->dae;
            }

            if ((strtoupper(getConfiguracionEmpresa($envio->pedido->id_configuracion_empresa)->codigo_pais) != strtoupper($codigo_pais))
                && (!isset(getCodigoDae(strtoupper($codigo_pais),Carbon::parse($request->fecha_envio)->format('m'),Carbon::parse($request->fecha_envio)->format('Y'),$envio->pedido->id_configuracion_empresa)->codigo_dae))
                && $dae == null) {
                return '<div class="alert alert-danger text-center">' .
                    '<p> No se ha configurado un código DAE para ' . $request->pais . ' en la fecha seleccionada </p>'
                    . '</div>';
            }

            //*******COMENTADO PARA QUE LA FACTURACIÓN FUNCIONE CON EL VENTURE*********//
            /*$fechaEmision = Carbon::now()->format('dmY');
            $tipoComprobante = '01';*/

            if($request->update == "true"){

                $request->id_comprobante == null
                    ? $dataComprobante = Comprobante::where('id_envio',$request->id_envio)
                    : $dataComprobante = Comprobante::where('comprobante.id_comprobante',$request->id_comprobante);
                    $dataComprobante->join('detalle_factura as df','comprobante.id_comprobante','df.id_comprobante')
                    ->join('impuesto_detalle_factura as idf','df.id_detalle_factura','idf.id_detalle_factura')
                    ->join('desglose_envio_factura as def','comprobante.id_comprobante','def.id_comprobante')
                    ->join('impuesto_desglose_envio_factura as idef','def.id_desglose_envio_factura','idef.id_desglose_envio_factura')
                    ->select('clave_acceso','comprobante.id_comprobante','df.id_detalle_factura','id_impuesto_desglose_envio_factura','comprobante.secuencial');

                $dataComprobante = $dataComprobante->get();
                $secuencial = $dataComprobante[0]->secuencial;

                //*******COMENTADO PARA QUE LA FACTURACIÓN FUNCIONE CON EL VENTURE*********//
                /*$secuencial = getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'SECUENCIAL');
                $ruc = getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'RUC');
                $codigo_numerico = getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'CODIGO_NUMERICO');
                $entorno = getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'ENTORNO');
                $serie = getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'SERIE');
                $tipo_emision = getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'TIPO_EMISION');
                $punto_acceso =  getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'PUNTO_ACCESO');
                $serie = '001' . $punto_acceso;*/

                $request->id_comprobante == null
                    ? $id_pedido = getEnvio($request->id_envio)->pedido->id_pedido
                    : $id_pedido = Pedido::where('id_comprobante_temporal',$request->id_comprobante)->first()->id_pedido;

                $objPedido = Pedido::find($id_pedido);
                $objPedido->update(['clave_acceso_temporal'=>null,'id_comprobante_temporal'=>null]);
                Comprobante::destroy($dataComprobante[0]->id_comprobante);

            }else{

                $secuencial_rehusado = false;
                $comprobante_rehusado = Comprobante::where('rehusar', true)->get();
                if($comprobante_rehusado->count() > 0){
                    foreach ($comprobante_rehusado as $cr){
                        if(Carbon::parse($cr->fecha_emision)->format('Y-m') == Carbon::parse(now()->format('Y-m'))->format('Y-m')){
                            $secuencial = $cr->secuencial;
                            Comprobante::destroy($cr->id_comprobante);
                            $secuencial_rehusado = true;
                            break;
                        }
                    }
                }
                if(!$secuencial_rehusado){
                    //*******COMENTADO PARA QUE LA FACTURACIÓN FUNCIONE CON EL VENTURE*********//
                    /*($envio->pedido->clave_acceso_temporal != null && $envio->pedido->clave_acceso_temporal != "")
                        ? $secuencial = getDetallesClaveAcceso($envio->pedido->clave_acceso_temporal, "SECUENCIAL")
                        : $secuencial = getSecuencial('01',getConfiguracionEmpresa($envio->pedido->id_configuracion_empresa));*/

                    $punto_acceso = getUsuario(session('id_usuario'))->punto_acceso;
                    $serie = '001' . $punto_acceso;

                    isset($envio->pedido->clave_acceso_temporal)
                        ? $secuencial = $envio->pedido->clave_acceso_temporal
                        : $secuencial = $serie.getSecuencial('01',getConfiguracionEmpresa($envio->pedido->id_configuracion_empresa));

                    //*******COMENTADO PARA QUE LA FACTURACIÓN FUNCIONE CON EL VENTURE*********//
                    /* $ruc = isset($envio->pedido->id_configuracion_empresa) ? getConfiguracionEmpresa($envio->pedido->id_configuracion_empresa)->ruc : getConfiguracionEmpresa()->ruc ;
                     $codigo_numerico = env('CODIGO_NUMERICO');
                     $entorno = env('ENTORNO');
                     $tipo_emision = '1';*/
                }

            }

            $datosEmpresa = getConfiguracionEmpresa($envio->pedido->id_configuracion_empresa);
            $tipoImpuesto = getTipoImpuesto($codigo_impuesto,$codigo_porcentaje_impuesto);
            $precio_total_con_impuestos = is_numeric($tipoImpuesto->porcentaje) ? number_format($precio_total_sin_impuestos * ($tipoImpuesto->porcentaje / 100), 2, ".", "") : "0.00";

            //*******COMENTADO PARA QUE LA FACTURACIÓN FUNCIONE CON EL VENTURE*********//
            /*$xml = new DomDocument('1.0', 'UTF-8');
            $factura = $xml->createElement('factura');
            $factura->setAttribute('id', 'comprobante');
            $factura->setAttribute('version', '1.0.0');
            $xml->appendChild($factura);
            $infoTributaria = $xml->createElement('infoTributaria');
            $factura->appendChild($infoTributaria);
            $cadena = $fechaEmision . $tipoComprobante . $ruc . $entorno . $serie . $secuencial . $codigo_numerico . $tipo_emision;
            $digito_verificador = generaDigitoVerificador($cadena);
            $claveAcceso = $cadena . $digito_verificador;
            $informacionTributaria = [
                'ambiente' => $entorno,
                'tipoEmision' => $tipo_emision,
                'razonSocial' => $datosEmpresa->razon_social,
                'nombreComercial' => $datosEmpresa->nombre,
                'ruc' => $ruc,
                'claveAcceso' => $claveAcceso,
                'codDoc' => '01',
                'estab' => '001',
                'ptoEmi' => $punto_acceso,
                'secuencial' => $secuencial,
                'dirMatriz' => $datosEmpresa->direccion_matriz
            ];

            foreach ($informacionTributaria as $key => $it) {
                $nodo = $xml->createElement($key, $it);
                $infoTributaria->appendChild($nodo);
            }

            if (!isset($tipoImpuesto->porcentaje)) {
                return '<div class="alert alert-danger text-center">' .
                    '<p> El tipo de impuesto asignado al cliente '.$nombre_cliente.' está deshabilitado, por favor habilítelo o asignele otro </p>'
                    . '</div>';
            }
            $infoFactura = $xml->createElement('infoFactura');
            $factura->appendChild($infoFactura);
            $informacionFactura = [
                'fechaEmision' => now()->format('d/m/Y'),
                'dirEstablecimiento' => $datosEmpresa->direccion_establecimiento,
                'obligadoContabilidad' => $datosEmpresa->obligado_contabilidad == 1 ? 'SI' : 'NO',
                'tipoIdentificacionComprador' => $codigo_identificacion,
                'razonSocialComprador' => $identificacion == "9999999999999" ? "CONSUMIDOR FINAL" : $nombre_cliente,
                'identificacionComprador' => $identificacion,
                'totalSinImpuestos' => number_format($precio_total_sin_impuestos, 2, ".", ""),
                'totalDescuento' => "0.00",
            ];

            foreach ($informacionFactura as $key => $if) {
                $nodo = $xml->createElement($key, $if);
                $infoFactura->appendChild($nodo);
            }

            $totalConImpuestos = $xml->createElement('totalConImpuestos');
            $infoFactura->appendChild($totalConImpuestos);

            $valorImpuesto = $precio_total_con_impuestos;
            $cantidad_impuestos = 1; //Número que indica la cantidad de impuestos que tiene la factura
            for ($i = 0; $i < $cantidad_impuestos; $i++) {
                $informacionImpuestos = [
                    'codigo' => $codigo_impuesto,
                    'codigoPorcentaje' => $codigo_porcentaje_impuesto,
                    'baseImponible' => number_format($precio_total_sin_impuestos, 2, ".", ""),
                    'valor' => $valorImpuesto
                ];
                $totalImpuesto = $xml->createElement('totalImpuesto');
                $totalConImpuestos->appendChild($totalImpuesto);
                foreach ($informacionImpuestos as $key => $iI) {
                    $nodo = $xml->createElement($key, $iI);
                    $totalImpuesto->appendChild($nodo);
                }
            }
            $propina = $xml->createElement('propina', '0.00');
            $infoFactura->appendChild($propina);
            $importeTotal = $xml->createElement('importeTotal', number_format($valorImpuesto + $precio_total_sin_impuestos, 2, ".", ""));
            $infoFactura->appendChild($importeTotal);
            switch (getConfiguracionEmpresa($envio->pedido->id_configuracion_empresa)->moneda){
                case "usd":
                    $m = 'DOLAR';
                    break;
            }
            $moneda = $xml->createElement('moneda', $m);
            $infoFactura->appendChild($moneda);
            $detalles = $xml->createElement('detalles');
            $factura->appendChild($detalles);

            if($envio->pedido->tipo_especificacion === "N") {
                foreach ($envio->pedido->detalles as $x => $det_ped) {
                    $precio = explode("|", $det_ped->precio);
                    $i = 0;
                    foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp) {
                        foreach ($esp_emp->detalles as $n => $det_esp_emp) {
                            $precio_x_variedad = ($det_esp_emp->cantidad * ((float)explode(";", $precio[$i])[0]) * $esp_emp->cantidad * $det_ped->cantidad);
                            $variedad = getVariedad($det_esp_emp->id_variedad);
                            if($det_esp_emp->longitud_ramo != null){
                                foreach (getUnidadesMedida($det_esp_emp->id_unidad_medida) as $umLongitud)
                                    if($umLongitud->tipo == "L")
                                        $umL = $umLongitud->siglas;
                            }else{
                                $umL ="";
                            }
                            $longitudRamo = $det_esp_emp->longitud_ramo != "" ? $det_esp_emp->longitud_ramo : "";
                            $clasificacionRamo = getClasificacionRamo($det_esp_emp->id_clasificacion_ramo);
                            foreach (getUnidadesMedida($clasificacionRamo->id_unidad_medida) as $umPeso)
                                $umPeso->tipo == "P" ? $umPeso = $umPeso->siglas : $umPeso ="";
                            $descripcion_detalle = $variedad->planta->nombre . " (" . $variedad->siglas . ") " . $clasificacionRamo->nombre.$umPeso . " " . $longitudRamo.$umL;
                            $detalle = $xml->createElement('detalle');
                            $detalles->appendChild($detalle);
                            $informacionDetalle = [
                                'codigoPrincipal' => 'ENV' . str_pad($request->id_envio, 9, "0", STR_PAD_LEFT),
                                'descripcion' => $descripcion_detalle,
                                'cantidad' => number_format(($det_esp_emp->cantidad* $esp_emp->cantidad * $det_ped->cantidad), 2, ".", ""),
                                'precioUnitario' => number_format(explode(";",$precio[$i])[0], 2, ".", ""),
                                'descuento' => '0.00',
                                'precioTotalSinImpuesto' => number_format($precio_x_variedad, 2, ".", "")
                            ];
                            foreach ($informacionDetalle as $key => $iD) {
                                $nodo = $xml->createElement($key, $iD);
                                $detalle->appendChild($nodo);
                            }

                            $impuestos = $xml->createElement('impuestos');
                            $detalle->appendChild($impuestos);

                            $impuesto = $xml->createElement('impuesto');
                            $impuestos->appendChild($impuesto);

                            $informacionImpuesto = [
                                'codigo' => $codigo_impuesto,
                                'codigoPorcentaje' => $codigo_porcentaje_impuesto,
                                'tarifa' => is_numeric($tipoImpuesto->porcentaje) ? number_format($tipoImpuesto->porcentaje, 2, ".", "") : "0.00",
                                'baseImponible' => number_format($precio_x_variedad, 2, ".", ""),
                                'valor' => is_numeric($tipoImpuesto->porcentaje) ? number_format($precio_x_variedad * ($tipoImpuesto->porcentaje / 100), 2, ".", "") : "0.00"
                            ];

                            foreach ($informacionImpuesto as $key => $iIp) {
                                $nodo = $xml->createElement($key, $iIp);
                                $impuesto->appendChild($nodo);
                            }
                            $i++;
                        }
                    }
                }
            }
            else if($envio->pedido->tipo_especificacion === "T"){
                foreach ($envio->pedido->detalles as $x => $det_ped) {
                    foreach($det_ped->coloraciones as $y => $coloracion){
                        $cant_esp_emp = $coloracion->especificacion_empaque->cantidad;
                        $i=0;
                        foreach($coloracion->marcaciones_coloraciones as $m_c){
                            if($m_c->cantidad > 0){
                                if($coloracion->precio==""){
                                    foreach (explode("|", $det_ped->precio) as $p)
                                        if($m_c->id_detalle_especificacionempaque == explode(";",$p)[1])
                                            $precio = explode(";",$p)[0];
                                }else{
                                    foreach(explode("|",$coloracion->precio) as $p)
                                        if($m_c->id_detalle_especificacionempaque == explode(";",$p)[1])
                                            $precio = explode(";",$p)[0];
                                }
                                $precio_x_variedad = $m_c->cantidad * $precio * $cant_esp_emp;
                                $variedad = getVariedad($m_c->detalle_especificacionempaque->id_variedad);//getVariedad($det_esp_emp->id_variedad);
                                if($m_c->detalle_especificacionempaque->longitud_ramo != null){
                                    foreach (getUnidadesMedida($m_c->detalle_especificacionempaque->id_unidad_medida) as $umLongitud)
                                        if($umLongitud->tipo == "L")
                                            $umL = $umLongitud->siglas;
                                }else{
                                    $umL ="";
                                }
                                $longitudRamo = $m_c->detalle_especificacionempaque->longitud_ramo != "" ? $m_c->detalle_especificacionempaque->longitud_ramo : "";
                                $clasificacionRamo = getClasificacionRamo($m_c->detalle_especificacionempaque->id_clasificacion_ramo);
                                foreach (getUnidadesMedida($clasificacionRamo->id_unidad_medida) as $umPeso)
                                    $umPeso->tipo == "P" ? $umPeso = $umPeso->siglas : $umPeso ="";
                                $descripcion_detalle = $variedad->planta->nombre . " (" . $variedad->siglas . ") " . $clasificacionRamo->nombre.$umPeso . " " . $longitudRamo.$umL. " ". $coloracion->color->nombre;
                                $detalle = $xml->createElement('detalle');
                                $detalles->appendChild($detalle);
                                $informacionDetalle = [
                                    'codigoPrincipal' => 'ENV' . str_pad($request->id_envio, 9, "0", STR_PAD_LEFT),
                                    'descripcion' => $descripcion_detalle,
                                    'cantidad' => number_format($m_c->cantidad, 2, ".", ""),
                                    'precioUnitario' =>number_format($precio, 2, ".", ""),
                                    'descuento' => '0.00',
                                    'precioTotalSinImpuesto' => number_format($precio_x_variedad, 2, ".", "")
                                ];
                                foreach ($informacionDetalle as $key => $iD) {
                                    $nodo = $xml->createElement($key, $iD);
                                    $detalle->appendChild($nodo);
                                }

                                $impuestos = $xml->createElement('impuestos');
                                $detalle->appendChild($impuestos);

                                $impuesto = $xml->createElement('impuesto');
                                $impuestos->appendChild($impuesto);

                                $informacionImpuesto = [
                                    'codigo' => $codigo_impuesto,
                                    'codigoPorcentaje' => $codigo_porcentaje_impuesto,
                                    'tarifa' => is_numeric($tipoImpuesto->porcentaje) ? number_format($tipoImpuesto->porcentaje, 2, ".", "") : "0.00",
                                    'baseImponible' => number_format($precio_x_variedad, 2, ".", ""),
                                    'valor' => is_numeric($tipoImpuesto->porcentaje) ? number_format($precio_x_variedad * ($tipoImpuesto->porcentaje / 100), 2, ".", "") : "0.00"
                                ];

                                foreach ($informacionImpuesto as $key => $iIp) {
                                    $nodo = $xml->createElement($key, $iIp);
                                    $impuesto->appendChild($nodo);
                                }
                                $i++;
                            }
                        }
                    }
                }
            }

            $informacionAdicional = $xml->createElement('infoAdicional');
            $factura->appendChild($informacionAdicional);

            $campos_adicionales = [
                'Dirección'=> $provincia . " " . $direccion,
                'Email'    => $correo,
                'Teléfono' => $telefono,
                'Carguera' => getAgenciaTransporte($envio->detalles[0]->id_aerolinea)->nombre,
            ];

            if (!empty($request->guia_madre))
                $campos_adicionales['GUIA_MADRE'] = $request->guia_madre;
            if (!empty($request->guia_hija))
                $campos_adicionales['GUIA_HIJA'] = $request->guia_hija;
            if(!empty($request->almacen))
                $campos_adicionales['ALMACEN'] = $almacen;
            if ((strtoupper(getConfiguracionEmpresa($envio->pedido->id_configuracion_empresa)->codigo_pais) != strtoupper($codigo_pais)))
                $campos_adicionales['DAE'] = $dae;

            foreach ($campos_adicionales as $key => $ca) {
                $campo_adicional = $xml->createElement('campoAdicional', $ca);
                $campo_adicional->setAttribute('nombre', $key);
                $informacionAdicional->appendChild($campo_adicional);
            }
            $xml->formatOutput = true;
            $xml->saveXML();
            $nombre_xml = $claveAcceso . ".xml";*/
            $nombre_xml = "fact_".$secuencial;

                //////////// GUARDAR ARCHIVO XML Y DATA EN BD //////////////

                $obj_comprobante = new Comprobante;

                //*******COMENTADO PARA QUE LA FACTURACIÓN FUNCIONE CON EL VENTURE*********//
                //$obj_comprobante->clave_acceso = $claveAcceso;

                $obj_comprobante->id_envio = $request->id_envio;
                $obj_comprobante->tipo_comprobante = "01"; //CÓDIGO DE FACTURA A CLIENTE EXTERNO
                $obj_comprobante->monto_total = number_format($precio_total_con_impuestos + $precio_total_sin_impuestos, 2, ".", "");
                $obj_comprobante->fecha_emision = $request->fecha_pedidos_search;//now()->toDateString();
                $obj_comprobante->peso = number_format(($peso_neto/1000)+($peso_caja/1000),2,".","");
                $obj_comprobante->ambiente = env('ENTORNO');

                //*******COMENTADO PARA QUE LA FACTURACIÓN FUNCIONE CON EL VENTURE*********//
                //$obj_comprobante->secuencial = getDetallesClaveAcceso($claveAcceso, "SECUENCIAL");
                $obj_comprobante->secuencial = $secuencial;
                $obj_comprobante->estado = 1; //PARA QUE LA FACTURACION FUNCIONE CON EL VENTURE

                if ($obj_comprobante->save()) {
                    $model_comprobante = Comprobante::all()->last();
                    bitacora('comprobante', $model_comprobante->id_comprobante, 'I', 'Creación de un nuevo comprobante electrónico (factura)');
                    $objDetalleFactura = new DetalleFactura;
                    $objDetalleFactura->id_comprobante = $model_comprobante->id_comprobante;
                    $objDetalleFactura->razon_social_emisor = $datosEmpresa->razon_social;
                    $objDetalleFactura->nombre_comercial_emisor = $datosEmpresa->nombre;
                    $objDetalleFactura->direccion_matriz_emisor = $datosEmpresa->direccion_matriz;
                    $objDetalleFactura->direccion_establecimiento_emisor = $datosEmpresa->direccion_establecimiento;
                    $objDetalleFactura->obligado_contabilidad =  $datosEmpresa->obligado_contabilidad;
                    $objDetalleFactura->tipo_identificacion_comprador = $codigo_identificacion;
                    $objDetalleFactura->razon_social_comprador = $identificacion == "9999999999999" ? "CONSUMIDOR FINAL" : $nombre_cliente;
                    $objDetalleFactura->identificacion_comprador = $identificacion;
                    $objDetalleFactura->total_sin_impuestos = number_format($precio_total_sin_impuestos, 2, ".", "");
                    $objDetalleFactura->total_descuento = "0.00";
                    $objDetalleFactura->propina = 0.00;
                    $objDetalleFactura->importe_total = number_format($precio_total_con_impuestos + $precio_total_sin_impuestos, 2, ".", "");

                    if ($objDetalleFactura->save()) {
                        $model_detalle_factura = DetalleFactura::all()->last();
                        bitacora('detalle_factura', $model_detalle_factura->id_detalle_factura, 'I', 'Creación de un nuevo detalle de factura');
                        $objImpuestoDetalleFactura = new ImpuestoDetalleFactura;
                        $objImpuestoDetalleFactura->id_detalle_factura = $model_detalle_factura->id_detalle_factura;
                        $objImpuestoDetalleFactura->codigo_impuesto = $codigo_impuesto;//$informacionImpuestos['codigo'];
                        $objImpuestoDetalleFactura->codigo_porcentaje = $codigo_porcentaje_impuesto;//$informacionImpuestos['codigoPorcentaje'];
                        $objImpuestoDetalleFactura->base_imponible = number_format($precio_x_variedad, 2, ".", "");//$informacionImpuestos['baseImponible'];
                        $objImpuestoDetalleFactura->valor = $precio_total_con_impuestos;//$informacionImpuestos['valor'];

                        if($objImpuestoDetalleFactura->save()){
                            $model_impuesto_detalle_factura = ImpuestoDetalleFactura::all()->last();
                            bitacora('impuesto_detalle_factura', $model_impuesto_detalle_factura->id_impuesto_detalle_factura, 'I', 'Creación de un nuevo impuesto de detalle de factura');
                            $iteracion = 0;
                            if($envio->pedido->tipo_especificacion === "N") {
                                foreach ($envio->pedido->detalles as $x => $det_ped) {
                                    $precio = explode("|", $det_ped->precio);
                                    $i = 0;
                                    foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp) {
                                        foreach ($esp_emp->detalles as $n => $det_esp_emp) {
                                            $precio_x_variedad = ($det_esp_emp->cantidad * ((float)explode(";", $precio[$i])[0]) * $esp_emp->cantidad * $det_ped->cantidad);
                                            $variedad = getVariedad($det_esp_emp->id_variedad);
                                            if ($det_esp_emp->longitud_ramo != null) {
                                                foreach (getUnidadesMedida($det_esp_emp->id_unidad_medida) as $umLongitud)
                                                    if ($umLongitud->tipo == "L")
                                                        $umL = $umLongitud->siglas;
                                            } else {
                                                $umL = "";
                                            }
                                            $longitudRamo = $det_esp_emp->longitud_ramo != "" ? $det_esp_emp->longitud_ramo : "";
                                            $clasificacionRamo = getClasificacionRamo($det_esp_emp->id_clasificacion_ramo);
                                            foreach (getUnidadesMedida($clasificacionRamo->id_unidad_medida) as $umPeso) $umPeso->tipo == "P" ? $umPeso = $umPeso->siglas : $umPeso = "";
                                            $objDesgloseEnvioFactura = new DesgloseEnvioFactura;
                                            $objDesgloseEnvioFactura->id_comprobante = $model_comprobante->id_comprobante;
                                            $objDesgloseEnvioFactura->codigo_principal = 'ENV' . str_pad($request->id_envio, 9, "0", STR_PAD_LEFT);
                                            $objDesgloseEnvioFactura->descripcion = $variedad->planta->nombre . " (" . $variedad->siglas . ") " . $clasificacionRamo->nombre . $umPeso . " " . $longitudRamo . $umL;
                                            $objDesgloseEnvioFactura->cantidad = number_format(($det_esp_emp->cantidad * $esp_emp->cantidad * $det_ped->cantidad), 2, ".", "");
                                            $objDesgloseEnvioFactura->precio_unitario = number_format(explode(";", $precio[$i])[0], 2, ".", "");
                                            $objDesgloseEnvioFactura->descuento = '0.00';
                                            $objDesgloseEnvioFactura->precio_total_sin_impuesto = number_format($precio_x_variedad, 2, ".", "");

                                            if ($objDesgloseEnvioFactura->save()) {
                                                $model_desglose_envio_factura = DesgloseEnvioFactura::all()->last();
                                                bitacora('desglose_envio_factura', $model_desglose_envio_factura->id_desglose_envio_factura, 'I', 'Creación de un nuevo desglose de envio de factura 123');
                                                $objImpuestoDesgloseEnvioFactura = new ImpuestoDesgloseEnvioFactura;
                                                $objImpuestoDesgloseEnvioFactura->id_desglose_envio_factura = $model_desglose_envio_factura->id_desglose_envio_factura;
                                                $objImpuestoDesgloseEnvioFactura->codigo_impuesto = $codigo_impuesto;
                                                $objImpuestoDesgloseEnvioFactura->codigo_porcentaje = $codigo_porcentaje_impuesto;
                                                $objImpuestoDesgloseEnvioFactura->base_imponible = number_format($precio_x_variedad, 2, ".", "");
                                                $objImpuestoDesgloseEnvioFactura->valor = is_numeric($tipoImpuesto->porcentaje) ? number_format($precio_x_variedad * ($tipoImpuesto->porcentaje / 100), 2, ".", "") : "0.00";
                                                if($objImpuestoDesgloseEnvioFactura->save()){
                                                    $omdel_impuesto_desglose_envio_factura = ImpuestoDesgloseEnvioFactura::all()->last();
                                                    $iteracion++;
                                                    bitacora('impuesto_desglose_envio_factura', $omdel_impuesto_desglose_envio_factura->id_impuesto_desglose_envio_factura, 'I', 'Creación de un nuevo impuesto del desglose de una factura');
                                                }
                                            }
                                            $i++;
                                        }
                                    }
                                }
                            }
                            else if($envio->pedido->tipo_especificacion === "T"){
                                foreach ($envio->pedido->detalles as $x => $det_ped) {
                                    foreach($det_ped->coloraciones as $y => $coloracion){
                                        $cant_esp_emp = $coloracion->especificacion_empaque->cantidad;
                                        $i=0;
                                        foreach($coloracion->marcaciones_coloraciones as $m_c){
                                            if($m_c->cantidad > 0 ){
                                                if($coloracion->precio==""){
                                                    foreach (explode("|", $det_ped->precio) as $p)
                                                        if($m_c->id_detalle_especificacionempaque == explode(";",$p)[1])
                                                            $precio = explode(";",$p)[0];
                                                }else{
                                                    foreach(explode("|",$coloracion->precio) as $p)
                                                        if($m_c->id_detalle_especificacionempaque == explode(";",$p)[1])
                                                            $precio = explode(";",$p)[0];
                                                }
                                                $precio_x_variedad = $m_c->cantidad * $precio * $cant_esp_emp;
                                                $variedad = getVariedad($m_c->detalle_especificacionempaque->id_variedad);
                                                if($m_c->detalle_especificacionempaque->longitud_ramo != null){
                                                    foreach (getUnidadesMedida($m_c->detalle_especificacionempaque->id_unidad_medida) as $umLongitud)
                                                        if($umLongitud->tipo == "L")
                                                            $umL = $umLongitud->siglas;
                                                }else{
                                                    $umL ="";
                                                }
                                                $longitudRamo = $m_c->detalle_especificacionempaque->longitud_ramo != "" ? $m_c->detalle_especificacionempaque->longitud_ramo : "";
                                                $clasificacionRamo = getClasificacionRamo($m_c->detalle_especificacionempaque->id_clasificacion_ramo);
                                                foreach (getUnidadesMedida($clasificacionRamo->id_unidad_medida) as $umPeso)
                                                    $umPeso->tipo == "P" ? $umPeso = $umPeso->siglas : $umPeso ="";
                                                $descripcion_detalle = $variedad->planta->nombre . " (" . $variedad->siglas . ") " . $clasificacionRamo->nombre.$umPeso . " " . $longitudRamo.$umL. " ". $coloracion->color->nombre;

                                                $objDesgloseEnvioFactura = new DesgloseEnvioFactura;
                                                $objDesgloseEnvioFactura->id_comprobante = $model_comprobante->id_comprobante;
                                                $objDesgloseEnvioFactura->codigo_principal = 'ENV' . str_pad($request->id_envio, 9, "0", STR_PAD_LEFT);
                                                $objDesgloseEnvioFactura->descripcion = $descripcion_detalle;
                                                $objDesgloseEnvioFactura->cantidad = number_format(($m_c->cantidad * $m_c->detalle_especificacionempaque->especificacion_empaque->cantidad * $m_c->marcacion->piezas), 2, ".", "");
                                                $objDesgloseEnvioFactura->precio_unitario = number_format($precio, 2, ".", "");
                                                $objDesgloseEnvioFactura->descuento = '0.00';
                                                $objDesgloseEnvioFactura->precio_total_sin_impuesto = number_format($precio_x_variedad, 2, ".", "");

                                                if ($objDesgloseEnvioFactura->save()) {
                                                    $model_desglose_envio_factura = DesgloseEnvioFactura::all()->last();
                                                    bitacora('desglose_envio_factura', $model_desglose_envio_factura->id_desglose_envio_factura, 'I', 'Creación de un nuevo desglose de envio de factura');
                                                    $objImpuestoDesgloseEnvioFactura = new ImpuestoDesgloseEnvioFactura;
                                                    $objImpuestoDesgloseEnvioFactura->id_desglose_envio_factura = $model_desglose_envio_factura->id_desglose_envio_factura;
                                                    $objImpuestoDesgloseEnvioFactura->codigo_impuesto = $codigo_impuesto;
                                                    $objImpuestoDesgloseEnvioFactura->codigo_porcentaje = $codigo_porcentaje_impuesto;
                                                    $objImpuestoDesgloseEnvioFactura->base_imponible = number_format($precio_x_variedad, 2, ".", "");
                                                    $objImpuestoDesgloseEnvioFactura->valor = is_numeric($tipoImpuesto->porcentaje) ? number_format($precio_x_variedad * ($tipoImpuesto->porcentaje / 100), 2, ".", "") : "0.00";
                                                    if($objImpuestoDesgloseEnvioFactura->save()){
                                                        $iteracion++;
                                                        $omdel_impuesto_desglose_envio_factura = ImpuestoDesgloseEnvioFactura::all()->last();
                                                        bitacora('impuesto_desglose_envio_factura', $omdel_impuesto_desglose_envio_factura->id_impuesto_desglose_envio_factura, 'I', 'Creación de un nuevo impuesto del desglose de una factura');
                                                    }
                                                 }
                                                $request->cant_variedades = $iteracion;
                                                $i++;
                                            }
                                        }
                                    }
                                }
                            }

                            if ($iteracion === (int)$request->cant_variedades) {

                                //*******COMENTADO PARA QUE LA FACTURACIÓN FUNCIONE CON EL VENTURE*********//
                                /*$save_xml = $xml->save(env('PATH_XML_GENERADOS')."/facturas/".$nombre_xml);
                                if ($save_xml && $save_xml > 0) {
                                    $resultado = firmarComprobanteXml($nombre_xml,"/facturas/",$model_comprobante->empresa->firma_electronica,$model_comprobante->empresa->contrasena_firma_digital);
                                    if ($resultado) {
                                        $class = 'warning';
                                        if ($resultado == 5) { //FIRMADO CON ÉXITO
                                            $class = 'success';
                                            $obj_comprobante = Comprobante::find($model_comprobante->id_comprobante);
                                            $obj_comprobante->estado = 1;
                                            $obj_comprobante->save();

                                            if($envio->pedido->id_comprobante_temporal != null && $envio->pedido->id_comprobante_temporal != ""){

                                                $dataComprobanteObsoleto = Comprobante::where('comprobante.id_comprobante',$envio->pedido->id_comprobante_temporal)
                                                    ->join('detalle_factura as df','comprobante.id_comprobante','df.id_comprobante')
                                                    ->join('impuesto_detalle_factura as idf','df.id_detalle_factura','idf.id_detalle_factura')
                                                    ->join('desglose_envio_factura as def','comprobante.id_comprobante','def.id_comprobante')
                                                    ->join('impuesto_desglose_envio_factura as idef','def.id_desglose_envio_factura','idef.id_desglose_envio_factura')
                                                    ->select('clave_acceso','comprobante.id_comprobante','df.id_detalle_factura','id_impuesto_desglose_envio_factura')->get();
                                                //dd($envio->pedido, $dataComprobanteObsoleto);
                                                if($dataComprobanteObsoleto != null && $dataComprobanteObsoleto != ""){
                                                    foreach ($dataComprobanteObsoleto as $item) ImpuestoDesgloseEnvioFactura::destroy($item->id_impuesto_desglose_envio_factura);
                                                    DesgloseEnvioFactura::where('id_comprobante',$dataComprobanteObsoleto[0]->id_comprobante)->delete();
                                                    ImpuestoDetalleFactura::where('id_detalle_factura', $dataComprobanteObsoleto[0]->id_detalle_factura)->delete();
                                                    DetalleFactura::where('id_comprobante', $dataComprobanteObsoleto[0]->id_comprobante)->delete();
                                                    Comprobante::destroy($dataComprobanteObsoleto[0]->id_comprobante);
                                                    $p = Pedido::find($envio->pedido->id_pedido);
                                                    $p->clave_acceso_temporal = null;
                                                    $p->id_comprobante_temporal = null;
                                                    $p->save();
                                                }
                                            }
                                        }
                                        $msg .= "<div class='alert text-center  alert-" . $class . "'>" .
                                            "<p> " . mensajeFirmaElectronica($resultado, getDetallesClaveAcceso($claveAcceso, 'SECUENCIAL')) . "</p>"
                                            . "</div>";

                                        if($request->envio_correo == "true"){
                                            if (file_exists(env('PATH_XML_FIRMADOS')."/facturas/".$claveAcceso.".xml")){
                                                $archivo = file_get_contents(env('PATH_XML_FIRMADOS')."/facturas/".$claveAcceso.".xml");
                                                $autorizacion = simplexml_load_string($archivo);
                                                $numeroComprobante = getDetallesClaveAcceso((String)$autorizacion->infoTributaria->claveAcceso, 'SERIE').getDetallesClaveAcceso((String)$autorizacion->infoTributaria->claveAcceso, 'SECUENCIAL');
                                                generaDocumentoPDF($autorizacion,"01",true);
                                                enviarMailComprobanteCliente("01", (String)$autorizacion->infoAdicional->campoAdicional[1], (String)$autorizacion->infoFactura->razonSocialComprador, (String)$autorizacion->infoTributaria->claveAcceso, $numeroComprobante,true,$request->arrCorreos);
                                            }else{
                                                $msg .= "<div class='alert text-center  alert-danger'>" .
                                                    "<p> EL correo no pudo ser enviado al cliente debido a que el archivo PDF de la factura creada no existe en el servidor, por favor contactarse con el área de sistemas. </p>"
                                                    . "</div>";
                                            }
                                        }

                                        if($request->envio_correo_agencia_carga == "true"){
                                            if (file_exists(env('PATH_XML_FIRMADOS')."/facturas/".$claveAcceso.".xml")){
                                                $archivo = file_get_contents(env('PATH_XML_FIRMADOS')."/facturas/".$claveAcceso.".xml");
                                                $autorizacion = simplexml_load_string($archivo);
                                                $numeroComprobante = getDetallesClaveAcceso((String)$autorizacion->infoTributaria->claveAcceso, 'SERIE').getDetallesClaveAcceso((String)$autorizacion->infoTributaria->claveAcceso, 'SECUENCIAL');
                                                generaDocumentoPDF($autorizacion,"01",true);
                                                $agenciaCarga  = getPedido(getEnvio($request->id_envio)->id_pedido)->detalles[0]->agencia_carga;
                                                enviarMailComprobanteAgenciaCarga("01", $agenciaCarga->correo, $agenciaCarga->nombre, (String)$autorizacion->infoTributaria->claveAcceso, $numeroComprobante,true);
                                            }else{
                                                $msg .= "<div class='alert text-center  alert-danger'>" .
                                                    "<p> EL correo no pudo ser enviado a la agencia de carga debido a que el archivo PDF de la factura creada no existe en el servidor, por favor contactarse con el área de sistemas. </p>"
                                                    . "</div>";
                                            }
                                        }

                                    } else {
                                        $msg .= "<div class='alert text-center  alert-danger'>" .
                                            "<p>Hubo un error al realizar el proceso de la firma de la factura N# " . $nombre_xml . " del envío N#" . str_pad($request->id_envio, 9, "0", STR_PAD_LEFT) . ", intente nuevamente realizar la firma del mismo filtrando por 'NO FIRMADOS'</p>"
                                            . "</div>";
                                    }
                                } else {
                                    $impuestosDesgloseFactura = getImpuestosDesglosesFacturas($model_comprobante->id_comprobante);
                                    foreach($impuestosDesgloseFactura as $impDesFact)
                                        ImpuestoDesgloseEnvioFactura::where('id_desglose_envio_factura', $impDesFact->id_desglose_envio_factura)->delete();
                                    ImpuestoDesgloseEnvioFactura::where('id_desglose_envio_factura', $model_desglose_envio_factura->id_desglose_envio_factura)->delete();
                                    DesgloseEnvioFactura::where('id_comprobante', $model_comprobante->id_comprobante)->delete();
                                    ImpuestoDetalleFactura::where('id_detalle_factura', $model_detalle_factura->id_detalle_factura)->delete();
                                    DetalleFactura::where('id_comprobante', $model_comprobante->id_comprobante)->delete();
                                    Comprobante::destroy($model_comprobante->id_comprobante);
                                    $msg .= "<div class='alert text-center  alert-danger'>" .
                                        "<p>La factura " . $nombre_xml . " del envío N#" . str_pad($request->id_envio, 9, "0", STR_PAD_LEFT) . " no pudo ser guardada, por favor intente facturar el envío nuevamente</p>"
                                        . "</div>";
                                }*/

                                $msg .= "<div class='alert text-center  alert-success'>" .
                                    "<p>Se ha registrado con éxito la factura ".$nombre_xml."</p>"
                                    . "</div>";

                            } else {
                                $impuestosDesgloseFactura = getImpuestosDesglosesFacturas($model_comprobante->id_comprobante);
                                foreach($impuestosDesgloseFactura as $impDesFact)
                                    ImpuestoDesgloseEnvioFactura::where('id_desglose_envio_factura', $impDesFact->id_desglose_envio_factura)->delete();
                                DesgloseEnvioFactura::where('id_comprobante', $model_comprobante->id_comprobante)->delete();
                                ImpuestoDetalleFactura::where('id_detalle_factura', $model_detalle_factura->id_detalle_factura)->delete();
                                DetalleFactura::where('id_comprobante', $model_comprobante->id_comprobante)->delete();
                                Comprobante::destroy($model_comprobante->id_comprobante);
                                $msg .= "<div class='alert text-center  alert-danger'>" .
                                    "<p>La factura " . $nombre_xml . " del envío N#" . str_pad($request->id_envio, 9, "0", STR_PAD_LEFT) . " no pudo ser generada, por favor intente facturar el envío nuevamente</p>"
                                    . "</div>";
                            }
                        } else {
                            DetalleFactura::where('id_comprobante', $model_detalle_factura->id_detalle_factura)->delete();
                            Comprobante::destroy($model_comprobante->id_comprobante);
                            $msg .= "<div class='alert text-center  alert-danger'>" .
                                "<p>Hubo un error al guardar la factura " . $nombre_xml . "  en la base de datos, por favor intente facturar el pedido nuevamente</p>"
                                . "</div>";
                        }
                    } else {
                        Comprobante::destroy($model_comprobante->id_comprobante);
                        $msg .= "<div class='alert text-center  alert-danger'>" .
                            "<p>Hubo un error al guardar la factura " . $nombre_xml . " en la base de datos, por favor intente facturar el pedido nuevamente</p>"
                            . "</div>";
                    }
                } else {
                    $msg .= "<div class='alert text-center  alert-danger'>" .
                        "<p>Hubo un error al guardar la factura " . $nombre_xml . " en la base de datos, por favor intente facturar el pedido nuevamente</p>"
                        . "</div>";
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
        return $msg;
    }

    public function generar_comprobante_lote(Request $request){
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $secuencial = getSecuencial('00',getConfiguracionEmpresa($request->id_configuracion_empresa));
        $punto_acceso = Usuario::where('id_usuario', session('id_usuario'))->first()->punto_acceso;
        $secuencial = str_pad($secuencial, 9, "0", STR_PAD_LEFT);
        $xml = new DomDocument('1.0', 'UTF-8');
        $factura = $xml->createElement('lote');
        $factura->setAttribute('version', '1.0.0');
        $xml->appendChild($factura);
        $fechaEmision = now()->format('dmY');
        $tipoComprobante = '00';
        $ruc = getConfiguracionEmpresa($request->id_configuracion_empresa)->ruc;
        $entorno = env('ENTORNO');
        $serie = '001' . $punto_acceso;
        $tipo_emision = '1';
        $codigo_numerico = env('CODIGO_NUMERICO');
        $cadena = $fechaEmision . $tipoComprobante . $ruc . $entorno . $serie . $secuencial . $codigo_numerico . $tipo_emision;
        $digito_verificador = generaDigitoVerificador($cadena);
        $claveAcceso = $cadena . $digito_verificador;
        $nodeClaveAcceso = $xml->createElement('claveAcceso', $claveAcceso);
        $factura->appendChild($nodeClaveAcceso);
        $nodeRuc = $xml->createElement('ruc', $ruc);
        $factura->appendChild($nodeRuc);
        $nodeComprobantes = $xml->createElement('comprobantes');
        $factura->appendChild($nodeComprobantes);
        $path_firmados = env('PATH_XML_FIRMADOS');
        $sub_carpeta = getSubCarpetaArchivo(false,$request->tipo_comprobante);

        foreach ($request->arrComprobante as $xml_firmado) {
            $data_xml_firmado = file_get_contents($path_firmados.$sub_carpeta.$xml_firmado.".xml");
            $nodeComprobante = $xml->createElement('comprobante', $data_xml_firmado);
            $nodeComprobantes->appendChild($nodeComprobante);
        }
        $xml->formatOutput = true;
        $xml->saveXML();

        $nombre_xml = $claveAcceso . ".xml";
        $msg = "<div class='alert text-center  alert-danger'>" .
            "<p>Hubo un error al realizar el proceso del envío del comprobante, intente facturar el envío nuevamente</p>"
            . "</div>";

        $obj_comprobante = new Comprobante;
        $obj_comprobante->clave_acceso = $claveAcceso;
        $obj_comprobante->estado = 1;
        $obj_comprobante->tipo_comprobante = $tipoComprobante; //CÓDIGO DE ENVIO POR LOTE
        $obj_comprobante->fecha_emision = now()->toDateString();
        $obj_comprobante->ambiente = env('ENTORNO');

        if ($obj_comprobante->save()) {
            $model_comprobante = Comprobante::all()->last();

            $save_xml = $xml->save(env('PATH_XML_FIRMADOS') .$sub_carpeta. $nombre_xml);
            if ($save_xml && $save_xml > 0) {
                $resultado = enviarComprobante($claveAcceso . ".xml", $claveAcceso,$sub_carpeta);
                if ($resultado) {
                    //$obj_comprobante = Comprobante::find($model_comprobante->id_comprobante);
                    switch ($resultado[0]) {
                        case '0':
                            $class = "warning";
                            //$this->eliminar_registro_archivo_lote($model_comprobante->id_comprobante,$claveAcceso,"firamdos");
                            //$obj_comprobante->estado = 4;
                            break;
                        case '1':
                            $class = "success";
                            //$obj_comprobante->estado = 5;
                            break;
                        case '2':
                            $class = "danger";
                            //$this->eliminar_registro_archivo_lote($model_comprobante->id_comprobante,$claveAcceso,"firamdos");
                            break;
                    }
                    $msg = respuesta_autorizacion_comprobante($claveAcceso,$sub_carpeta,$request->envio_correo);
                    /*$msg = "<div class='alert text-center  alert-".$class."'>" .
                        "<p> ".mensaje_envio_comprobante($resultado[0])."</p>"
                        . "</div>";*/
                } else {
                    $this->eliminar_registro_archivo_lote($model_comprobante->id_comprobante, $claveAcceso, "firamdos");
                }
            } else {
                $this->eliminar_registro_archivo_lote($model_comprobante->id_comprobante, $claveAcceso, "");
            }
        }
        return $msg;
    }

    public function firmar_comprobante(Request $request){
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        foreach ($request->arrNoFirmados as $idComprobante) {
            $msg = '';
            $comprobante = Comprobante::where('id_comprobante', $idComprobante)->first();
            if(!file_exists(env('PATH_XML_GENERADOS').$comprobante->clave_acceso.".xml")){
                $msg .= "<div class='alert text-center  alert-danger'>" .
                    "<p> No se encontró el comprobante electrónico generado relacionado a este registro, comuníquese con el área de sistemas</p>"
                    . "</div>";
            }else {
                $resultado = firmarComprobanteXml($comprobante->clave_acceso . ".xml",$request->carpeta,$comprobante->empresa->firma_electronica,$comprobante->empresa->contrasena_firma_digital);
                if ($resultado) {
                    $class = 'warning';
                    if ($resultado == 5) {
                        $class = 'success';
                        $obj_comprobante = Comprobante::find($idComprobante);
                        $obj_comprobante->estado = 1;
                        $obj_comprobante->save();
                    }
                    $msg .= "<div class='alert text-center  alert-" . $class . "'>" .
                                "<p> " . mensajeFirmaElectronica($resultado, getDetallesClaveAcceso($comprobante->clave_acceso, 'SECUENCIAL')) . "</p>"
                          . "</div>";
                } else {
                    $msg .= "<div class='alert text-center  alert-danger'>" .
                        "<p>Hubo un error al realizar el proceso de la firma del comprobante N# " .$comprobante->clave_acceso.".xml, intente nuevamente realizar la firma del mismo filtrando por GENERADOS</p>"
                        . "</div>";
                }
            }
        }
        return $msg;
    }

    /*public function enviar_documento_electronico(Request $request){

        $resultado = enviarComprobante("0702201900179244632500110010010000001401234567813.xml","0702201900179244632500110010010000001401234567813");
        if($resultado){
            switch ($resultado[0]) {
                case '0':
                    $class = "warning";
                    break;
                case '1':
                    $class = "success";
                    break;
                case '2':
                    $class = "danger";
                    break;
            }
            $msg = "<div class='alert text-center  alert-".$class."'>" .
                "<p> ".mensaje_envio_comprobante($resultado[0])."</p>"
                . "</div>";
        }else{
            $msg = "<div class='alert text-center  alert-danger'>" .
                "<p>Hubo un error al realizar el proceso del envío del comprobante, intente el envío nuevamente</p>"
                . "</div>";
        }
        return $msg;
    }

    public function autorizacion_comprobante(Request $request){
        respuesta_autorizacion_comprobante("0602201900179244632500110010010000001361234567817");
    }

    public function formulario_facturacion(Request $request){
        return view('adminlte.gestion.configuracion_facturacion.tipo_comprobantes.forms.form_facturacion');
    }*/

    public function eliminar_registro_archivo_lote($id_comprobante,$claveAcceso,$firmados=""){
        unlink(env('PATH_XML_FIRMADOS').$claveAcceso.".xml");
        Comprobante::destroy($id_comprobante);
        if($firmados!="")
            unlink(env('PATH_XML_FIRMADOS').$claveAcceso.".xml");
    }

    public function reenviar_correo(Request $request){
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $msg = "<div class='alert text-center  alert-success'>" .
            "<p> El correo ha sido enviado con éxito al cliente </p>"
            . "</div>";
        $comprobante = Comprobante::join('envio as e','comprobante.id_envio','e.id_envio')
            ->join('pedido as p','e.id_pedido','p.id_pedido')
            ->join('detalle_cliente as dc','p.id_cliente','dc.id_cliente')
            ->where([
                ['dc.estado',1],
                ['comprobante.clave_acceso',$request->comprobante]
        ])->select('dc.nombre','dc.correo','comprobante.numero_comprobante')->first();

        if(isset($comprobante->numero_comprobante) && $comprobante->numero_comprobante == null){
            $msg = "<div class='alert text-center  alert-danger'>" .
            "<p> El mail no puede ser enviado debido a que este registro no posee número de comprobante, comuníquese con el departamento de tecnología </p>"
            . "</div>";
        }else {
            enviarMailComprobanteCliente("01", $comprobante->correo, $comprobante->nombre, $request->comprobante, $comprobante->numero_comprobante);
        }
        return $msg;
    }

    public function ver_factura_aprobada_sri($clave_acceso){

        $tipo_documento = getDetallesClaveAcceso($clave_acceso,'TIPO_COMPROBANTE');

        if($tipo_documento == "01")
            $dataComprobante = Comprobante::where([
                ['clave_acceso', $clave_acceso],
                ['tipo_comprobante','01']
            ])->select('numero_comprobante','id_envio')->first();

        if($tipo_documento == "06")
            $dataComprobante = Comprobante::where([
                ['clave_acceso', $clave_acceso],
                ['tipo_comprobante','06']
            ])->join('detalle_guia_remision as dgr','comprobante.id_comprobante','dgr.id_comprobante')->select('id_comprobante_relacionado')->first();

        $cliente = new SoapClient(env('URL_WS_ATURIZACION'));
        $response = $cliente->autorizacionComprobante(["claveAccesoComprobante" => $clave_acceso]);
        $autorizacion = $response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion;

        $data = [
            'autorizacion' => $autorizacion,
            'img_clave_acceso' => generateCodeBarGs1128((String)$autorizacion->numeroAutorizacion),
            'obj_xml' => simplexml_load_string($autorizacion->comprobante),
            'numeroComprobante' => $dataComprobante->numero_comprobante,
            'detalles_envio' => $tipo_documento == "01" ? getEnvio($dataComprobante->id_envio)->detalles : "",
            'pedido' => $tipo_documento == "06" ? getComprobante($dataComprobante->id_comprobante_relacionado)->envio->pedido : ""
        ];
        if($tipo_documento == "01")
           return PDF::loadView('adminlte.gestion.comprobante.partials.pdf.factura', compact('data'))->stream();
        if($tipo_documento == "06")
           return PDF::loadView('adminlte.gestion.comprobante.partials.pdf.guia', compact('data'))->stream();
    }

    public function ver_pre_factura($clave_acceso,$cliente=false){

        $sub_carpeta = getSubCarpetaArchivo($clave_acceso);
        if (file_exists(env('PATH_XML_FIRMADOS').$sub_carpeta.$clave_acceso.".xml")){
            $archivo = file_get_contents(env('PATH_XML_FIRMADOS').$sub_carpeta.$clave_acceso.".xml");
            $dataComprobante = Comprobante::where('clave_acceso',$clave_acceso)->select('id_envio')->first();
            $autorizacion = simplexml_load_string($archivo);
            $data = [
                'autorizacion' => $autorizacion,
                'img_clave_acceso' => null,
                'obj_xml' => $autorizacion,
                'numeroComprobante' => getDetallesClaveAcceso((String)$autorizacion->infoTributaria->claveAcceso, 'SERIE').getDetallesClaveAcceso((String)$autorizacion->infoTributaria->claveAcceso, 'SECUENCIAL'),
                'detalles_envio' => getEnvio($dataComprobante->id_envio)->detalles
            ];
            if(!$cliente){
                return PDF::loadView('adminlte.gestion.comprobante.partials.pdf.factura', compact('data'))->stream();
            }else{
                return PDF::loadView('adminlte.gestion.comprobante.partials.pdf.factura_cliente', compact('data'))->stream();
            }

        }else{
            $msg = "<div class='alert text-center  alert-danger'>" .
            "<p> El archivo del documento no existe, por favor contactarse con el área de sistemas. </p>"
            . "</div>";
            return $msg;
        }

    }

    //FUNCION PARA QUE LA FACTURACION FUNCIONE CON EL VENTURE (SE LEEN LOS DATOS DESDE LA BD PARA CREAR LA PRE-FACTURA)
    public function ver_pre_factura_bd(Request $request, $secuencial,$cliente=false){
        $path = explode('/',$request->path())[0];
        if($path === "comprobante"){
            $comprobante = Comprobante::where([
                ['secuencial',$secuencial],
                ['tipo_comprobante', '01']
            ])->first();
        }elseif($path=== "pedidos"){
            $comprobante = getPedido($secuencial)->envios[0]->comprobante;
        }

        if($comprobante == null){
            $data = null;
        }else{
            $img_clave_acceso = null;
            if($comprobante->clave_acceso != null)
                $img_clave_acceso = generateCodeBarGs1128($comprobante->clave_acceso);
            $data = [
                'empresa' => $comprobante->envio->pedido->empresa,
                'secuencial' => $comprobante->secuencial,
                'comprobante' => $comprobante,
                'img_clave_acceso' => $img_clave_acceso
            ];
        }

        if(!$cliente){
            return PDF::loadView('adminlte.gestion.comprobante.partials.pdf.factura_bd', compact('data'))->stream();
        }else{
            return PDF::loadView('adminlte.gestion.comprobante.partials.pdf.factura_cliente_bd', compact('data'))->stream();
        }
    }

    public function ver_guia_remision_bd($secuencial){
        $comprobante = Comprobante::where([['secuencial',$secuencial],['tipo_comprobante', '06']])->first();
        $dataComprobanteRelacionado = Comprobante::where([
            ['secuencial',$secuencial],
            ['tipo_comprobante',"06"]
        ])->join('detalle_guia_remision as dgr','comprobante.id_comprobante','dgr.id_comprobante')->select('id_comprobante_relacionado')->first();
        $detalle_despacho = getDetalleDespacho(getComprobante($dataComprobanteRelacionado->id_comprobante_relacionado)->envio->pedido->id_pedido);
        $img_clave_acceso = null;
        if($comprobante->clave_acceso != null) $img_clave_acceso = generateCodeBarGs1128($comprobante->clave_acceso);
        $data = [
            'img_clave_acceso' => $img_clave_acceso,
            'pedido' => getComprobante($dataComprobanteRelacionado->id_comprobante_relacionado)->envio->pedido,
            'comprobante' => $comprobante,
            'secuencial' => $comprobante->secuencial,
            'detalle_despacho' => $detalle_despacho,
            'dataComprobanteRelacionado' => $dataComprobanteRelacionado
        ];
        return PDF::loadView('adminlte.gestion.comprobante.partials.pdf.guia_bd', compact('data'))->stream();
    }

    public function ver_pre_guia_remision($clave_acceso){
        $sub_carpeta = getSubCarpetaArchivo($clave_acceso);
        if (file_exists(env('PATH_XML_FIRMADOS').$sub_carpeta.$clave_acceso.".xml")){
            $archivo = file_get_contents(env('PATH_XML_FIRMADOS').$sub_carpeta.$clave_acceso.".xml");
            $dataComprobante = Comprobante::where('clave_acceso',$clave_acceso)->join('detalle_guia_remision as dgr','comprobante.id_comprobante','dgr.id_comprobante')->select('id_comprobante_relacionado')->first();
            $autorizacion = simplexml_load_string($archivo);
            $data = [
                'autorizacion' => $autorizacion,
                'img_clave_acceso' => null,
                'obj_xml' => $autorizacion,
                'numeroComprobante' => getDetallesClaveAcceso((String)$autorizacion->infoTributaria->claveAcceso, 'SERIE').getDetallesClaveAcceso((String)$autorizacion->infoTributaria->claveAcceso, 'SECUENCIAL'),
                'pedido' => getComprobante($dataComprobante->id_comprobante_relacionado)->envio->pedido
            ];
            return PDF::loadView('adminlte.gestion.comprobante.partials.pdf.guia', compact('data'))->stream();
        }else{
            $msg = "<div class='alert text-center  alert-danger'>" .
                "<p> El archivo del documento no existe, por favor contactarse con el área de sistemas. </p>"
                . "</div>";
            return $msg;
        }
    }

    public function generar_comprobante_guia_remision(Request $request){
        $msg = "";
        $success =false;
        $despacho = getDetalleDespacho(getComprobante($request->id_comprobante)->envio->pedido->id_pedido);
        if($despacho == null){
            $msg = "<div class='alert text-center  alert-danger'>" .
                "<p>Debe realizar primero el despacho del pedido de esta factura antes de realizar la guía de remisión</p>"
                . "</div>";
        }else{
            ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
            $despacho = $despacho->despacho;
            $valida = Validator::make($request->all(), [
                'id_comprobante' => 'required',
                'ruta' => 'required',
            ]);
            $fechaEmision = Carbon::now()->format('dmY');
            if(!$valida->fails()) {
                if($request->update === "true"){
                    $dataComprobante = Comprobante::where('id_envio',$request->id_envio)
                        ->join('detalle_factura as df','comprobante.id_comprobante','df.id_comprobante')
                        ->join('impuesto_detalle_factura as idf','df.id_detalle_factura','idf.id_detalle_factura')
                        ->join('desglose_envio_factura as def','comprobante.id_comprobante','def.id_comprobante')
                        ->join('impuesto_desglose_envio_factura as idef','def.id_desglose_envio_factura','idef.id_desglose_envio_factura')
                        ->select('clave_acceso','comprobante.id_comprobante','df.id_detalle_factura','id_impuesto_desglose_envio_factura')->get();

                    $secuencial = getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'SECUENCIAL');
                    $ruc = getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'RUC');
                    $codigo_numerico = getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'CODIGO_NUMERICO');
                    $tipoComprobante = getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'TIPO_COMPROBANTE');
                    $entorno = getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'ENTORNO');
                    $serie = getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'SERIE');
                    $tipo_emision = getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'TIPO_EMISION');
                    $punto_acceso=  getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'PUNTO_ACCESO');

                    foreach ($dataComprobante as $item)
                        ImpuestoDesgloseEnvioFactura::where('id_desglose_envio_factura', $item->id_impuesto_desglose_envio_factura)->delete();

                    DesgloseEnvioFactura::where('id_comprobante',$dataComprobante[0]->id_comprobante)->delete();
                    ImpuestoDetalleFactura::where('id_detalle_factura', $dataComprobante[0]->id_detalle_factura)->delete();
                    DetalleFactura::where('id_comprobante', $dataComprobante[0]->id_comprobante)->delete();
                    Comprobante::destroy($dataComprobante[0]->id_comprobante);

                }else{
                    $secuencial = getSecuencial('06',getComprobante($request->id_comprobante)->empresa);
                    $ruc = getComprobante($request->id_comprobante)->empresa->ruc;
                    $codigo_numerico = env('CODIGO_NUMERICO');
                    $tipoComprobante = '06';
                    $entorno = env('ENTORNO');
                    $punto_acceso = getUsuario(session('id_usuario'))->punto_acceso;
                    $serie = '001' . $punto_acceso;
                    $tipo_emision = '1';
                }

                $datosEmpresa = getComprobante($request->id_comprobante)->empresa;
                $xml = new DomDocument('1.0', 'UTF-8');
                $guiaRemision = $xml->createElement('guiaRemision');
                $guiaRemision->setAttribute('id', 'comprobante');
                $guiaRemision->setAttribute('version', '1.0.0');
                $xml->appendChild($guiaRemision);
                $infoTributaria = $xml->createElement('infoTributaria');
                $guiaRemision->appendChild($infoTributaria);
                $cadena = $fechaEmision . $tipoComprobante . $ruc . $entorno . $serie . $secuencial . $codigo_numerico . $tipo_emision;
                $digito_verificador = generaDigitoVerificador($cadena);
                $claveAcceso = $cadena . $digito_verificador;
                $informacionTributaria = [
                    'ambiente' => $entorno,
                    'tipoEmision' => $tipo_emision,
                    'razonSocial' => $datosEmpresa->razon_social,
                    'nombreComercial' => $datosEmpresa->nombre,
                    'ruc' => $ruc,
                    'claveAcceso' => $claveAcceso,
                    'codDoc' => '06',
                    'estab' => '001',
                    'ptoEmi' => $punto_acceso,
                    'secuencial' => $secuencial,
                    'dirMatriz' => $datosEmpresa->direccion_matriz
                ];

                foreach ($informacionTributaria as $key => $it) {
                    $nodo = $xml->createElement($key, $it);
                    $infoTributaria->appendChild($nodo);
                }

                $comprobante = getComprobante($request->id_comprobante);
                $pedido = $comprobante->envio->pedido;
                $envio = $comprobante->envio;
                $infoGuiaRemision = $xml->createElement('infoGuiaRemision');
                $guiaRemision->appendChild($infoGuiaRemision);
                $informacionGuiaRemision = [
                    'dirEstablecimiento' => $datosEmpresa->direccion_establecimiento,
                    'dirPartida' => $datosEmpresa->direccion_establecimiento,
                    'razonSocialTransportista' => $despacho->conductor->nombre,
                    'tipoIdentificacionTransportista'=>$despacho->conductor->tipo_identificacion,
                    'rucTransportista' => $despacho->conductor->ruc,
                    'obligadoContabilidad' => $datosEmpresa->obligado_contabilidad == 1 ? 'SI' : 'NO',
                    'fechaIniTransporte' => Carbon::parse($despacho->fecha_despacho)->format('d/m/Y'),
                    'fechaFinTransporte' => Carbon::parse($despacho->fecha_despacho)->format('d/m/Y'),
                    'placa' => $despacho->camion->placa
                ];

                foreach ($informacionGuiaRemision as $key => $iGR) {
                    $nodo = $xml->createElement($key, $iGR);
                    $infoGuiaRemision->appendChild($nodo);
                }

                $clienteTercero = FacturaClienteTercero::where('id_envio',$envio->id_envio)->first();
                $detallesPedido = DesgloseEnvioFactura::where('id_comprobante',$request->id_comprobante)->get();

                foreach ($pedido->cliente->detalles as $det_cliente)
                    if($pedido->cliente->estado == 1 && $det_cliente->estado == 1)
                        $cliente = $det_cliente;

                $clienteTercero !=null
                    ? $cliente = $clienteTercero
                    : $cliente = $cliente;

                $destinatarios = $xml->createElement('destinatarios');
                $guiaRemision->appendChild($destinatarios);
                $cantidad_destinatarios = 1;
                for ($i = 0; $i < $cantidad_destinatarios; $i++) {
                    $informacionDestinatario = [
                        'identificacionDestinatario' => $pedido->detalles[0]->agencia_carga->identificacion,
                        'razonSocialDestinatario' => isset($cliente->nombre) ? $cliente->nombre : $cliente->nombre_cliente_tercero,
                        'dirDestinatario' => $pedido->detalles[0]->agencia_carga->nombre,
                        'motivoTraslado' => 'Egreso por venta',
                        'ruta' => $request->ruta,
                        'codDocSustento' => $comprobante->tipo_comprobante,
                        'numDocSustento' => $comprobante->numero_comprobante,
                        'numAutDocSustento' => $comprobante->clave_acceso,
                        'fechaEmisionDocSustento' => Carbon::parse($comprobante->fecha_emision)->format('d/m/Y')
                    ];
                    $destinatario = $xml->createElement('destinatario');
                    $destinatarios->appendChild($destinatario);
                    foreach ($informacionDestinatario as $key => $iI) {
                        $nodo = $xml->createElement($key, $iI);
                        $destinatario->appendChild($nodo);
                    }

                    $detalles = $xml->createElement('detalles');
                    $destinatario->appendChild($detalles);

                    foreach ($detallesPedido as $det_ped) {
                        $informacionDetallePedido=[
                            'codigoInterno' => $det_ped->codigo_principal,
                            'descripcion' => $det_ped->descripcion,
                            'cantidad' => $det_ped->cantidad
                        ];
                        $detalle = $xml->createElement('detalle');
                        $detalles->appendChild($detalle);
                        foreach ($informacionDetallePedido as $key => $iI) {
                            $nodo = $xml->createElement($key, $iI);
                            $detalle->appendChild($nodo);
                        }
                    }
                }

                $informacionAdicional = $xml->createElement('infoAdicional');
                $guiaRemision->appendChild($informacionAdicional);

                $campos_adicionales = [
                    'Dirección'=> $cliente->direccion,
                    'Teléfono' => $cliente->telefono,
                    'Email' => getDetalleDespacho($pedido->id_pedido)->despacho->mail_resp_ofi_despacho,
                ];

                foreach ($campos_adicionales as $key => $ca) {
                    $campo_adicional = $xml->createElement('campoAdicional', $ca);
                    $campo_adicional->setAttribute('nombre', $key);
                    $informacionAdicional->appendChild($campo_adicional);
                }
                $xml->formatOutput = true;
                $xml->saveXML();
                $nombre_xml = $claveAcceso . ".xml";

                $obj_comprobante = new Comprobante;
                $obj_comprobante->clave_acceso = $claveAcceso;
                $obj_comprobante->tipo_comprobante = "06"; //CÓDIGO DE GUÍA DE REMISIÓN
                $obj_comprobante->fecha_emision = now()->toDateString();
                $obj_comprobante->ambiente = env('ENTORNO');

                if ($obj_comprobante->save()) {
                    $model_comprobante = Comprobante::all()->last();
                    bitacora('comprobante', $model_comprobante->id_comprobante, 'I', 'Creación de un nuevo comprobante electrónico');

                    $objDetalleGuiaRemision = new DetalleGuiaRemision;
                    $objDetalleGuiaRemision->id_comprobante = $model_comprobante->id_comprobante;
                    $objDetalleGuiaRemision->id_comprobante_relacionado = Comprobante::where('clave_acceso',$comprobante->clave_acceso)->first()->id_comprobante;
                    if($objDetalleGuiaRemision->save()){
                        $model_detalle_guia_remision = DetalleGuiaRemision::all()->last();
                        bitacora('detalle_guia_remision', $model_detalle_guia_remision->id_detalle_guia_remision, 'I', 'Creación de un nuevo detalle de guía de remisión');

                        $save_xml = $xml->save(env('PATH_XML_GENERADOS') ."/guias_remision/". $nombre_xml);
                        if ($save_xml && $save_xml > 0) {
                            $resultado = firmarComprobanteXml($nombre_xml,"/guias_remision/",$model_comprobante->empresa->firma_electronica,$model_comprobante->empresa->contrasena_firma_digital);
                            if ($resultado) {
                                $class = 'warning';
                                if ($resultado == 5) {
                                    $class = 'success';
                                    $obj_comprobante = Comprobante::find($model_comprobante->id_comprobante);
                                    $obj_comprobante->estado = 1;
                                    $obj_comprobante->save();
                                }
                                $msg = "<div class='alert text-center  alert-" . $class . "'>" .
                                    "<p> " . mensajeFirmaElectronica($resultado, getDetallesClaveAcceso($claveAcceso, 'SECUENCIAL')) . "</p>"
                                    . "</div>";
                                $success =true;
                            } else {
                                $msg = "<div class='alert text-center  alert-danger'>" .
                                    "<p>Hubo un error al realizar el proceso de la firma de la guía de remisión N# " . getDetallesClaveAcceso($claveAcceso, 'SECUENCIAL') . ", intente nuevamente realizar la firma del mismo filtrando por GENERADOS</p>"
                                    . "</div>";
                            }
                        }
                    }else{
                        Comprobante::destroy($model_comprobante->id_comprpobante);
                        $msg = "<div class='alert text-center  alert-danger'>" .
                            "<p>La guía de remisión " . $nombre_xml . str_pad(getDetallesClaveAcceso($claveAcceso, 'SECUENCIAL'), 9, "0", STR_PAD_LEFT) . " no pudo ser generada, por favor intente generarla nuevamente</p>"
                            . "</div>";
                    }
                }else{
                    $msg = "<div class='alert text-center  alert-danger'>" .
                        "<p>La guía de remisión " . $nombre_xml . str_pad(getDetallesClaveAcceso($claveAcceso, 'SECUENCIAL'), 9, "0", STR_PAD_LEFT) . " no pudo ser generada, por favor intente generarla nuevamente</p>"
                        . "</div>";
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
        }

        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function generar_comprobante_guia_remision_factura(Request $request){ //PARA EL VENTURE
        $msg = "";
        $success =false;
        $despacho = getDetalleDespacho(getComprobante($request->id_comprobante)->envio->pedido->id_pedido);
        if($despacho == null){
            $msg = "<div class='alert text-center  alert-danger'>" .
                "<p>Debe realizar primero el despacho del pedido de esta factura antes de realizar la guía de remisión</p>"
                . "</div>";
        }else{
            ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
            $valida = Validator::make($request->all(), [
                'id_comprobante' => 'required',
                'ruta' => 'required',
            ]);
            $fechaEmision = Carbon::now()->format('dmY');
            if(!$valida->fails()) {
                $ruc = getComprobante($request->id_comprobante)->empresa->ruc;
                $serie = '001'. getUsuario(session('id_usuario'))->punto_acceso;;
                $tipo_emision = '1';
                $datosEmpresa = getComprobante($request->id_comprobante)->empresa;
                $secuencial = $serie.getSecuencial('06',getComprobante($request->id_comprobante)->empresa);
                $comprobante = Comprobante::find($request->id_comprobante);

                $obj_comprobante = new Comprobante;
                $obj_comprobante->tipo_comprobante = "06"; //CÓDIGO DE GUÍA DE REMISIÓN
                $obj_comprobante->fecha_emision = now()->toDateString();
                $obj_comprobante->estado = 1;
                $obj_comprobante->secuencial = $secuencial;
                $obj_comprobante->ambiente = env('ENTORNO');

                if ($obj_comprobante->save()) {
                    $model_comprobante = Comprobante::all()->last();
                    bitacora('comprobante', $model_comprobante->id_comprobante, 'I', 'Creación de una nueva guía de remisión');
                    $objDetalleGuiaRemision = new DetalleGuiaRemision;
                    $objDetalleGuiaRemision->id_comprobante = $model_comprobante->id_comprobante;
                    $objDetalleGuiaRemision->id_comprobante_relacionado = Comprobante::where('secuencial',$comprobante->secuencial)->first()->id_comprobante;
                    $objDetalleGuiaRemision->razon_social_emisor = $datosEmpresa->razon_social;
                    $objDetalleGuiaRemision->direccion_matriz_emisor = $datosEmpresa->direccion_matriz;
                    $objDetalleGuiaRemision->direccion_establecimiento_emisor = $datosEmpresa->direccion_establecimiento;
                    $objDetalleGuiaRemision->obligado_contabilidad = $datosEmpresa->obligado_contabilidad;
                    $objDetalleGuiaRemision->identificacion_emisor = $model_comprobante->empresa->ruc;
                    $objDetalleGuiaRemision->destino = $request->ruta;
                    if($objDetalleGuiaRemision->save()){
                        $model_detalle_guia_remision = DetalleGuiaRemision::all()->last();
                        bitacora('detalle_guia_remision', $model_detalle_guia_remision->id_detalle_guia_remision, 'I', 'Creación de un nuevo detalle de guía de remisión');
                        $msg = "<div class='alert text-center  alert-success'>" .
                                    "<p>Se ha registrado la guía de remisión ".$secuencial." con éxito</p>"
                                ."</div>";
                        $success = true;
                    }else{
                        $success = false;
                        Comprobante::destroy($model_comprobante->id_comprobante);
                        $msg = "<div class='alert text-center  alert-danger'>" .
                                    "<p>La guía de remisión ".$secuencial." no pudo ser generada, por favor intente generarla nuevamente</p>"
                                ."</div>";
                    }
                }else{
                    $success = false;
                    $msg = "<div class='alert text-center  alert-danger'>" .
                                "<p>La guía de remisión ".$secuencial." no pudo ser generada, por favor intente generarla nuevamente</p>"
                            ."</div>";
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
        }

        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function integrar_factura_venture(Request $request){

        $valida = Validator::make($request->all(), [
            'id_comprobante' => 'required',
            'fecha_integrado' => 'required',
        ],[
            'id_comprobante.required' => 'No se obtuve el identificador del comprobante',
            'fecha_integrado.required' => 'Debe escribir una fecha valida para integrar la  factura'
        ]);

        $msg='<div class="alert text-center  alert-danger">' .
                '<p>No se ha podido integrar la factura, intente nuevamnete </p>'
            .'</div>';
        $success = false;

        if (!$valida->fails()) {
            $objComprobante = Comprobante::find($request->id_comprobante);
            $save = $objComprobante->update([
                'integrado'=> true,
                'fecha_integrado' => $request->fecha_integrado
            ]);
            if($save){
                $msg='<div class="alert text-center  alert-success">' .
                    '<p>Se ha integrado la factura '.getComprobante($request->id_comprobante)->secuencial .' con éxito </p>'
                    .'</div>';
                $success = true;
                bitacora('comprobante', $request->id_comprobante, 'U', 'Se ha integrado '.getComprobante($request->id_comprobante)->secuencial .' la factura con éxtio');
            }
        }else {
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

    /**
     * @param Request $request
     */
    public function descargar_txt(Request $request){

        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="text_integrador.txt"');
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $contenido = "";

        $comprobante = Comprobante::where([
            ['tipo_comprobante' , $request->tipo_comprobante],
            ['integrado',true],
            ['estado',1]
        ])->whereBetween('fecha_emision',[$request->desde,$request->hasta])->whereNotNull('fecha_integrado')->get();
        $success = true;
        foreach ($comprobante as $c){
            if($c->tipo_comprobante === "01"){
                $pedido = getComprobante($c->id_comprobante)->envio->pedido;
                if(!isset($pedido->cliente->detalle()->informacion_adicional('codigo venture')->varchar)){
                    $success = false;
                    $opResult =[
                        'msg' => '<div class="alert alert-warning text-center"><p>El cliente '.$pedido->cliente->detalle()->nombre.' no ha sido vinculado con su código del Venture</p></div>',
                        'success'=>$success,
                    ];
                    echo json_encode($opResult);
                    return;
                }
                if($pedido->tipo_especificacion === "N") {
                    $piezas = 0;
                    $caja_full = 0;
                    $tallos= 0;
                    foreach ($pedido->detalles as $x => $det_ped){
                        foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp){
                            $caja_full += $det_ped->cantidad*explode("|",$esp_emp->empaque->nombre)[1];
                            foreach ($esp_emp->detalles as $n => $det_esp_emp) {
                                $tallos += $det_ped->cantidad*$det_esp_emp->tallos_x_ramos*$esp_emp->cantidad*$det_esp_emp->cantidad;
                            }
                        }
                        $piezas += $det_ped->cantidad;
                    }
                    foreach ($pedido->detalles as $x => $det_ped) {
                        $precio = explode("|", $det_ped->precio);
                        $i = 0;
                        foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp) {
                            foreach ($esp_emp->detalles as $n => $det_esp_emp) {
                                $codigoPresentacion = getCodigoVenturePresentacion($det_esp_emp->variedad->planta->id_planta,$det_esp_emp->variedad->id_variedad,$det_esp_emp->clasificacion_ramo->id_clasificacion_ramo,$det_esp_emp->clasificacion_ramo->unidad_medida->id_unidad_medida,$det_esp_emp->tallos_x_ramos,$det_esp_emp->longitud_ramo,$det_esp_emp->unidad_medida->id_unidad_medida);
                                if($codigoPresentacion == ""){
                                    $success = false;
                                    $opResult =[
                                        'msg' => '<div class="alert alert-warning text-center"><p>La presentación '.$det_esp_emp->variedad->planta->nombre.' '.$det_esp_emp->variedad->nombre.' '. $det_esp_emp->clasificacion_ramo->nombre .' '.$det_esp_emp->clasificacion_ramo->unidad_medida->siglas. ', '. $det_esp_emp->tallos_x_ramos .' Tallos '. $det_esp_emp->longitud_ramo . $det_esp_emp->unidad_medida->siglas.' no ha sido vinculada con su código del Venture</p></div>',
                                        'success'=>$success,
                                    ];
                                    echo json_encode($opResult);
                                    return;
                                }
                                $contenido .= Carbon::parse($pedido->envios[0]->comprobante->fecha_integrado)->format('d/m/Y')."\t".$pedido->envios[0]->comprobante->secuencial."\t".$pedido->cliente->detalle()->informacion_adicional('codigo venture')->varchar."\t". Carbon::parse($pedido->envios[0]->comprobante->fecha_emision)->addDay(21)->format('d/m/Y')."\t";
                                $contenido .= $codigoPresentacion."\t";
                                $contenido .= ($det_ped->cantidad*$det_esp_emp->cantidad)."\t".explode(";", $precio[$i])[0]."\t".($pedido->cliente->detalle()->codigo_pais != getConfiguracionEmpresa($c->envio->pedido->id_configuracion_empresa)->codigo_pais ? 0 : 1)."\t".$c->envio->pedido->empresa->codigo_fpo/*Código venture para forma de pago de los clientes*/."\t".(isset($pedido->envios[0]->dae) ? $pedido->envios[0]->dae : "N")."\t".(isset($pedido->envios[0]->dae) ? $pedido->envios[0]->dae : "N")."\t"."N"."\t"."N"."\t"."1113495085"."\t".$pedido->envios[0]->guia_madre."\t";
                                $contenido .= $piezas." Piezas. ".$caja_full." FULL BOXES"."\t"."\t"."\t"."0"."\t"."\t"."\t"."\t"."\t".$c->envio->pedido->empresa->codigo_tvn/*codigo_tvn venture para el tipo de venta de la empresa*/."\t".$pedido->cliente->detalle()->nombre."\t"."\t"."\t"."\t"."\t".$tallos." Tallos."."\t"."\t"."\t"."\t"."\t". $pedido->envios[0]->guia_hija."\t".$pedido->detalles[0]->agencia_carga->codigo.chr(13).chr(10);
                                $i++;
                            }
                        }
                    }
                }
                else if($pedido->tipo_especificacion === "T") {
                    $piezas = 0;
                    $caja_full = 0;
                    $tallos= 0;
                    foreach ($pedido->detalles as $x => $det_ped){
                        foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp){
                            $caja_full += $det_ped->cantidad*explode("|",$esp_emp->empaque->nombre)[1];
                            foreach ($esp_emp->detalles as $n => $det_esp_emp) {
                                $tallos += $det_ped->cantidad*$det_esp_emp->tallos_x_ramos*$esp_emp->cantidad*$det_esp_emp->cantidad;
                            }
                        }
                        $piezas += $det_ped->cantidad;
                    }
                    foreach ($det_ped->coloraciones as $y => $coloracion){
                        foreach ($coloracion->marcaciones_coloraciones as $m_c) {
                            $codigoPresentacion = getCodigoVenturePresentacion($m_c->detalle_especificacionempaque->variedad->planta->id_planta,$m_c->detalle_especificacionempaque->variedad->id_variedad,$m_c->detalle_especificacionempaque->clasificacion_ramo->id_clasificacion_ramo,$m_c->detalle_especificacionempaque->clasificacion_ramo->unidad_medida->id_unidad_medida,$m_c->detalle_especificacionempaque->tallos_x_ramos,$m_c->detalle_especificacionempaque->longitud_ramo,$m_c->detalle_especificacionempaque->unidad_medida->id_unidad_medida);
                            if($codigoPresentacion == ""){
                                $success = false;
                                $opResult =[
                                    'msg' => '<div class="alert alert-warning text-center"><p>La presentación '.$m_c->detalle_especificacionempaque->variedad->planta->nombre.' '.$m_c->detalle_especificacionempaque->variedad->nombre.' '. $m_c->detalle_especificacionempaque->clasificacion_ramo->nombre .' '.$m_c->detalle_especificacionempaque->clasificacion_ramo->unidad_medida->siglas. ', '. $m_c->detalle_especificacionempaque->tallos_x_ramos .' Tallos '. $m_c->detalle_especificacionempaque->longitud_ramo . $m_c->detalle_especificacionempaque->unidad_medida->siglas.' no ha sido vinculada con su código del Venture</p></div>',
                                    'success'=>$success,
                                ];
                                echo json_encode($opResult);
                                return;
                            }
                            if ($m_c->cantidad > 0){
                                if ($coloracion->precio == "") {
                                    foreach (explode("|", $det_ped->precio) as $p)
                                        if ($m_c->id_detalle_especificacionempaque == explode(";", $p)[1])
                                            $precio = explode(";", $p)[0];
                                } else {
                                    foreach (explode("|", $coloracion->precio) as $p)
                                        if ($m_c->id_detalle_especificacionempaque == explode(";", $p)[1])
                                            $precio = explode(";", $p)[0];
                                }
                                $contenido .= Carbon::parse($pedido->envios[0]->comprobante->fecha_integrado)->format('d/m/Y') . "\t" . $pedido->envios[0]->comprobante->secuencial . "\t" . $pedido->cliente->detalle()->informacion_adicional('codigo venture')->varchar . "\t" . Carbon::parse($pedido->envios[0]->comprobante->fecha_emision)->addDay(21)->format('d/m/Y') . "\t";
                                $contenido .= $codigoPresentacion . "\t";
                                $contenido .= $m_c->cantidad . "\t" . $precio . "\t" . ($pedido->cliente->detalle()->codigo_pais != getConfiguracionEmpresa($c->envio->pedido->id_configuracion_empresa)->codigo_pais ? 0 : 1) . "\t" . $c->envio->pedido->empresa->codigo_fpo/*Código venture para forma de pago de los clientes*/ . "\t" . (isset($pedido->envios[0]->dae) ? $pedido->envios[0]->dae : "N") . "\t" . (isset($pedido->envios[0]->dae) ? $pedido->envios[0]->dae : "N") . "\t" . "N" . "\t" . "N" . "\t" . "1113495085" . "\t" . $pedido->envios[0]->guia_madre . "\t";
                                $contenido .= $piezas . " Piezas. " . $caja_full . " FULL BOXES" . "\t" . "\t" . "\t" . "0" . "\t" . "\t" . "\t" . "\t" . "\t" .  $c->envio->pedido->empresa->codigo_tvn/*codigo_tvn venture para el tipo de venta de la empresa*/ . "\t" . $pedido->cliente->detalle()->nombre . "\t" . "\t" . "\t" . "\t" . "\t" . $tallos . " Tallos." . "\t" . "\t" . "\t" . "\t" . "\t" . $pedido->envios[0]->guia_hija . "\t" . $pedido->detalles[0]->agencia_carga->codigo.chr(13).chr(10);
                            }
                        }
                    }
                }
            }
        }
        $opResult = array(
            'success' => $success,
            'fecha' => now()->toDateString(),
            'data' => "data:text/plain;base64,".base64_encode($contenido)
        );
        echo json_encode($opResult);
    }

    public function enviar_correo(Request $request){
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $comprobante = Comprobante::where('id_comprobante',$request->id_comprobante)->first();
        $img_clave_acceso = null;
        $correos = [];

        if($comprobante->clave_acceso != null)
            $img_clave_acceso = generateCodeBarGs1128($comprobante->clave_acceso);

        if($comprobante->tipo_comprobante === "01"){
            $data = [
                'empresa' => getConfiguracionEmpresa($comprobante->envio->pedido->id_configuracion_empresa),
                'secuencial' => $comprobante->secuencial,
                'comprobante' => $comprobante,
                'img_clave_acceso' => $img_clave_acceso
            ];
        }else if($comprobante->tipo_comprobante === "06"){
            $comprobanteRelacionado = Comprobante::where([
                ['secuencial',$comprobante->secuencial],
                ['tipo_comprobante',"06"]
            ])->join('detalle_guia_remision as dgr','comprobante.id_comprobante','dgr.id_comprobante')->select('id_comprobante_relacionado')->first();
            $detalle_despacho = getDetalleDespacho(getComprobante($comprobanteRelacionado->id_comprobante_relacionado)->envio->pedido->id_pedido);
            $data = [
                'img_clave_acceso' => $img_clave_acceso,
                'pedido' => getComprobante($comprobanteRelacionado->id_comprobante_relacionado)->envio->pedido,
                'comprobante' => $comprobante,
                'secuencial' => $comprobante->secuencial,
                'detalle_despacho' => $detalle_despacho,
                'dataComprobanteRelacionado' => $comprobanteRelacionado
            ];
        }


        if($request->cliente == "true"){
            if($comprobante->tipo_comprobante === "01"){
                $comprobante->envio->fatura_cliente_tercero != null
                    ? $mail = $comprobante->envio->fatura_cliente_tercero->correo
                    : $mail = $comprobante->envio->pedido->cliente->detalle()->correo;
            }else if($comprobante->tipo_comprobante === "06"){
                getComprobante($comprobanteRelacionado->id_comprobante_relacionado)->envio->fatura_cliente_tercero != null
                    ? $mail = getComprobante($comprobanteRelacionado->id_comprobante_relacionado)->fatura_cliente_tercero->envio->correo
                    : $mail = getComprobante($comprobanteRelacionado->id_comprobante_relacionado)->envio->pedido->cliente->detalle()->correo;
            }

            $correos[] = $mail;
        }

        if($request->agencia_carga == "true"){
            if($comprobante->tipo_comprobante === "01"){
                $correos[] = $comprobante->envio->pedido->detalles[0]->agencia_carga->correo;
                $comprobante->envio->pedido->detalles[0]->agencia_carga->correo2 != null ? $correos[] = $comprobante->envio->pedido->detalles[0]->agencia_carga->correo2 : "";
                $comprobante->envio->pedido->detalles[0]->agencia_carga->correo3 != null ? $correos[] = $comprobante->envio->pedido->detalles[0]->agencia_carga->correo3 : "";
            }else if($comprobante->tipo_comprobante === "06"){
                $correos[] = getComprobante($comprobanteRelacionado->id_comprobante_relacionado)->envio->pedido->detalles[0]->agencia_carga->correo;
            }
        }

        if($comprobante->tipo_comprobante === "01"){
            foreach ($comprobante->envio->pedido->cliente->detalle()->informacion_adicional_correo() as $correo)
                $correos[] = $correo->varchar;
        }else if($comprobante->tipo_comprobante === "06"){
            foreach (getComprobante($comprobanteRelacionado->id_comprobante_relacionado)->envio->pedido->cliente->detalle()->informacion_adicional_correo() as $correo)
                $correos[] = $correo->varchar;
        }

        if($request->csv_etiqueta == "true"){
            $f =fopen(env('ETIQUETAS_FACTURAS_TEMPORAL').'label_fact_'.$comprobante->secuencial.'.csv','w');
            $cadena = $comprobante->detalle_factura->razon_social_emisor.", "."\n"."Invoice No. ,".$comprobante->secuencial."\n"."AWB No. ,".$comprobante->envio->guia_madre."\n"."DATE : ,".$comprobante->fecha_emision."\n";
            $cadena .= "PIECES,ITEM,BUNCHES/BOX,BUNCHES,PRICE,PRICE/BUNCHES,BARCODE"."\n";
            $total_piezas = 0;
            $total_ramos = 0;
            $total_monto = 0;

            if(isset($comprobante->etiqueta_factura->detalles)){
                foreach ($comprobante->etiqueta_factura->detalles as $x =>  $detalle) {
                    $id_det_esp_emp = explode("|",$detalle->id_detalle_especificacion_empaque);
                    foreach($id_det_esp_emp as $id){
                        $det_esp_emp = getDetalleEspecificacionEmpaque($id);
                        $variedad = getVariedad($det_esp_emp->id_variedad);
                        $clasificacionRamo = getClasificacionRamo($det_esp_emp->id_clasificacion_ramo);
                        if($det_esp_emp->longitud_ramo != null){
                            foreach (getUnidadesMedida($clasificacionRamo->id_unidad_medida) as $umPeso)
                                $umPeso->tipo == "P" ? $umPeso = $umPeso->siglas : $umPeso ="";
                        }
                        foreach ($comprobante->envio->pedido->detalles as $det_ped){
                            $i=0;
                            $precio = explode("|", $det_ped->precio);
                            foreach ($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $esp_emp){
                                foreach ($esp_emp->detalles as $z=> $det_esp_emp){
                                    if($det_esp_emp->id_detalle_especificacionempaque == $id){
                                        $det_esp_emp_cantidad = $det_esp_emp->cantidad;
                                        $precio_presentacion = (float)explode(";", $precio[$i])[0];
                                        break;
                                    }
                                    $i++;
                                }
                            }
                        }
                        $presentaciones = substr($variedad->planta->nombre,0,3)." ". $variedad->siglas . " " . $clasificacionRamo->nombre.$umPeso. " ";
                        $ramos_x_caja = $det_esp_emp_cantidad * $det_esp_emp->especificacion_empaque->cantidad;
                        $etiqueta = $comprobante->etiqueta_factura->detalles[$x];
                        $cadena .= $detalle->cantidad.",".$presentaciones.",".$ramos_x_caja.",".($ramos_x_caja*$detalle->cantidad).",".$precio_presentacion.",".$precio_presentacion*($ramos_x_caja*$detalle->cantidad).",".$etiqueta->et_inicial." - ".$etiqueta->et_final."\n";
                        $total_piezas +=$detalle->cantidad;
                        $total_ramos += $ramos_x_caja*$detalle->cantidad;
                        $total_monto += ($precio_presentacion*$ramos_x_caja*$detalle->cantidad);
                    }
                }
                $cadena.= $total_piezas.","."TOTALS".",".",".$total_ramos.",".",".$total_monto."\n";
            }

            fwrite($f,substr($cadena,0,-1));
            fclose($f);

        }

        if($request->dist_cajas === "true"){
            $pedido = getPedido($comprobante->envio->pedido->id_pedido);
            $empresa = getConfiguracionEmpresa($comprobante->envio->pedido->id_configuracion_empresa);
            $despacho = isset(getDetalleDespacho($pedido->id_pedido)->despacho) ? getDetalleDespacho($pedido->id_pedido)->despacho : null;
            $facturaTercero = isset($pedido->envios) ? getFacturaClienteTercero($pedido->envios[0]->id_envio) : null;
            if($facturaTercero !== null){
                $cliente = [
                    'nombre' =>$facturaTercero->nombre_cliente_tercero,
                    'identificacion' => $facturaTercero->identificacion,
                    'tipo_identificacion' => getTipoIdentificacion($facturaTercero->codigo_identificacion)->nombre,
                    'pais' => getPais($facturaTercero->codigo_pais)->nombre,
                    'provincia' => $facturaTercero->provincia,
                    'direccion' => $facturaTercero->direccion,
                    'telefono' => $facturaTercero->telefono,
                    'dae' => $facturaTercero->dae,
                ];
            }else{
                foreach($pedido->cliente->detalles as $det_cli)
                    if($det_cli->estado == 1)
                        $cliente = $det_cli;
                $cliente = [
                    'nombre' =>$cliente->nombre,
                    'identificacion' => $cliente->ruc,
                    'tipo_identificacion' => getTipoIdentificacion($cliente->codigo_identificacion)->nombre,
                    'pais' => getPais($cliente->codigo_pais)->nombre,
                    'provincia' => $cliente->provincia,
                    'direccion' => $cliente->direccion,
                    'telefono' => $cliente->telefono,
                    'dae' => $pedido->envios[0]->dae
                ];
            }
        }

        if($request->factura_sri == "true")
            PDF::loadView('adminlte.gestion.comprobante.partials.pdf.factura_bd', compact('data'))->save(env('PDF_FACTURAS_TEMPORAL')."fact_sri_".$comprobante->secuencial.".pdf");
        if($request->factura_cliente == "true")
            PDF::loadView('adminlte.gestion.comprobante.partials.pdf.factura_cliente_bd', compact('data'))->save(env('PDF_FACTURAS_TEMPORAL')."fact_cliente_".$comprobante->secuencial.".pdf");
        if($request->dist_cajas === "true")
            PDF::loadView('adminlte.gestion.comprobante.partials.pdf.lista_distribucion', compact('cliente','empresa','despacho','comprobante'))->save(env('PDF_FACTURAS_TEMPORAL')."dist_cajas_".$comprobante->secuencial.".pdf");
        if($request->guia_remision === "true")
            PDF::loadView('adminlte.gestion.comprobante.partials.pdf.guia_bd', compact('data'))->save(env('PDF_FACTURAS_TEMPORAL')."guia_factura_".$comprobante->secuencial.".pdf");

        $correos[] = "sales@dasalflor.com";
        $correos[] = "obrian@pyganflor.com"; // solo para pruebas, comentar en produccion
                    //$correos[0]
        Mail::to("pruebas-c26453@inbox.mailtrap.io")
            ->cc($correos)->send(new CorreoFacturaVenture($request->factura_cliente,$request->factura_sri,$comprobante->secuencial,$request->csv_etiqueta,$request->dist_cajas,$request->guia_remision));

        if($request->factura_sri == "true" && file_exists(env('PDF_FACTURAS_TEMPORAL')."fact_sri_".$comprobante->secuencial.'.pdf'))
            unlink(env('PDF_FACTURAS_TEMPORAL')."fact_sri_".$comprobante->secuencial.'.pdf');

        if($request->factura_cliente == "true" && file_exists(env('PDF_FACTURAS_TEMPORAL')."fact_cliente_".$comprobante->secuencial.'.pdf'))
            unlink(env('PDF_FACTURAS_TEMPORAL')."fact_cliente_".$comprobante->secuencial.'.pdf');

        if($request->csv_etiqueta == "true" && file_exists(env('ETIQUETAS_FACTURAS_TEMPORAL').'label_fact_'.$comprobante->secuencial.'.csv'))
            unlink(env('ETIQUETAS_FACTURAS_TEMPORAL')."label_fact_".$comprobante->secuencial.'.csv');

        if($request->dist_cajas === "true" && file_exists(env('PDF_FACTURAS_TEMPORAL')."dist_cajas_".$comprobante->secuencial.'.pdf'))
            unlink(env('PDF_FACTURAS_TEMPORAL')."dist_cajas_".$comprobante->secuencial.'.pdf');

        if($request->guia_remision === "true" && file_exists(env('PDF_FACTURAS_TEMPORAL')."guia_factura_".$comprobante->secuencial.".pdf"))
            unlink(env('PDF_FACTURAS_TEMPORAL')."guia_factura_".$comprobante->secuencial.".pdf");

        return[
            'mensaje' => '<div class="alert text-center  alert-success">' .
                          '<p>Se han enviado los correos con éxito </p>'
                    .'</div>',
            'success' => true,
        ];

    }

    public function desvincular_factura_venture(Request $request){
        $success = false;
        $msg = '<div class="alert alert-warning text-center">' .
            '<p> Ha ocurrido un problema al desvincular la factura, intente nuevamente</p>'
            . '</div>';

        $objComprobante = Comprobante::find($request->id_comprobante);
        if($objComprobante->update(['integrado'=>false,'fecha_integrado' => null])){
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha desvinculado la facutra exitosamente</p>'
                . '</div>';
        }

        return [
            'success' => $success,
            'mensaje' => $msg,
        ];

    }

    public function actualizar_comprobante_venture(Request $request){
        $valida = Validator::make($request->all(), [
            'desde' => 'required',
            'hasta' => 'required',
        ],[
            'desde.required'=>'Debe elegir un fecha desde',
            'hasta.required'=>'Debe elegir un fecha hasta'
        ]);

        if(!$valida->fails()) {
            $comprobantes = Comprobante::where([
                ['tipo_comprobante',$request->tipo_comprobante],
                ['estado',1],
                ['habilitado',true],
                ['integrado',true]
            ])->whereBetween('fecha_emision',[$request->desde,$request->hasta])->get();

            $msg ='<div class="alert alert-warning text-center">' .
                        '<p> No se han integrado las facturas generadas en las fechas seleccionadas </p>
                   </div>';
            $success = false;
            if($request->tipo_comprobante == "01"){
                $documento = "factura";
                $arhcivo = "fac_";
                $carpeta = "facturas/";
            }

            if($request->tipo_comprobante == "06"){
                $documento = "guía de remisión";
                $arhcivo = "gui_";
                $carpeta = "guias_remision/";
            }

            if($comprobantes->count() > 0) $msg='';

            foreach ($comprobantes as $comprobante){

                if(file_exists(env('PATH_XML_AUTORIZADOS').$carpeta.$arhcivo.$comprobante->secuencial.'.xml')){
                    $cadena = file_get_contents(env('PATH_XML_AUTORIZADOS').$carpeta.$arhcivo.$comprobante->secuencial.'.xml');
                    $objXmlAutorizado = simplexml_load_string($cadena);
                    $objComprobante = Comprobante::find($comprobante->id_comprobante);
                    $secuencial = "001".getDetallesClaveAcceso((String)$objXmlAutorizado->numeroAutorizacion, 'PUNTO_ACCESO').getDetallesClaveAcceso((String)$objXmlAutorizado->numeroAutorizacion, 'SECUENCIAL');

                    if($secuencial ===  $comprobante->secuencial){
                        if((String)$objXmlAutorizado->estado === "AUTORIZADO"){
                            $formato_fecha = explode("/",explode(" ",(String)$objXmlAutorizado->fechaAutorizacion)[0]);
                            $fecha = $formato_fecha[2]."-".$formato_fecha[1]."-".$formato_fecha[0];
                            $hora = Carbon::parse(explode(" ",(String)$objXmlAutorizado->fechaAutorizacion)[1])->format('H:m:s');
                           // dd((String)$objXmlAutorizado->ambiente,(String)$objXmlAutorizado->ambiente == "PRODUCCIÓN");
                            $save = $objComprobante->update([
                                'estado' => 5,
                                'clave_acceso' => (String)$objXmlAutorizado->numeroAutorizacion,
                                'fecha_autorizacion' => $fecha ." ".$hora,
                                'numero_comprobante' => "001-".getDetallesClaveAcceso((String)$objXmlAutorizado->numeroAutorizacion, 'PUNTO_ACCESO')."-".getDetallesClaveAcceso((String)$objXmlAutorizado->numeroAutorizacion, 'SECUENCIAL'),
                                'ambiente' => ((String)$objXmlAutorizado->ambiente == "PRODUCCIÓN") ? 2 : 1
                            ]);
                            if($save){
                                $success = true;
                                $msg .= '<div class="alert alert-success text-center">' .
                                    '<p> La '.$documento.' '.$comprobante->secuencial.' ha sido actualizada con éxito</p>
                                    </div>';
                            }
                        }else if((String)$objXmlAutorizado->estado === "RECHAZADA" || (String)$objXmlAutorizado->estado === "DEVUELTA"){
                            $objComprobante->update([
                                'estado' => 3,
                                'causa' => ""
                            ]);
                            $msg .= '<div class="alert alert-warning text-center">' .
                                '<p> La '.$documento.' '.$comprobante->secuencial.' no fue autorizada por el SRI verifique su status en el Venture</p>
                                </div>';
                        }else if((String)$objXmlAutorizado->estado === "NO AUTORIZADA"){
                            $objComprobante->update([
                                'estado' => 4,
                                'causa' => ""
                            ]);
                            $msg .= '<div class="alert alert-warning text-center">' .
                                '<p> La '.$documento.' '.$comprobante->secuencial.' no fue autorizada por el SRI verifique su status en el Venture</p>
                                </div>';
                        }
                    }else{
                        $success = false;
                        $msg .= '<div class="alert alert-warning text-center">' .
                            '<p> El contenido del archivo '.$arhcivo.$comprobante->secuencial.'.xml no corresponde a la '.$documento.' N# '.$comprobante->secuencial.'</p>
                                </div>';
                    }
                    
                }else{
                    $success = false;
                    $msg .= '<div class="alert alert-warning text-center">' .
                                '<p> El archivo '.$arhcivo.$comprobante->secuencial.'.xml no existe en la ruta '.env('PATH_XML_AUTORIZADOS').$carpeta.', debe cargar el archivo para porder actualizar el comprobante</p>
                            </div>';
                }
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
        return [
            'mensaje' => $msg,
            'success' => $success
        ];
    }

    public function anular_factura(Request $request){
        $success = false;
        $msg = '<div class="alert alert-warning text-center">' .
            '<p> Ha ocurrido un problema al anular la factura, intente nuevamente</p>'
            . '</div>';

        $objComprobante = Comprobante::find($request->id_comprobante);
        if($objComprobante->update(['estado'=>6])){
            $success = true;
            $msg = '<div class="alert alert-success text-center">' .
                '<p> Se ha anulado la facutra exitosamente</p>'
                . '</div>';
        }
        return [
            'success' => $success,
            'mensaje' => $msg,
        ];
    }

    public function carga_xml(Request $request){

        $files = $request->file('archivos');
        $msg = "";
        $success = false;
        switch ($request->tipo_comprobante){
            case '01':
                $carpeta = 'facturas/';
                break;
            case '06':
                $carpeta = 'guias_remision/';
                break;
        }

        foreach($files as $file) {
            $filename = $file->getClientOriginalName();
            if($file->getClientMimeType() === "text/xml"){
                $prefijo = explode("_",$filename)[0];
                $archivo = true;
                if($request->tipo_comprobante == '01' && $prefijo!="fac")
                    $archivo = false;
                if($request->tipo_comprobante == '06' && $prefijo!="gui")
                    $archivo = false;

                if($archivo){
                    $carga_archivo = $file->move(env('PATH_XML_AUTORIZADOS').$carpeta, $filename);
                    if($carga_archivo){
                        $msg .= "<label class='alert alert-success text-center' style='width:100%'>El archivo ".$filename." se ha cargado con éxito</label>";
                        $success = true;
                    }else{
                        $msg .= "<label class='alert alert-warning text-center' style='width:100%'>Ha ocurrido un error al cargar el  archivo ".$filename." intente nuevamente</label>";
                    }
                }else{
                    $msg .= "<label class='alert alert-danger text-center' style='width:100%'>El archivo ".$filename." debe comenzar con el prefijo 'fac_'</label>";
                }
            }else{
                $msg .= "<label class='alert alert-danger text-center' style='width:100%'>El archivo ".$filename." debe tener el formato .xml para poder cargarse</label>";
            }
        }
        return [
            'mensaje' => $msg,
            'success' => $success
        ];


    }
}
