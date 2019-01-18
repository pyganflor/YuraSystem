<?php

Route::get('despachos', 'DespachosController@inicio');
Route::get('despachos/listar_resumen_pedidos', 'DespachosController@listar_resumen_pedidos');
Route::get('despachos/empaquetar', 'DespachosController@empaquetar');
Route::post('despachos/update_stock_empaquetado', 'DespachosController@update_stock_empaquetado');