<?php
    Route::get('consignatario','ConsignatarioController@inicio');
    Route::get('consignatario/buscar','ConsignatarioController@buscar_listado');
    Route::get('consignatario/add','ConsignatarioController@addConsignatario');
    Route::post('consignatario/store','ConsignatarioController@storeConsignatario');
    Route::post('consignatario/update_estado','ConsignatarioController@updateEstadoConsignatario');
