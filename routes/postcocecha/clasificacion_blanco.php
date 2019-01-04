<?php

Route::get('clasificacion_blanco', 'ClasificacionBlancoController@inicio');
Route::get('clasificacion_blanco/listar_resumen_pedidos', 'ClasificacionBlancoController@listar_resumen_pedidos');
Route::get('clasificacion_blanco/empaquetar', 'ClasificacionBlancoController@empaquetar');