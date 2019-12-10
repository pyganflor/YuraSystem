<?php

Route::get('db_jobs', 'dbController@jobs');
Route::get('db_jobs/actualizar', 'dbController@actualizar_jobs');
Route::post('db_jobs/delete_job', 'dbController@delete_job');
Route::post('db_jobs/send_queue_job', 'dbController@send_queue_job');
Route::get('db_indicadores', 'dbController@indicadores');
Route::post('db_indicadores/store_indicador', 'dbController@store_indicador');
Route::post('db_indicadores/update_indicador', 'dbController@update_indicador');
Route::get('intervalo_indicador', 'dbController@intervaloIndicador');
Route::get('intervalo_indicador/add_intervalo', 'dbController@addIntervaloIndicador');
Route::get('intervalo_indicador/add_row_intervalo', 'dbController@addRowIntervaloIndicador');
Route::post('intervalo_indicador/store_intervalo', 'dbController@storeIntervaloIndicador');
