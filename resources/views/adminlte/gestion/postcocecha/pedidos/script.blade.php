<script>
    buscar_listado_pedidos('{{$idCliente}}');

    function buscar_listado_pedidos(id_cliente) {
        $.LoadingOverlay('show');
        datos = {
            busquedaAnno: $('#anno').val(),
            id_especificaciones: $("#id_especificaciones").val(),
            desde: $("#desde").val(),
            hasta: $("#hasta").val(),
            id_cliente: id_cliente
        };
        $.get('{{url('clientes/ver_pedidos')}}', datos, function (retorno) {
            $('#div_listado_pedidos').html(retorno);
            estructura_tabla('table_content_pedidos');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function update_status_detalle_pedido(id_detalle_pedido, estado, id_cliente) {
        $.LoadingOverlay('show');
        datos = {
            _token: '{{csrf_token()}}',
            id_detalle_pedido: id_detalle_pedido,
            estado: estado,
        };
        get_jquery('{{url('clientes/actualizar_estado_pedido_detalle')}}', datos, function () {
            cerrar_modals();
            detalles_cliente(id_cliente);
            setTimeout(function () {
                cargar_opcion('div_pedidos', id_cliente, 'clientes/listar_pedidos');
            }, 1000);

        });
        $.LoadingOverlay('hide');
    }

    $(document).on("click", "#pagination_listado_pedidos .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?busquedaAnno=' + $('#anno').val() +
            '&id_especificaciones=' + $('#id_especificaciones').val() +
            '&desde=' + $('#desde').val() + '&' +
            '&hasta=' + $('#hasta').val() + '&' +
            '&id_cliente=' + {{$idCliente}} +'&');
        $('#div_listado_pedidos').html($('#table_pedidos').html());
        $.get(url, function (resul) {
            //console.log(resul);
            $('#div_listado_pedidos').html(resul);
            estructura_tabla('table_content_pedidos');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });
</script>
