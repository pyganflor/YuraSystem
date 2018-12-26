<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', 'YuraController@login');
Route::post('login', 'YuraController@verificaUsuario');
Route::get('logout', 'YuraController@logout');


/*Route::get('prueba-wsdl',function (){

    $url = 'https://celcer.sri.gob.ec/comprobantes-electronicos-ws/RecepcionComprobantes?wsdl';
    $cliente = new SoapClient($url);
    $c = $cliente;
    //$c  = $cliente->__getTypes();

    //dd($c);
    $xml = file_get_contents("C:/factura.xml");

    $x = base64_encode($xml);

    $xml  = "<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:ec='http://ec.gob.sri.ws.recepcion'>"."\r\n";
    $xml .= "<soapenv:Header/>";
    $xml .= "<soapenv:Body>";
    $xml .= "<ec:autorizacionComprobante>";
    $xml .= "<ec:validarComprobante>";
    $xml .= "<xml>";
    $xml .= $x;
    $xml .= "</xml>";
    $xml .= "</ec:validarComprobante>";
    $xml .= "</ec:autorizacionComprobante>";
    $xml .= "</soapenv:Body>";
    $xml .= "</soapenv:Envelope>";
    dd($c->validarComprobante(['xml'=>$xml]));
});*/


Route::get('configuracion/inputs_dinamicos_detalle_empaque', 'ConfiguracionEmpresaController@vistaInputsDetallesEmpaque')->name('view.inputs_detalle_empaque');
Route::get('configuracion/campos_empaques', 'ConfiguracionEmpresaController@campos_empaque')->name('view.campos_empaque');

Route::group(['middleware' => 'autenticacion'], function () {

    Route::get('/', 'YuraController@inicio');
    Route::post('save_config_user', 'YuraController@save_config_user');
    Route::get('perfil', 'YuraController@perfil');
    Route::post('perfil/update_usuario', 'YuraController@update_usuario');
    Route::post('perfil/update_image_perfil', 'YuraController@update_image_perfil');
    Route::post('perfil/update_password', 'YuraController@update_password');

    Route::post('usuarios/get_usuario_json', 'UsuarioController@get_usuario_json');

    include 'documento/rutas.php';

    Route::group(['middleware' => 'permiso'], function () {
        /* ========================== POSTCPCECHA ========================*/
        include 'postcocecha/lotes.php';
        include 'postcocecha/clasificacion_blanco.php';
        include 'postcocecha/apertura.php';
        include 'postcocecha/clasificacion_verde.php';
        include 'postcocecha/recepcion.php';
        include 'postcocecha/clientes.php';

        include 'sectores_modulos/rutas.php';
        include 'semanas/rutas.php';
        include 'plantas_variedades/rutas.php';

        include 'menu_sistema/rutas.php';
        include 'permisos/rutas.php';
        include 'usuarios/rutas.php';

        include 'configuracion_empresa/rutas.php';
        include 'postcocecha/agencias_carga.php';
        include 'postcocecha/marcas.php';
        include 'postcocecha/pedidos_ventas.php';
        include 'postcocecha/envios.php';
        include 'postcocecha/agencias_transporte.php';

        include 'facturacion/comprobantes.php';
    });


});
