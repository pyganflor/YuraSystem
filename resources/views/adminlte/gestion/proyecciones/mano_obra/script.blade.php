<script>
    function listar_proyecciones() {
        datos = {
            desde: $('#filtro_desde').val(),
            hasta: $('#filtro_hasta').val(),
        };
        get_jquery('{{url('proy_mano_obra/listar_proyecciones')}}', datos, function (retorno) {
            $('#div_listado_proyecciones').html(retorno);
        })
    }
</script>