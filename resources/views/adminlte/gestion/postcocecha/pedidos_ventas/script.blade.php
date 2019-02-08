<script>
    buscar_listado_pedidos();

    function buscar_listado_pedidos() {
        $.LoadingOverlay('show');
        datos = {
            //busqueda: $('#busqueda_pedidos').val().trim(),
            id_cliente: $("#id_cliente").val(),
            anno: $("#anno").val(),
            desde: $("#desde").val(),
            hasta: $("#hasta").val(),
            estado: $("#estado").val()
        };
        $.get('{{url('pedidos/buscar')}}', datos, function (retorno) {
            $('#div_listado_pedidos').html(retorno);
            estructura_tabla('table_content_pedidos');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    $(document).on("click", "#pagination_listado_pedidos .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?busquedaAnno=' + $('#anno').val() +
            '&desde=' + $('#desde').val() + '&' +
            '&hasta=' + $('#hasta').val() + '&' +
            '&busqueda_pedidos=' + $('#busqueda_pedidos').val() + '&');
        $('#div_listado_pedidos').html($('#table_pedidos').html());
        $.get(url, function (resul) {
            //console.log(resul);
            $('#div_listado_pedidos').html(resul);
            estructura_tabla('table_content_pedidos');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });

    function ver_envio(id_pedido) {
        $.LoadingOverlay('show');
        datos = {
            id_pedido: id_pedido
        };
        $.get('{{url('pedidos/ver_envio')}}', datos, function (retorno) {
            modal_view('modal_view_envios_facturas', retorno, '<i class="fa fa-plane" aria-hidden="true"></i> Desglose de los envíos del pedido', true, false, '75%');
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

    function editar_pedido(id_cliente,id_pedido) {
        add_pedido('','','pedidos',id_pedido);

        datos = {
            id_cliente : id_cliente,
        };
        setTimeout(function(){


        $.get('{{url('clientes/inputs_pedidos')}}', datos, function (retorno) {
            $("#tbody_inputs_pedidos").html(retorno);
            $('select#id_cliente_venta option[value='+id_cliente+']').attr('selected',true);
            $('select#id_cliente_venta').attr('disabled',true);

            datos = {
                id_pedido : id_pedido,
            };
            $.get('{{url('pedidos/editar_pedido')}}', datos, function (retorno) {
                console.log(retorno);
                $("#fecha_de_entrega").val(retorno[0].fecha_pedido);
                $("#descripcion").val(retorno[0].descripcion);

                for(var i=0;i<retorno.length;i++){
                    $("td#td_input_cantidad_"+retorno[i].id_especificacion+" input").val(retorno[i].cantidad_especificacion);
                    $("td#td_select_agencia_carga_"+retorno[i].id_especificacion+" select option[value='"+retorno[i].id_agencia_carga+"']").attr('selected',true);
                }
            }).always(function () {
                $.LoadingOverlay('hide');
            });

        })/*.always(function () {
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
