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
            estructura_tabla('table_content_aperturas');
            buscar_pedidos();
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

</script>