<?php

use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;

Route::get('comprobante', 'ComprobanteController@inicio');
Route::get('comprobante/buscar', 'ComprobanteController@buscar_comprobante');
Route::get('comprobante/generar_comprobante_factura', 'ComprobanteController@generar_comprobante_factura');
Route::get('comprobante/enviar_comprobante', 'ComprobanteController@enviar_documento_electronico');
Route::get('comprobante/generar_comprobante_lote', 'ComprobanteController@generar_comprobante_lote');
Route::get('comprobante/firmar_comprobante', 'ComprobanteController@firmar_comprobante');
Route::get('comprobante/autorizacion_comprobante', 'ComprobanteController@autorizacion_comprobante');
Route::get('comprobante/formulario_facturacion', 'ComprobanteController@formulario_facturacion');
Route::get('comprobante/prueba', function () {

    dd(substr("1502201901179244632500110010010000002881234567811",30,9));
    $a = simplexml_load_string("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<factura id=\"comprobante\" version=\"1.0.0\">
  <infoTributaria>
    <ambiente>1</ambiente>
    <tipoEmision>1</tipoEmision>
    <razonSocial>Pyganflor S.A</razonSocial>
    <nombreComercial>Pyganflor</nombreComercial>
    <ruc>1792446325001</ruc>
    <claveAcceso>1502201901179244632500110010010000002881234567811</claveAcceso>
    <codDoc>01</codDoc>
    <estab>001</estab>
    <ptoEmi>001</ptoEmi>
    <secuencial>000000288</secuencial>
    <dirMatriz>Pasaje la Paz E9-01 y Av. 6 de Diciembre</dirMatriz>
  </infoTributaria>
  <infoFactura>
    <fechaEmision>15/02/2019</fechaEmision>
    <dirEstablecimiento>Vía San Jose de Minas Vía al Pisque</dirEstablecimiento>
    <obligadoContabilidad>SI</obligadoContabilidad>
    <tipoIdentificacionComprador>08</tipoIdentificacionComprador>
    <razonSocialComprador>THE QUEENS FLOWERS</razonSocialComprador>
    <identificacionComprador>979865784</identificacionComprador>
    <totalSinImpuestos>1048.00</totalSinImpuestos>
    <totalDescuento>0.00</totalDescuento>
    <totalConImpuestos>
      <totalImpuesto>
        <codigo>2</codigo>
        <codigoPorcentaje>7</codigoPorcentaje>
        <baseImponible>1048.00</baseImponible>
        <valor>0.00</valor>
      </totalImpuesto>
    </totalConImpuestos>
    <propina>0.00</propina>
    <importeTotal>1048.00</importeTotal>
    <moneda>DOLAR</moneda>
  </infoFactura>
  <detalles>
    <detalle>
      <codigoPrincipal>ENV000000115</codigoPrincipal>
      <descripcion>GYPSOPHILA (XL) 1000gr 50 cm</descripcion>
      <cantidad>200.00</cantidad>
      <precioUnitario>5.00</precioUnitario>
      <descuento>0.00</descuento>
      <precioTotalSinImpuesto>1000.00</precioTotalSinImpuesto>
      <impuestos>
        <impuesto>
          <codigo>2</codigo>
          <codigoPorcentaje>7</codigoPorcentaje>
          <tarifa>0.00</tarifa>
          <baseImponible>1000.00</baseImponible>
          <valor>0.00</valor>
        </impuesto>
      </impuestos>
    </detalle>
    <detalle>
      <codigoPrincipal>ENV000000115</codigoPrincipal>
      <descripcion>GYPSOPHILA (XL) 1250gr </descripcion>
      <cantidad>1.00</cantidad>
      <precioUnitario>4.00</precioUnitario>
      <descuento>0.00</descuento>
      <precioTotalSinImpuesto>4.00</precioTotalSinImpuesto>
      <impuestos>
        <impuesto>
          <codigo>2</codigo>
          <codigoPorcentaje>7</codigoPorcentaje>
          <tarifa>0.00</tarifa>
          <baseImponible>4.00</baseImponible>
          <valor>0.00</valor>
        </impuesto>
      </impuestos>
    </detalle>
    <detalle>
      <codigoPrincipal>ENV000000115</codigoPrincipal>
      <descripcion>GYPSOPHILA (XL) 500gr </descripcion>
      <cantidad>11.00</cantidad>
      <precioUnitario>4.00</precioUnitario>
      <descuento>0.00</descuento>
      <precioTotalSinImpuesto>44.00</precioTotalSinImpuesto>
      <impuestos>
        <impuesto>
          <codigo>2</codigo>
          <codigoPorcentaje>7</codigoPorcentaje>
          <tarifa>0.00</tarifa>
          <baseImponible>44.00</baseImponible>
          <valor>0.00</valor>
        </impuesto>
      </impuestos>
    </detalle>
  </detalles>
  <infoAdicional>
    <campoAdicional nombre=\"Dirección\">Florida West Palm Beach</campoAdicional>
    <campoAdicional nombre=\"Email\">flower@flor.com</campoAdicional>
    <campoAdicional nombre=\"Teléfono\">90058456</campoAdicional>
    <campoAdicional nombre=\"Carguera\">AT1</campoAdicional>
    <campoAdicional nombre=\"DAE\">6515928952</campoAdicional>
  </infoAdicional>
</factura>");

    dd((string)$a->infoAdicional->campoAdicional[1]);
});
