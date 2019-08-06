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

    function select_celda(tipo, mod, sem, model) {
        if (['F', 'P', 'S', 'Y'].indexOf(tipo) >= 0) {
            datos = {
                tipo: tipo,
                modulo: mod,
                semana: sem,
                model: model,
                variedad: $('#filtro_predeterminado_variedad').val(),
            };
            get_jquery('{{url('proy_cosecha/select_celda')}}', datos, function (retorno) {
                modal_view('modal-view_select_celda', retorno, '<i class="fa fa-fw fa-tasks"></i> Proyección Cosecha', true, false,
                    '{{isPC() ? '50%' : ''}}');
            });
        }
    }
</script>