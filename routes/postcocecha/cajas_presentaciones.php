<?php

    Route::get('caja_presentacion','CajasPresentacionesController@inicio');
    Route::get('caja_presentacion/buscar_empaque','CajasPresentacionesController@buscar_empaque');
    Route::get('caja_presentacion/add_empaque','CajasPresentacionesController@form_add_empaque');
    Route::get('caja_presentacion/store_empaque','CajasPresentacionesController@store_empaque');
    Route::get('caja_presentacion/update_estado_empaque','CajasPresentacionesController@update_estado_empaque');
    Route::post('caja_presentacion/exportar_detalle_empaque','CajasPresentacionesController@exportar_detalle_empaque');
    Route::get('caja_presentacion/form_file_detalle_empaque','CajasPresentacionesController@form_file_detalle_empaque');
    Route::post('caja_presentacion/importar_detalle_empaque','CajasPresentacionesController@importar_detalle_empaque');
    Route::get('caja_presentacion/detalle_empaque','CajasPresentacionesController@detalle_empaque');
    Route::get('caja_presentacion/store_detalle_empaque','CajasPresentacionesController@store_detalle_empaque');
    Route::get('caja_presentacion/delete_detalle_empaque','CajasPresentacionesController@delete_detalle_empaque');




