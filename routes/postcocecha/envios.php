<?php

Route::get('clientes/add_envio','EnvioController@add_envio');
Route::get('clientes/add_form_envio','EnvioController@add_form_envio');
Route::post('clientes/store_envio','EnvioController@store_envio');
