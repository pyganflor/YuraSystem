<?php

    Route::get('tipo_comprobante','TipoComprobanteController@index');
    Route::get('tipo_comprobante/buscar','TipoComprobanteController@buscar_tipo_comprobantes');
    Route::get('tipo_comprobante/add_tipo_comprobantes','TipoComprobanteController@add_tipo_comprobantes');
    Route::post('tipo_comprobante/store_tipo_comprobantes','TipoComprobanteController@store_tipo_comprobantes');
    Route::post('tipo_comprobante/actualizar_estado_tipo_comprobantes','TipoComprobanteController@actualizar_estado_tipo_comprobantes');

