<?php

    Route::get('facturacion/generar_comprobante_xml','FacturacionController@comprobante_xml_factura');
    Route::get('facturacion/enviar_comprobante','FacturacionController@enviar_documento_electronico');
    Route::get('facturacion/generar_comprobante_lote','FacturacionController@comprobante_lote');
    Route::get('facturacion/autorizacion_comprobante','FacturacionController@autorizacion_comprobante');

