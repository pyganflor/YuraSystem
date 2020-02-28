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

    function rendimiento_mesas() {
        $.LoadingOverlay('show');
        datos = {
            fecha_blanco: $('#fecha_blanco').val().trim(),
        };
        $.get('{{url('clasificacion_blanco/rendimiento_mesas')}}', datos, function (retorno) {
            modal_view('modal-view_rendimiento_mesas', retorno, '<i class="fa fa-fw fa-cubes"></i> Rendimiento por mesa', true, false, '99%')
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    if ($(document).width() >= 1024) { // mostrar arbol
        $('.label_blanco').html('Trabajar con la Clasificación en Blanco correspondiente a la fecha:');
    } else {    // ocultar arbol
        $('.label_blanco').html('Clasificación Blanco:');
    }
</script>