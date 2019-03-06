<?php

    Route::get('precio','PrecioController@inicio');
    Route::get('precio/buscar','PrecioController@buscar');
    Route::get('precio/form_asignar_precio','PrecioController@form_asignar_precio');
    Route::get('precio/add_input','PrecioController@add_input');
    Route::post('precio/store_precio','PrecioController@store_precio');
