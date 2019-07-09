<?php

    Route::get('etiqueta_factura','EtiquetaFacturaController@inicio');
    Route::get('etiqueta_factura/listado','EtiquetaFacturaController@listado');
    Route::get('etiqueta_factura/form_etiqueta','EtiquetaFacturaController@form_etiqueta');
    Route::get('etiqueta_factura/campos_etiqueta','EtiquetaFacturaController@campos_etiqueta');
    Route::post('etiqueta_factura/store_etiqueta_factura','EtiquetaFacturaController@store_etiqueta_factura');
    Route::post('etiqueta_factura/delete_etiqueta_factura','EtiquetaFacturaController@delete_etiqueta_factura');

