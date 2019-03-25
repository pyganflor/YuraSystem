<script>
    cargar_cosecha();

    function cargar_cosecha() {
        get_jquery('{{url('crm_postcosecha/cargar_cosecha')}}', {}, function (retorno) {
            $('#div_cosecha').html(retorno);
        });
    }

    function select_option_cosecha(option) {
        $('.div_option_cosecha').hide();
        $('#div_' + option + '_cosecha').show();
    }

    function show_data_cajas(desde, hasta) {
        datos = {
            desde: desde,
            hasta: hasta
        };
        get_jquery('{{url('crm_postcosecha/show_data_cajas')}}', datos, function (retorno) {
            modal_view('modal_view-show_data_cajas', retorno, '<i class="fa fa-fw fa-gift"></i> Reporte de Cajas', true, false, '{{isPC() ? '60%' : ''}}');
        });
    }

    function show_data_tallos(desde, hasta) {
        datos = {
            desde: desde,
            hasta: hasta
        };
        get_jquery('{{url('crm_postcosecha/show_data_tallos')}}', datos, function (retorno) {
            modal_view('modal_view-show_data_tallos', retorno, '<i class="fa fa-fw fa-gift"></i> Reporte de Tallos', true, false, '{{isPC() ? '60%' : ''}}');
        });
    }

    function show_data_desechos(desde, hasta) {
        datos = {
            desde: desde,
            hasta: hasta
        };
        get_jquery('{{url('crm_postcosecha/show_data_desechos')}}', datos, function (retorno) {
            modal_view('modal_view-show_data_desechos', retorno, '<i class="fa fa-fw fa-gift"></i> Reporte de % Desechos', true, false, '{{isPC() ? '60%' : ''}}');
        });
    }

    function show_data_rendimientos(desde, hasta) {
        datos = {
            desde: desde,
            hasta: hasta
        };
        get_jquery('{{url('crm_postcosecha/show_data_rendimientos')}}', datos, function (retorno) {
            modal_view('modal_view-show_data_rendimientos', retorno, '<i class="fa fa-fw fa-gift"></i> Reporte de Rendimientos', true, false,
                '{{isPC() ? '60%' : ''}}');
        });
    }

    function show_data_calibres(desde, hasta) {
        datos = {
            desde: desde,
            hasta: hasta
        };
        get_jquery('{{url('crm_postcosecha/show_data_calibres')}}', datos, function (retorno) {
            modal_view('modal_view-show_data_calibres', retorno, '<i class="fa fa-fw fa-gift"></i> Reporte de Calibres', true, false,
                '{{isPC() ? '60%' : ''}}');
        });
    }

    function filtrar_predeterminado() {
        if ($('#filtro_predeterminado').val() != '') {
            diario = false;
            mensual = false;
            semanal = false;
            $('.check_filtro_cosecha').prop('checked', false);
            $('.check_filtro_cosecha_variedad').prop('checked', false);
            if ($('#filtro_predeterminado').val() == 1) {
                diario = true;
                desde = rest_dias(30);
                $('#check_filtro_diario').prop('checked', true);
            } else if ($('#filtro_predeterminado').val() == 2) {
                semanal = true;
                desde = rest_dias(90);
                $('#check_filtro_semanal').prop('checked', true);
            } else if ($('#filtro_predeterminado').val() == 3) {
                mensual = true;
                desde = rest_dias(180);
                $('#check_filtro_mensual').prop('checked', true);
            } else if ($('#filtro_predeterminado').val() == 4) {
                mensual = true;
                desde = rest_dias(365);
                $('#check_filtro_mensual').prop('checked', true);
            }

            id_variedad = '';
            x_variedad = false;
            total = false;
            if ($('#filtro_predeterminado_variedad').val() == 'T') {
                total = true;
                $('#check_filtro_todas_variedad').prop('checked', true);
                select_checkbox_cosecha_variedad('check_filtro_todas_variedad');
            } else if ($('#filtro_predeterminado_variedad').val() != 'A') {
                x_variedad = true;
                id_variedad = $('#filtro_predeterminado_variedad').val();
                $('#check_filtro_x_variedad').prop('checked', true);
                select_checkbox_cosecha_variedad('check_filtro_x_variedad');
                $('#check_filtro_variedad').val(id_variedad);
            } else {
                $('#check_filtro_todas_variedad').prop('checked', false);
                $('#check_filtro_x_variedad').prop('checked', false);
                $('.op_check_filtro_x_variedad').hide();
            }

            $('#check_filtro_desde').val(desde);
            $('#check_filtro_hasta').val(rest_dias(1));

            datos = {
                anual: false,
                mensual: mensual,
                semanal: semanal,
                diario: diario,
                x_variedad: x_variedad,
                total: total,
                desde: desde,
                hasta: rest_dias(1),
                id_variedad: id_variedad,
            };

            get_jquery('{{url('crm_postcosecha/buscar_reporte_cosecha_chart')}}', datos, function (retorno) {
                $('#div_chart_cosecha').html(retorno);
            });
        }
    }

    function actualizar_cosecha_x_variedad() {
        datos = {};
        /* ============= COMPARACION ===========*/
        get_jquery('{{url('crm_postcosecha/actualizar_cosecha_x_variedad')}}', datos, function (retorno) {
            $('#div_cosecha_x_variedad_cosecha').html(retorno);
        }, 'div_cosecha_x_variedad_cosecha');
    }
</script>