<?php

Route::get('codigo_dae','CodigoDaeController@inicio');
Route::get('codigo_dae/buscar','CodigoDaeController@buscar_codigo_dae');
Route::get('codigo_dae/seleccionar_pais','CodigoDaeController@seleccionar_pais');
Route::get('codigo_dae/busqueda_pais_modal','CodigoDaeController@busqueda_pais_modal');
Route::get('codigo_dae/pais','CodigoDaeController@pais');
Route::get('codigo_dae/add','CodigoDaeController@add_codigo_dae');
Route::post('codigo_dae/exportar_paises','CodigoDaeController@exportar_paises');
Route::get('codigo_dae/form_file_codigo_dae','CodigoDaeController@form_file_codigo_dae');
Route::post('codigo_dae/importar_codigo_dae','CodigoDaeController@importar_codigo_dae');
Route::post('codigo_dae/descactivar_codigo','CodigoDaeController@descactivar_codigo');

