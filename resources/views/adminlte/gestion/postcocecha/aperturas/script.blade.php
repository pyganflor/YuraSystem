<script>
    //buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            fecha_desde: $('#fecha_desde_search').val().trim(),
            fecha_hasta: $('#fecha_hasta_search').val(),
            variedad: $('#variedad_search').val(),
            unitaria: $('#unitaria_search').val(),
        };
        $.get('{{url('apertura/buscar_aperturas')}}', datos, function (retorno) {
            $('#div_listado_aperturas').html(retorno);
            //estructura_tabla('table_content_aperturas');
            buscar_pedidos();
            $('#div_form_group_coches').show();
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function exportar_aperturas() {
        $.LoadingOverlay('show');
        window.open('{{url('apertura/exportar_aperturas')}}' + '?busqueda=' + $('#busqueda_aperturas').val().trim() +
            '&fecha_ingreso=' + $('#fecha_ingreso_search').val() +
            '&proceso=' + $('#proceso_search').val() +
            '&anno=' + $('#anno_search').val(), '_blank');
        $.LoadingOverlay('hide');
    }

    function mouseOver(tr, id) {
        tr.css('background-color', '#add8e6');
        $('#table_desglose_apertura_' + id).addClass('sombra_estandar');
        $('#table_desglose_apertura_' + id).css('border', '3px solid #add8e6');
    }

    function mouseLeave(tr, id) {
        tr.css('background-color', '');
        $('#table_desglose_apertura_' + id).removeClass('sombra_estandar');
        $('#table_desglose_apertura_' + id).css('border', '1px solid #add8e6');
    }

    function buscar_pedidos() {
        datos = {
            fecha: $('#fecha_pedidos').val(),
            id_variedad: $('#variedad_search').val()
        };
        get_jquery('{{url('apertura/listar_pedidos')}}', datos, function (retorno) {
            $('#div_listado_pedidos').html(retorno);
            $('#btn_sacar').hide();
        });
    }

    function calcularConvercion(calibre) {
        aperturas = $('.ids_apertura');

        for (i = 0; i < aperturas.length; i++) {
            if ($('#tipo_calibre_unitatio_' + aperturas[i].value).val() === 'P') {
                disponible = $('#cantidad_disponible_' + aperturas[i].value).val();
                unitaria = $('#calibre_unitario_' + aperturas[i].value).val();
                factor = Math.round((calibre / unitaria) * 100) / 100;
                convercion = Math.round((disponible / factor) * 100) / 100;
                $('#celda_ramos_convertidos_' + aperturas[i].value).html(convercion);
                $('#sacar_' + aperturas[i].value).html(convercion);
                $('#ramos_convertidos_' + aperturas[i].value).val(convercion);
            }
        }

        fechas_totales = $('.fechas_aperturas');

        for (i = 0; i < fechas_totales.length; i++) {
            ids = $('#fecha_ids_aperturas_' + fechas_totales[i].value).val().split('|');
            ids.pop();
            total_ramos = 0;
            for (a = 0; a < ids.length; a++) {
                if ($('#tipo_calibre_unitatio_' + ids[a]).val() === 'P' || true) {
                    total_ramos += Math.round(parseFloat($('#ramos_convertidos_' + ids[a]).val()) * 100) / 100;
                }
            }

            $('#celda_total_ramos_' + fechas_totales[i].value).html(total_ramos);
            $('#total_ramos_' + fechas_totales[i].value).val(total_ramos);
        }
    }

    function calcular_tallos_x_coche() {
        ids_apertura = $('.ids_apertura');
        tallos_x_coche = $('#tallos_x_coche').val();
        if (tallos_x_coche != '' && tallos_x_coche > 0) {

            for (i = 0; i < ids_apertura.length; i++) {
                id_apertura = ids_apertura[i].value;
                $('#sacar_' + id_apertura).prop('max', Math.round(($('#sacar_ini_' + id_apertura).val() / tallos_x_coche) * 10000) / 10000);
                //if (!$('#checkbox_sacar_' + id_apertura).prop('checked'))
                //$('#sacar_' + id_apertura).val(Math.round(($('#sacar_ini_' + id_apertura).val() / tallos_x_coche) * 10000) / 10000);
            }

            listado = $('.checkbox_sacar');
            $('#btn_sacar').hide();
            cantidad_seleccionada = 0;
            for (i = 0; i < listado.length; i++) {
                if (listado[i].checked) {
                    $('#btn_sacar').show();
                    factor = $('#factor_calibre_unitario_' + listado[i].id.substr(15)).val();
                    seleccionados = parseFloat($('#sacar_' + listado[i].id.substr(15)).val()) * tallos_x_coche;
                    cantidad_seleccionada += (Math.round((seleccionados / factor) * 100) / 100);
                    $('#html_current_sacar').html('Seleccionados: ' + Math.round(cantidad_seleccionada * 100) / 100);
                }
            }
        } else {
            buscar_listado();
        }
    }

    function show_hide_filtro() {
        if ($('#check_filtro').prop('checked')) {
            $('#table_filtro').show();
        } else {
            $('#table_filtro').hide();
        }
    }

    function mover_fecha(apertura) {
        datos = {
            id_apertura: apertura
        };
        get_jquery('{{url('apertura/mover_fecha')}}', datos, function (retorno) {
            modal_view('modal-view_mover_fecha', retorno, '<i class="fa fa-fw fa-calendar"></i> Mover de Fecha', true, false, '{{isPC() ? '50%' : ''}}');
        })
    }
</script>