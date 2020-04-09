<?php

Route::get('temperaturas', 'Proyecciones\proyTemperaturaController@inicio');
Route::get('temperaturas/listar_ciclos', 'Proyecciones\proyTemperaturaController@listar_ciclos');
Route::get('temperaturas/add_temperatura', 'Proyecciones\proyTemperaturaController@add_temperatura');
Route::post('temperaturas/store_temperatura', 'Proyecciones\proyTemperaturaController@store_temperatura');
Route::post('temperaturas/buscar_temperatura', 'Proyecciones\proyTemperaturaController@buscar_temperatura');
