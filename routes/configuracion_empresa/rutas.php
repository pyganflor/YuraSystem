<?php
Route::get('configuracion/admin_clasificacion_unitaria', 'ClasificacionesController@admin_clasificacion_unitaria');
Route::get('configuracion/admin_clasificacion_ramo', 'ClasificacionesController@admin_clasificacion_ramo');
Route::get('configuracion/seleccionar_unidad_medida', 'ClasificacionesController@seleccionar_unidad_medida');
Route::post('configuracion/update_unitaria', 'ClasificacionesController@update_unitaria');
Route::post('configuracion/update_ramo', 'ClasificacionesController@update_ramo');

Route::resource('configuracion', 'ConfiguracionEmpresaController');
Route::post('configuracion/actualizar_esatdo_clasificacion', 'ConfiguracionEmpresaController@actualizarEstado');
Route::post('configuracion/guaradar_detalle_empaque', 'ConfiguracionEmpresaController@guardarDetalleEmpaque')->name('store.detalle_empaque');
Route::post('configuracion/actualizar_esatdo_detalle_empaque', 'ConfiguracionEmpresaController@actualizarEstadoDetalleEmpaque')->name('update.detalle_empaque');

