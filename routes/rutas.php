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

Route::get('configuracion/inputs_dinamicos_detalle_empaque', 'ConfiguracionEmpresaController@vistaInputsDetallesEmpaque')->name('view.inputs_detalle_empaque');
Route::get('configuracion/campos_empaques', 'ConfiguracionEmpresaController@campos_empaque')->name('view.campos_empaque');

Route::group(['middleware' => 'autenticacion'], function () {
    Route::group(['middleware' => 'controlsession'], function () {
        Route::get('/', 'YuraController@inicio');
        Route::get('mostrar_indicadores_claves', 'YuraController@mostrar_indicadores_claves');
        Route::post('save_config_user', 'YuraController@save_config_user');
        Route::get('perfil', 'YuraController@perfil');
        Route::post('perfil/update_usuario', 'YuraController@update_usuario');
        Route::post('perfil/update_image_perfil', 'YuraController@update_image_perfil');
        Route::post('perfil/update_password', 'YuraController@update_password');

        Route::post('usuarios/get_usuario_json', 'UsuarioController@get_usuario_json');

        Route::get('select_planta', 'YuraController@select_planta');

        include 'documento/rutas.php';
        include 'crm/dashboard.php';

        Route::group(['middleware' => 'permiso'], function () {
            /* ========================== POSTCPCECHA ========================*/
            include 'postcocecha/lotes.php';
            include 'postcocecha/clasificacion_blanco.php';
            include 'postcocecha/cuarto_frio.php';
            include 'postcocecha/despachos.php';
            include 'postcocecha/apertura.php';
            include 'postcocecha/clasificacion_verde.php';
            include 'postcocecha/recepcion.php';
            include 'postcocecha/clientes.php';
            include 'postcocecha/consignatario.php';

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
            include 'postcocecha/aerolinea.php';
            include 'postcocecha/especificacion.php';
            include 'postcocecha/cajas_presentaciones.php';
            include 'postcocecha/precio.php';
            include 'postcocecha/dato_exportacion.php';
            include 'postcocecha/transportista.php';
            include 'postcocecha/etiqueta.php';
            include 'postcocecha/etiqueta_factura.php';

            /* ========================== CRM ========================*/
            include 'crm/postcosecha.php';
            include 'crm/ventas.php';
            include 'crm/ventas_m2.php';
            include 'crm/crm_area.php';
            include 'crm/rendimiento_desecho.php';
            include 'crm/tbl_postcosecha.php';
            include 'crm/fue.php';
            include 'crm/regalias_semanas.php';
            include 'crm/tbl_ventas.php';
            include 'crm/tbl_rendimiento.php';
            include 'crm/fenograma_ejecucion.php';
            include 'crm/crm_proyeccion.php';


            /* ========================== FACTURACIÓN ========================*/
            include 'facturacion/tipo_comprobante.php';
            include 'facturacion/tipo_identificacion.php';
            include 'facturacion/tipo_impuesto.php';
            include 'facturacion/emision_comprobante.php';
            include 'facturacion/codigo_dae.php';
            include 'facturacion/producto_venture.php';
            include 'facturacion/orden_factura.php';

            /* ================== IMPORTAR DATA =================== */
            include 'importar_data/rutas.php';

            /* ================== NOTIFICACIONES =================== */
            include 'notificaciones/rutas.php';

            /* ================== PROYECCIONES =================== */
            include 'proyecciones/cosecha.php';
            include 'proyecciones/ventas_x_cliente.php';
            include 'proyecciones/resumen_total.php';
            include 'proyecciones/mano_obra.php';

            /* ================== COSTOS =================== */
            include 'costos/insumo.php';
            include 'costos/mano_obra.php';
            include 'costos/importar.php';

            /* ================== DB =================== */
            include 'db/rutas.php';

        });

        include 'colores/rutas.php';
        include 'codigo_barra/rutas.php';
        include 'facturacion/comprobante.php';
    });

});
include 'notificaciones/otras.php';
