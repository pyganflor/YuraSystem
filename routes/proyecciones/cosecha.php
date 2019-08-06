<?php

Route::get('proy_cosecha', 'Proyecciones\proyCosechaController@inicio');
Route::get('proy_cosecha/listar_proyecciones', 'Proyecciones\proyCosechaController@listar_proyecciones');
Route::get('proy_cosecha/select_celda', 'Proyecciones\proyCosechaController@select_celda');
Route::post('proy_cosecha/store_proyeccion', 'Proyecciones\proyCosechaController@store_proyeccion');