<?php

Route::get('cuarto_frio', 'CuartoFrioController@inicio');
Route::post('cuarto_frio/add_inventario', 'CuartoFrioController@add_inventario');
Route::post('cuarto_frio/delete_dia', 'CuartoFrioController@delete_dia');
Route::post('cuarto_frio/save_dia', 'CuartoFrioController@save_dia');