<?php

Route::get('plantas_variedades', 'PlantaController@inicio');
Route::get('plantas_variedades/select_planta', 'PlantaController@select_planta');
Route::get('plantas_variedades/add_planta', 'PlantaController@add_planta');
Route::post('plantas_variedades/store_planta', 'PlantaController@store_planta');
Route::get('plantas_variedades/edit_planta', 'PlantaController@edit_planta');
Route::post('plantas_variedades/update_planta', 'PlantaController@update_planta');
Route::post('plantas_variedades/cambiar_estado_planta', 'PlantaController@cambiar_estado_planta');


Route::get('plantas_variedades/add_variedad', 'PlantaController@add_variedad');
Route::post('plantas_variedades/store_variedad', 'PlantaController@store_variedad');
Route::get('plantas_variedades/edit_variedad', 'PlantaController@edit_variedad');
Route::post('plantas_variedades/update_variedad', 'PlantaController@update_variedad');
Route::post('plantas_variedades/cambiar_estado_variedad', 'PlantaController@cambiar_estado_variedad');


Route::get('plantas_variedades/form_precio_variedad','PlantaController@form_precio_variedad');
Route::post('plantas_variedades/store_precio','PlantaController@store_precio');
Route::post('plantas_variedades/update_precio','PlantaController@update_precio');
Route::get('plantas_variedades/add_inptus_precio_variedad','PlantaController@add_inptus_precio_variedad');

Route::get('plantas_variedades/vincular_variedad_unitaria','PlantaController@vincular_variedad_unitaria');
Route::post('plantas_variedades/store_vinculo','PlantaController@store_vinculo');
Route::get('plantas_variedades/add_regalias','PlantaController@add_regalias');
Route::post('plantas_variedades/buscar_regalias','PlantaController@buscar_regalias');
Route::post('plantas_variedades/store_regalias','PlantaController@store_regalias');
