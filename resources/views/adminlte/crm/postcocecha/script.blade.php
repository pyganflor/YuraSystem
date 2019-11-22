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

    function filtrar_predeterminado(option) {
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
                semanal = true;
                desde = rest_dias(180);
                $('#check_filtro_mensual').prop('checked', true);
            } else if ($('#filtro_predeterminado').val() == 4) {
                semanal = true;
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
                x_variedad: x_variedad,
                total: total,
                desde: desde,
                hasta: rest_dias(1),
                id_variedad: id_variedad,
                annos: option == 0 ? list_annos : [],
            };

            get_jquery('{{url('crm_postcosecha/buscar_reporte_cosecha_chart')}}', datos, function (retorno) {
                $('#div_chart_cosecha').html(retorno);

                /*setTimeout("activar_tab('tallos')", 1000);
                setTimeout("activar_tab('calibres')", 1500);
                setTimeout("activar_tab('cajas')", 2000);*/
            });
        }
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

    function exportar_excel() {
        if ($('#filtro_predeterminado').val() != '') {
            enviar_dashboard_exportar();
        }
    }

    function enviar_dashboard_exportar() {
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
            semanal = true;
            desde = rest_dias(180);
            $('#check_filtro_mensual').prop('checked', true);
        } else if ($('#filtro_predeterminado').val() == 4) {
            semanal = true;
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

        list_annos = [];
        if ($('#filtro_predeterminado_annos').val() != '') {
            li_annos = $('#filtro_predeterminado_annos').val().split(' - ');
            for (i = 0; i < li_annos.length; i++) {
                list_annos.push(li_annos[i]);
            }
        }

        list_variedades = $('.listado_variedades');
        array_variedades = [];
        for (i = 0; i < list_variedades.length; i++) {
            id = list_variedades[i].value;
            array_variedades.push({
                id: id,
                calibre: $('#calibre_var_' + id).val(),
                cosechados: $('#cosechados_var_' + id).val(),
                clasificados: $('#clasificados_var_' + id).val(),
            });
        }

        convertCanvasToImage('chart_cajas');
        convertCanvasToImage('chart_calibres');
        convertCanvasToImage('chart_tallos');

        $.LoadingOverlay('show');
        $.ajax({
            type: "POST",
            dataType: "html",
            contentType: "application/x-www-form-urlencoded",
            url: '{{url('crm_postcosecha/exportar_dashboard')}}',
            data: {
                _token: '{{csrf_token()}}',
                anual: false,
                mensual: mensual,
                semanal: semanal,
                diario: diario,
                x_variedad: x_variedad,
                total: total,
                desde: desde,
                hasta: rest_dias(1),
                id_variedad: id_variedad,
                annos: list_annos,

                indicador_cajas: $('#indicador_cajas').val(),
                indicador_tallos: $('#indicador_tallos').val(),
                indicador_calibre: $('#indicador_calibre').val(),
                src_imagen_chart_cajas: $('#src_imagen_chart_cajas').val(),
                src_imagen_chart_tallos: $('#src_imagen_chart_tallos').val(),
                src_imagen_chart_calibres: $('#src_imagen_chart_calibres').val(),
                filtro_predeterminado: $('#filtro_predeterminado').val(),
                filtro_predeterminado_variedad: $('#filtro_predeterminado_variedad').val(),
                filtro_predeterminado_annos: $('#filtro_predeterminado_annos').val(),
                calibre_dia: $('#calibre_dia').val(),
                clasificados_dia: $('#clasificados_dia').val(),
                cosechados_dia: $('#cosechados_dia').val(),
                array_variedades: array_variedades,
            },
            success: function (data) {
                var opResult = JSON.parse(data);
                var $a = $("<a>");
                $a.attr("href", opResult.data);
                $("body").append($a);
                $a.attr("download", "DASHBOARD-Postcosecha.xlsx");
                $a[0].click();
                $a.remove();
            }
        });
        $.LoadingOverlay('hide');
    }

    function activar_tab(id) {
        $('.li_tab_chart').removeClass('active');
        $('.div_tab_chart').removeClass('active');
        $('#li_tab_' + id).addClass('active');
        $('#' + id + '-chart').addClass('active');
    }

    function convertCanvasToImage(id_canvas) {
        //var image = document.getElementById('imagen_' + id_canvas);
        var canvas = document.getElementById(id_canvas);
        var src = canvas.toDataURL("image/png");

        $('#src_imagen_' + id_canvas).val(src);
    }
</script>