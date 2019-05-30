<?php

Route::get('sectores_modulos', 'SectorController@inicio');

Route::get('sectores_modulos/select_sector', 'SectorController@select_sector');
Route::get('sectores_modulos/listar_modulos_x_sector', 'SectorController@listar_modulos_x_sector');
Route::get('sectores_modulos/select_modulo', 'SectorController@select_modulo');

Route::get('sectores_modulos/add_sector', 'SectorController@add_sector');
Route::post('sectores_modulos/store_sector', 'SectorController@store_sector');
Route::get('sectores_modulos/add_modulo', 'SectorController@add_modulo');
Route::post('sectores_modulos/store_modulo', 'SectorController@store_modulo');
Route::get('sectores_modulos/add_lote', 'SectorController@add_lote');
Route::post('sectores_modulos/store_lote', 'SectorController@store_lote');

Route::get('sectores_modulos/edit_sector', 'SectorController@edit_sector');
Route::post('sectores_modulos/update_sector', 'SectorController@update_sector');
Route::post('sectores_modulos/cambiar_estado_sector', 'SectorController@cambiar_estado_sector');
Route::get('sectores_modulos/edit_modulo', 'SectorController@edit_modulo');
Route::post('sectores_modulos/update_modulo', 'SectorController@update_modulo');
Route::post('sectores_modulos/cambiar_estado_modulo', 'SectorController@cambiar_estado_modulo');
Route::get('sectores_modulos/edit_lote', 'SectorController@edit_lote');
Route::post('sectores_modulos/update_lote', 'SectorController@update_lote');
Route::post('sectores_modulos/cambiar_estado_lote', 'SectorController@cambiar_estado_lote');

/* =================== CICLOS ==================*/
Route::get('sectores_modulos/listar_ciclos', 'CiclosController@listar_ciclos');
Route::get('sectores_modulos/ver_ciclos', 'CiclosController@ver_ciclos');
Route::post('sectores_modulos/store_ciclo', 'CiclosController@store_ciclo');
Route::post('sectores_modulos/terminar_ciclo', 'CiclosController@terminar_ciclo');
Route::post('sectores_modulos/update_ciclo', 'CiclosController@update_ciclo');
Route::post('sectores_modulos/eliminar_ciclo', 'CiclosController@eliminar_ciclo');