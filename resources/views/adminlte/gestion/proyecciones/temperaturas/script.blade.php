<script>
    listar_temperaturas();

    function add_temperatura() {
        get_jquery('{{url('temperaturas/add_temperatura')}}', {}, function (retorno) {
            modal_view('modal-view_add_temperatura', retorno, '<i class="fa fa-fw fa-plus"></i> Ingresar datos', true, false, '{{isPC() ? '50%' : ''}}')
        });
    }

    function listar_temperaturas() {
        datos = {
            desde: $('#filtro_desde').val(),
            hasta: $('#filtro_hasta').val(),
        };
        get_jquery('{{url('temperaturas/listar_temperaturas')}}', datos, function (retorno) {
            $('#div_listado_ciclos').html(retorno);
        }, 'div_listado_ciclos');
    }
</script>