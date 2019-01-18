<?php

Route::get('pedidos', 'PedidoVentaController@listar_pedidos');
Route::get('pedidos/buscar', 'PedidoVentaController@buscar_pedidos');
Route::get('pedidos/cargar_especificaciones', 'PedidoVentaController@cargar_especificaciones');
Route::get('pedidos/add_orden_semanal', 'PedidoVentaController@add_orden_semanal');
