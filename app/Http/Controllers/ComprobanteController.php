<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DomDocument;
use SoapClient;
use yura\Modelos\Comprobante;
use Validator;
use yura\Modelos\Envio;
use yura\Jobs\EnvioComprobanteElectronico;

class ComprobanteController extends Controller
{

    public function comprobante_lote(){

        $xml = new DomDocument('1.0', 'UTF-8');
        $factura = $xml->createElement('lote');
        $factura->setAttribute('version','1.0.0');
        $xml->appendChild($factura);
        $fechaEmision = Carbon::now()->format('dmY');
        $tipoComprobante = '00';
        $ruc = env('RUC');
        $entorno = env('ENTORNO');
        $serie = '001001';
        $secuencial = '000000088';
        $tipo_emision = '1';
        $codigo_numerico = '22222228';
        $cadena = $fechaEmision.$tipoComprobante.$ruc.$entorno.$serie.$secuencial.$codigo_numerico.$tipo_emision;
        $digito_verificador = generaDigitoVerificador($cadena);
        $claveAcceso = $cadena.$digito_verificador;
        $nodeClaveAcceso = $xml->createElement('claveAcceso',$claveAcceso);
        $factura->appendChild($nodeClaveAcceso);
        $nodeRuc = $xml->createElement('ruc',$ruc);
        $factura->appendChild($nodeRuc);
        $nodeComprobantes = $xml->createElement('comprobantes');
        $factura->appendChild($nodeComprobantes);

        $path_firmados = env('PATH_XML_FIRMADOS');
        $xml_firmados= [
            '1401201901179244632500110010010000000841234567615',
            '1401201901179244632500110010010000000851234567610',
            '1401201901179244632500110010010000000861234567616',
            '1401201901179244632500110010010000000871234567611'
        ];

        foreach ($xml_firmados as $xml_firmado){
            $data_xml_firmado = file_get_contents($path_firmados.$xml_firmado.".xml");
            $nodeComprobante = $xml->createElement('comprobante',$data_xml_firmado);
            $nodeComprobantes->appendChild($nodeComprobante);
        }

        $xml->formatOutput = true;
        $xml->saveXML();
        $nombre_xml = "lote".$claveAcceso.".xml";
        $xml->save(env('PATH_XML_GENERADOS').$nombre_xml);

    }

    public function enviar_documento_electronico(Request $request){

        $resultado = enviar_comprobante("1701201901179244632500110010010000000911234567811.xml","1701201901179244632500110010010000000911234567811");
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

    public function autorizacion_comprobante(){
        $cliente = new SoapClient(env('URL_WS_ATURIZACION'));
        //dd($cliente->__getFunctions());
        dd($cliente->autorizacionComprobante(["claveAccesoComprobante"=>"1701201901179244632500110010010000000911234567811"]));
    }

    public function formulario_facturacion(Request $request){
        return view('adminlte.gestion.configuracion_facturacion.tipo_comprobantes.forms.form_facturacion');
    }

    public function generar_comprobante_cliente(Request $request){

        $valida= Validator::make($request->all(), [
            'arrEnvios' => 'required|Array'
        ]);
        $msg = "";
        if (!$valida->fails()) {

            $inicio_secuencial = env('INICIAL_FACTURA');
            foreach ($request->arrEnvios as $dataEnvio) {

                $datos_xml = Envio::where([
                    ['envio.id_envio',$dataEnvio[0]],
                    ['dc.estado',1]
                ])->join('detalle_envio as de', 'envio.id_envio','de.id_envio')
                    ->join('pedido as p', 'envio.id_pedido','p.id_pedido')
                    ->join('detalle_cliente as dc','p.id_cliente','dc.id_cliente')
                    ->join('especificacion as e','de.id_especificacion','e.id_especificacion')
                    ->join('especificacion_empaque as eemp', 'e.id_especificacion','eemp.id_especificacion')
                    ->join('empaque as emp','eemp.id_empaque','emp.id_empaque')
                    ->join('configuracion_empresa as ce','emp.id_configuracion_empresa','ce.id_configuracion_empresa')
                    ->join('agencia_transporte as at','de.id_agencia_transporte','at.id_agencia_transporte')

                    ->first();
                dd($datos_xml);
                $secuencial = $inicio_secuencial+1;
                $cant_reg = Comprobante::count();
                if($cant_reg > 0)
                    $secuencial = $cant_reg + $inicio_secuencial + 1;

                $secuencial = str_pad($secuencial,9,"0",STR_PAD_LEFT);
                //dd($secuencial);

                $xml = new DomDocument('1.0', 'UTF-8');
                $factura = $xml->createElement('factura');
                $factura->setAttribute('id','comprobante');
                $factura->setAttribute('version','1.0.0');
                $xml->appendChild($factura);
                $infoTributaria = $xml->createElement('infoTributaria');
                $factura->appendChild($infoTributaria);
                $fechaEmision = Carbon::now()->format('dmY');
                $tipoComprobante = '01';
                $ruc = env('RUC');
                $entorno = env('ENTORNO');
                $serie = '001001';
                $codigo_numerico = env('CODIGO_NUMERICO');
               // $secuencial = '000000091';
                $tipo_emision = '1';
                $cadena = $fechaEmision.$tipoComprobante.$ruc.$entorno.$serie.$secuencial.$codigo_numerico.$tipo_emision;
                $digito_verificador = generaDigitoVerificador($cadena);
                //factura normal codigo 01
                //tipo empision  codigo 1
                $claveAcceso = $cadena.$digito_verificador;
                $informacionTributaria = [
                    'ambiente'=>$entorno,
                    'tipoEmision'=>$tipo_emision,
                    'razonSocial'=>'PRUEBAS SERVICIO DE RENTAS INTERNAS',
                    'nombreComercial'=>'LE HACE BIEN AL PAIS',
                    'ruc' => $ruc,
                    'claveAcceso' => $claveAcceso,
                    'codDoc' => '01',
                    'estab' => '001',
                    'ptoEmi'=> '001',
                    'secuencial'=> $secuencial,
                    'dirMatriz' => 'AMAZONAS Y ROCA'
                ];

                foreach ($informacionTributaria as $key => $it){
                    $nodo = $xml->createElement($key,$it);
                    $infoTributaria->appendChild($nodo);
                }

                $infoFactura = $xml->createElement('infoFactura');
                $factura->appendChild($infoFactura);
                $informacionFactura = [
                    'fechaEmision'=>Carbon::now()->format('d/m/Y'),
                    'dirEstablecimiento'=>'Vía San Jose de Minas Vía al Pisque',
                    'obligadoContabilidad'=>'SI',
                    'tipoIdentificacionComprador'=> '09',
                    'razonSocialComprador' => 'FLOREXPO LLC',
                    'identificacionComprador' => '1',
                    'totalSinImpuestos' => '100.00',
                    'totalDescuento' => '0.00',
                ];

                foreach ($informacionFactura as $key => $if){
                    $nodo = $xml->createElement($key, $if);
                    $infoFactura->appendChild($nodo);
                }

                $totalConImpuestos = $xml->createElement('totalConImpuestos');
                $infoFactura->appendChild($totalConImpuestos);

                $cantidad_impuestos = 1; //Número que indica la cantidad de impuestos que tiene la factura
                for ($i=0; $i < $cantidad_impuestos; $i++){
                    $informacionImpuestos = [
                        'codigo'=>'2',
                        'codigoPorcentaje'=>'0',
                        //'descuentoAdicional'=>'0.00',
                        'baseImponible'=>'100.00',
                        'tarifa'=> '0.00',
                        'valor'=> '0.00'
                    ];

                    $totalImpuesto = $xml->createElement('totalImpuesto');
                    $totalConImpuestos->appendChild($totalImpuesto);

                    foreach ($informacionImpuestos as $key => $iI){
                        $nodo = $xml->createElement($key,$iI);
                        $totalImpuesto->appendChild($nodo);
                    }
                }
                $propina = $xml->createElement('propina','0.00');
                $infoFactura->appendChild($propina);
                $importeTotal = $xml->createElement('importeTotal','100.00');
                $infoFactura->appendChild($importeTotal);
                $moneda = $xml->createElement('moneda','DOLAR');
                $infoFactura->appendChild($moneda);

                $detalles = $xml->createElement('detalles');
                $factura->appendChild($detalles);

                $cantidad_detalles = 1; //Número que indica la cantidad de detalles
                for($j=0; $j<$cantidad_detalles; $j++){

                    $detalle = $xml->createElement('detalle');
                    $detalles->appendChild($detalle);

                    $informacionDetalle= [
                        'codigoPrincipal' => '0011601010003',
                        'descripcion'     => 'GYP. MS. 250 GR 75 CM',
                        'cantidad'        => '2.00',
                        'precioUnitario'  => '50.00',
                        'descuento'       => '0.00',
                        'precioTotalSinImpuesto' => '100.00'
                    ];

                    foreach ($informacionDetalle as $key => $iD){
                        $nodo = $xml->createElement($key,$iD);
                        $detalle->appendChild($nodo);
                    }

                    $impuestos = $xml->createElement('impuestos');
                    $detalle->appendChild($impuestos);

                    $cantidad_impuesto = 1;

                    for($z=0; $z<$cantidad_impuesto; $z++){
                        $impuesto = $xml->createElement('impuesto');
                        $impuestos->appendChild($impuesto);

                        $informacionImpuesto = [
                            'codigo' => '2',
                            'codigoPorcentaje' => '0',
                            //'descuentoAdicional'=>'0.00',
                            'tarifa'=> '0.00',
                            'baseImponible' => '100.00',
                            'valor' => '0.00'
                        ];

                        foreach ($informacionImpuesto as $key => $iIp){
                            $nodo = $xml->createElement($key,$iIp);
                            $impuesto->appendChild($nodo);
                        }
                    }

                    $informacionAdicional =  $xml->createElement('infoAdicional');
                    $factura->appendChild($informacionAdicional);

                    $campos_adicionales = [
                        'Dirección' => '1960 KELLOGG AVE',
                        'Email'=> 'daniela@ecuabloom.com',
                        'Teléfono' => '59322555440',
                        'DAE' => '05520184000859803',
                        'AWB' => '729 6908 7771'
                    ];

                    foreach ($campos_adicionales as $key => $ca){
                        $campo_adicional = $xml->createElement('campoAdicional',$ca);
                        $campo_adicional->setAttribute('nombre',$key);
                        $informacionAdicional->appendChild($campo_adicional);
                    }
                }
                $xml->formatOutput = true;
                $xml->saveXML();
                $nombre_xml = $claveAcceso.".xml";

                $obj_comprobante = new Comprobante;
                $obj_comprobante->clave_acceso = $claveAcceso;
                if($obj_comprobante->save()){
                    $model = Comprobante::all()->last();
                    $save = $xml->save(env('PATH_XML_GENERADOS').$nombre_xml);
                    if($save && $save > 0){
                        $resultado = firmar_comprobante_xml($nombre_xml);
                        if($resultado){
                            $class = 'warning';
                            if($resultado == 5){
                                $class = 'success';
                                $obj_comprobante = Comprobante::find($model->id_comprobante);
                                $obj_comprobante->estado = 1;
                                $obj_comprobante->save();
                            }
                            $msg .= "<div class='alert text-center  alert-".$class."'>" .
                                "<p> ".mensaje_firma_electronica($resultado,str_pad($dataEnvio[0],9,"0",STR_PAD_LEFT))."</p>"
                                . "</div>";
                        }
                        else{
                            $msg .= "<div class='alert text-center  alert-danger'>" .
                                "<p>Hubo un error al realizar el proceso de la firma del comprobante, intente nuevamente realizar la firma del mismo filtrando por GENERADOS</p>"
                                . "</div>";
                        }
                    }else{
                        Comprobante::destroy($model->id_comprobante);
                        $msg .= "<div class='alert text-center  alert-danger'>" .
                            "<p>La factura ".$nombre_xml." del envío N#". str_pad($dataEnvio[0],9,"0",STR_PAD_LEFT) ." se creó vacía por favor intente facturarla nuevamente</p>"
                            . "</div>";
                    }
                }
                else{
                    $msg .= "<div class='alert text-center  alert-danger'>" .
                        "<p>Hubo un error al guardar la factura ".$nombre_xml." del envío N#".str_pad($dataEnvio[0],9,"0",STR_PAD_LEFT) ." en la base de datos por favor intente facturarla nuevamente</p>"
                        . "</div>";
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
        return $msg;

    }

}
