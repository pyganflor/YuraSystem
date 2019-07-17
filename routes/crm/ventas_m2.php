<?php

Route::get('ventas_m2', 'CRM\VentasM2Controller@inicio');
Route::post('ventas_m2/exportar_excel', 'CRM\VentasM2Controller@exportar_excel');