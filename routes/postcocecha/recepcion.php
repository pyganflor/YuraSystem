<?php

Route::get('recepcion', 'RecepcionController@inicio');
Route::get('recepcion/buscar_recepciones', 'RecepcionController@buscar_recepciones');
Route::get('recepcion/add_desglose', 'RecepcionController@add_desglose');
Route::get('recepcion/add_recepcion', 'RecepcionController@add_recepcion');
Route::post('recepcion/store_recepcion', 'RecepcionController@store_recepcion');
Route::get('recepcion/ver_recepcion', 'RecepcionController@ver_recepcion');
Route::post('recepcion/update_recepcion', 'RecepcionController@update_recepcion');
Route::get('recepcion/exportar_recepciones', 'RecepcionController@exportar_recepciones');
Route::get('recepcion/buscarCosechaByFecha', 'RecepcionController@getIdCosechaByFecha');
Route::post('recepcion/store_cosecha', 'RecepcionController@store_cosecha');
Route::get('recepcion/ver_rendimiento', 'RecepcionController@ver_rendimiento');
Route::post('recepcion/buscar_cosecha', 'RecepcionController@buscar_cosecha');
Route::get('recepcion/editar_desglose_recepcion', 'RecepcionController@editar_desglose_recepcion');
Route::post('recepcion/update_desglose', 'RecepcionController@update_desglose');
Route::post('recepcion/delete_desglose', 'RecepcionController@delete_desglose');
Route::post('recepcion/store_desglose', 'RecepcionController@store_desglose');
Route::post('recepcion/select_modulo_recepcion', 'RecepcionController@select_modulo_recepcion');