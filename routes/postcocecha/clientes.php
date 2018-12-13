<?php

Route::get('clientes', 'ClienteController@inicio');
Route::get('clientes/buscar', 'ClienteController@buscar_clientes');
Route::post('clientes/eliminar', 'ClienteController@eliminar_clientes');
Route::get('clientes/add', 'ClienteController@add_clientes');
Route::post('clientes/store', 'ClienteController@store_clientes');
Route::post('clientes/store_agencia_carga', 'ClienteController@store_agencia_carga');
Route::get('clientes/ver_agencias_carga', 'ClienteController@ver_agencia_carga');
Route::post('clientes/update_cliente', 'ClienteController@update_cliente');
Route::post('clientes/update_estado_cliente_agencia_carga', 'ClienteController@update_estado_cliente_agencia_carga');
Route::get('clientes/exportar', 'ClienteController@exportar_clientes');
Route::get('clientes/ver_detalles_cliente', 'ClienteController@detales_cliente');
Route::get('clientes/ver_contactos_clientes', 'ClienteController@ver_contactos_clientes');
Route::post('clientes/store_contactos', 'ClienteController@store_contactos');
Route::post('clientes/actualizar_estado_contacto', 'ClienteController@actualizar_estado_contacto');

include 'especificaciones.php';
include 'pedidos.php';
