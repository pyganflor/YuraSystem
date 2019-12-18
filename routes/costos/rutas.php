<?php

Route::get('costos_gestion', 'Costos\CostosController@gestion');
Route::post('costos_gestion/store_area', 'Costos\CostosController@store_area');
Route::post('costos_gestion/update_area', 'Costos\CostosController@update_area');
Route::post('costos_gestion/store_actividad', 'Costos\CostosController@store_actividad');
Route::post('costos_gestion/update_actividad', 'Costos\CostosController@update_actividad');
Route::post('costos_gestion/delete_actividad', 'Costos\CostosController@delete_actividad');
Route::get('costos_gestion/importar_actividad', 'Costos\CostosController@importar_actividad');
Route::post('costos_gestion/importar_file_actividad', 'Costos\CostosController@importar_file_actividad');
Route::post('costos_gestion/store_producto', 'Costos\CostosController@store_producto');
Route::post('costos_gestion/update_producto', 'Costos\CostosController@update_producto');
Route::post('costos_gestion/delete_producto', 'Costos\CostosController@delete_producto');
Route::get('costos_gestion/importar_producto', 'Costos\CostosController@importar_producto');
Route::post('costos_gestion/importar_file_producto', 'Costos\CostosController@importar_file_producto');
Route::get('costos_gestion/vincular_actividad_producto', 'Costos\CostosController@vincular_actividad_producto');
Route::post('costos_gestion/store_actividad_producto', 'Costos\CostosController@store_actividad_producto');
Route::post('costos_gestion/importar_file_act_producto', 'Costos\CostosController@importar_file_act_producto');
