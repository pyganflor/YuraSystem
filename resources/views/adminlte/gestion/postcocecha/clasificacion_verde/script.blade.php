<script>
    set_max_today($('#fecha_verde_search'));
    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            fecha_verde: $('#fecha_verde_search').val().trim(),
        };
        $.get('{{url('clasificacion_verde/buscar_clasificaciones')}}', datos, function (retorno) {
            $('#div_listado_clasificaciones').html(retorno);
            //estructura_tabla('table_content_clasificaciones');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function rendimiento_mesas() {
        $.LoadingOverlay('show');
        datos = {
            fecha_verde: $('#fecha_verde_search').val().trim(),
        };
        $.get('{{url('clasificacion_verde/rendimiento_mesas')}}', datos, function (retorno) {
            modal_view('modal-view_rendimiento_mesas', retorno, '<i class="fa fa-fw fa-cubes"></i> Rendimiento por mesa', true, false, '99%')
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    $(document).on("click", "#pagination_listado_clasificaciones .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?semana_desde=' + $('#semana_desde_search').val().trim() +
            '&semana_hasta=' + $('#semana_hasta_search').val() +
            '&fecha_desde=' + $('#fecha_desde_search').val() +
            '&fecha_hasta=' + $('#fecha_hasta_search').val() +
            '&fecha_verde=' + $('#fecha_verde_search').val() +
            '&anno=' + $('#anno_search').val() + '&');
        $('#div_listado_clasificaciones').html($('#table_clasificaciones').html());
        $.get(url, function (resul) {
            $('#div_listado_clasificaciones').html(resul);
            //estructura_tabla('table_content_clasificaciones');
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

    function add_verde(fecha) {
        $.LoadingOverlay('show');
        datos = {
            fecha: fecha
        };
        if ($(document).width() >= 1024) { // mostrar arbol
            $.get('{{url('clasificacion_verde/add_verde')}}', datos, function (retorno) {
                modal_view('modal_add_clasificacion_verde', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir clasificación', true, false, '{{isPC() ? '85%' : ''}}');
            });
        } else {    // ocultar arbol
            $.get('{{url('clasificacion_verde/add_verde_mobil')}}', datos, function (retorno) {
                modal_view('modal_add_clasificacion_verde', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir clasificación', true, false, '100%');
            });
        }

        $.LoadingOverlay('hide');
    }

    function store_verde() {
        alert('store_verde()_script');
        if ($('#form-add-verde').valid()) {
            $.LoadingOverlay('show');
            arreglo = [];
            for (i = 1; i <= cant_forms; i++) {
                data = {
                    id_variedad: $('#id_variedad_' + i).val(),
                    cantidad_mallas: $('#cantidad_mallas_' + i).val(),
                    tallos_x_malla: $('#tallos_x_malla_' + i).val(),
                };
                arreglo.push(data);
            }
            datos = {
                _token: '{{csrf_token()}}',
                fecha_ingreso: $('#fecha_ingreso').val(),
                cantidad: arreglo
            };
            post_jquery('{{url('clasificacion_verde/store_recepcion')}}', datos, function () {
                add_recepcion();
                cerrar_modals();
                buscar_listado();
            });
            $.LoadingOverlay('hide');
        }
    }

    function ver_clasificacion(id_clasificacion_verde) {
        $.LoadingOverlay('show');
        datos = {
            id_clasificacion_verde: id_clasificacion_verde
        };
        $.get('{{url('clasificacion_verde/ver_clasificacion')}}', datos, function (retorno) {
            modal_view('modal_view_clasificacion_verde', retorno, '<i class="fa fa-fw fa-eye"></i> Detalles de clasificación', true, false,
                '{{isPC() ? '75%' : ''}}');
            $.LoadingOverlay('hide');
        });
        $.LoadingOverlay('hide');
    }

    function ver_rendimiento(verde) {
        datos = {
            id_clasificacion_verde: verde
        };

        get_jquery('{{url('clasificacion_verde/ver_rendimiento')}}', datos, function (retorno) {
            modal_view('modal_view_ver_rendimiento', retorno, '<i class="fa fa-fw fa-balance-scale"></i> Rendimiento', true, false, '{{isPC() ? '65%' : ''}}');
        });
    }

    function store_lote_re_from(variedad) {
        if ($('#form-add_lote_re_' + variedad).valid()) {
            posiciones = $('.pos_lotes_re_' + variedad);

            arreglo = [];
            success = true;

            for (i = 0; i < posiciones.length; i++) {
                pos = posiciones[i].value;
                total_tallos = parseInt($('#tallos_x_unitaria_fecha_' + variedad + '_' + pos).val());
                apertura = parseInt($('#apertura_' + variedad + '_' + pos).val());
                guarde = parseInt($('#guarde_' + variedad + '_' + pos).val());
                dias = parseInt($('#dias_' + variedad + '_' + pos).val());
                fecha = $('#fecha_unitaria_' + variedad + '_' + pos).val();

                if ((apertura + guarde) != total_tallos) {
                    $('#apertura_' + variedad + '_' + pos).addClass('error');
                    $('#guarde_' + variedad + '_' + pos).addClass('error');
                    $('#badge_tallos_x_unitaria_' + variedad + '_' + pos).addClass('error');
                    success = false;
                } else {
                    $('#apertura_' + variedad + '_' + pos).removeClass('error');
                    $('#guarde_' + variedad + '_' + pos).removeClass('error');
                    $('#badge_tallos_x_unitaria_' + variedad + '_' + pos).removeClass('error');

                    lote = {
                        id_clasificacion_unitaria: parseInt($('#id_clasificacion_unitaria_' + variedad + '_' + pos).val()),
                        dias: dias,
                        fecha: fecha,
                        apertura: apertura,
                        guarde: guarde,
                    };

                    arreglo.push(lote);
                }
            }
            if (success) {
                datos = {
                    _token: '{{csrf_token()}}',
                    fecha: $('#fecha_ingreso').val(),
                    id_variedad: variedad,
                    id_clasificacion_verde: $('#id_clasificacion_verde').val(),
                    arreglo: arreglo,
                    terminar: 1
                };

                return datos;
            } else {
                alerta('<p class="text-center">Debe distribuir la cantidad exacta de tallos entre Apertura y Guarde</p>');
            }
        } else {
            alerta('<p class="text-center">Faltan datos en el formulario</p>');
        }
    }

    function check_filtro() {
        if ($('#check_filtro_verde').prop('checked')) {
            $('#check_filtro_verde').prop('checked', false);
            $('#table_filtro').hide();
        } else {
            $('#check_filtro_verde').prop('checked', true);
            $('#table_filtro').show();
        }
    }

    /* ============= FUNCION PARA AÑADIR DOCUMENTO =================*/
    function add_info(codigo) {
        add_documento('clasificacion_verde', codigo, function () {
            ver_clasificacion(codigo);
        });
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
            '<td style="border-color: #9d9d9d" class="text-center" colspan="2">' +
            '<div class="form-group">' +
            '<input type="number" id="tallos_x_malla_' + cant_forms + '" name="tallos_x_malla_' + cant_forms + '" required class="form-control"' +
            '       min="1" max="50">' +
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

    function destinar_lotes(variedad, clasificacion) {
        datos = {
            id_variedad: variedad,
            id_clasificacion_verde: clasificacion
        };
        get_jquery('{{url('clasificacion_verde/destinar_lotes')}}', datos, function (retorno) {
            modal_view('modal_destinar_lotes', retorno, '<i class="fa fa-fw fa-exchange"></i> Destinar lotes', true, false, '{{isPC() ? '75%' : ''}}');
        });
    }

    function ver_lotes(variedad, clasificacion) {
        datos = {
            id_variedad: variedad,
            id_clasificacion_verde: clasificacion
        };
        get_jquery('{{url('clasificacion_verde/ver_lotes')}}', datos, function (retorno) {
            modal_view('modal_ver_lotes', retorno, '<i class="fa fa-fw fa-exchange"></i> Lotes', true, false, '{{isPC() ? '75%' : ''}}');
        });
    }

    function calcular_stock(unitaria) {
        datos = {
            id_clasificacion_unitaria: unitaria,
            id_variedad: $('#id_variedad').val(),
            fecha_ingreso: $('#fecha_ingreso').val(),
            dias: $('#dias_' + unitaria).val(),
            cantidad_tallos: $('#apertura_' + unitaria).val(),
        };
        $.LoadingOverlay('show');
        $.get('{{url('clasificacion_verde/calcular_stock')}}', datos, function (retorno) {
            $('#stock_' + unitaria).val(retorno.stock);
            $('#disponible_' + unitaria).val(retorno.disponible);
            $('#fecha_disponible_' + unitaria).html(retorno.fecha);
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
            alert('Ha ocurrido un problema al enviar la información');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function cargar_opcion(opcion, id) {
        $.LoadingOverlay('show');
        datos = {
            id_clasificacion_verde: id
        };
        get_jquery('{{url('clasificacion_verde')}}/' + opcion, datos, function (retorno) {
            $('#div_content_opciones').html(retorno);
        });
        $.LoadingOverlay('hide');
    }

    function destinar_lotes_form(variedad, clasificacion) {
        datos = {
            id_variedad: variedad,
            id_clasificacion_verde: clasificacion
        };
        get_jquery('{{url('clasificacion_verde/destinar_lotes_form')}}', datos, function (retorno) {
            $('#div_destinar_lotes_' + variedad).html(retorno);
        });
    }

    set_max_today($('#fecha_ingreso_search'));
    $('#fecha_ingreso_search').val('');
</script>