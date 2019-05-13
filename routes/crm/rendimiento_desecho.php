<?php

Route::get('crm_rendimiento', 'CRM\crmRendimientoController@inicio');
Route::get('crm_rendimiento/filtrar_graficas', 'CRM\crmRendimientoController@filtrar_graficas');
Route::get('crm_rendimiento/desglose_indicador', 'CRM\crmRendimientoController@desglose_indicador');