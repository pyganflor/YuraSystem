<script>
    listar_proyecciones();

    function listar_proyecciones() {
        datos = {
            variedad: $('#filtro_predeterminado_variedad').val(),
            tipo: $('#filtro_predeterminado_tipo').val(),
            desde: $('#filtro_predeterminado_desde').val(),
            hasta: $('#filtro_predeterminado_hasta').val(),
        };
        if (datos['variedad'] != 'T')
            get_jquery('{{url('proy_cosecha/listar_proyecciones')}}', datos, function (retorno) {
                $('#div_listado_proyecciones').html(retorno);
            });
    }
</script>