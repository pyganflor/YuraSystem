<?php

Route::get('notificaciones', 'NotificacionesController@inicio');
Route::post('notificaciones/store_notificacion', 'NotificacionesController@store_notificacion');
Route::post('notificaciones/update_notificacion', 'NotificacionesController@update_notificacion');
Route::post('notificaciones/cambiar_estado', 'NotificacionesController@cambiar_estado');