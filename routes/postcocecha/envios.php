<?php

use Illuminate\Http\Request;
use Carbon\Carbon;
use yura\Modelos\ConfiguracionEmpresa;

Route::get('clientes/add_envio','EnvioController@add_envio');
Route::get('clientes/add_form_envio','EnvioController@add_form_envio');
Route::post('clientes/store_envio','EnvioController@store_envio');
Route::get('envio','EnvioController@ver_envio');
Route::get('envio/buscar','EnvioController@buscar_envio');
Route::get('envio/exportar','EnvioController@generar_excel_envios');
Route::get('envio/editar_envio','EnvioController@editar_envio');
Route::post('envio/actualizar_envio','EnvioController@actualizar_envio');
Route::get('envio/buscar_codigo_dae','EnvioController@buscar_codigo_dae');
Route::get('envio/factura_cliente_tercero', 'EnvioController@factura_cliente_tercero');
Route::post('envio/store_datos_factura_cliente_tercero', 'EnvioController@store_datos_factura_cliente_tercero');
Route::post('envio/delete_datos_factura_cliente_tercero', 'EnvioController@delete_datos_factura_cliente_tercero');
Route::get('envio/agregar_correo', 'EnvioController@agregar_correo');

