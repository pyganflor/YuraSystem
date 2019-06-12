<script>
    buscar_listado();

    function buscar_listado() {
        datos = {
            variedad: $('#variedad').val(),
            desde: $('#desde').val(),
            hasta: $('#hasta').val(),
        };
        if (datos['desde'] != '' && datos['hasta'] != '' && datos['desde'] <= datos['hasta'])
            get_jquery('{{url('regalias_semanas/buscar_listado')}}', datos, function (retorno) {
                $('#div_listado').html(retorno);
            });
        else
            alerta('<div class="alert alert-warning text-center">La semana Inicial debe ser menor o igual que la Final</div>');
    }
</script>