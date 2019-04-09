<?php

Route::get('aerolinea','AerolineaController@index');
Route::get('aerolinea/list','AerolineaController@list_aerolinea');
Route::get('aerolinea/create','AerolineaController@crear_aerolinea');
Route::post('aerolinea/store','AerolineaController@store_aerolinea');
Route::post('aerolinea/update','AerolineaController@update_aerolinea');
Route::get('aerolinea/excel','AerolineaController@excel_aerolinea');

