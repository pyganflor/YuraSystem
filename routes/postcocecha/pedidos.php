<?php
Route::get('clientes/listar_pedidos', 'PedidoController@listar_pedidos');
Route::get('clientes/ver_pedidos', 'PedidoController@ver_pedidos');
Route::get('clientes/add_pedido', 'PedidoController@add_pedido');
Route::post('clientes/store_pedidos', 'PedidoController@store_pedidos');
Route::get('clientes/inputs_pedidos', 'PedidoController@inputs_pedidos');
Route::get('clientes/inputs_pedidos_edit', 'PedidoController@inputs_pedidos_edit');
Route::get('clientes/actualizar_estado_pedido_detalle', 'PedidoController@actualizar_estado_pedido_detalle');
Route::post('clientes/cancelar_pedido', 'PedidoController@cancelar_pedido');
Route::get('clientes/opcion_pedido_fijo', 'PedidoController@opcion_pedido_fijo');
Route::get('clientes/add_fechas_pedido_fijo_personalizado', 'PedidoController@add_fechas_pedido_fijo_personalizado');
Route::get('clientes/buscar_saldos', 'YuraController@buscar_saldos');
Route::get('pedidos/crear_packing_list/{id_pedido}', 'PedidoController@crear_packing_list');



