<?php
    Route::get('proy_venta_semanal','Proyecciones\ProyVentaController@inicio');
    Route::get('proy_venta_semanal/listar_proyeccion_venta_semanal','Proyecciones\ProyVentaController@listarProyecionVentaSemanal');
    Route::post('proy_venta_semanal/store_factor_cliente','Proyecciones\ProyVentaController@storeFactorCliente');
    Route::post('proy_venta_semanal/store_proyeccion_venta','Proyecciones\ProyVentaController@storeProyeccionVenta');
