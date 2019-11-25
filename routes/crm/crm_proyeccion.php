<?php

    Route::get('crm_proyeccion', 'CRM\CrmProyeccionesController@inicio');
    Route::get('crm_proyeccion/desglose_indicador', 'CRM\CrmProyeccionesController@desgloseIndicador');
    Route::get('crm_proyeccion/desglose_cosecha_4_semanas', 'CRM\CrmProyeccionesController@desgloseCosecha4Semanas');
    Route::get('crm_proyeccion/desglose_venta_4_semanas', 'CRM\CrmProyeccionesController@desgloseVenta4Semanas');
    Route::get('crm_proyeccion/desglose_3_meses', 'CRM\CrmProyeccionesController@desgloseVenta3Meses');
    Route::get('crm_proyeccion/chart_inicio', 'CRM\CrmProyeccionesController@chartInicio');
