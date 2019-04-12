<?php

Route::get('importar_data', 'ImportarDataController@inicio');
Route::post('importar_data/postcosecha', 'ImportarDataController@importar_cosecha');