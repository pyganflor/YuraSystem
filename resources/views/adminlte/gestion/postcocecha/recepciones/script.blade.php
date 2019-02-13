<script>
    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            busqueda: $('#busqueda_recepciones').val().trim(),
            fecha_ingreso: $('#fecha_ingreso_search').val(),
            anno: $('#anno_search').val(),
        };
        $.get('{{url('recepcion/buscar_recepciones')}}', datos, function (retorno) {
            $('#div_listado_recepciones').html(retorno);
            estructura_tabla('table_content_recepciones');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function buscarRecepcionByFecha() {
        datos = {};
    }

    $(document).on("click", "#pagination_listado_recepciones .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?busqueda=' + $('#busqueda_recepciones').val().trim() +
            '&fecha_ingreso=' + $('#fecha_ingreso_search').val() +
            '&anno=' + $('#anno_search').val() + '&');
        $('#div_listado_recepciones').html($('#table_recepciones').html());
        $.get(url, function (resul) {
            $('#div_listado_recepciones').html(resul);
            estructura_tabla('table_content_recepciones');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });

    function exportar_recepciones() {
        $.LoadingOverlay('show');
        window.open('{{url('recepcion/exportar_recepciones')}}' + '?busqueda=' + $('#busqueda_recepciones').val().trim() +
            '&fecha_ingreso=' + $('#fecha_ingreso_search').val() + '&anno=' + $('#anno_search').val(), '_blank');
        $.LoadingOverlay('hide');
    }

    function add_recepcion() {
        $.LoadingOverlay('show');
        $.get('{{url('recepcion/add_recepcion')}}', {}, function (retorno) {
            modal_form('modal_add_recepcion', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir cosecha', true, false, '{{isPC() ? '85%' : ''}}', function () {
                store_recepcion();
                $.LoadingOverlay('hide');
            });
        });
        $.LoadingOverlay('hide');
    }

    function store_recepcion() {
        if ($('#form-add-recepcion').valid()) {
            $.LoadingOverlay('show');
            arreglo = [];
            for (i = 1; i <= cant_forms; i++) {
                data = {
                    id_variedad: $('#id_variedad_' + i).val(),
                    cantidad_mallas: $('#cantidad_mallas_' + i).val(),
                    tallos_x_malla: $('#tallos_x_malla_' + i).val(),
                    id_modulo: $('#id_modulo_' + i).val(),
                };
                arreglo.push(data);
            }
            datos = {
                _token: '{{csrf_token()}}',
                id_cosecha: $('#id_cosecha').val(),
                personal: $('#personal').val(),
                hora_inicio: $('#hora_inicio').val(),
                fecha_ingreso: $('#fecha_ingreso').val(),
                cantidad: arreglo
            };
            post_jquery('{{url('recepcion/store_recepcion')}}', datos, function () {
                add_recepcion();
                cerrar_modals();
                buscar_listado();
                set_max_today($('#fecha_ingreso'));
            });
            $.LoadingOverlay('hide');
        }
    }

    function ver_recepcion(id_recepcion) {
        $.LoadingOverlay('show');
        datos = {
            id_recepcion: id_recepcion
        };
        $.get('{{url('recepcion/ver_recepcion')}}', datos, function (retorno) {
            modal_view('modal_view_recepcion', retorno, '<i class="fa fa-fw fa-eye"></i> Detalles de ingreso', true, false, '{{isPC() ? '75%' : ''}}');
            $.LoadingOverlay('hide');
        });
        $.LoadingOverlay('hide');
    }

    function add_tallo_malla() {
        cant_forms++;
        $('#table_forms_tallos_mallas').append('<tr id="row_form_' + cant_forms + '">' +
            '<td style="border-color: #9d9d9d" class="text-center">' +
            '<div class="form-group">' +
            '<select id="id_variedad_' + cant_forms + '" name="id_variedad_' + cant_forms + '" required class="form-control">' +
            '<option value="">Seleccione...</option></select>' +
            '</div>' +
            '</td>' +
            '<td style="border-color: #9d9d9d" class="text-center">' +
            '<div class="form-group">' +
            '<input type="number" id="cantidad_mallas_' + cant_forms + '" name="cantidad_mallas_' + cant_forms + '" required class="form-control"' +
            '       min="1" max="1000">' +
            '</div>' +
            '</td>' +
            '<td style="border-color: #9d9d9d" class="text-center">' +
            '<div class="form-group">' +
            '<input type="number" id="tallos_x_malla_' + cant_forms + '" name="tallos_x_malla_' + cant_forms + '" required class="form-control"' +
            '       min="1" max="50">' +
            '</div>' +
            '</td>' +
            '<td style="border-color: #9d9d9d" class="text-center" colspan="2">' +
            '<div class="form-group">' +
            '<select id="id_modulo_' + cant_forms + '" name="id_modulo_' + cant_forms + '" required class="form-control">' +
            $('#id_modulo_1').html() +
            '</select>' +
            '</div>' +
            '</td>' +
            '</tr>');
        $('#btn_del_form').show();
        for (i = 0; i < $('.option_variedades_form').length; i++) {
            $('#id_variedad_' + cant_forms).append('<option value="' + $('.option_variedades_form')[i].value + '">' +
                $('.option_variedades_form')[i].text +
                '</option>');
        }
    }

    function del_tallo_malla() {
        if (cant_forms > 1) {
            $('#row_form_' + cant_forms).remove();
            cant_forms--;
        }
        if (cant_forms == 1) {
            $('#btn_del_form').hide();
        }
    }

    function cargar_opcion(opcion, id) {
        $.LoadingOverlay('show');
        datos = {
            id_recepcion: id
        };
        get_jquery('{{url('recepcion')}}/' + opcion, datos, function (retorno) {
            $('#div_content_opciones').html(retorno);
        });
        $.LoadingOverlay('hide');
    }

    /* ============= FUNCION PARA AÑADIR DOCUMENTO =================*/
    function add_info(codigo) {
        add_documento('recepcion', codigo, function () {
            ver_recepcion(codigo);
        });
    }

    set_max_today($('#fecha_ingreso_search'));
    $('#fecha_ingreso_search').val('');
</script>
