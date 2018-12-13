<?php

Route::get('lotes', 'LotesController@inicio');
Route::get('lotes/buscar_lotes', 'LotesController@buscar_lotes');
Route::get('lotes/ver_lote', 'LotesController@ver_lote');
Route::get('lotes/etapas', 'LotesController@etapas');
Route::post('lotes/store_etapa', 'LotesController@store_etapa');