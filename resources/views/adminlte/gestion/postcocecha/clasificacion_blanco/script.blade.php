<script>
    function listar_clasificacion_blanco(variedad) {
        $.LoadingOverlay('show');
        datos = {
            variedad: variedad,
        };
        $.get('{{url('clasificacion_blanco/listar_clasificacion_blanco')}}', datos, function (retorno) {
            $('#div_listado_blanco').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }
</script>