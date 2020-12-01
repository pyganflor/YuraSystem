<script>
    function listar_parametro() {
        datos = {
            tipo: $('#tipo_parametro').val()
        };
        get_jquery('{{url('parametros/listar_parametro')}}', datos, function (retorno) {
            $('#div_contenido_parametro').html(retorno);
        });
    }
</script>