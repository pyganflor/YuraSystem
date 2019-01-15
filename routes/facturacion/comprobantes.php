<?php

    Route::get('comprobantes','ComprobanteController@index');
    Route::get('comprobantes/buscar','ComprobanteController@buscar_comprobantes');
    Route::get('comprobantes/add_comprobantes','ComprobanteController@add_comprobantes');
    Route::post('comprobantes/store_comprobantes','ComprobanteController@store_comprobantes');
    Route::post('comprobantes/actualizar_estado_comprobantes','ComprobanteController@actualizar_estado_comprobantes');
    Route::get('comprobantes/firmar','FacturacionController@firmar_xml');

