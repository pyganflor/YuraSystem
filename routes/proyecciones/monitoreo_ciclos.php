<?php

Route::get('monitoreo_ciclos', 'MonitoreoController@inicio');
Route::get('monitoreo_ciclos/listar_ciclos', 'MonitoreoController@listar_ciclos');
Route::post('monitoreo_ciclos/guardar_monitoreo', 'MonitoreoController@guardar_monitoreo');