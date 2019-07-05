<?php

    Route::get('etiqueta_factura','EtiquetaFacturaController@inicio');
    Route::get('etiqueta_factura/listado','EtiquetaFacturaController@listado');
    Route::get('etiqueta_factura/form_etiqueta','EtiquetaFacturaController@form_etiqueta');
    Route::get('etiqueta_factura/campos_etiqueta','EtiquetaFacturaController@campos_etiqueta');
