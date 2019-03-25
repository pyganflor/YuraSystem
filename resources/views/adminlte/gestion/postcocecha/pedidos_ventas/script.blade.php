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
                $('select#id_cliente_venta option[value=' + id_cliente + ']').attr('selected', true);
                $('select#id_cliente_venta').attr('disabled', true);
                datos = {
                    id_pedido: id_pedido,
                };

                $.get('{{url('pedidos/editar_pedido')}}', datos, function (retorno) {
                    $("#fecha_de_entrega").val(retorno.pedido[0].fecha_pedido);
                    calcular_precio_pedido();
                    //$("#descripcion").val(retorno[0].descripcion);
                        /*for(p=0;p<retorno.duplicados.length;p++){
                            duplicar = retorno.duplicados[p].cant_esp_dup-1;
                            if(duplicar > 0)
                                for(q=0;q<duplicar;q++){
                                    duplicar_especificacion(retorno.duplicados[p].id_especificacion);
                                }
                        }*/
                        /*setTimeout(function () {
                            for (i = 0; i < retorno.pedido.length; i++) {
                                data_precio = retorno.pedido[i].precio.split("|");
                                data_option = [];
                                $.each(data_precio,function (a,b) {
                                    arr_precio = b.split(";");
                                    precio = parseFloat(arr_precio[0]);
                                    $(".cantidad_"+arr_precio[1]).val(retorno.pedido[i].cantidad_especificacion);
                                    $("#precio_"+arr_precio[1]+"_"+(i+1)+" option[value='" + precio + "']").remove();
                                    $("#precio_"+arr_precio[1]+"_"+(i+1)).append("<option value='" + precio + "'>" + precio + "</option>");
                                    $("#precio_"+arr_precio[1]+"_"+(i+1)+" option[value='" + precio + "']").attr('selected', true);
                                    $("#precio_"+arr_precio[1]+"_"+(a+1)).val(precio);
                                    $("select#id_agencia_carga_"+arr_precio[1] +" option[value='" + retorno.pedido[i].id_agencia_carga + "']").attr('selected', true);
                                });
                            }
                            calcular_precio_pedido();
                            $.LoadingOverlay('hide');
                        },1000);*/
                }).always(function () {
                    $.LoadingOverlay('hide');
                });

            })
            /*.always(function () {
                        $.LoadingOverlay('hide');
                    });*/
        },300);

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

</script>
