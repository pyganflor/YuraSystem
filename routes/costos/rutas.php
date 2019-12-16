<?php

Route::get('costos_gestion', 'Costos\CostosController@gestion');
Route::post('costos_gestion/store_area', 'Costos\CostosController@store_area');
Route::post('costos_gestion/update_area', 'Costos\CostosController@update_area');
Route::post('costos_gestion/store_actividad', 'Costos\CostosController@store_actividad');
Route::post('costos_gestion/update_actividad', 'Costos\CostosController@update_actividad');
Route::get('costos_gestion/importar_actividad', 'Costos\CostosController@importar_actividad');
Route::post('costos_gestion/importar_file_actividad', 'Costos\CostosController@importar_file_actividad');