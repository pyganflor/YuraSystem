<?php
Route::get('orden_factura', 'OrdenFacturaController@inicio');
Route::get('orden_factura/buscar_pedido_facturada_generada', 'OrdenFacturaController@buscar_pedido_facturada_generada');
Route::post('orden_factura/update_secuencial_factura', 'OrdenFacturaController@update_secuencial_factura');
