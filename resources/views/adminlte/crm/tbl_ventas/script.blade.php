<script>
    filtrar_tablas();

    function filtrar_tablas() {
        datos = {
            desde: parseInt($('#desde').val()),
            hasta: parseInt($('#hasta').val()),
            annos: $('#annos').val(),
            cliente: $('#cliente').val(),
            variedad: $('#variedad').val(),
            criterio: $('#criterio').val(),
            rango: $('#rango').val(),
        };
        if (datos['desde'] <= datos['hasta']) {
            get_jquery('{{url('tbl_ventas/filtrar_tablas')}}', datos, function (retorno) {
                $('#div_contentido_tablas').html(retorno);
            });
        }
    }

    function navegar_tabla(rango, criterio, periodo, tipo, anno, cliente, desde = null, hasta = null) {
        $('.li_anno').removeClass('bg-aqua-active');
        $('#li_anno_' + anno).addClass('bg-aqua-active');
        $('#annos').val(anno);

        datos = {
            tipo: tipo,
            periodo: periodo,
            anno: anno,
            cliente: cliente,
            criterio: criterio,
            rango: rango,
            desde: desde,
            hasta: hasta,
            filtro_cliente: $('#cliente').val(),
            filtro_variedad: $('#variedad').val(),
        };
        get_jquery('{{url('tbl_ventas/navegar_tabla')}}', datos, function (retorno) {
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
</script>