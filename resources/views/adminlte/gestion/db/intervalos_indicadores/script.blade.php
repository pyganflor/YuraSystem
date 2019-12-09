<script>
    function add_intervalo(id_indicador) {
        datos = {
            id_indicador : id_indicador
        };
        $.LoadingOverlay('show');
        $.get('{{url('marcas/add')}}', datos, function (retorno) {
            modal_form('modal_add_intervalos_indicadores', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir intervalos', true, false, '{{isPC() ? '60%' : ''}}', function () {
                $.LoadingOverlay('hide');
            });
        });
        $.LoadingOverlay('hide');
    }
</script>
