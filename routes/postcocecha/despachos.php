<?php

Route::get('despachos', 'DespachosController@inicio');
Route::get('despachos/listar_resumen_pedidos', 'DespachosController@listar_resumen_pedidos');
Route::post('despachos/update_stock_empaquetado', 'DespachosController@update_stock_empaquetado');
Route::get('despachos/ver_envios', 'DespachosController@ver_envios');