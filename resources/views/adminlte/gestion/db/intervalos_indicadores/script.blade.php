<script>
    listar_indicadores();

    function listar_indicadores(){
        $.get('{{url('intervalo_indicador/listar')}}', datos, function (retorno) {
            $('#div_listado_intervalo_indicador').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }
</script>
