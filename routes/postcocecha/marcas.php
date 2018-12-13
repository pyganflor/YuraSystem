<?php

Route::get('marcas','MarcaController@inicio');
Route::get('marcas/buscar','MarcaController@buscar_marcas');
Route::get('marcas/add','MarcaController@add_marcas');
Route::post('marcas/store','MarcaController@store_marcas');
