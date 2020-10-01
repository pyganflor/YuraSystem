<?php

Route::get('cosecha_plantas_madres', 'Propagacion\propagCosechaPtasMadresController@inicio');
Route::get('cosecha_plantas_madres/listar_cosechas', 'Propagacion\propagCosechaPtasMadresController@listar_cosechas');
Route::post('cosecha_plantas_madres/select_cama', 'Propagacion\propagCosechaPtasMadresController@select_cama');
Route::post('cosecha_plantas_madres/store_cosechas', 'Propagacion\propagCosechaPtasMadresController@store_cosechas');
Route::post('cosecha_plantas_madres/update_cosecha', 'Propagacion\propagCosechaPtasMadresController@update_cosecha');
Route::post('cosecha_plantas_madres/eliminar_cosecha', 'Propagacion\propagCosechaPtasMadresController@eliminar_cosecha');