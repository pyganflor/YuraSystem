<?php
Route::get('agrencias_carga','AgenciaCargaController@index');
Route::get('agrencias_carga/list_agencias','AgenciaCargaController@listAgenciasCarga')->name('list.agencias_carga');
Route::get('agrencias_carga/create_agencia','AgenciaCargaController@createAgenciaCarga')->name('create.agencias_carga');
Route::post('agrencias_carga/store_agencia','AgenciaCargaController@storeAgenciaCarga')->name('store.agencias_carga');
Route::post('agrencias_carga/update_estado_agencia','AgenciaCargaController@actualizarEstadoAgenciaCarga')->name('update_estado.agencias_carga');
Route::get('agrencias_carga/excel_agencias_carga','AgenciaCargaController@exportarAgenciasCarga')->name('excel.agencias_carga');