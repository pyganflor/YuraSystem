<?php

Route::get('proy_mano_obra', 'Proyecciones\proyManoObraController@inicio');
Route::get('proy_mano_obra/listar_proyecciones', 'Proyecciones\proyManoObraController@listar_proyecciones');
Route::post('proy_mano_obra/update_horas_diarias_cosecha', 'Proyecciones\proyManoObraController@update_horas_diarias_cosecha');
Route::post('proy_mano_obra/update_horas_diarias_verde', 'Proyecciones\proyManoObraController@update_horas_diarias_verde');