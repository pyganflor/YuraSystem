<?php

Route::get('comprobante', 'ComprobanteController@inicio');
Route::get('comprobante/buscar', 'ComprobanteController@buscar_comprobante');
Route::get('comprobante/generar_comprobante_factura', 'ComprobanteController@generar_comprobante_factura');
Route::get('comprobante/enviar_comprobante', 'ComprobanteController@enviar_documento_electronico');
Route::get('comprobante/generar_comprobante_lote', 'ComprobanteController@generar_comprobante_lote');
Route::get('comprobante/firmar_comprobante', 'ComprobanteController@firmar_comprobante');
Route::get('comprobante/autorizacion_comprobante', 'ComprobanteController@autorizacion_comprobante');
Route::get('comprobante/formulario_facturacion', 'ComprobanteController@formulario_facturacion');
Route::get('comprobante/reenviar_correo', 'ComprobanteController@reenviar_correo');
Route::get('comprobante/comprobante_aprobado_sri/{clave_acceso}', 'ComprobanteController@ver_factura_aprobada_sri');
Route::get('comprobante/pre_factura/{clave_acceso}/{cliente?}', 'ComprobanteController@ver_pre_factura');
Route::post('comprobante/generar_comprobante_guia_remision','ComprobanteController@generar_comprobante_guia_remision');
Route::get('comprobante/pre_guia_remision/{clave_acceso}', 'ComprobanteController@ver_pre_guia_remision');
Route::get('comprobante/prueba', function () {

    /* =========== SEMANAL ============= */
   /* $pedidos_semanal = \yura\Modelos\Pedido::All()->where('estado', 1)
        ->where('fecha_pedido', '>=', opDiasFecha('-', 7, date('Y-m-d')))
        ->where('fecha_pedido', '<=', opDiasFecha('-', 1, date('Y-m-d')));
    $valor = 0;
    $cajas = 0;
    $tallos = 0;

    $test = [];
    foreach ($pedidos_semanal as $p) {
        $valor += $p->getPrecio();
        $cajas += $p->getCajas();
        $tallos += $p->getTallos();

        array_push($test, [
            'pedido' => $p,
            'valor' => $p->getPrecio(),
            'cajas' => $p->getCajas(),
        ]);
    }
    $ramos_estandar = $cajas * getConfiguracionEmpresa()->ramos_x_caja;
    $precio_x_ramo = $ramos_estandar > 0 ? round($valor / $ramos_estandar, 2) : 0;
    $precio_x_tallo = $tallos > 0 ? round($valor / $tallos, 2) : 0;

    $semanal = [
        'valor' => $valor,
        'cajas' => $cajas,
        'precio_x_ramo' => $precio_x_ramo,
        'precio_x_tallo' => $precio_x_tallo,
    ];

    dd($semanal, $pedidos_semanal, $test,
        getPedido(868)->getPrecio()
    );*/
});
