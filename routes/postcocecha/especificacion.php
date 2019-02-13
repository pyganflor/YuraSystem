<?php

    Route::get('especificacion','EspecificacionController@inicio');
    Route::get('especificacion/listado','EspecificacionController@listado_especificaciones');
    Route::get('especificacion/form_asignacion_especificacion','EspecificacionController@form_asignacion_especificacion');
    Route::get('especificacion/store_asignacion_especificacion','EspecificacionController@sotre_asignacion_especificacion');
