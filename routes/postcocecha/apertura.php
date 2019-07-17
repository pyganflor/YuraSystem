<?php

Route::get('apertura', 'AperturaController@inicio');
Route::get('apertura/buscar_aperturas', 'AperturaController@buscar_aperturas');
Route::get('apertura/exportar_aperturas', 'AperturaController@exportar_aperturas');
Route::get('apertura/listar_pedidos', 'AperturaController@listar_pedidos');
Route::post('apertura/sacar', 'AperturaController@sacar');
Route::get('apertura/mover_fecha', 'AperturaController@mover_fecha');
Route::post('apertura/store_mover_fecha', 'AperturaController@store_mover_fecha');