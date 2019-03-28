<?php

Route::get('pedidos', 'PedidoVentaController@listar_pedidos');
Route::get('pedidos/buscar', 'PedidoVentaController@buscar_pedidos');
Route::get('pedidos/cargar_especificaciones', 'PedidoVentaController@cargar_especificaciones');
Route::get('pedidos/ver_envio', 'EnvioController@ver_envios');
Route::get('pedidos/add_orden_semanal', 'PedidoVentaController@add_orden_semanal');
Route::post('pedidos/store_orden_semanal', 'OrdenSemanalController@store_orden_semanal');
Route::get('pedidos/buscar_agencia_carga', 'OrdenSemanalController@buscar_agencia_carga');
Route::get('pedidos/distribuir_orden_semanal', 'OrdenSemanalController@distribuir_orden_semanal');
Route::get('pedidos/orden_semanal/listar_especificaciones_x_cliente', 'OrdenSemanalController@listar_especificaciones_x_cliente');
Route::get('pedidos/editar_pedido', 'PedidoVentaController@editar_pedido');
Route::get('pedidos/editar_pedido_tinturado', 'OrdenSemanalController@editar_pedido_tinturado');
Route::post('pedidos/update_orden_tinturada', 'OrdenSemanalController@update_orden_tinturada');
Route::get('pedidos/add_pedido_personalizado', 'OrdenSemanalController@add_pedido_personalizado');
Route::post('pedidos/store_pedido_personalizado', 'OrdenSemanalController@store_pedido_personalizado');
Route::get('pedidos/listar_agencias_carga', 'OrdenSemanalController@listar_agencias_carga');
Route::post('pedidos/editar_coloracion', 'OrdenSemanalController@editar_coloracion');
Route::post('pedidos/editar_marcacion', 'OrdenSemanalController@editar_marcacion');
Route::post('pedidos/update_distribucion', 'OrdenSemanalController@update_distribucion');
Route::post('pedidos/update_pedido_orden_semanal', 'OrdenSemanalController@update_pedido_orden_semanal');
Route::get('pedidos/distribuir_marcaciones', 'OrdenSemanalController@distribuir_marcaciones');
Route::post('pedidos/calcular_distribucion', 'OrdenSemanalController@calcular_distribucion');
Route::get('pedidos/duplicar_especificacion', 'PedidoVentaController@duplicar_especificacion');
Route::get('pedidos/form_duplicar_pedido', 'PedidoVentaController@form_duplicar_pedido');
Route::post('pedidos/store_duplicar_pedido', 'PedidoVentaController@store_duplicar_pedido');
