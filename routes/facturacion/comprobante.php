<?php

Route::get('comprobante', 'ComprobanteController@inicio');
Route::get('comprobante/buscar', 'ComprobanteController@buscar_comprobante');
Route::get('comprobante/generar_comprobante_factura', 'ComprobanteController@generar_comprobante_factura');
Route::get('comprobante/enviar_comprobante', 'ComprobanteController@enviar_documento_electronico');
Route::get('comprobante/generar_comprobante_lote', 'ComprobanteController@generar_comprobante_lote');
Route::get('comprobante/firmar_comprobante', 'ComprobanteController@firmar_comprobante');
Route::get('comprobante/autorizacion_comprobante', 'ComprobanteController@autorizacion_comprobante');
Route::get('comprobante/formulario_facturacion', 'ComprobanteController@formulario_facturacion');
Route::get('comprobante/reenviar_correo', 'ComprobanteController@reenviar_correo');
Route::get('comprobante/comprobante_aprobado_sri/{clave_acceso}', 'ComprobanteController@ver_factura_aprobada_sri');
Route::get('comprobante/pre_factura/{clave_acceso}/{cliente?}', 'ComprobanteController@ver_pre_factura');
Route::post('comprobante/generar_comprobante_guia_remision','ComprobanteController@generar_comprobante_guia_remision');
Route::get('comprobante/pre_guia_remision/{clave_acceso}', 'ComprobanteController@ver_pre_guia_remision');
Route::get('comprobanteguia_remision/{secuencial}', 'ComprobanteController@ver_guia_remision');
//RUTAS PARA QUE LA FACTURACION FUNCIONE CON EL VENTURE (LEE LOS DATOS DE LA PRE-FACTURA DESDE LA BD)
Route::post('comprobante/integrar_factura_venture', 'ComprobanteController@integrar_factura_venture');
Route::post('comprobante/descargar_txt', 'ComprobanteController@descargar_txt');
Route::get('comprobante/documento_pre_factura/{secuencial}/{cliente?}', 'ComprobanteController@ver_pre_factura_bd');
Route::post('comprobante/enviar_correo', 'ComprobanteController@enviar_correo');
Route::post('comprobante/desvincular_factura_venture', 'ComprobanteController@desvincular_factura_venture');
Route::post('comprobante/actualizar_comprobante_venture', 'ComprobanteController@actualizar_comprobante_venture');
Route::post('comprobante/anular_factura', 'ComprobanteController@anular_factura');
Route::post('comprobante/generar_comprobante_guia_remision_factura','ComprobanteController@generar_comprobante_guia_remision_factura');
Route::get('comprobante/guia_remision/{secuencial}', 'ComprobanteController@ver_guia_remision_bd');
Route::post('comprobante/carga_xml', 'ComprobanteController@carga_xml');

