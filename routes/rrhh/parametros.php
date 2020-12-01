<?php

Route::get('parametros', 'RRHH\rrhhParametrosController@inicio');
Route::get('parametros/listar_parametro', 'RRHH\rrhhParametrosController@listar_parametro');
Route::post('parametros/store_banco', 'RRHH\rrhhParametrosController@store_banco');