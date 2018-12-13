<?php

Route::get('menu_sistema', 'MenuSistemaController@inicio');

Route::get('menu_sistema/select_grupo_menu', 'MenuSistemaController@select_grupo_menu');
Route::get('menu_sistema/listar_menus_x_grupo', 'MenuSistemaController@listar_menus_x_grupo');
Route::get('menu_sistema/select_menu', 'MenuSistemaController@select_menu');

Route::get('menu_sistema/add_grupo_menu', 'MenuSistemaController@add_grupo_menu');
Route::post('menu_sistema/store_grupo_menu', 'MenuSistemaController@store_grupo_menu');
Route::get('menu_sistema/add_menu', 'MenuSistemaController@add_menu');
Route::post('menu_sistema/store_menu', 'MenuSistemaController@store_menu');
Route::get('menu_sistema/add_submenu', 'MenuSistemaController@add_submenu');
Route::post('menu_sistema/store_submenu', 'MenuSistemaController@store_submenu');

Route::get('menu_sistema/edit_grupo_menu', 'MenuSistemaController@edit_grupo_menu');
Route::post('menu_sistema/update_grupo_menu', 'MenuSistemaController@update_grupo_menu');
Route::post('menu_sistema/cambiar_estado_grupo_menu', 'MenuSistemaController@cambiar_estado_grupo_menu');
Route::get('menu_sistema/edit_menu', 'MenuSistemaController@edit_menu');
Route::post('menu_sistema/update_menu', 'MenuSistemaController@update_menu');
Route::post('menu_sistema/cambiar_estado_menu', 'MenuSistemaController@cambiar_estado_menu');
Route::get('menu_sistema/edit_submenu', 'MenuSistemaController@edit_submenu');
Route::post('menu_sistema/update_submenu', 'MenuSistemaController@update_submenu');
Route::post('menu_sistema/cambiar_estado_submenu', 'MenuSistemaController@cambiar_estado_submenu');