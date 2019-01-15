<?php

Route::get('clasificacion_blanco', 'ClasificacionBlancoController@inicio');
Route::get('clasificacion_blanco/listar_resumen_pedidos', 'ClasificacionBlancoController@listar_resumen_pedidos');
Route::get('clasificacion_blanco/empaquetar', 'ClasificacionBlancoController@empaquetar');
Route::post('clasificacion_blanco/update_stock_empaquetado', 'ClasificacionBlancoController@update_stock_empaquetado');