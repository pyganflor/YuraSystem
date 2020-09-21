<?php

Route::get('costos_importar', 'Costos\CostosController@costos_importar');
Route::post('costos_importar/importar_file_costos', 'Costos\CostosController@importar_file_costos');
Route::post('costos_importar/importar_file_costos_details', 'Costos\CostosController@importar_file_costos_details');
