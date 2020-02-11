<?php

Route::get('tbl_ventas', 'CRM\tblVentasController@inicio');
Route::get('tbl_ventas/filtrar_tablas', 'CRM\tblVentasController@filtrar_tablas');
Route::get('tbl_ventas/exportar_tabla', 'CRM\tblVentasController@exportar_tabla');
Route::get('tbl_ventas/navegar_tabla', 'CRM\tblVentasController@navegar_tabla');

/* ----------------------------------------------------------------------------- */
Route::get('tbl_ventas/pedidos_cliente', 'PedidoController@pedidos_cliente');