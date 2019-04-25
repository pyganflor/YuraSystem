<script>
    $('.select2').select2();
    filtrar_predeterminado(1);

    function filtrar_predeterminado(option) {
        diario = false;
        mensual = false;
        semanal = false;
        $('.check_filtro_cosecha').prop('checked', false);
        $('.check_filtro_cosecha_variedad').prop('checked', false);
        if ($('#filtro_predeterminado_rango').val() == 1) {
            diario = true;
            desde = rest_dias(30);
        } else if ($('#filtro_predeterminado_rango').val() == 2) {
            semanal = true;
            desde = rest_dias(90);
        } else if ($('#filtro_predeterminado_rango').val() == 3) {
            semanal = true;
            desde = rest_dias(180);
        } else if ($('#filtro_predeterminado_rango').val() == 4) {
            semanal = true;
            desde = rest_dias(365);
        }

        id_cliente = '';
        x_cliente = false;
        total = false;
        if ($('#filtro_predeterminado_criterio').val() == 'A') {
            total = true;
        } else {
            x_cliente = true;
            id_cliente = $('#filtro_predeterminado_criterio').val();
        }

        list_annos = [];
        li_annos = $('.select2-selection__choice');
        for (i = 0; i < li_annos.length; i++) {
            list_annos.push(li_annos[i].title);
        }

        datos = {
            anual: false,
            mensual: mensual,
            semanal: semanal,
            diario: diario,
            x_cliente: x_cliente,
            total: total,
            desde: desde,
            hasta: rest_dias(1),
            id_cliente: id_cliente,
            annos: option == 0 ? list_annos : [],
        };

        get_jquery('{{url('crm_ventas/filtrar_graficas')}}', datos, function (retorno) {
            $('#div_graficas').html(retorno);
        });
    }
</script>