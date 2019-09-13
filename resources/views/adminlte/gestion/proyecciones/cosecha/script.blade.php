<script>
    listar_proyecciones();

    function listar_proyecciones() {
        datos = {
            variedad: $('#filtro_predeterminado_variedad').val(),
            tipo: $('#filtro_predeterminado_tipo').val(),
            desde: $('#filtro_predeterminado_desde').val(),
            hasta: $('#filtro_predeterminado_hasta').val(),
            opcion: $('#filtro_predeterminado_opciones').val(),
            detalle: $('#filtro_predeterminado_detalle').val(),
        };
        if (datos['variedad'] != 'T')
            get_jquery('{{url('proy_cosecha/listar_proyecciones')}}', datos, function (retorno) {
                $('#div_listado_proyecciones').html(retorno);
            });
    }

    function select_celda(tipo, mod, sem, variedad) {
        if (['F', 'P', 'S', 'T', 'Y'].indexOf(tipo) >= 0) {
            datos = {
                tipo: tipo,
                modulo: mod,
                semana: sem,
                variedad: variedad,
            };
            get_jquery('{{url('proy_cosecha/select_celda')}}', datos, function (retorno) {
                modal_view('modal-view_select_celda', retorno, '<i class="fa fa-fw fa-tasks"></i> Proyección Cosecha', true, false,
                    '{{isPC() ? '50%' : ''}}');
            });
        }
    }

    function mouse_over_celda(id, action) {
        $('.celda_hovered').css('border', '1px solid #9d9d9d');
        if (action == 1) {  // over
            $('#' + id).css('border', '3px solid black');
        }
    }

</script>