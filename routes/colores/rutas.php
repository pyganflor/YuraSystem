<?php

Route::get('admin_colores', 'ColorController@inicio');
Route::post('admin_colores/store_color', 'ColorController@store_color');
Route::post('admin_colores/update_color', 'ColorController@update_color');