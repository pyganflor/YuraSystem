<?php

Route::get('despachos', 'DespachosController@inicio');
Route::get('despachos/listar_resumen_pedidos', 'DespachosController@listar_resumen_pedidos');
Route::post('despachos/update_stock_empaquetado', 'DespachosController@update_stock_empaquetado');
Route::get('despachos/ver_envios', 'DespachosController@ver_envios');
Route::post('despachos/crear_despacho', 'DespachosController@crear_despacho');
Route::get('despachos/list_camiones_conductores', 'DespachosController@list_camiones_conductores');
Route::get('despachos/list_placa_camion', 'DespachosController@list_placa_camion');
Route::post('despachos/store_despacho', 'DespachosController@store_despacho');
Route::get('despachos/descargar_despacho/{n_despacho}', 'DespachosController@descargar_despacho');
Route::get('despachos/ver_despachos', 'DespachosController@ver_despachos');
Route::post('despachos/update_estado_despachos', 'DespachosController@update_estado_despachos');
Route::get('despachos/distribuir_despacho', 'DespachosController@distribuir_despacho');
Route::get('despachos/add_pedido_piezas', 'DespachosController@add_pedido_piezas');
Route::post('despachos/exportar_pedidos_despacho', 'DespachosController@exportar_pedidos_despacho');
Route::post('despachos/exportar_listado_pedidos_despacho', 'DespachosController@exportar_excel_listado_despacho');
Route::post('despachos/exportar_pedidos_despacho_cuarto_frio', 'DespachosController@exportar_pedidos_despacho_cuarto_frio');
