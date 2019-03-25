<?php

    Route::get('datos_exportacion', 'DatosExportacionController@inicio');
    Route::get('datos_exportacion/buscar', 'DatosExportacionController@buscar');
    Route::get('datos_exportacion/add_dato_exportacion', 'DatosExportacionController@add_dato_exportacion');
    Route::get('datos_exportacion/add_input_dato_exportacion', 'DatosExportacionController@add_input_dato_exportacion');
    Route::post('datos_exportacion/store_datos_exportacion', 'DatosExportacionController@store_datos_exportacion');
    Route::post('datos_exportacion/update_estado_datos_exportacion', 'DatosExportacionController@update_estado_datos_exportacion');
    Route::get('datos_exportacion/form_asignacion_dato_exportacion', 'DatosExportacionController@form_asignacion_dato_exportacion');
    Route::post('datos_exportacion/asignar_dato_exportacion', 'DatosExportacionController@asignar_dato_exportacion');
