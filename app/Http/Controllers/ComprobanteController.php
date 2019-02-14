<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DomDocument;
use yura\Modelos\Comprobante;
use yura\Modelos\ClasificacionRamo;
use yura\Modelos\ImpuestoDetalleFactura;
use yura\Modelos\DetalleFactura;
use yura\Modelos\DesgloseEnvioFactura;
use yura\Modelos\ImpuestoDesgloseEnvioFactura;
use yura\Modelos\InformacionAdicionalFactura;
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
        $busquedaDesde = $request->has('desde') ? $request->desde : '';
        $busquedaHasta = $request->has('hasta') ? $request->hasta : '';
        $busquedaComprobante = $request->has('codigo_comprobante') ? $request->codigo_comprobante : '';

        $listado = Comprobante::where([
            ['comprobante.estado', isset($request->estado) ? $request->estado : 1],
            ['dc.estado', 1],
            ['tipo_comprobante', '!=', "00"]
        ])->join('tipo_comprobante as tc', 'comprobante.tipo_comprobante', 'tc.codigo')
            ->join('envio as e', 'comprobante.id_envio', 'e.id_envio')
            ->join('pedido as p', 'e.id_pedido', 'p.id_pedido')
            ->join('detalle_cliente as dc', 'p.id_cliente', 'dc.id_cliente');

        if ($busquedaAnno != '')
            $listado = $listado->where(DB::raw('YEAR(de.fecha_emision)'), $busquedaAnno);
        if ($busquedacliente != '')
            $listado = $listado->where('dc.id_cliente', $busquedacliente);
        if ($busquedaHasta != '' && $request->hasta != '')
            $listado = $listado->whereBetween('comprobante.fecha_emision', [$busquedaDesde == '' ? '2000-01-01' : $busquedaDesde, $busquedaHasta]);
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
            'arrEnvios' => 'required|Array'
        ]);
        $msg = "";
        if (!$valida->fails()) {

            $inicio_secuencial = env('INICIAL_FACTURA');
            foreach ($request->arrEnvios as $dataEnvio) {

                $datos_xml = getDatosFacturaEnvio($dataEnvio[0]);
                $precio_total_sin_impuestos = 0;
                foreach ($datos_xml->get() as $dataXml) {

                    $precio_unitario_individual_sin_impuestos = Precio::where([
                        ['id_variedad', $dataXml->id_variedad],
                        ['id_clasificacion_ramo', $dataXml->id_clasificacion_ramo]
                    ])->select('cantidad')->first();

                    if (!isset($precio_unitario_individual_sin_impuestos->cantidad)) {
                        $variedad = Variedad::where('id_variedad', $dataXml->id_variedad)->first();
                        $clasificacion_ramo = ClasificacionRamo::where('id_clasificacion_ramo', $dataXml->id_clasificacion_ramo)
                            ->join('unidad_medida as um', 'clasificacion_ramo.id_unidad_medida', 'um.id_unidad_medida')->select('clasificacion_ramo.nombre as nombre_clasificiacion_ramo', 'um.siglas')->first();
                        return '<div class="alert alert-danger text-center">' .
                            '<p> No se ha configurado precio para la variedad ' . $variedad->nombre . ' de ' . $clasificacion_ramo->nombre_clasificiacion_ramo . ' ' . $clasificacion_ramo->siglas . ' </p>'
                            . '</div>';
                    }

                    $total_individual_ramos = $dataXml->cantidad_ramos * $dataXml->cantidad_cajas;
                    $precio_total_individual = $total_individual_ramos * $precio_unitario_individual_sin_impuestos->cantidad;
                    $precio_total_sin_impuestos += $precio_total_individual;
                }

                if (!isset(getCodigoDae($dataEnvio[4])->codigo_dae)) {
                    return '<div class="alert alert-danger text-center">' .
                        '<p> No se ha configurado un código DAE para ' . $dataEnvio[6] . ' en este mes </p>'
                        . '</div>';
                }

                if ($dataEnvio[1] > $precio_total_sin_impuestos) {
                    return '<div class="alert alert-danger text-center">' .
                        '<p> El descuento es mayor que el sub total de la factura perteneciente al envío: N#' . str_pad($dataEnvio[0], 9, "0", STR_PAD_LEFT) . '<br/> 
                        Sub total: $' . number_format($precio_total_sin_impuestos, 2, ".", "") . ', Descuento propuesto $' . number_format($dataEnvio[1], 2, ".", "") . ' </p>'
                        . '</div>';
                }

                $dataEnvio[1] > 0 ? $precio_total_sin_impuestos = $precio_total_sin_impuestos - $dataEnvio[1] : '';

                $secuencial = getSecuencial();
                $xml = new DomDocument('1.0', 'UTF-8');
                $factura = $xml->createElement('factura');
                $factura->setAttribute('id', 'comprobante');
                $factura->setAttribute('version', '1.0.0');
                $xml->appendChild($factura);
                $infoTributaria = $xml->createElement('infoTributaria');
                $factura->appendChild($infoTributaria);
                $fechaEmision = Carbon::now()->format('dmY');
                $tipoComprobante = '01';
                $ruc = env('RUC');
                $entorno = env('ENTORNO');
                $punto_acceso = Usuario::where('id_usuario', session('id_usuario'))->first()->punto_acceso;
                $serie = '001' . $punto_acceso;
                $codigo_numerico = env('CODIGO_NUMERICO');
                $tipo_emision = '1';
                $cadena = $fechaEmision . $tipoComprobante . $ruc . $entorno . $serie . $secuencial . $codigo_numerico . $tipo_emision;
                $digito_verificador = generaDigitoVerificador($cadena);
                $claveAcceso = $cadena . $digito_verificador;
                $informacionTributaria = [
                    'ambiente' => $entorno,
                    'tipoEmision' => $tipo_emision,
                    'razonSocial' => $dataXml->razon_social,
                    'nombreComercial' => $dataXml->nombre_empresa,
                    'ruc' => $ruc,
                    'claveAcceso' => $claveAcceso,
                    'codDoc' => '01',
                    'estab' => '001',
                    'ptoEmi' => $punto_acceso,
                    'secuencial' => $secuencial,
                    'dirMatriz' => $dataXml->direccion_matriz
                ];

                foreach ($informacionTributaria as $key => $it) {
                    $nodo = $xml->createElement($key, $it);
                    $infoTributaria->appendChild($nodo);
                }

                $infoFactura = $xml->createElement('infoFactura');
                $factura->appendChild($infoFactura);
                $informacionFactura = [
                    'fechaEmision' => Carbon::now()->format('d/m/Y'),
                    'dirEstablecimiento' => $dataXml->direccion_establecimiento,
                    'obligadoContabilidad' => env('OBLIGADO_CONTABILIDAD'),
                    'tipoIdentificacionComprador' => $dataXml->codigo_identificacion,
                    'razonSocialComprador' => $dataXml->identificacion == "9999999999999" ? "CONSUMIDOR FINAL" : $dataXml->nombre_cliente,
                    'identificacionComprador' => $dataXml->identificacion,
                    'totalSinImpuestos' => number_format($precio_total_sin_impuestos, 2, ".", ""),
                    'totalDescuento' => $dataEnvio[1] > 0 ? number_format($dataEnvio[1], 2, ".", "") : "0.00",
                ];

                foreach ($informacionFactura as $key => $if) {
                    $nodo = $xml->createElement($key, $if);
                    $infoFactura->appendChild($nodo);
                }

                $totalConImpuestos = $xml->createElement('totalConImpuestos');
                $infoFactura->appendChild($totalConImpuestos);
                $precio_total_con_impuestos = is_numeric($dataXml->porcntaje_iva) ? number_format($precio_total_sin_impuestos * ($dataXml->porcntaje_iva / 100), 2, ".", "") : "0.00";
                $valorImpuesto = $precio_total_con_impuestos;
                $cantidad_impuestos = 1; //Número que indica la cantidad de impuestos que tiene la factura
                for ($i = 0; $i < $cantidad_impuestos; $i++) {
                    $informacionImpuestos = [
                        'codigo' => $dataXml->codigo_impuesto,
                        'codigoPorcentaje' => $dataXml->codigo_porcentaje,
                        //'descuentoAdicional'=>'0.00',
                        'baseImponible' => number_format($precio_total_sin_impuestos, 2, ".", ""),
                        //'tarifa'=> '0.00',
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
                $moneda = $xml->createElement('moneda', 'DOLAR');
                $infoFactura->appendChild($moneda);

                $detalles = $xml->createElement('detalles');
                $factura->appendChild($detalles);
                //dd($datos_xml->count());
                for ($j = 0; $j < $datos_xml->count(); $j++) {
                    $data_arr_consulta = $datos_xml->get()[$j];
                    $precio_unitario_individual_sin_impuestos = Precio::where([
                        ['id_variedad', $data_arr_consulta->id_variedad],
                        ['id_clasificacion_ramo', $data_arr_consulta->id_clasificacion_ramo]
                    ])->select('cantidad')->first();

                    $total_individual_ramos = $data_arr_consulta->cantidad_ramos * $data_arr_consulta->cantidad_cajas;
                    $precio_total_individual = $total_individual_ramos * $precio_unitario_individual_sin_impuestos->cantidad;

                    $dataEnvio[1] > 0 ? $precio_total_individual = $precio_total_individual - ($dataEnvio[1] / $datos_xml->count()) : '';

                    $detalle = $xml->createElement('detalle');
                    $detalles->appendChild($detalle);

                    $informacionDetalle = [
                        'codigoPrincipal' => 'ENV' . str_pad($dataEnvio[0], 9, "0", STR_PAD_LEFT),
                        'descripcion' => $data_arr_consulta->nombre_planta . " (" . $data_arr_consulta->siglas_variedad . ") " . $data_arr_consulta->nombre_clasificacion . $data_arr_consulta->siglas_unidad_medida_peso_ramo . " " . $data_arr_consulta->longitud_ramo . $data_arr_consulta->siglas_unidad_medida_lognitud_ramo,
                        'cantidad' => number_format($data_arr_consulta->cantidad_ramos * $data_arr_consulta->cantidad_cajas, 2, ".", ""),
                        'precioUnitario' => number_format($precio_unitario_individual_sin_impuestos->cantidad, 2, ".", ""),
                        'descuento' => $dataEnvio[1] > 0 ? $dataEnvio[1] / $datos_xml->count() : '0.00',
                        'precioTotalSinImpuesto' => number_format($precio_total_individual, 2, ".", "")
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
                        'codigo' => $dataXml->codigo_impuesto,
                        'codigoPorcentaje' => $dataXml->codigo_porcentaje,
                        //'descuentoAdicional'=>'0.00',
                        'tarifa' => is_numeric($dataXml->porcntaje_iva) ? number_format($dataXml->porcntaje_iva, 2, ".", "") : "0.00",
                        'baseImponible' => number_format($precio_total_individual, 2, ".", ""),
                        'valor' => is_numeric($dataXml->porcntaje_iva) ? number_format($precio_total_individual * ($dataXml->porcntaje_iva / 100), 2, ".", "") : "0.00"
                    ];
                    foreach ($informacionImpuesto as $key => $iIp) {
                        $nodo = $xml->createElement($key, $iIp);
                        $impuesto->appendChild($nodo);
                    }
                }

                $informacionAdicional = $xml->createElement('infoAdicional');
                $factura->appendChild($informacionAdicional);

                $campos_adicionales = [
                    'Dirección' => $dataXml->provincia . " " . $dataXml->direccion,
                    'Email' => $dataXml->correo,
                    'Teléfono' => $dataXml->telefono,
                ];

                if (getConfiguracionEmpresa()->codigo_pais != $dataEnvio[4])
                    $campos_adicionales['DAE'] = getCodigoDae($dataEnvio[4])->codigo_dae;
                if (!empty($dataEnvio[2]))
                    $campos_adicionales['GUIA_MADRE'] = $dataEnvio[2];
                if (!empty($dataEnvio[3]))
                    $campos_adicionales['GUIA_HIJA'] = $dataEnvio[3];

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
                $obj_comprobante->id_envio = $dataEnvio[0];
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
                            for ($p = 0; $p < $datos_xml->count(); $p++) {

                                $datos_xml_iteracion = $datos_xml->get()[$p];
                                $precio_unitario_individual_sin_impuestos = Precio::where([
                                    ['id_variedad', $datos_xml_iteracion->id_variedad],
                                    ['id_clasificacion_ramo', $datos_xml_iteracion->id_clasificacion_ramo]
                                ])->select('cantidad')->first();

                                $total_individual_ramos = $datos_xml_iteracion->cantidad_ramos * $datos_xml_iteracion->cantidad_cajas;
                                $precio_total_individual = $total_individual_ramos * $precio_unitario_individual_sin_impuestos->cantidad;

                                $dataEnvio[1] > 0 ? $precio_total_individual = $precio_total_individual - ($dataEnvio[1] / $datos_xml_iteracion->count()) : '';

                                $objDesgloseEnvioFactura = new DesgloseEnvioFactura;
                                $objDesgloseEnvioFactura->id_comprobante = $model_comprobante->id_comprobante;
                                $objDesgloseEnvioFactura->codigo_principal = 'ENV' . str_pad($dataEnvio[0], 9, "0", STR_PAD_LEFT);
                                $objDesgloseEnvioFactura->descripcion = $datos_xml_iteracion->nombre_planta . " (" . $datos_xml_iteracion->siglas_variedad . ") " . $datos_xml_iteracion->nombre_clasificacion . $datos_xml_iteracion->siglas_unidad_medida_peso_ramo . " " . $datos_xml_iteracion->longitud_ramo . $datos_xml_iteracion->siglas_unidad_medida_lognitud_ramo;
                                $objDesgloseEnvioFactura->cantidad = number_format($datos_xml_iteracion->cantidad_ramos * $datos_xml_iteracion->cantidad_cajas, 2, ".", "");
                                $objDesgloseEnvioFactura->precio_unitario = number_format($precio_unitario_individual_sin_impuestos->cantidad, 2, ".", "");
                                $objDesgloseEnvioFactura->descuento = $dataEnvio[1] > 0 ? $dataEnvio[1] / $datos_xml_iteracion->count() : '0.00';
                                $objDesgloseEnvioFactura->precio_total_sin_impuesto = number_format($precio_total_individual, 2, ".", "");

                                if($objDesgloseEnvioFactura->save()){
                                    bitacora('impuesto_detalle_factura', $model_impuesto_detalle_factura->id_impuesto_detalle_factura, 'I', 'Creación de un nuevo impuesto de detalle de factura');
                                    $model_desglose_envio_factura = DesgloseEnvioFactura::all()->last();

                                    $objImpuestoDesgloseEnvioFactura = new ImpuestoDesgloseEnvioFactura;
                                    $objImpuestoDesgloseEnvioFactura->id_desglose_envio_factura = $model_desglose_envio_factura->id_desglose_envio_factura;
                                    $objImpuestoDesgloseEnvioFactura->codigo_impuesto           = $datos_xml_iteracion->codigo_impuesto;
                                    $objImpuestoDesgloseEnvioFactura->codigo_porcentaje	        = $datos_xml_iteracion->codigo_porcentaje;
                                    $objImpuestoDesgloseEnvioFactura->base_imponible            = number_format($precio_total_individual,2,".","");
                                    $objImpuestoDesgloseEnvioFactura->valor                     = is_numeric($datos_xml_iteracion->porcntaje_iva) ? number_format($precio_total_individual*($datos_xml_iteracion->porcntaje_iva/100),2,".","") : "0.00";
                                    $objImpuestoDesgloseEnvioFactura->save() ?  $iteracion++ : '';
                                }
                            }

                            if ($iteracion === $datos_xml->count()) {

                                $objInformacionAdicionalFactura = new InformacionAdicionalFactura;
                                $objInformacionAdicionalFactura->id_comprobante = $model_comprobante->id_comprobante;
                                $objInformacionAdicionalFactura->direccion      = $campos_adicionales['Dirección'];
                                $objInformacionAdicionalFactura->email          = $campos_adicionales['Email'];
                                $objInformacionAdicionalFactura->telefono       = $campos_adicionales['Teléfono'];
                                if(isset($campos_adicionales['DAE']))
                                $objInformacionAdicionalFactura->dae            = $campos_adicionales['DAE'];
                                if(isset($campos_adicionales['GUIA_MADRE']))
                                $objInformacionAdicionalFactura->guia_madre     = $campos_adicionales['GUIA_MADRE'];
                                if(isset($campos_adicionales['GUIA_HIJA']))
                                $objInformacionAdicionalFactura->guia_hija      = $campos_adicionales['GUIA_HIJA'];

                                if( $objInformacionAdicionalFactura->save()) {
                                    $model_informacion_adicional_factura = InformacionAdicionalFactura::all()->last();
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
                                            bitacora('informacion_adicional_factura', $model_informacion_adicional_factura->id_informacion_adicional_factura , 'I', 'Creación de una nueva información de factura');
                                            $msg .= "<div class='alert text-center  alert-" . $class . "'>" .
                                                "<p> " . mensajeFirmaElectronica($resultado, str_pad($dataEnvio[0], 9, "0", STR_PAD_LEFT)) . "</p>"
                                                . "</div>";
                                        } else {
                                            $msg .= "<div class='alert text-center  alert-danger'>" .
                                                "<p>Hubo un error al realizar el proceso de la firma de la factura N# " . $nombre_xml . " del envío N#" . str_pad($dataEnvio[0], 9, "0", STR_PAD_LEFT) . ", intente nuevamente realizar la firma del mismo filtrando por GENERADOS</p>"
                                                . "</div>";
                                        }
                                    } else {
                                        InformacionAdicionalFactura::where('id_comprobante', $model_comprobante->id_comprobante)->delete();
                                        ImpuestoDesgloseEnvioFactura::where('id_desglose_envio_factura', $model_desglose_envio_factura->id_desglose_envio_factura)->delete();
                                        DesgloseEnvioFactura::where('id_comprobante', $model_comprobante->id_comprobante)->delete();
                                        ImpuestoDetalleFactura::where('id_detalle_factura', $model_detalle_factura->id_detalle_factura)->delete();
                                        DetalleFactura::where('id_comprobante', $model_comprobante->id_comprobante)->delete();
                                        Comprobante::destroy($model_comprobante->id_comprobante);
                                        $msg .= "<div class='alert text-center  alert-danger'>" .
                                            "<p>La factura " . $nombre_xml . " del envío N#" . str_pad($dataEnvio[0], 9, "0", STR_PAD_LEFT) . " no pudo ser generada, por favor intente facturarla nuevamente</p>"
                                            . "</div>";
                                    }

                                } else {
                                    ImpuestoDesgloseEnvioFactura::where('id_desglose_envio_factura', $model_desglose_envio_factura->id_desglose_envio_factura)->delete();
                                    DesgloseEnvioFactura::where('id_comprobante', $model_comprobante->id_comprobante)->delete();
                                    ImpuestoDetalleFactura::where('id_detalle_factura', $model_detalle_factura->id_detalle_factura)->delete();
                                    DetalleFactura::where('id_comprobante', $model_comprobante->id_comprobante)->delete();
                                    Comprobante::destroy($model_comprobante->id_comprobante);
                                    $msg .= "<div class='alert text-center  alert-danger'>" .
                                        "<p>La factura " . $nombre_xml . " del envío N#" . str_pad($dataEnvio[0], 9, "0", STR_PAD_LEFT) . " no pudo ser generada, por favor intente facturarla nuevamente</p>"
                                        . "</div>";
                                }
                            } else {
                                DesgloseEnvioFactura::where('id_comprobante', $model_comprobante->id_comprobante)->delete();
                                ImpuestoDetalleFactura::where('id_detalle_factura', $model_detalle_factura->id_detalle_factura)->delete();
                                DetalleFactura::where('id_comprobante', $model_comprobante->id_comprobante)->delete();
                                Comprobante::destroy($model_comprobante->id_comprobante);
                                $msg .= "<div class='alert text-center  alert-danger'>" .
                                    "<p>La factura " . $nombre_xml . " del envío N#" . str_pad($dataEnvio[0], 9, "0", STR_PAD_LEFT) . " no pudo ser generada, por favor intente facturarla nuevamente</p>"
                                    . "</div>";
                            }
                        } else {
                            DetalleFactura::where('id_comprobante', $model_detalle_factura->id_detalle_factura)->delete();
                            Comprobante::destroy($model_comprobante->id_comprobante);
                            $msg .= "<div class='alert text-center  alert-danger'>" .
                                "<p>Hubo un error al guardar la factura " . $nombre_xml . " del envío N#" . str_pad($dataEnvio[0], 9, "0", STR_PAD_LEFT) . " en la base de datos, por favor intente facturar el envío nuevamente</p>"
                                . "</div>";
                        }
                    } else {
                        Comprobante::destroy($model_comprobante->id_comprobante);
                        $msg .= "<div class='alert text-center  alert-danger'>" .
                            "<p>Hubo un error al guardar la factura " . $nombre_xml . " del envío N#" . str_pad($dataEnvio[0], 9, "0", STR_PAD_LEFT) . " en la base de datos, por favor intente facturar el envío nuevamente</p>"
                            . "</div>";
                    }
                } else {
                    $msg .= "<div class='alert text-center  alert-danger'>" .
                        "<p>Hubo un error al guardar la factura " . $nombre_xml . " del envío N# ENV" . str_pad($dataEnvio[0], 9, "0", STR_PAD_LEFT) . " en la base de datos, por favor intente facturar el envío nuevamente</p>"
                        . "</div>";
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
        return $msg;
    }

    public function generar_comprobante_lote(Request $request)
    {
        $secuencial = getSecuencial();
        $punto_acceso = Usuario::where('id_usuario', session('id_usuario'))->first()->punto_acceso;
        $secuencial = str_pad($secuencial, 9, "0", STR_PAD_LEFT);
        $xml = new DomDocument('1.0', 'UTF-8');
        $factura = $xml->createElement('lote');
        $factura->setAttribute('version', '1.0.0');
        $xml->appendChild($factura);
        $fechaEmision = Carbon::now()->format('dmY');
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

        foreach ($request->arrNoFirmados as $idComprobante) {
            $comprobante = Comprobante::where('id_comprobante', $idComprobante)->first();
            $msg = '';
            $resultado = firmarComprobanteXml($comprobante->clave_acceso.".xml");
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
                    "<p>Hubo un error al realizar el proceso de la firma de la factura N# " . $comprobante->clave_acceso.".xml" . " del envío N#" . str_pad($comprobante->id_envio, 9, "0", STR_PAD_LEFT) . ", intente nuevamente realizar la firma del mismo filtrando por GENERADOS</p>"
                    . "</div>";
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
}
