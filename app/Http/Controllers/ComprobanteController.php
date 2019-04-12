<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DomDocument;
use yura\Modelos\Comprobante;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\ConfiguracionEmpresa;
use yura\Modelos\DetalleCliente;
use yura\Modelos\Impuesto;
use yura\Modelos\ImpuestoDetalleFactura;
use yura\Modelos\DetalleFactura;
use yura\Modelos\DesgloseEnvioFactura;
use yura\Modelos\ImpuestoDesgloseEnvioFactura;
use yura\Modelos\InformacionAdicionalFactura;
use yura\Modelos\Pais;
use yura\Modelos\TipoIdentificacion;
use yura\Modelos\TipoImpuesto;
use yura\Modelos\Variedad;
use yura\Modelos\Precio;
use yura\Modelos\Usuario;
use yura\Modelos\Submenu;
use yura\Modelos\TipoComprobante;
use yura\Modelos\CodigoDae;
use yura\Modelos\Cliente;
use Validator;
use Storage;
use DB;
use SoapClient;
use Barryvdh\DomPDF\Facade as PDF;
use yura\Modelos\FacturaClienteTercero;


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
                'clientes' => Cliente::join('detalle_cliente as dc', 'cliente.id_cliente', 'dc.id_cliente')->where('dc.estado', 1)->select('nombre', 'cliente.id_cliente')->get()
            ]);
    }

    public function buscar_comprobante(Request $request)
    {
        $busquedaAnno = $request->has('anno') ? $request->anno : '';
        $busquedacliente = $request->has('id_cliente') ? $request->id_cliente : '';
        $fecha = $request->has('fecha') ? $request->fecha : '';
        $busquedaComprobante = $request->has('codigo_comprobante') ? $request->codigo_comprobante : '';
        $listado = Comprobante::where([
            ['comprobante.estado', isset($request->estado) ? $request->estado : 1],
            ['dc.estado', 1],
            ['tipo_comprobante', '!=', "00"],
            ['p.estado',1]
            //['comprobante.fecha_emision', $fecha]
        ])->join('tipo_comprobante as tc', 'comprobante.tipo_comprobante', 'tc.codigo')
            ->join('envio as e', 'comprobante.id_envio', 'e.id_envio')
            ->join('pedido as p', 'e.id_pedido', 'p.id_pedido')
            ->join('detalle_cliente as dc', 'p.id_cliente', 'dc.id_cliente');

        if ($busquedaAnno != '')
            $listado = $listado->where(DB::raw('YEAR(de.fecha_emision)'), $busquedaAnno);
        if ($busquedacliente != '')
            $listado = $listado->where('dc.id_cliente', $busquedacliente);
        if ($busquedaComprobante != '' && $request->codigo_comprobante != '')
            $listado = $listado->where('comprobante.tipo_comprobante', $busquedaComprobante);

        $listado = $listado->orderBy('id_comprobante', 'Desc')
            ->select('comprobante.*', 'tc.nombre as nombre_comprobante', 'dc.nombre as nombre_cliente')->paginate(20);
        $datos = [
            'listado' => $listado,
            'columna_causa' => ($request->estado == 3 || $request->estado == 4) ? true : false,
            'firmar_comprobante' => isset($request->estado) ? true : false
        ];

        return view('adminlte.gestion.comprobante.partials.listado', $datos);
    }

    public function generar_comprobante_factura(Request $request)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $valida = Validator::make($request->all(), [
            'id_envio' => 'required',
            'guia_madre' => 'required',
            'codigo_pais' => 'required',
            'destino' => 'required',
            'email' => 'required',
            'telefono' => 'required',
        ]);
        $msg = "";
        if (!$valida->fails()) {
            $precio_total_sin_impuestos = 0.00;
            $envio = getEnvio($request->id_envio);
            foreach($envio->pedido->detalles as $x => $det_ped){
                if($envio->pedido->tipo_especificacion === "N"){ //FLOR NO TINTURADA
                    $precio = explode("|",$det_ped->precio);
                }else if($envio->pedido->tipo_especificacion === "T"){ //FLOR TINTURADA
                    $precio = 0;
                }

                $i=0;
                foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp){
                    foreach ($esp_emp->detalles as $n => $det_esp_emp){
                      $precio_x_variedad = ($det_esp_emp->cantidad * ((float)explode(";",$precio[$i])[0]) * $esp_emp->cantidad * $det_ped->cantidad);
                      $precio_total_sin_impuestos += $precio_x_variedad;
                      $i++;
                    }
                }
            }

            $facturaClienTeTercero = getFacturaClienteTercero($envio->id_envio);
            $dataCliente = DetalleCliente::where([
                ['id_cliente' , $envio->pedido->cliente->id_cliente],
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
                $direccion = $facturaClienTeTercero->direccion;
                $correo = $facturaClienTeTercero->correo;
                $telefono = $facturaClienTeTercero->telefono;
                $almacen = $facturaClienTeTercero->almacen;
                $dae = $facturaClienTeTercero->dae;
            }else{
                $codigo_pais = $request->codigo_pais;
                $codigo_impuesto = $dataCliente->codigo_impuesto;
                $codigo_porcentaje_impuesto = $dataCliente->codigo_porcentaje_impuesto;
                $codigo_identificacion = $dataCliente->codigo_porcentaje_impuesto;
                $identificacion = $dataCliente->ruc;
                $nombre_cliente = $dataCliente->nombre;
                $provincia = $dataCliente->provincia;
                $direccion = $dataCliente->direccion;
                $correo = $dataCliente->correo;
                $telefono = $dataCliente->telefono;
                $almacen = $request->almacen;
                $dae = $request->dae;
            }

            if ((strtoupper(getConfiguracionEmpresa()->codigo_pais) != strtoupper($codigo_pais))
                && (!isset(getCodigoDae(strtoupper($codigo_pais),Carbon::parse($request->fecha_envio)->format('m'),Carbon::parse($request->fecha_envio)->format('Y'))->codigo_dae))
                && $request->dae == null) {
                return '<div class="alert alert-danger text-center">' .
                    '<p> No se ha configurado un código DAE para ' . $request->pais . ' en la fecha seleccionada </p>'
                    . '</div>';
            }

            if($request->update === "true"){
                $dataComprobante = Comprobante::where('id_envio',$request->id_envio)
                    ->join('detalle_factura as df','comprobante.id_comprobante','df.id_comprobante')
                    ->join('impuesto_detalle_factura as idf','df.id_detalle_factura','idf.id_detalle_factura')
                    ->join('desglose_envio_factura as def','comprobante.id_comprobante','def.id_comprobante')
                    ->join('impuesto_desglose_envio_factura as idef','def.id_desglose_envio_factura','idef.id_desglose_envio_factura')
                    ->select('clave_acceso','comprobante.id_comprobante','df.id_detalle_factura','id_impuesto_desglose_envio_factura')->get();

                $secuencial = getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'SECUENCIAL');
                $fechaEmision = getDetallesClaveAcceso($dataComprobante[0]->clave_acceso, 'FECHA_EMISION');
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
                $secuencial = getSecuencial();
                $fechaEmision = Carbon::now()->format('dmY');
                $ruc = env('RUC');
                $codigo_numerico = env('CODIGO_NUMERICO');
                $tipoComprobante = '01';
                $entorno = env('ENTORNO');
                $punto_acceso = getUsuario(session('id_usuario'))->punto_acceso;
                $serie = '001' . $punto_acceso;
                $tipo_emision = '1';
            }

            $datosEmpresa = getConfiguracionEmpresa();
            $xml = new DomDocument('1.0', 'UTF-8');
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

            $tipoImpuesto = getTipoImpuesto($codigo_impuesto,$codigo_porcentaje_impuesto);

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
                'obligadoContabilidad' => env('OBLIGADO_CONTABILIDAD'),
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
            $precio_total_con_impuestos = is_numeric($tipoImpuesto->porcentaje) ? number_format($precio_total_sin_impuestos * ($tipoImpuesto->porcentaje / 100), 2, ".", "") : "0.00";
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
            switch (getConfiguracionEmpresa()->moneda){
                case "usd":
                    $m = 'DOLAR';
                    break;
            }
            $moneda = $xml->createElement('moneda', $m);
            $infoFactura->appendChild($moneda);
            $detalles = $xml->createElement('detalles');
            $factura->appendChild($detalles);

            foreach($envio->pedido->detalles as $x => $det_ped){

                if($envio->pedido->tipo_especificacion === "N"){ //FLOR NO TINTURADA
                    $precio = explode("|",$det_ped->precio);
                }else if($envio->pedido->tipo_especificacion === "T"){ //FLOR TINTURADA
                    $precio = 0;
                }

                $i=0;
                foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp){
                    foreach ($esp_emp->detalles as $n => $det_esp_emp){
                        $precio_x_variedad = ($det_esp_emp->cantidad * ((float)explode(";",$precio[$i])[0]) * $esp_emp->cantidad * $det_ped->cantidad);
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
            if ((strtoupper(getConfiguracionEmpresa()->codigo_pais) != strtoupper($codigo_pais)))
                $campos_adicionales['DAE'] = $dae;

            foreach ($campos_adicionales as $key => $ca) {
                $campo_adicional = $xml->createElement('campoAdicional', $ca);
                $campo_adicional->setAttribute('nombre', $key);
                $informacionAdicional->appendChild($campo_adicional);
            }
            $xml->formatOutput = true;
            $xml->saveXML();
            $nombre_xml = $claveAcceso . ".xml";

                //////////// GUARDAR ARCHIVO XML Y DATA EN BD //////////////

                $obj_comprobante = new Comprobante;
                $obj_comprobante->clave_acceso = $claveAcceso;
                $obj_comprobante->id_envio = $request->id_envio;
                $obj_comprobante->tipo_comprobante = "01"; //CÓDIGO DE FACTURA A CLIENTE EXTERNO
                $obj_comprobante->monto_total = number_format($valorImpuesto + $precio_total_sin_impuestos, 2, ".", "");

                if ($obj_comprobante->save()) {
                    $model_comprobante = Comprobante::all()->last();
                    bitacora('comprobante', $model_comprobante->id_comprpobante, 'I', 'Creación de un nuevo comprobante electrónico');
                    $objDetalleFactura = new DetalleFactura;
                    $objDetalleFactura->id_comprobante = $model_comprobante->id_comprobante;
                    $objDetalleFactura->razon_social_emisor = $informacionTributaria['razonSocial'];
                    $objDetalleFactura->nombre_comercial_emisor = $informacionTributaria['nombreComercial'];
                    $objDetalleFactura->direccion_matriz_emisor = $informacionTributaria['dirMatriz'];
                    $objDetalleFactura->direccion_establecimiento_emisor = $informacionFactura['dirEstablecimiento'];
                    $objDetalleFactura->obligado_contabilidad = $informacionFactura['obligadoContabilidad'] == "SI" ? 1 : 0;
                    $objDetalleFactura->tipo_identificacion_comprador = $informacionFactura['tipoIdentificacionComprador'];
                    $objDetalleFactura->razon_social_comprador = $informacionFactura['razonSocialComprador'];
                    $objDetalleFactura->identificacion_comprador = $informacionFactura['identificacionComprador'];
                    $objDetalleFactura->total_sin_impuestos = $informacionFactura['totalSinImpuestos'];
                    $objDetalleFactura->total_descuento = $informacionFactura['totalDescuento'];
                    $objDetalleFactura->propina = 0.00;
                    $objDetalleFactura->importe_total = number_format($valorImpuesto + $precio_total_sin_impuestos, 2, ".", "");

                    if ($objDetalleFactura->save()) {
                        $model_detalle_factura = DetalleFactura::all()->last();
                        bitacora('detalle_factura', $model_detalle_factura->id_detalle_factura, 'I', 'Creación de un nuevo detalle de factura');
                        $objImpuestoDetalleFactura = new ImpuestoDetalleFactura;
                        $objImpuestoDetalleFactura->id_detalle_factura = $model_detalle_factura->id_detalle_factura;
                        $objImpuestoDetalleFactura->codigo_impuesto = $informacionImpuestos['codigo'];
                        $objImpuestoDetalleFactura->codigo_porcentaje = $informacionImpuestos['codigoPorcentaje'];
                        $objImpuestoDetalleFactura->base_imponible = $informacionImpuestos['baseImponible'];
                        $objImpuestoDetalleFactura->valor = $informacionImpuestos['valor'];

                        if($objImpuestoDetalleFactura->save()){
                            $model_impuesto_detalle_factura = ImpuestoDetalleFactura::all()->last();
                            bitacora('impuesto_detalle_factura', $model_impuesto_detalle_factura->id_impuesto_detalle_factura, 'I', 'Creación de un nuevo impuesto de detalle de factura');
                            $iteracion = 0;

                            foreach($envio->pedido->detalles as $x => $det_ped){
                                if($envio->pedido->tipo_especificacion === "N"){ //FLOR NO TINTURADA
                                    $precio = explode("|",$det_ped->precio);
                                }else if($envio->pedido->tipo_especificacion === "T"){ //FLOR TINTURADA
                                    $precio = 0;
                                }
                                $i=0;
                                foreach($det_ped->cliente_especificacion->especificacion->especificacionesEmpaque as $m => $esp_emp){
                                    foreach ($esp_emp->detalles as $n => $det_esp_emp){
                                        $precio_x_variedad = ($det_esp_emp->cantidad * ((float)explode(";",$precio[$i])[0]) * $esp_emp->cantidad * $det_ped->cantidad);
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
                                        $objDesgloseEnvioFactura = new DesgloseEnvioFactura;
                                        $objDesgloseEnvioFactura->id_comprobante = $model_comprobante->id_comprobante;
                                        $objDesgloseEnvioFactura->codigo_principal = 'ENV' . str_pad($request->id_envio, 9, "0", STR_PAD_LEFT);
                                        $objDesgloseEnvioFactura->descripcion = $variedad->planta->nombre . " (" . $variedad->siglas . ") " . $clasificacionRamo->nombre.$umPeso . " " . $longitudRamo.$umL;
                                        $objDesgloseEnvioFactura->cantidad = number_format(($det_esp_emp->cantidad* $esp_emp->cantidad * $det_ped->cantidad), 2, ".", "");
                                        $objDesgloseEnvioFactura->precio_unitario = number_format(explode(";",$precio[$i])[0], 2, ".", "");
                                        $objDesgloseEnvioFactura->descuento = '0.00';
                                        $objDesgloseEnvioFactura->precio_total_sin_impuesto = number_format($precio_x_variedad, 2, ".", "");

                                        if($objDesgloseEnvioFactura->save()){
                                            bitacora('impuesto_detalle_factura', $model_impuesto_detalle_factura->id_impuesto_detalle_factura, 'I', 'Creación de un nuevo impuesto de detalle de factura');
                                            $model_desglose_envio_factura = DesgloseEnvioFactura::all()->last();

                                            $objImpuestoDesgloseEnvioFactura = new ImpuestoDesgloseEnvioFactura;
                                            $objImpuestoDesgloseEnvioFactura->id_desglose_envio_factura = $model_desglose_envio_factura->id_desglose_envio_factura;
                                            $objImpuestoDesgloseEnvioFactura->codigo_impuesto           = $codigo_impuesto;
                                            $objImpuestoDesgloseEnvioFactura->codigo_porcentaje	        = $codigo_porcentaje_impuesto;
                                            $objImpuestoDesgloseEnvioFactura->base_imponible            = number_format($precio_x_variedad,2,".","");
                                            $objImpuestoDesgloseEnvioFactura->valor                     = is_numeric($tipoImpuesto->porcentaje) ? number_format($precio_x_variedad * ($tipoImpuesto->porcentaje / 100), 2, ".", "") : "0.00";
                                            $objImpuestoDesgloseEnvioFactura->save() ?  $iteracion++ : '';
                                        }
                                        $i++;
                                    }
                                }
                            }

                            if ($iteracion === (int)$request->cant_variedades) {
                                $save_xml = $xml->save(env('PATH_XML_GENERADOS') . $nombre_xml);
                                if ($save_xml && $save_xml > 0) {
                                    $resultado = firmarComprobanteXml($nombre_xml);
                                    if ($resultado) {
                                        $class = 'warning';
                                        if ($resultado == 5) {
                                            $class = 'success';
                                            $obj_comprobante = Comprobante::find($model_comprobante->id_comprobante);
                                            $obj_comprobante->estado = 1;
                                            $obj_comprobante->save();
                                        }
                                        $msg .= "<div class='alert text-center  alert-" . $class . "'>" .
                                            "<p> " . mensajeFirmaElectronica($resultado, str_pad($request->id_envio, 9, "0", STR_PAD_LEFT)) . "</p>"
                                            . "</div>";
                                    } else {
                                        $msg .= "<div class='alert text-center  alert-danger'>" .
                                            "<p>Hubo un error al realizar el proceso de la firma de la factura N# " . $nombre_xml . " del envío N#" . str_pad($request->id_envio, 9, "0", STR_PAD_LEFT) . ", intente nuevamente realizar la firma del mismo filtrando por GENERADOS</p>"
                                            . "</div>";
                                    }
                                } else {
                                    ImpuestoDesgloseEnvioFactura::where('id_desglose_envio_factura', $model_desglose_envio_factura->id_desglose_envio_factura)->delete();
                                    DesgloseEnvioFactura::where('id_comprobante', $model_comprobante->id_comprobante)->delete();
                                    ImpuestoDetalleFactura::where('id_detalle_factura', $model_detalle_factura->id_detalle_factura)->delete();
                                    DetalleFactura::where('id_comprobante', $model_comprobante->id_comprobante)->delete();
                                    Comprobante::destroy($model_comprobante->id_comprobante);
                                    $msg .= "<div class='alert text-center  alert-danger'>" .
                                        "<p>La factura " . $nombre_xml . " del envío N#" . str_pad($request->id_envio, 9, "0", STR_PAD_LEFT) . " no pudo ser generada, por favor intente facturar el envío nuevamente</p>"
                                        . "</div>";
                                }
                            } else {

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
                                "<p>Hubo un error al guardar la factura " . $nombre_xml . " del envío N#" . str_pad($request->id_envio, 9, "0", STR_PAD_LEFT) . " en la base de datos, por favor intente facturar el envío nuevamente</p>"
                                . "</div>";
                        }
                    } else {
                        Comprobante::destroy($model_comprobante->id_comprobante);
                        $msg .= "<div class='alert text-center  alert-danger'>" .
                            "<p>Hubo un error al guardar la factura " . $nombre_xml . " del envío N#" . str_pad($request->id_envio, 9, "0", STR_PAD_LEFT) . " en la base de datos, por favor intente facturar el envío nuevamente</p>"
                            . "</div>";
                    }
                } else {
                    $msg .= "<div class='alert text-center  alert-danger'>" .
                        "<p>Hubo un error al guardar la factura " . $nombre_xml . " del envío N# ENV" . str_pad($request->id_envio, 9, "0", STR_PAD_LEFT) . " en la base de datos, por favor intente facturar el envío nuevamente</p>"
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

    public function generar_comprobante_lote(Request $request)
    {
        ini_set('max_execution_time', env('MAX_EXECUTION_TIME'));
        $secuencial = getSecuencial();
        $punto_acceso = Usuario::where('id_usuario', session('id_usuario'))->first()->punto_acceso;
        $secuencial = str_pad($secuencial, 9, "0", STR_PAD_LEFT);
        $xml = new DomDocument('1.0', 'UTF-8');
        $factura = $xml->createElement('lote');
        $factura->setAttribute('version', '1.0.0');
        $xml->appendChild($factura);
        $fechaEmision = now()->format('dmY');
        $tipoComprobante = '00';
        $ruc = env('RUC');
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
        foreach ($request->arrPreFacturas as $xml_firmado) {
            $data_xml_firmado = file_get_contents($path_firmados . $xml_firmado . ".xml");
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

        if ($obj_comprobante->save()) {
            $model_comprobante = Comprobante::all()->last();

            $save_xml = $xml->save(env('PATH_XML_FIRMADOS') . $nombre_xml);
            if ($save_xml && $save_xml > 0) {
                $resultado = enviarComprobante($claveAcceso . ".xml", $claveAcceso);
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
                    sleep(3);
                    $msg = respuesta_autorizacion_comprobante($claveAcceso);
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
                    "<p> No se encontro el comprobante electrónico generado relacionado a este registro</p>"
                    . "</div>";
            }else {
                $resultado = firmarComprobanteXml($comprobante->clave_acceso . ".xml");
                if ($resultado) {
                    $class = 'warning';
                    if ($resultado == 5) {
                        $class = 'success';
                        $obj_comprobante = Comprobante::find($idComprobante);
                        $obj_comprobante->estado = 1;
                        $obj_comprobante->save();
                    }
                    $msg .= "<div class='alert text-center  alert-" . $class . "'>" .
                        "<p> " . mensajeFirmaElectronica($resultado, str_pad($comprobante->id_envio, 9, "0", STR_PAD_LEFT)) . "</p>"
                        . "</div>";
                } else {
                    $msg .= "<div class='alert text-center  alert-danger'>" .
                        "<p>Hubo un error al realizar el proceso de la firma de la factura N# " . $comprobante->clave_acceso . ".xml" . " del envío N#" . str_pad($comprobante->id_envio, 9, "0", STR_PAD_LEFT) . ", intente nuevamente realizar la firma del mismo filtrando por GENERADOS</p>"
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

        if($comprobante->numero_comprobante == null){
            $msg = "<div class='alert text-center  alert-danger'>" .
            "<p> El mail no puede ser enviado debido a que este registro no posee número de comprobante, comuníquese con el departamento de tecnología </p>"
            . "</div>";
        }else {
            enviarMailComprobanteCliente("01", $comprobante->correo, $comprobante->nombre, $request->comprobante, $comprobante->numero_comprobante);
        }
        return $msg;
    }

    public function  ver_factura_aprobada_sri($clave_acceso){
        $cliente = new SoapClient(env('URL_WS_ATURIZACION'));
        $response = $cliente->autorizacionComprobante(["claveAccesoComprobante" => $clave_acceso]);
        $autorizacion = $response->RespuestaAutorizacionComprobante->autorizaciones->autorizacion;
        $dataComprobante = Comprobante::where('clave_acceso',$clave_acceso)->select('numero_comprobante','id_envio')->first();
        $data = [
            'autorizacion' => $autorizacion,
            'img_clave_acceso' => generateCodeBarGs1128((String)$autorizacion->numeroAutorizacion),
            'obj_xml' => simplexml_load_string($autorizacion->comprobante),
            'numeroComprobante' => $dataComprobante->numero_comprobante,
            'detalles_envio' => getEnvio($dataComprobante->id_envio)->detalles
        ];
       return PDF::loadView('adminlte.gestion.comprobante.partials.pdf.factura', compact('data'))->stream();
    }

    public function ver_pre_factura($clave_acceso){
        if (file_exists(env('PATH_XML_FIRMADOS').$clave_acceso.".xml")){
            $archivo = file_get_contents(env('PATH_XML_FIRMADOS').$clave_acceso.".xml");
            $dataComprobante = Comprobante::where('clave_acceso',$clave_acceso)->select('id_envio')->first();
            $autorizacion = simplexml_load_string($archivo);
            $data = [
                'autorizacion' => $autorizacion,
                'img_clave_acceso' => null,
                'obj_xml' => $autorizacion,
                'numeroComprobante' => getDetallesClaveAcceso((String)$autorizacion->infoTributaria->claveAcceso, 'SERIE').getDetallesClaveAcceso((String)$autorizacion->infoTributaria->claveAcceso, 'PUNTO_ACCESO').getDetallesClaveAcceso((String)$autorizacion->infoTributaria->claveAcceso, 'SECUENCIAL'),
                'detalles_envio' => getEnvio($dataComprobante->id_envio)->detalles
            ];
            return PDF::loadView('adminlte.gestion.comprobante.partials.pdf.factura', compact('data'))->stream();
        }else{
            $msg = "<div class='alert text-center  alert-danger'>" .
            "<p> El archivo del documento no existe, por favor contactarse con el área de sistemas. </p>"
            . "</div>";
            return $msg;
        }
    }

}
