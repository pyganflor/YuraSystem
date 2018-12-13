<?php

Route::get('permisos', 'PermisoController@inicio');
Route::get('permisos/select_rol/submenus', 'PermisoController@select_rol_submenus');
Route::get('permisos/select_rol/usuarios', 'PermisoController@select_rol_usuarios');
Route::get('permisos/listar_menus_x_grupo', 'PermisoController@listar_menus_x_grupo');
Route::get('permisos/listar_submenus_x_menu', 'PermisoController@listar_submenus_x_menu');

Route::get('permisos/add_rol', 'PermisoController@add_rol');
Route::post('permisos/store_rol', 'PermisoController@store_rol');
Route::get('permisos/add_submenu', 'PermisoController@add_submenu');
Route::post('permisos/store_submenu', 'PermisoController@store_submenu');
Route::get('permisos/add_usuario', 'PermisoController@add_usuario');
Route::post('permisos/store_usuario', 'PermisoController@store_usuario');

Route::post('permisos/cambiar_estado_rol_submenu', 'PermisoController@cambiar_estado_rol_submenu');
Route::post('permisos/cambiar_estado_rol', 'PermisoController@cambiar_estado_rol');