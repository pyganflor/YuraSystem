<script>
    listar_proyecciones();

    function listar_proyecciones() {
        datos = {
            area: $('#area_trabajo').val(),
            desde: $('#filtro_desde').val(),
            hasta: $('#filtro_hasta').val(),
        };
        get_jquery('{{url('proy_mano_obra/listar_proyecciones')}}', datos, function (retorno) {
            $('#div_listado_proyecciones').html(retorno);
        })
    }

    function mouse_over_celda(id, action) {
        $('.celda_hovered').css('border', '1px solid #9d9d9d');
        if (action == 1) {  // over
            $('#' + id).css('border', '3px solid black');
        }
    }
</script>