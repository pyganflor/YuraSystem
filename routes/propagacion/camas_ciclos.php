<?php

/* ============================ CAMAS =========================== */
Route::get('camas_ciclos', 'Propagacion\CamasCiclosController@inicio');
Route::get('camas_ciclos/listar_camas', 'Propagacion\CamasCiclosController@listar_camas');
Route::get('camas_ciclos/add_cama', 'Propagacion\CamasCiclosController@add_cama');
Route::post('camas_ciclos/store_cama', 'Propagacion\CamasCiclosController@store_cama');
Route::get('camas_ciclos/edit_cama', 'Propagacion\CamasCiclosController@edit_cama');
Route::post('camas_ciclos/update_cama', 'Propagacion\CamasCiclosController@update_cama');
Route::post('camas_ciclos/eliminar_cama', 'Propagacion\CamasCiclosController@eliminar_cama');

/* ============================ CICLOS =========================== */
Route::get('camas_ciclos/listar_ciclos', 'Propagacion\CamasCiclosController@listar_ciclos');
Route::get('camas_ciclos/crear_ciclo', 'Propagacion\CamasCiclosController@crear_ciclo');
Route::post('camas_ciclos/store_ciclo', 'Propagacion\CamasCiclosController@store_ciclo');
Route::post('camas_ciclos/update_ciclo', 'Propagacion\CamasCiclosController@update_ciclo');
Route::get('camas_ciclos/edit_ciclo_contenedores', 'Propagacion\CamasCiclosController@edit_ciclo_contenedores');
Route::post('camas_ciclos/update_ciclo_contenedores', 'Propagacion\CamasCiclosController@update_ciclo_contenedores');
Route::post('camas_ciclos/terminar_ciclo', 'Propagacion\CamasCiclosController@terminar_ciclo');
Route::post('camas_ciclos/eliminar_ciclo', 'Propagacion\CamasCiclosController@eliminar_ciclo');
