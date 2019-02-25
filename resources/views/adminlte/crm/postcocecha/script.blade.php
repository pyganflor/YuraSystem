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
</script>