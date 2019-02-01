<?php

    Route::get('tipo_impuesto','TipoImpuestoController@index');
    Route::get('tipo_impuesto/buscar','TipoImpuestoController@buscar_tipo_impuesto');
    Route::get('tipo_impuesto/add_tipo_impuesto','TipoImpuestoController@add_tipo_impuesto');
    Route::get('tipo_impuesto/get_tipo_impuesto','TipoImpuestoController@get_tipo_impuestos');
    Route::post('tipo_impuesto/store_tipo_impuesto','TipoImpuestoController@store_tipo_impuesto');
    Route::post('tipo_impuesto/actualizar_estado_tipo_impuesto','TipoImpuestoController@actualizar_estado_tipo_impuesto');
