<script>
    listar_resumen_pedidos($('#fecha_pedidos_search').val());

    function listar_resumen_pedidos(fecha) {
        $.LoadingOverlay('show');
        datos = {
            fecha: fecha,
        };
        $.get('{{url('despachos/listar_resumen_pedidos')}}', datos, function (retorno) {
            $('#div_listado_blanco').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function empaquetar(fecha) {
        $.LoadingOverlay('show');
        datos = {
            fecha: fecha,
        };
        $.get('{{url('despachos/empaquetar')}}', datos, function (retorno) {
            modal_view('modal_view_empaquetar', retorno, '<i class="fa fa-fw fa-gift"></i> Empaquetar', true, false, '{{isPc() ? '35%' : ''}}');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }
</script>