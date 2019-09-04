<?php
    Route::get('producto_venture','ProductoVentureController@inicio');
    Route::post('producto_venture/vincular_yura_system_venture','ProductoVentureController@vincularProductosVenture');
    Route::get('producto_venture/listar_productos_vinculados','ProductoVentureController@listadoProdcutosVinculados');
    Route::post('producto_venture/delete_vinculados','ProductoVentureController@deleteProdcutosVinculados');
    Route::get('producto_venture/listar_productos','ProductoVentureController@listarProductos');
