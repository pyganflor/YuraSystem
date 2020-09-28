<?php

Route::get('propag_config', 'Propagacion\propagConfiguracionesController@inicio');
Route::get('propag_config/listar_contenedores', 'Propagacion\propagConfiguracionesController@listar_contenedores');
Route::get('propag_config/add_contenedor', 'Propagacion\propagConfiguracionesController@add_contenedor');
Route::post('propag_config/store_contenedor', 'Propagacion\propagConfiguracionesController@store_contenedor');
Route::get('propag_config/edit_contenedor', 'Propagacion\propagConfiguracionesController@edit_contenedor');
Route::post('propag_config/update_contenedor', 'Propagacion\propagConfiguracionesController@update_contenedor');
Route::post('propag_config/eliminar_contenedor', 'Propagacion\propagConfiguracionesController@eliminar_contenedor');