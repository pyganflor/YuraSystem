<?php

Route::get('monitoreo_ciclos', 'Proyecciones\MonitoreoController@inicio');
Route::get('monitoreo_ciclos/listar_ciclos', 'Proyecciones\MonitoreoController@listar_ciclos');
Route::post('monitoreo_ciclos/guardar_monitoreo', 'Proyecciones\MonitoreoController@guardar_monitoreo');
Route::post('monitoreo_ciclos/store_nuevos_ingresos', 'Proyecciones\MonitoreoController@store_nuevos_ingresos');