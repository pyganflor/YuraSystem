<?php

Route::get('gestion_mano_obra', 'Costos\CostosController@gestion_mano_obra');
Route::post('gestion_mano_obra/store_area', 'Costos\CostosController@store_area');
Route::post('gestion_mano_obra/update_area', 'Costos\CostosController@update_area');
Route::post('gestion_mano_obra/store_actividad', 'Costos\CostosController@store_actividad');
Route::post('gestion_mano_obra/update_actividad', 'Costos\CostosController@update_actividad');
Route::post('gestion_mano_obra/delete_actividad', 'Costos\CostosController@delete_actividad');
Route::get('gestion_mano_obra/importar_actividad', 'Costos\CostosController@importar_actividad');
Route::post('gestion_mano_obra/importar_file_actividad', 'Costos\CostosController@importar_file_actividad');
Route::post('gestion_mano_obra/store_producto', 'Costos\CostosController@store_producto');
Route::post('gestion_mano_obra/update_producto', 'Costos\CostosController@update_producto');
Route::post('gestion_mano_obra/delete_producto', 'Costos\CostosController@delete_producto');
Route::get('gestion_mano_obra/importar_producto', 'Costos\CostosController@importar_producto');
Route::post('gestion_mano_obra/importar_file_producto', 'Costos\CostosController@importar_file_producto');
Route::get('gestion_mano_obra/vincular_actividad_producto', 'Costos\CostosController@vincular_actividad_producto');
Route::post('gestion_mano_obra/store_actividad_producto', 'Costos\CostosController@store_actividad_producto');
Route::post('gestion_mano_obra/importar_file_act_producto', 'Costos\CostosController@importar_file_act_producto');