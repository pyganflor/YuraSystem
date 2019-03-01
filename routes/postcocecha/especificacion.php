<?php

    Route::get('especificacion','EspecificacionController@inicio');
    Route::get('especificacion/listado','EspecificacionController@listado_especificaciones');
    Route::get('especificacion/form_asignacion_especificacion','EspecificacionController@form_asignacion_especificacion');
    Route::get('especificacion/store_asignacion_especificacion','EspecificacionController@sotre_asignacion_especificacion');
    Route::get('especificacion/verificar_pedido_especificacion','EspecificacionController@verificar_pedido_especificacion');
    Route::get('especificacion/delete_asignacion_especificacion','EspecificacionController@delete_asignacion_especificacion');
    Route::get('especificacion/add_row_especificacion','EspecificacionController@nueva_especificacion');
    Route::post('especificacion/store_row_especificacion','EspecificacionController@store_row_especificacion');
    /*Route::get('especificacion/prueba',function(){
        $data = getEspecificacion(75);

        $a = [];
        foreach ($data->especificacionesEmpaque as $desp)
            foreach ($desp->detalles as $det)
                $a[] = [
                    'variedad'     => $det->variedad->nombre,
                    'calibre'      => $det->clasificacion_ramo->nombre,
                    'caja'         => $desp->empaque->nombre,
                    'rxc'          => $det->cantidad,
                    'presentacion' => $det->empaque_p->nombre,
                    'txr'          => $det->tallos_x_ramos,
                    'longitud'     => $det->longitud_ramo,
                    'unidad_medida_longitud'=> isset($det->unidad_medida->siglas) ? $det->unidad_medida->siglas : null,
                ];

            dd($a);
    });*/



