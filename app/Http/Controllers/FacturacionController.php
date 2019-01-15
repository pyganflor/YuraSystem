<?php

namespace yura\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DomDocument;

class FacturacionController extends Controller
{
    public function genera_comprobante_xml_factura(){

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
        $secuencial = '000000003';
        $modulo11= $this->generaModulo11();
        $codigo_numerico =  $modulo11['codigo_numerico'];
        $digito_verificador = $modulo11['digito_verificador'];
        $tipo_emision = '1';
        //factura normal codigo 01
        //tipo empision  codigo 1
        $claveAcceso = $fechaEmision.$tipoComprobante.$ruc.$entorno.$serie.$secuencial.$codigo_numerico.$tipo_emision.$digito_verificador;
        $informacionTributaria = [
            'ambiente'=>'1',
            'tipoEmision'=>'1',
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
            'totalSinImpuestos' => '100',
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
    }

    public function firmar_comprobante_xml(){

        $ruta_xml_generado = env('PATH_XML_GENERADOS');
        $xml_generado     = '0801201901179244632500110010010000000031234567814.xml';
        $ruta_xml_firmado = env('PATH_XML_FIRMADOS');
        $ruta_firma_digital =env('PATH_FIRMA_DIGITAL');
        $nombre_certificado = env('NOMBRE_ARCHIVO_FIRMA_DIGITAL');
        $contrasena_certificado = env('CONTRASENA_FIRMA_DIGITAL');
        $ruta_jar  = env('PATH_JAR_FIRMADOR');

        exec('java -Dfile.encoding=UTF-8 -jar '.$ruta_jar.' '
            .$ruta_xml_generado." "
            . $xml_generado." "
            . $ruta_xml_firmado." "
            . $ruta_firma_digital." "
            .$contrasena_certificado." "
            .$nombre_certificado." ",
            $a,$b);
        dd($a);
    }

    public function enviar_comprobante_xml(){

        $ruta_xml_firmado = env('PATH_XML_FIRMADOS');
        $xml_firmado     = '0801201901179244632500110010010000000031234567814.xml';
        $ruta_xml_enviados = env('PATH_XML_ENVIADOS');
        $ruta_firma_rechazados = env('PATH_XML_RECHAZADOS');
        $ruta_firma_autorizados = env('PATH_XML_AUTORIZADOS');
        $ruta_firma_no_autorizados = env('PATH_XML_NO_AUTORIZADOS');
        $url_ws_recepcion = env('URL_WS_RECEPCION');
        $url_ws_autorizacion = env('URL_WS_ATURIZACION');
        $ruta_jar  = env('PATH_JAR_ENVIADOR');
        $clave_acceso = '0801201901179244632500110010010000000031234567814';

        exec('java -jar '.$ruta_jar.' '
            .$ruta_xml_firmado." "
            . $xml_firmado." "
            . $ruta_xml_enviados." "
            . $ruta_firma_rechazados." "
            . $ruta_firma_autorizados." "
            . $ruta_firma_no_autorizados." "
            . $url_ws_recepcion." "
            . $url_ws_autorizacion." "
            . $clave_acceso." ",
            $a,$b);
        dd($a);
    }

    public function generaModulo11(){

        $n_aleatorio = mt_rand(0,99999999);
        while (strlen($n_aleatorio) < 8) {
            $n_aleatorio = mt_rand(0,99999999);
        }

        $arr_num = str_split($n_aleatorio);
        $sumatoria = ($arr_num[0]*3)+($arr_num[1]*2)+($arr_num[2]*7)+($arr_num[3]*6)+($arr_num[4]*5)+($arr_num[5]*4)+($arr_num[6]*3)+($arr_num[7]*2);
        $cociente = $sumatoria/11 ;
        $producto = ((int)$cociente)*11;
        $resultado = $sumatoria-$producto;
        $digito_verificador = 11-$resultado;

        if((11*(int)$cociente)+$resultado === $sumatoria){

            if($digito_verificador == 10)
                $digito_verificador = 1;
            elseif($digito_verificador == 11)
                $digito_verificador = 0;

            echo "aleatorio= ".$n_aleatorio ."<br/>";
            echo "sumatoria= ".$sumatoria ."<br/>";
            echo "cociente=  ".(int)$cociente ."<br/>";
            echo "producto=  ".$producto ."<br/>";
            echo "resultado= ".$resultado ."<br/>";
            echo "digito_verificador= ".$digito_verificador ."<br/>";

            return [
                'codigo_numerico'=>$n_aleatorio,
                'digito_verificador'=>$digito_verificador
            ];
        }else{
            dd("intente generar nuevamente el archivo");
        }
    }
}
