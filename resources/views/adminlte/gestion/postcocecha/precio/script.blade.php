<script>
    buscar_cliente();
    buscar_especificacion();

    function buscar_cliente() {
        $.LoadingOverlay('show');
        $.get('{{url('precio/buscar_cliente')}}', {}/*datos*/, function (retorno) {
            $('#listado_clientes').html(retorno);
            estructura_tabla('table_content_clientes');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function buscar_especificacion() {
        $.LoadingOverlay('show');
        $.get('{{url('precio/buscar_especificacion')}}', {}/*datos*/, function (retorno) {
            $('#listado_especificaciones').html(retorno);
            estructura_tabla('table_content_especificaciones');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    $(document).on("click", "#pagination_listado_clientes .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?busqueda=&');
        $('div#listado_clientes').html($('#table_cliente').html());
        $.get(url, function (resul) {
            $('div#listado_clientes').html(resul);
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });

    $(document).on("click", "#pagination_listado_especicaciones .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?busqueda=&');
        $('div#listado_especificaciones').html($('#table_especificacion').html());
        $.get(url, function (resul) {
            $('div#listado_especificaciones').html(resul);
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });

    function precio_especificacion_cliente(id_especificacion) {
        datos = {
            id_especificacion: id_especificacion
        };
        $.get('{{url('precio/form_asignar_precio_especificacion_cliente')}}', datos, function (retorno) {
            modal_form('modal_precio_especificaciones', retorno, '<i class="fa fa-user-plus" aria-hidden="true"></i> Asignar precio a la especificación', true, false, '{{isPC() ? '70%' : ''}}', function () {
                store_precio_especificacion_cliente(id_especificacion);
            });
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function store_precio_especificacion_cliente(id_especificacion) {
        if ($('#form_add_precio_especificicacion_cliente').valid()) {
            arrPrecios = [];
            $.each($('select[name=id_cliente]'), function (i, j) {
                arrPrecios.push({
                    'id_cliente': $("#id_cliente_" + (i + 1)).val(),
                    'precio': $("#precio_" + (i + 1)).val(),
                    'id_cliente_pedido_especificacion': $("#id_cliente_pedido_especificacion_" + (i + 1)).val()
                });
            });
            datos = {
                _token: '{{csrf_token()}}',
                arrPrecios: arrPrecios,
                id_especificacion: id_especificacion
            };
            post_jquery('{{url('precio/store_precio_especificacio_cliente')}}', datos, function () {
                cerrar_modals();
            });
            $.LoadingOverlay('hide');
        }
    }

    function add_input() {
        $("#btn_add_input").attr('disabled', true);
        cant_rows = $('select[name=id_cliente]').length;
        console.log(cant_rows);
        $.get('{{url('precio/add_input')}}', datos, function (retorno) {
            $("#form_add_precio_especificicacion_cliente").append(
                " <div class='row'  id='row_" + (cant_rows + 1) + "'>" +
                "    <input type='hidden' id='id_cliente_pedido_especificacion_" + (cant_rows + 1) + "'>" +
                "    <div class='col-md-8'>" +
                "        <div class='form-group'>" +
                "             <label for='nombre_marca'>Cliente</label>" +
                "             <select id='id_cliente_" + (cant_rows + 1) + "' name='id_cliente' class='form-control' required>" +
                "                 <option disabled selected> Seleccione </option>" +
                "             </select>" +
                "        </div>" +
                "    </div>" +
                "    <div class='col-md-4'>" +
                "        <div class='form-group'>" +
                "            <label for='identificacion'>Precio</label>" +
                "            <input type='number' id='precio_" + (cant_rows + 1) + "' name='precio' class='form-control' min='1' value='' required>" +
                "        </div>" +
                "    </div>" +
                "</div>");
            for (var x = 0; x < retorno.length; x++) {
                $("#id_cliente_" + (cant_rows + 1)).append("<option value='" + retorno[x].id_cliente + "'> " + retorno[x].nombre + " </option>");
            }
        }).always(function () {
            $.LoadingOverlay('hide');
            $("#btn_add_input").attr('disabled', false);
        });
    }

    function delete_input(cant_exist) {
        cant_rows = $('select[name=id_cliente]').length;
        if (cant_rows > cant_exist) {
            $('#row_' + cant_rows).remove();
        }
    }

    function precio_cliente_especificacion(id_cliente) {
        datos = {
            id_cliente: id_cliente
        };
        $.get('{{url('precio/form_asignar_precio_cliente_especificacion')}}', datos, function (retorno) {
            modal_form('modal_precio_especificaciones', retorno, '<i class="fa fa-money"></i> Asignar precio a las especificaciones del cliente', true, false, '{{isPC() ? '80%' : ''}}', function () {
                store_precio_cliente_especificacion(id_cliente);
            });
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function store_precio_cliente_especificacion(id_cliente) {
        if ($('#form_add_precio_cliente_especificicacion').valid()) {
            arrPrecios = [];
            $.each($('.id_detalle_especificacion_empaque'), function (i, j) {
                arrPrecios.push({
                    'precio': $("#precio_" + (j.value)).val(),
                    'id_detalle_especificacionempaque': $("#" + (j.id)).val()
                });
            });

            datos = {
                _token: '{{csrf_token()}}',
                arrPrecios: arrPrecios,
                id_cliente: id_cliente
            };
            post_jquery('{{url('precio/store_precio_cliente_especificacion')}}', datos, function () {
                cerrar_modals();
            });
            $.LoadingOverlay('hide');
        }
    }

</script>
