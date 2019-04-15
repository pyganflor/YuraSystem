<?php

    Route::get('transportista','TransportistaController@incio');
    Route::get('transportista/list','TransportistaController@buscar');
    Route::get('transportista/add','TransportistaController@create_transportista');
    Route::post('transportista/store','TransportistaController@store_transportista');
    Route::post('transportista/update_estado','TransportistaController@update_estado');
    Route::get('transportista/list_camiones_conductores','TransportistaController@list_camiones_conductores');
    Route::get('transportista/add_camion','TransportistaController@add_camion');
    Route::post('transportista/store_camion','TransportistaController@store_camion');
    Route::post('transportista/update_estado_camion','TransportistaController@update_estado_camion');
    Route::get('transportista/add_conductor','TransportistaController@add_conductor');
    Route::post('transportista/store_conductor','TransportistaController@store_conductor');
    Route::post('transportista/update_estado_conductor','TransportistaController@update_estado_conductor');
