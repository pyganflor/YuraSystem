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
    /*$barcode = new BarcodeGenerator();
    $barcode->setText("0801201901200100200001369417924463252");
    $barcode->setType(BarcodeGenerator::Gs1128);
    $barcode->setNoLengthLimit(true);
    $barcode->setAllowsUnknownIdentifier(true);
    $code = $barcode->generate();
    echo '<img src="data:image/png;base64,'.$code.'" />';*/
    $autorizacion="hola";
    $pdf = PDF::loadView('adminlte.gestion.comprobante.partials.pdf.factura', compact('autorizacion'))->save(env('PDF_FACTURAS').".pdf");
    dd($pdf);
});
