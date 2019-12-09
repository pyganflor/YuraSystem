<script>
    listar_indicadores();

    function listar_indicadores(){
        $.get('{{url('intervalo_indicador/listar')}}', {}, function (retorno) {
            console.log(retorno);
            $('#div_listado_intervalo_indicador').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }
</script>
