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
