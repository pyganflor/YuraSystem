<?php

Route::get('apertura', 'AperturaController@inicio');
Route::get('apertura/buscar_aperturas', 'AperturaController@buscar_aperturas');
Route::get('apertura/exportar_aperturas', 'AperturaController@exportar_aperturas');
Route::get('apertura/listar_pedidos', 'AperturaController@listar_pedidos');