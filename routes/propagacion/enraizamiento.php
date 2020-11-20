<?php

Route::get('enraizamiento', 'Propagacion\EnraizamientoController@inicio');
Route::post('enraizamiento/store_enraizamiento', 'Propagacion\EnraizamientoController@store_enraizamiento');
Route::post('enraizamiento/buscar_enraizamiento_semanal', 'Propagacion\EnraizamientoController@buscar_enraizamiento_semanal');