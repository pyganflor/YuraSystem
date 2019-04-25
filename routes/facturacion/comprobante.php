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
Route::get('comprobante/factura_aprobada_sri/{clave_acceso}', 'ComprobanteController@ver_factura_aprobada_sri');
Route::get('comprobante/pre_factura/{clave_acceso}', 'ComprobanteController@ver_pre_factura');
Route::post('comprobante/generar_comprobante_guia_remision','ComprobanteController@generar_comprobante_guia_remision');
/*Route::get('comprobante/prueba', function () {

    $code = generateCodeBarGs1128("0C002");
    echo '<img src="data:image/png;base64,'.$code.'" />';

     ========== CODIGO PARA BORRAR DATOS DUPLICADOS EN RECPCION_CLASIFICACION_VERDE ==========    segun id_recepcion; id_clasificacion_verde
    $listado = \Illuminate\Support\Facades\DB::table('recepcion_clasificacion_verde')
        ->select('id_recepcion', 'id_clasificacion_verde')->distinct()
        ->get();

    $arreglo = [];
    foreach ($listado as $r) {
        $r = \yura\Modelos\RecepcionClasificacionVerde::All()
            ->where('id_recepcion', '=', $r->id_recepcion)
            ->where('id_clasificacion_verde', '=', $r->id_clasificacion_verde)->first();
        $targets = \yura\Modelos\RecepcionClasificacionVerde::All()
            ->where('id_recepcion_clasificacion_verde', '!=', $r->id_recepcion_clasificacion_verde)
            ->where('id_recepcion', '=', $r->id_recepcion)
            ->where('id_clasificacion_verde', '=', $r->id_clasificacion_verde);

        array_push($arreglo, [
            'relacion' => ['id: ' . $r->id_recepcion_clasificacion_verde, 'par: ' . $r->id_recepcion . ' ; ' . $r->id_clasificacion_verde],
            'targets' => $targets
        ]);
    }
    foreach ($arreglo as $item) {
        foreach ($item['targets'] as $t) {
            $t->delete();
        }
    }


});*/

