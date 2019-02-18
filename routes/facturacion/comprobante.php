<?php

use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;

Route::get('comprobante', 'ComprobanteController@inicio');
Route::get('comprobante/buscar', 'ComprobanteController@buscar_comprobante');
Route::get('comprobante/generar_comprobante_factura', 'ComprobanteController@generar_comprobante_factura');
Route::get('comprobante/enviar_comprobante', 'ComprobanteController@enviar_documento_electronico');
Route::get('comprobante/generar_comprobante_lote', 'ComprobanteController@generar_comprobante_lote');
Route::get('comprobante/firmar_comprobante', 'ComprobanteController@firmar_comprobante');
Route::get('comprobante/autorizacion_comprobante', 'ComprobanteController@autorizacion_comprobante');
Route::get('comprobante/formulario_facturacion', 'ComprobanteController@formulario_facturacion');
Route::get('comprobante/prueba', function () {
    /*$barcode = new BarcodeGenerator();
    $barcode->setText("0801201901200100200001369417924463252");
    $barcode->setType(BarcodeGenerator::Gs1128);
    $barcode->setNoLengthLimit(true);
    $barcode->setAllowsUnknownIdentifier(true);
    $code = $barcode->generate();
    echo '<img src="data:image/png;base64,'.$code.'" />';*/

    /* ========== CODIGO PARA BORRAR DATOS DUPLICADOS EN RECPCION_CLASIFICACION_VERDE ==========    segun id_recepcion; id_clasificacion_verde*/
    /*$listado = \Illuminate\Support\Facades\DB::table('recepcion_clasificacion_verde')
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
    }*/

});