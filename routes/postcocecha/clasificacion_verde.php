<?php

Route::get('clasificacion_verde', 'ClasificacionVerdeController@inicio');
Route::get('clasificacion_verde/add_verde', 'ClasificacionVerdeController@add_verde');
Route::get('clasificacion_verde/buscar_clasificaciones', 'ClasificacionVerdeController@buscar_clasificaciones');
Route::get('clasificacion_verde/ver_clasificacion', 'ClasificacionVerdeController@ver_clasificacion');
Route::get('clasificacion_verde/detalles_estandar', 'ClasificacionVerdeController@detalles_estandar');
Route::get('clasificacion_verde/buscar_detalles_estandar', 'ClasificacionVerdeController@buscar_detalles_estandar');
Route::get('clasificacion_verde/clasificaciones_x_fecha', 'ClasificacionVerdeController@clasificaciones_x_fecha');
Route::get('clasificacion_verde/detalles_x_variedad', 'ClasificacionVerdeController@detalles_x_variedad');
Route::get('clasificacion_verde/detalles_reales', 'ClasificacionVerdeController@detalles_reales');
Route::get('clasificacion_verde/buscar_detalles_reales', 'ClasificacionVerdeController@buscar_detalles_reales');
Route::get('clasificacion_verde/buscar_recepciones_byFecha', 'ClasificacionVerdeController@buscar_recepciones_byFecha');
Route::get('clasificacion_verde/ver_lotes', 'ClasificacionVerdeController@ver_lotes');
Route::post('clasificacion_verde/destinar_a', 'ClasificacionVerdeController@destinar_a');
Route::get('clasificacion_verde/destinar_lotes', 'ClasificacionVerdeController@destinar_lotes');
Route::get('clasificacion_verde/destinar_lotes_form', 'ClasificacionVerdeController@destinar_lotes_form');
Route::post('clasificacion_verde/store_lote_re', 'ClasificacionVerdeController@store_lote_re');
Route::get('clasificacion_verde/add/cargar_tabla_variedad', 'ClasificacionVerdeController@cargar_tabla_variedad');
Route::post('clasificacion_verde/store', 'ClasificacionVerdeController@store');
Route::post('clasificacion_verde/store_detalles', 'ClasificacionVerdeController@store_detalles');
Route::get('clasificacion_verde/calcular_stock', 'ClasificacionVerdeController@calcular_stock');
Route::post('clasificacion_verde/terminar', 'ClasificacionVerdeController@terminar');
Route::post('clasificacion_verde/store_personal', 'ClasificacionVerdeController@store_personal');
Route::get('clasificacion_verde/ver_rendimiento', 'ClasificacionVerdeController@ver_rendimiento');
Route::get('clasificacion_verde/rendimiento_mesas', 'ClasificacionVerdeController@rendimiento_mesas');
Route::post('clasificacion_verde/store_lote_re_from', 'ClasificacionVerdeController@store_lote_re_from');

/* ------------------------------------- mobil ------------------------------------------------------ */
Route::get('clasificacion_verde/add_verde_mobil', 'ClasificacionVerdeController@add_verde_mobil');
Route::post('clasificacion_verde/store_form_verde', 'ClasificacionVerdeController@store_form_verde');
Route::get('clasificacion_verde/select_fecha_recepciones', 'ClasificacionVerdeController@select_fecha_recepciones');
Route::get('clasificacion_verde/construir_tabla', 'ClasificacionVerdeController@construir_tabla');
Route::post('clasificacion_verde/store_detalle_verde', 'ClasificacionVerdeController@store_detalle_verde');