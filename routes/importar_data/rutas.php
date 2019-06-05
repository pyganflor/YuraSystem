<?php

Route::get('importar_data', 'ImportarDataController@inicio');
Route::post('importar_data/postcosecha', 'ImportarDataController@importar_cosecha');
Route::post('importar_data/venta', 'ImportarDataController@importar_venta');
Route::post('importar_data/area', 'ImportarDataController@importar_area');