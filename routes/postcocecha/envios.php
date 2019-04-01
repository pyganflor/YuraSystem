<?php

use Illuminate\Http\Request;

Route::get('clientes/add_envio','EnvioController@add_envio');
Route::get('clientes/add_form_envio','EnvioController@add_form_envio');
Route::post('clientes/store_envio','EnvioController@store_envio');
Route::get('envio','EnvioController@ver_envio');
Route::get('envio/buscar','EnvioController@buscar_envio');
Route::get('envio/exportar','EnvioController@generar_excel_envios');
Route::get('envio/editar_envio','EnvioController@editar_envio');
Route::post('envio/actualizar_envio','EnvioController@actualizar_envio');
Route::get('envio/buscar_codigo_dae',function (Request $request){
   return getCodigoDae($request->codigo_pais,\Carbon\Carbon::parse($request->fecha_envio)->format('m'),\Carbon\Carbon::parse($request->fecha_envio)->format('Y'));
});
