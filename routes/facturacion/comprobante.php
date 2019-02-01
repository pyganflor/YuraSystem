<?php

    Route::get('comprobante/generar_factura_cliente','ComprobanteController@generar_factura_cliente');
    Route::get('comprobante/enviar_comprobante','ComprobanteController@enviar_documento_electronico');
    Route::get('comprobante/generar_comprobante_lote','ComprobanteController@comprobante_lote');
    Route::get('comprobante/autorizacion_comprobante','ComprobanteController@autorizacion_comprobante');
    Route::get('comprobante/formulario_facturacion','ComprobanteController@formulario_facturacion');


