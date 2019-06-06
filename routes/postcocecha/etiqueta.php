<?php

    Route::get('etiqueta','EtiquetaController@inicio');
    Route::get('etiqueta/listado','EtiquetaController@listado');
    Route::post('etiqueta/exportar_excel','EtiquetaController@exportar_excel');

