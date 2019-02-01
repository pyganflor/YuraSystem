<?php

Route::get('pedidos', 'PedidoVentaController@listar_pedidos');
Route::get('pedidos/buscar', 'PedidoVentaController@buscar_pedidos');
Route::get('pedidos/cargar_especificaciones', 'PedidoVentaController@cargar_especificaciones');
Route::get('pedidos/ver_envio', 'EnvioController@ver_envios');
Route::get('pedidos/add_orden_semanal', 'PedidoVentaController@add_orden_semanal');
Route::post('pedidos/store_orden_semanal', 'OrdenSemanalController@store_orden_semanal');
Route::get('pedidos/buscar_agencia_carga', 'OrdenSemanalController@buscar_agencia_carga');
Route::get('pedidos/distribuir_orden_semanal', 'OrdenSemanalController@distribuir_orden_semanal');
Route::get('pedidos/add_pedido_personalizado', 'OrdenSemanalController@add_pedido_personalizado');
Route::post('pedidos/store_pedido_personalizado', 'OrdenSemanalController@store_pedido_personalizado');
Route::get('pedidos/listar_agencias_carga', 'OrdenSemanalController@listar_agencias_carga');