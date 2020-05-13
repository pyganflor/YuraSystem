<?php

Route::get('plantas_variedades', 'PlantaVariedadController@inicio');
Route::get('plantas_variedades/select_planta', 'PlantaVariedadController@select_planta');
Route::get('plantas_variedades/add_planta', 'PlantaVariedadController@add_planta');
Route::post('plantas_variedades/store_planta', 'PlantaVariedadController@store_planta');
Route::get('plantas_variedades/edit_planta', 'PlantaVariedadController@edit_planta');
Route::post('plantas_variedades/update_planta', 'PlantaVariedadController@update_planta');
Route::post('plantas_variedades/cambiar_estado_planta', 'PlantaVariedadController@cambiar_estado_planta');


Route::get('plantas_variedades/add_variedad', 'PlantaVariedadController@add_variedad');
Route::post('plantas_variedades/store_variedad', 'PlantaVariedadController@store_variedad');
Route::get('plantas_variedades/edit_variedad', 'PlantaVariedadController@edit_variedad');
Route::post('plantas_variedades/update_variedad', 'PlantaVariedadController@update_variedad');
Route::post('plantas_variedades/cambiar_estado_variedad', 'PlantaVariedadController@cambiar_estado_variedad');


Route::get('plantas_variedades/form_precio_variedad','PlantaVariedadController@form_precio_variedad');
Route::post('plantas_variedades/store_precio','PlantaVariedadController@store_precio');
Route::post('plantas_variedades/update_precio','PlantaVariedadController@update_precio');
Route::get('plantas_variedades/add_inptus_precio_variedad','PlantaVariedadController@add_inptus_precio_variedad');

Route::get('plantas_variedades/vincular_variedad_unitaria','PlantaVariedadController@vincular_variedad_unitaria');
Route::post('plantas_variedades/store_vinculo','PlantaVariedadController@store_vinculo');
Route::get('plantas_variedades/add_regalias','PlantaVariedadController@add_regalias');
Route::post('plantas_variedades/buscar_regalias','PlantaVariedadController@buscar_regalias');
Route::post('plantas_variedades/store_regalias','PlantaVariedadController@store_regalias');
