<?php

    Route::get('crm_proyeccion', 'CRM\CrmProyeccionesController@inicio');
    Route::get('crm_proyeccion/desglose_indicador', 'CRM\CrmProyeccionesController@desgloseIndicador');
    Route::get('crm_proyeccion/desglose_cosecha_4_semanas', 'CRM\CrmProyeccionesController@desgloseCosecha4Semanas');
    //Route::get('crm_area/desglose_indicador', 'CRM\crmAreaController@desglose_indicador');
