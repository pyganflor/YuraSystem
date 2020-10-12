<script>
    filtrar_ciclos();

    function filtrar_ciclos() {
        datos = {
            variedad: $('#filtro_predeterminado_variedad').val(),
            fecha: $('#filtro_predeterminado_fecha').val(),
            tipo: $('#filtro_predeterminado_tipo').val(),
        };
        get_jquery('{{url('fenograma_propag/filtrar_ciclos')}}', datos, function (retorno) {
            $('#div_listado_ciclos').html(retorno);
        });
    }
</script>