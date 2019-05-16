<script>
    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            fecha_desde: $('#fecha_desde_search').val().trim(),
            fecha_hasta: $('#fecha_hasta_search').val(),
            variedad: $('#variedad_search').val(),
            unitaria: $('#unitaria_search').val(),
            etapa: $('#etapa_search').val(),
            en_tiempo: $('#en_tiempo_search').val(),
            clasificacion_ramo: $('#clasificacion_ramo_search').val(),
        };
        $.get('{{url('lotes/buscar_lotes')}}', datos, function (retorno) {
            $('#div_listado_lotes').html(retorno);
            estructura_tabla('table_content_lotes');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    $(document).on("click", "#pagination_listado_lotes .pagination li a", function (e) {
        $.LoadingOverlay("show");
        //para que la pagina se cargen los elementos
        e.preventDefault();
        var url = $(this).attr("href");
        url = url.replace('?', '?fecha_desde=' + $('#fecha_desde_search').val().trim() +
            '&fecha_hasta=' + $('#fecha_hasta_search').val() +
            '&variedad=' + $('#variedad_search').val() +
            '&unitaria=' + $('#unitaria_search').val() +
            '&en_tiempo=' + $('#en_tiempo_search').val() +
            '&clasificacion_ramo=' + $('#clasificacion_ramo_search').val() +
            '&etapa=' + $('#etapa_search').val() + '&');
        $('#div_listado_lotes').html($('#table_lotes').html());
        $.get(url, function (resul) {
            $('#div_listado_lotes').html(resul);
            estructura_tabla('table_content_lotes');
        }).always(function () {
            $.LoadingOverlay("hide");
        });
    });

    function exportar_aperturas() {
        $.LoadingOverlay('show');
        window.open('{{url('apertura/exportar_aperturas')}}' + '?busqueda=' + $('#busqueda_aperturas').val().trim() +
            '&fecha_ingreso=' + $('#fecha_ingreso_search').val() +
            '&proceso=' + $('#proceso_search').val() +
            '&anno=' + $('#anno_search').val(), '_blank');
        $.LoadingOverlay('hide');
    }

    function ver_lote(id_lote_re) {
        datos = {
            id_lote_re: id_lote_re
        };
        get_jquery('{{url('lotes/ver_lote')}}', datos, function (retorno) {
            modal_view('modal_view_lote', retorno, '<i class="fa fa-fw fa-eye"></i> Detalles de lote', true, false, '{{isPC() ? '50%' : ''}}');
        });
    }

    /* ============= FUNCION PARA AÑADIR DOCUMENTO =================*/
    function add_info(codigo) {
        add_documento('lote_re', codigo, function () {
            ver_lote(codigo);
        });
    }

    function cargar_opcion(opcion, id) {
        $.LoadingOverlay('show');
        datos = {
            id_lote_re: id
        };
        get_jquery('{{url('lotes')}}/' + opcion, datos, function (retorno) {
            $('#div_content_opciones').html(retorno);
        });
        $.LoadingOverlay('hide');
    }
</script>
