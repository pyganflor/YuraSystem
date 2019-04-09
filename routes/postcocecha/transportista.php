<?php

    Route::get('transportista','TransportistaController@incio');
    Route::get('transportista/list','TransportistaController@buscar');
    Route::get('transportista/add','TransportistaController@create_transportista');
    Route::post('transportista/store','TransportistaController@store_transportista');
