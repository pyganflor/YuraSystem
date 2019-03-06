<?php

    Route::get('precio','PrecioController@inicio');
    Route::get('precio/buscar','PrecioController@buscar');
    Route::get('precio/form_asignar_precio_especificacion_cliente','PrecioController@form_asignar_precio_especificacion_cliente');
    Route::get('precio/form_asignar_precio_cliente_especificacion','PrecioController@form_asignar_precio_cliente_especificacion');
    Route::get('precio/add_input','PrecioController@add_input');
    Route::post('precio/store_precio_especificacio_cliente','PrecioController@store_precio_especificacio_cliente');
    Route::post('precio/store_precio_cliente_especificacion','PrecioController@store_precio_cliente_especificacion');
