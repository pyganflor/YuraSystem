<script>
    filtrar_tablas();

    function filtrar_tablas() {
        datos = {
            desde: parseInt($('#desde').val()),
            hasta: parseInt($('#hasta').val()),
            annos: $('#annos').val(),
            variedad: $('#variedad').val(),
            criterio: $('#criterio').val(),
            rango: $('#rango').val(),
            acumulado: $('#acumulado').prop('checked'),
        };
        if (datos['desde'] <= datos['hasta']) {
            get_jquery('{{url('tbl_postcosecha/filtrar_tablas')}}', datos, function (retorno) {
                $('#div_contentido_tablas').html(retorno);
            });
        }
    }

    function exportar_tabla() {
        datos = {
            desde: parseInt($('#desde').val()),
            hasta: parseInt($('#hasta').val()),
            annos: $('#annos').val(),
            variedad: $('#variedad').val(),
            criterio: $('#criterio').val(),
            rango: $('#rango').val(),
            acumulado: $('#acumulado').prop('checked'),
        };
        if (datos['desde'] <= datos['hasta']) {
            $.LoadingOverlay('show');
            window.open('{{url('tbl_postcosecha/exportar_tabla')}}' + '?desde=' + datos['desde'] + '&hasta=' + datos['hasta'] +
                '&annos=' + datos['annos'] + '&variedad=' + datos['variedad'] + '&criterio=' + datos['criterio'] + '&rango=' +
                datos['rango'] + '&acumulado=' + datos['acumulado'], '_blank');
            $.LoadingOverlay('hide');
        }
    }

    function navegar_tabla(rango, criterio, periodo, tipo, anno, variedad, desde = null, hasta = null) {
        $('.li_anno').removeClass('bg-aqua-active');
        $('#li_anno_' + anno).addClass('bg-aqua-active');
        $('#annos').val(anno);

        datos = {
            tipo: tipo,
            periodo: periodo,
            anno: anno,
            variedad: variedad,
            criterio: criterio,
            rango: rango,
            desde: desde,
            hasta: hasta,
            filtro_variedad: $('#variedad').val(),
            acumulado: $('#acumulado').prop('checked'),
        };
        get_jquery('{{url('tbl_postcosecha/navegar_tabla')}}', datos, function (retorno) {
            $('#div_contentido_tablas').html(retorno);
        });
    }

    function select_anno(a) {
        text = $('#annos').val();
        if (text == '')
            $('#annos').val(a);
        else {
            arreglo = $('#annos').val().split(' - ');
            if (arreglo.includes(a)) {  // año seleccionado: quitar año de la lista
                pos = arreglo.indexOf(a);
                arreglo.splice(pos, 1);

                $('#annos').val('');

                for (i = 0; i < arreglo.length; i++) {
                    text = $('#annos').val();
                    if (i == 0)
                        $('#annos').val(arreglo[i]);
                    else
                        $('#annos').val(text + ' - ' + arreglo[i]);
                }

                $('#li_anno_' + a).removeClass('bg-aqua-active');
            }
            else {  // año no seleccionado: agregar año a la lista
                $('#annos').val(text + ' - ' + a);
                $('#li_anno_' + a).addClass('bg-aqua-active');
            }
        }
    }

    function select_mes(m, option) {
        text = m.length == 1 ? '0' + m : m;

        $('.li_mes_' + option).removeClass('bg-aqua-active');
        $('#li_mes_' + option + '_' + m).addClass('bg-aqua-active');

        $('#' + option).val(text);
    }

    function select_rango(value) {
        if (value == 'M') {
            $('.btn_desde-hasta_M').show();
            $('.btn_desde-hasta_S').hide();

            $('#desde').val('{{date('m')}}');
            $('#hasta').val('{{date('m')}}');

            $('#desde').prop('readonly', true);
            $('#hasta').prop('readonly', true);

            $('.li_mes_desde').removeClass('bg-aqua-active');
            $('#li_mes_desde_' + parseInt('{{date('m')}}')).addClass('bg-aqua-active');
            $('.li_mes_hasta').removeClass('bg-aqua-active');
            $('#li_mes_hasta_' + parseInt('{{date('m')}}')).addClass('bg-aqua-active');
        } else {
            $('.btn_desde-hasta_M').hide();
            $('.btn_desde-hasta_S').show();

            $('#desde').val('{{substr(getSemanaByDate(date('Y-m-d'))->codigo, 2)}}');
            $('#hasta').val('{{substr(getSemanaByDate(date('Y-m-d'))->codigo, 2)}}');

            $('#desde').prop('readonly', false);
            $('#hasta').prop('readonly', false);
        }
    }
</script>