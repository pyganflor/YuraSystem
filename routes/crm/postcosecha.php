<?php

Route::get('crm_postcosecha', 'CRM\crmPostocechaController@inicio');
Route::get('crm_postcosecha/cargar_cosecha', 'CRM\crmPostocechaController@cargar_cosecha');
Route::get('crm_postcosecha/buscar_reporte_cosecha_indicadores', 'CRM\crmPostocechaController@buscar_reporte_cosecha_indicadores');
Route::get('crm_postcosecha/buscar_reporte_cosecha_comparacion', 'CRM\crmPostocechaController@buscar_reporte_cosecha_comparacion');
Route::get('crm_postcosecha/buscar_reporte_cosecha_chart', 'CRM\crmPostocechaController@buscar_reporte_cosecha_chart');