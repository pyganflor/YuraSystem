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
</script>