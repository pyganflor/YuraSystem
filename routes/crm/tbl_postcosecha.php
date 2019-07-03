<?php

Route::get('tbl_postcosecha', 'CRM\tblPostcosechaController@inicio');
Route::get('tbl_postcosecha/filtrar_tablas', 'CRM\tblPostcosechaController@filtrar_tablas');
Route::get('tbl_postcosecha/exportar_tabla', 'CRM\tblPostcosechaController@exportar_tabla');
Route::get('tbl_postcosecha/navegar_tabla', 'CRM\tblPostcosechaController@navegar_tabla');