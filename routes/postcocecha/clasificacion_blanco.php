<?php

Route::get('clasificacion_blanco', 'ClasificacionBlancoController@inicio');
Route::get('clasificacion_blanco/listar_clasificacion_blanco', 'ClasificacionBlancoController@listar_clasificacion_blanco');
Route::post('clasificacion_blanco/confirmar_pedidos', 'ClasificacionBlancoController@confirmar_pedidos');
Route::post('clasificacion_blanco/store_armar', 'ClasificacionBlancoController@store_armar');
Route::get('clasificacion_blanco/maduracion', 'ClasificacionBlancoController@maduracion');
Route::post('clasificacion_blanco/update_inventario', 'ClasificacionBlancoController@update_inventario');
Route::post('clasificacion_blanco/update_stock_empaquetado', 'ClasificacionBlancoController@update_stock_empaquetado');
Route::post('clasificacion_blanco/store_blanco', 'ClasificacionBlancoController@store_blanco');
Route::get('clasificacion_blanco/ver_rendimiento', 'ClasificacionBlancoController@ver_rendimiento');
Route::get('clasificacion_blanco/rendimiento_mesas', 'ClasificacionBlancoController@rendimiento_mesas');