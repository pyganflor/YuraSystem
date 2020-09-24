<?php
Route::get('clientes/listar_pedidos', 'PedidoController@listar_pedidos');
Route::get('clientes/ver_pedidos', 'PedidoController@ver_pedidos');
Route::get('clientes/add_pedido', 'PedidoController@add_pedido');
Route::post('clientes/store_pedidos', 'PedidoController@store_pedidos');
Route::get('clientes/inputs_pedidos', 'PedidoController@inputs_pedidos');
Route::get('clientes/inputs_pedidos_edit', 'PedidoController@inputs_pedidos_edit');
Route::post('clientes/store_especificacion_pedido', 'PedidoController@store_especificacion_pedido');
Route::get('clientes/actualizar_estado_pedido_detalle', 'PedidoController@actualizar_estado_pedido_detalle');
Route::post('clientes/cancelar_pedido', 'PedidoController@cancelar_pedido');
Route::get('clientes/opcion_pedido_fijo', 'PedidoController@opcion_pedido_fijo');
Route::get('clientes/add_fechas_pedido_fijo_personalizado', 'PedidoController@add_fechas_pedido_fijo_personalizado');
Route::get('clientes/buscar_saldos', 'YuraController@buscar_saldos');
Route::get('clientes/buscar_codigo_venture', 'PedidoController@buscar_codigo_venture');
Route::get('pedidos/facturar_pedido', 'PedidoController@facturar_pedido');
Route::get('pedidos/ver_factura_pedido/{id_pedido}', 'PedidoController@ver_factura_pedido');
Route::get('pedidos/desglose_pedido/{id_pedido}', 'PedidoController@desglose_pedido');
//URL PARA QUE LA FACTURACION FUNCIONE CON EL VENTURE
Route::get('pedidos/documento_pre_factura/{secuencial}/{cliente?}', 'ComprobanteController@ver_pre_factura_bd');
Route::post('pedidos/cambia_tipo_pedido','PedidoController@cambia_tipo_pedido');
Route::get('pedidos/modificar_comprobante','PedidoController@modificar_comprobante');
Route::post('pedidos/update_comprobante','PedidoController@update_comprobante');
