<?php

    Route::get('comprobantes','ComprobanteController@index');
    Route::get('comprobantes/buscar','ComprobanteController@buscar_comprobantes');
    Route::get('comprobantes/add_comprobantes','ComprobanteController@add_comprobantes');

