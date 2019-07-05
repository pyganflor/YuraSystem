<?php

Route::get('tbl_rendimiento', 'CRM\tblRendimientoController@inicio');
Route::get('tbl_rendimiento/filtrar_tablas', 'CRM\tblRendimientoController@filtrar_tablas');
Route::get('tbl_rendimiento/exportar_tabla', 'CRM\tblRendimientoController@exportar_tabla');
Route::get('tbl_rendimiento/navegar_tabla', 'CRM\tblRendimientoController@navegar_tabla');