<script>
    $('.select2').select2();

    filtrar_predeterminado(1);

    function filtrar_predeterminado() {
        diario = false;
        mensual = false;
        semanal = false;
        if ($('#filtro_predeterminado_rango').val() == 1) {
            diario = true;
            desde = (15);
        } else if ($('#filtro_predeterminado_rango').val() == 2) {
            semanal = true;
            desde = (90);
        } else if ($('#filtro_predeterminado_rango').val() == 3) {
            semanal = true;
            desde = (180);
        } else if ($('#filtro_predeterminado_rango').val() == 4) {
            semanal = true;
            desde = (365);
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
            desde: desde,
            hasta: rest_dias(1),
            criterio: $('#filtro_predeterminado_criterio').val(),
            id_variedad: $('#filtro_predeterminado_variedad').val(),
            annos: list_annos,
        };

        get_jquery('{{url('crm_rendimiento/filtrar_graficas')}}', datos, function (retorno) {
            $('#div_graficas').html(retorno);
        });
    }

    function desglose_indicador(option) {
        datos = {
            option: option
        };
        get_jquery('{{url('crm_rendimiento/desglose_indicador')}}', datos, function (retorno) {
            modal_view('modal_view_desglose_indicador', retorno, '<i class="fa fa-fw fa-bar-chart"></i> Desglose', true, false,
                '{{isPC() ? '95%' : ''}}');
        });
    }

    function ver_rendimiento_cosecha(id_cosecha) {
        if (id_cosecha != '') {
            datos = {
                id_cosecha: id_cosecha
            };
            get_jquery('{{url('recepcion/ver_rendimiento')}}', datos, function (retorno) {
                modal_view('modal_view_ver_rendimiento', retorno, '<i class="fa fa-fw fa-balance-scale"></i> Rendimiento', true, false,
                    '{{isPC() ? '75%' : ''}}');
            });
        }
    }

    function ver_rendimiento_verde(verde) {
        if (verde != '') {
            datos = {
                id_clasificacion_verde: verde
            };

            get_jquery('{{url('clasificacion_verde/ver_rendimiento')}}', datos, function (retorno) {
                modal_view('modal_view_ver_rendimiento', retorno, '<i class="fa fa-fw fa-balance-scale"></i> Rendimiento', true, false, '{{isPC() ? '65%' : ''}}');
            });
        }
    }

    function ver_rendimiento_blanco(blanco) {
        if (blanco != '') {
            datos = {
                blanco: blanco
            };
            get_jquery('{{url('clasificacion_blanco/ver_rendimiento')}}', datos, function (retorno) {
                modal_view('moda-view_ver_rendimiento', retorno, '<i class="fa fa-fw fa-balance-scale"></i> Rendimiento', true, false,
                    '{{isPC() ? '80%' : ''}}');
            });
        }
    }
</script>