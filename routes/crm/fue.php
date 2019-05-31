<?php

    Route::get('fue','FueController@inicio');
    Route::get('fue/buscar','FueController@buscar');
    Route::post('fue/actualizar_fue','FueController@actualizar_fue');
    Route::get('fue/reporte_fue','FueController@reporte_fue');
    Route::get('fue/reporte_fue_filtrado','FueController@reporte_fue_filtrado');
    Route::post('fue/exportar_reporte_dae','FueController@exportar_reporte_dae');

