<script>
    set_max_today($('#fecha_blanco'));

    function listar_clasificacion_blanco(variedad) {
        $.LoadingOverlay('show');
        datos = {
            variedad: variedad,
            fecha_blanco: $('#fecha_blanco').val(),
        };
        $.get('{{url('clasificacion_blanco/listar_clasificacion_blanco')}}', datos, function (retorno) {
            $('#div_listado_blanco').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function store_blanco() {
        datos = {
            _token: '{{csrf_token()}}',
        };
        post_jquery('{{url('clasificacion_blanco/store_blanco')}}', datos, function () {
            $.LoadingOverlay('show');
            location.reload();
        });
    }

    function ver_rendimiento(blanco) {
        datos = {
            blanco: blanco
        };
        get_jquery('{{url('clasificacion_blanco/ver_rendimiento')}}', datos, function (retorno) {
            modal_view('moda-view_ver_rendimiento', retorno, '<i class="fa fa-fw fa-balance-scale"></i> Rendimiento', true, false,
                '{{isPC() ? '80%' : ''}}');
        });
    }
</script>