<?php

Route::get('documento/add_documento', 'DocumentoController@add_documento');
Route::post('documento/store_documento', 'DocumentoController@store_documento');
Route::get('documento/load_input', 'DocumentoController@load_input');
Route::get('documento/ver_documentos', 'DocumentoController@ver_documentos');
Route::post('documento/update_documento', 'DocumentoController@update_documento');
Route::post('documento/delete_documento', 'DocumentoController@delete_documento');