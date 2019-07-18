<?php
    Route::get('codigo_barra/form_codigo_barra',function (){
        return view('layouts.adminlte.partials.form_codigo_barra');
    });

    Route::get('codigo_barra/generar_codigo_barra/{codigo}/{prefijo?}',function ($codigo,$prefijo=""){
        $code = generateCodeBarGs1128($prefijo.$codigo);
        return '<img src="data:image/png;base64,'.$code.'" />';
    });
