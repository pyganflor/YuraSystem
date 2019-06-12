<script>
    filtrar_predeterminado();

    function filtrar_predeterminado() {
        diario = false;
        mensual = false;
        semanal = false;
        if ($('#filtro_predeterminado_rango').val() == 1) {
            diario = true;
            desde = (30);
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
        if ($('#filtro_predeterminado_annos').val() != '') {
            li_annos = $('#filtro_predeterminado_annos').val().split(' - ');
            for (i = 0; i < li_annos.length; i++) {
                list_annos.push(li_annos[i]);
            }
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

    function select_anno(a) {
        text = $('#filtro_predeterminado_annos').val();
        if (text == '') {
            $('#filtro_predeterminado_annos').val(a);
            $('#li_anno_' + a).addClass('bg-aqua-active');
        }
        else {
            arreglo = $('#filtro_predeterminado_annos').val().split(' - ');
            if (arreglo.includes(a)) {  // año seleccionado: quitar año de la lista
                pos = arreglo.indexOf(a);
                arreglo.splice(pos, 1);

                $('#filtro_predeterminado_annos').val('');

                for (i = 0; i < arreglo.length; i++) {
                    text = $('#filtro_predeterminado_annos').val();
                    if (i == 0)
                        $('#filtro_predeterminado_annos').val(arreglo[i]);
                    else
                        $('#filtro_predeterminado_annos').val(text + ' - ' + arreglo[i]);
                }

                $('#li_anno_' + a).removeClass('bg-aqua-active');
            }
            else {  // año no seleccionado: agregar año a la lista
                $('#filtro_predeterminado_annos').val(text + ' - ' + a);
                $('#li_anno_' + a).addClass('bg-aqua-active');
            }
        }
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