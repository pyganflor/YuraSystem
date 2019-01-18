<?php

    Route::get('tipo_iva','TipoIvaController@index');
    Route::get('tipo_iva/buscar','TipoIvaController@buscar_tipo_iva');
    Route::get('tipo_iva/add_tipo_iva','TipoIvaController@add_tipo_iva');
    Route::post('tipo_iva/store_tipo_iva','TipoIvaController@store_tipo_iva');
    Route::post('tipo_iva/actualizar_estado_tipo_iva','TipoIvaController@actualizar_estado_tipo_iva');
