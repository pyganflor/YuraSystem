<?php

Route::get('proy_cosecha', 'Proyecciones\proyCosechaController@inicio');
Route::get('proy_cosecha/listar_proyecciones', 'Proyecciones\proyCosechaController@listar_proyecciones');
Route::get('proy_cosecha/select_celda', 'Proyecciones\proyCosechaController@select_celda');
Route::get('proy_cosecha/load_celda', 'Proyecciones\proyCosechaController@load_celda');
Route::post('proy_cosecha/store_proyeccion', 'Proyecciones\proyCosechaController@store_proyeccion');
Route::post('proy_cosecha/update_proyeccion', 'Proyecciones\proyCosechaController@update_proyeccion');
Route::post('proy_cosecha/update_ciclo', 'Proyecciones\proyCosechaController@update_ciclo');
Route::post('proy_cosecha/restaurar_proyeccion', 'Proyecciones\proyCosechaController@restaurar_proyeccion');
