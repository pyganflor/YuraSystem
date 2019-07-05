<?php

Route::get('crm_postcosecha', 'CRM\crmPostocechaController@inicio');
Route::get('crm_postcosecha/cargar_cosecha', 'CRM\crmPostocechaController@cargar_cosecha');
Route::get('crm_postcosecha/buscar_reporte_cosecha_indicadores', 'CRM\crmPostocechaController@buscar_reporte_cosecha_indicadores');
Route::get('crm_postcosecha/buscar_reporte_cosecha_comparacion', 'CRM\crmPostocechaController@buscar_reporte_cosecha_comparacion');
Route::get('crm_postcosecha/buscar_reporte_cosecha_chart', 'CRM\crmPostocechaController@buscar_reporte_cosecha_chart');
Route::get('crm_postcosecha/show_data_cajas', 'CRM\crmPostocechaController@show_data_cajas');
Route::get('crm_postcosecha/show_data_tallos', 'CRM\crmPostocechaController@show_data_tallos');
Route::get('crm_postcosecha/show_data_desechos', 'CRM\crmPostocechaController@show_data_desechos');
Route::get('crm_postcosecha/show_data_rendimientos', 'CRM\crmPostocechaController@show_data_rendimientos');
Route::get('crm_postcosecha/show_data_calibres', 'CRM\crmPostocechaController@show_data_calibres');
Route::get('crm_postcosecha/actualizar_cosecha_x_variedad', 'CRM\crmPostocechaController@actualizar_cosecha_x_variedad');
Route::post('crm_postcosecha/exportar_dashboard', 'CRM\crmPostocechaController@exportar_dashboard');
