<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', 'YuraController@login');
Route::post('login', 'YuraController@verificaUsuario');
Route::get('logout', 'YuraController@logout');

Route::group(['middleware' => 'autenticacion'], function () {

    Route::get('/', 'YuraController@inicio');
    Route::post('save_config_user', 'YuraController@save_config_user');
    Route::get('perfil', 'YuraController@perfil');
    Route::post('perfil/update_usuario', 'YuraController@update_usuario');
    Route::post('perfil/update_image_perfil', 'YuraController@update_image_perfil');
    Route::post('perfil/update_password', 'YuraController@update_password');

    Route::post('usuarios/get_usuario_json', 'UsuarioController@get_usuario_json');

    Route::group(['middleware' => 'permiso'], function () {
        /* ========================== POSTCPCECHA ========================*/
        include 'postcocecha/recepcion.php';

        include 'sectores_modulos/rutas.php';
        include 'semanas/rutas.php';
        include 'plantas_variedades/rutas.php';

        include 'menu_sistema/rutas.php';
        include 'permisos/rutas.php';
        include 'usuarios/rutas.php';
    });

});
