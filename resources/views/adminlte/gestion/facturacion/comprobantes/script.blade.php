<script>
    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            busqueda: $('#busqueda_comprobantes').val().trim(),
        };
        $.get('{{url('comprobantes/buscar')}}', datos, function (retorno) {
            $('#div_listado_comprobantes').html(retorno);
            estructura_tabla('table_content_comprobantes');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function add_comprobante(id_comprobante){

        datos = {
            id_comprobante : id_comprobante
        };
        $.LoadingOverlay('show');
        $.get('{{url('comprabandtes/add')}}', datos, function (retorno) {
            modal_form('modal_add_comprobante', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir comprobante', true, false, '{{isPC() ? '60%' : ''}}', function () {
                    store_comprobante();
                $.LoadingOverlay('hide');
            });
        });
        $.LoadingOverlay('hide');
    }
</script>
