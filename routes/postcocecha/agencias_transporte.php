<?php

Route::get('agencias_transporte','AgenciaTransporteController@index');
Route::get('agencias_transporte/list','AgenciaTransporteController@list_agencias_transporte');
Route::get('agencias_transporte/create','AgenciaTransporteController@crear_agencias_transporte');
Route::post('agencias_transporte/store','AgenciaTransporteController@store_agencias_transporte');
Route::post('agencias_transporte/update','AgenciaTransporteController@update_agencias_transporte');
Route::get('agencias_transporte/excel','AgenciaTransporteController@excel_agencias_transporte');
