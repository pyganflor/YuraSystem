<?php
 Route::get('emision_comprobantes','EmisionComprobanteController@index');
 Route::get('emision_comprobantes/add_punto_emision','EmisionComprobanteController@add_punto_emision');
 Route::post('emision_comprobantes/store_punto_emision','EmisionComprobanteController@store_punto_emision');
