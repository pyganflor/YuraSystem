<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DomDocument;
use SoapClient;
use yura\Jobs\EnvioComprobanteElectronico;

class FacturacionController extends Controller
{
    public function comprobante_xml_factura(){
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
        $secuencial = '000000090';
        $tipo_emision = '1';
        $codigo_numerico = '12345676';
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

        $cantidad_detalles = 1; //Número que indica la cantidad de detalles (envios a facturar) que tiene la factura
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
        $xml->save(env('PATH_XML_GENERADOS').$nombre_xml);

        $resultado = firmar_comprobante_xml($nombre_xml);

        if($resultado){
            mensaje_firma_electronica($resultado) == 5 ? $class = 'success': $class = 'warning';
            $msg = "<div class='alert text-center  alert-".$class."'>" .
                        "<p> ".mensaje_firma_electronica($resultado)."</p>"
                . "</div>";
        }else{
            $msg = "<div class='alert text-center  alert-danger'>" .
                        "<p>Hubo un error al realizar el proceso de la firma del comprobante, intente nuevamente realizar la firma del mismo filtrando por GENERADOS</p>"
                . "</div>";
        }
        return $msg;
    }

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

        $resultado = enviar_comprobante("1501201910179244632500110010010000000901234567611.xml","1501201910179244632500110010010000000901234567611");
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
        dd($cliente->autorizacionComprobanteLote(["claveAccesoLote"=>"1501201910179244632500110010010000000901234567611"]));
    }


}
