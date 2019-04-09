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
Route::get('envio/buscar_codigo_dae',function (Request $request){
   $dae = getCodigoDae($request->codigo_pais,Carbon::parse($request->fecha_envio)->format('m'),Carbon::parse($request->fecha_envio)->format('Y'));
   return response()->json([
       'codigo_dae' => isset($dae->codigo_dae) ? $dae->codigo_dae : "",
        'codigo_empresa' => ConfiguracionEmpresa::select('codigo_pais')->first()->codigo_pais
   ]);
});
Route::get('envio/factura_cliente_tercero', 'EnvioController@factura_cliente_tercero');
Route::post('envio/store_datos_factura_cliente_tercero', 'EnvioController@store_datos_factura_cliente_tercero');
Route::post('envio/delete_datos_factura_cliente_tercero', 'EnvioController@delete_datos_factura_cliente_tercero');

