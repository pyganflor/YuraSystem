<?php

Route::get('clientes/admin_especificaciones', 'EspecificacionController@admin_especificaciones');
Route::get('clientes/ver_especificacion', 'EspecificacionController@ver_especificacion');
Route::get('clientes/add_especificacion', 'EspecificacionController@add_especificacion');
Route::get('clientes/cargar_form_especificacion_empaque', 'EspecificacionController@cargar_form_especificacion_empaque');
Route::get('clientes/cargar_form_detalle_especificacion_empaque', 'EspecificacionController@cargar_form_detalle_especificacion_empaque');
Route::post('clientes/store_especificacion', 'EspecificacionController@store_especificacion');
Route::get('clientes/listar_especificaciones', 'EspecificacionController@listar_especificaciones');
Route::get('clientes/ver_especificaciones', 'EspecificacionController@ver_especificaciones');
Route::post('clientes/update_especificaciones', 'EspecificacionController@update_especificaciones');
Route::post('clientes/asignar_especificacion', 'EspecificacionController@asignar_especificacion');
Route::get('clientes/obtener_calsificacion_ramos', 'EspecificacionController@obtener_calsificacion_ramos');

