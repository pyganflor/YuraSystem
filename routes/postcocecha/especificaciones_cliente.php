<?php

Route::get('clientes/admin_especificaciones', 'EspecificacionClienteController@admin_especificaciones');
Route::get('clientes/ver_especificacion', 'EspecificacionClienteController@ver_especificacion');
Route::get('clientes/add_especificacion', 'EspecificacionClienteController@add_especificacion');
Route::get('clientes/cargar_form_especificacion_empaque', 'EspecificacionClienteController@cargar_form_especificacion_empaque');
Route::get('clientes/cargar_form_detalle_especificacion_empaque', 'EspecificacionClienteController@cargar_form_detalle_especificacion_empaque');
Route::post('clientes/store_especificacion', 'EspecificacionClienteController@store_especificacion');
Route::get('clientes/listar_especificaciones', 'EspecificacionClienteController@listar_especificaciones');
Route::get('clientes/ver_especificaciones', 'EspecificacionClienteController@ver_especificaciones');
Route::post('clientes/update_especificaciones', 'EspecificacionClienteController@update_especificaciones');
Route::post('clientes/asignar_especificacion', 'EspecificacionClienteController@asignar_especificacion');
Route::get('clientes/obtener_calsificacion_ramos', 'EspecificacionClienteController@obtener_calsificacion_ramos');

