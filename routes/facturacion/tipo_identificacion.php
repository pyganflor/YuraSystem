<?php

    Route::get('tipo_identificacion','TipoIdentificacionController@index');
    Route::get('tipo_identificacion/buscar','TipoIdentificacionController@buscar_tipo_identificacion');
    Route::get('tipo_identificacion/add_tipo_identificacion','TipoIdentificacionController@add_tipo_identificacion');
    Route::post('tipo_identificacion/store_tipo_identificacion','TipoIdentificacionController@store_tipo_identificacion');
    Route::post('tipo_identificacion/actualizar_estado_tipo_identificacion','TipoIdentificacionController@actualizar_estado_tipo_identificacion');

