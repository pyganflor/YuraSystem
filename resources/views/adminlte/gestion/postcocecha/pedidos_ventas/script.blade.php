<script>

    listar_resumen_pedidos($("#fecha_pedidos_search").val(), true);

    function ver_envio(id_pedido) {
        $.LoadingOverlay('show');
        datos = {
            id_pedido: id_pedido
        };

        $.get('{{url('pedidos/ver_envio')}}', datos, function (retorno) {
            modal_view('modal_view_envios_facturas', retorno, '<i class="fa fa-plane" aria-hidden="true"></i> Desglose de los envíos del pedido', true, false, '85%');
            //estructura_tabla('table_content_pedidos');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function add_orden_semanal() {
        get_jquery('{{url('pedidos/add_orden_semanal')}}', {}, function (retorno) {
            modal_view('modal-view_add_orden_semanal', retorno, '<i class="fa fa-fw fa-plus"></i> Agregar Orden Semanal', true, false,
                '{{isPC() ? '95%' : ''}}');

        });
    }

    function editar_pedido(id_cliente, id_pedido) {
        add_pedido('', '', 'pedidos', id_pedido);
        datos = {
            id_cliente: id_cliente,
            id_pedido: id_pedido
        };
        setTimeout(function () {
            $.get('{{url('clientes/inputs_pedidos_edit')}}', datos, function (retorno) {
                $("#table_campo_pedido").html(retorno);
                $('select#id_cliente_venta option[value='+id_cliente+']').attr('selected', true);
                $('select#id_cliente_venta').attr('disabled', true);
                datos = {
                    id_pedido: id_pedido,
                };

                $.get('{{url('pedidos/editar_pedido')}}', datos, function (retorno) {
                    $("#fecha_de_entrega").val(retorno.pedido[0].fecha_pedido);
                    calcular_precio_pedido();
                    //$("#descripcion").val(retorno[0].descripcion);
                });
            }).always(function () {
                $.LoadingOverlay('hide');
            });
        }, 300);

    }

    function add_pedido_personalizado() {
        get_jquery('{{url('pedidos/add_pedido_personalizado')}}', {}, function (retorno) {
            modal_view('modal_view_add_pedido_personalizado', retorno, '<i class="fa fa-fw fa-gift"></i> Pedidos personalzados', true, false,
                '{{isPC() ? '95%' : ''}}');
        });
    }

    function distribuir_orden_semanal(id_pedido) {
        datos = {
            id_pedido: id_pedido
        };
        get_jquery('{{url('pedidos/distribuir_orden_semanal')}}', datos, function (vista) {
            modal_view('modal-view_distribuir_orden_semanal', vista, '<i class="fa fa-fw fa-gift"></i> Distribución', true, false,
                '{{isPC() ? '95%' : ''}}');
        });
    }

    function ver_distribucion_orden_semanal(id_pedido) {
        datos = {
            id_pedido: id_pedido
        };
        get_jquery('{{url('pedidos/ver_distribucion_orden_semanal')}}', datos, function (vista) {
            modal_view('modal-view_ver_distribucion_orden_semanal', vista, '<i class="fa fa-fw fa-gift"></i> Distribución', true, false,
                '{{isPC() ? '95%' : ''}}');
        });
    }

    function cambiar_color(c, col, esp_emp) {
        fondo = $('#fondo_color_' + c).val();
        texto = $('#texto_color_' + c).val();

        $('.elemento_color_' + col + '_' + esp_emp).css('background-color', fondo);
        $('.elemento_color_' + col + '_' + esp_emp).css('color', texto);
    }

    function crear_packing_list(id_pedido){
        modal_quest('modal_packing_list', "<div class='alert alert-info text-center'>¿Desea crear el Packing List de este pedido?</div>", "<i class='fa fa-exclamation-triangle' ></i> Seleccione una opción",true, false, '{{isPC() ? '35%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_pedido : id_pedido
            };
            post_jquery('{{url('pedidos/crear_packing_list')}}', datos, function (retorno) {
                listar_resumen_pedidos($("#fecha_pedidos_search").val(), true);
               cerrar_modals();
            });
        });
    }

    function store_especificacion_pedido(id_detalle_pedido,id_cliente_pedido_especificacion,orden,id_agencia_carga){
        $.LoadingOverlay('show');
        datos ={
            id_detalle_pedido : id_detalle_pedido,
            id_cliente_pedido_especificacion : id_cliente_pedido_especificacion,
            orden : orden,
            id_agencia_carga : id_agencia_carga
        };
        post_jquery('clientes/store_especificacion_pedido', datos, function () {
            cerrar_modals();
            listar_resumen_pedidos($('#fecha_pedidos_search').val(), true);
            if (vista != 'pedidos') {
                detalles_cliente(id_cliente == '' ? id_cliente = $("#id_cliente_venta").val() : id_cliente);
            }
        });
        $.LoadingOverlay('hide');


    }

</script>
