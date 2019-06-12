<?php

Route::get('crm_area', 'CRM\crmAreaController@inicio');
Route::get('crm_area/filtrar_graficas', 'CRM\crmAreaController@filtrar_graficas');
Route::get('crm_area/desglose_indicador', 'CRM\crmAreaController@desglose_indicador');