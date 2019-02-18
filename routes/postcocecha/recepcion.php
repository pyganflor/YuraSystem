<?php

Route::get('recepcion', 'RecepcionController@inicio');
Route::get('recepcion/buscar_recepciones', 'RecepcionController@buscar_recepciones');
Route::get('recepcion/add_recepcion', 'RecepcionController@add_recepcion');
Route::post('recepcion/store_recepcion', 'RecepcionController@store_recepcion');
Route::get('recepcion/ver_recepcion', 'RecepcionController@ver_recepcion');
Route::post('recepcion/update_recepcion', 'RecepcionController@update_recepcion');
Route::get('recepcion/exportar_recepciones', 'RecepcionController@exportar_recepciones');
Route::get('recepcion/buscarCosechaByFecha', 'RecepcionController@getIdCosechaByFecha');
Route::post('recepcion/store_cosecha', 'RecepcionController@store_cosecha');
Route::get('recepcion/ver_rendimiento', 'RecepcionController@ver_rendimiento');
Route::post('recepcion/buscar_cosecha', 'RecepcionController@buscar_cosecha');