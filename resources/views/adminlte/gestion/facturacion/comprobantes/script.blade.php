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
        $.LoadingOverlay('show');
        datos = {
            id_comprobante : id_comprobante
        };
        $.get('{{url('comprobantes/add_comprobantes')}}', datos, function (retorno) {
            modal_form('modal_add_comprobante', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir comprobante', true, false, '{{isPC() ? '60%' : ''}}', function () {
                comprobantes_store();
            });
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }
    
    function comprobantes_store() {
        if ($('#form_add_cliente').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token     : '{{csrf_token()}}',
                marca      : $('#marca').val(),
                descripcion: $('#descripcion').val(),
                id_marca   : $('#id_marca').val()
            };
            post_jquery('{{url('marcas/store')}}', datos, function () {
                cerrar_modals();
                buscar_listado();
            });
            $.LoadingOverlay('hide');
        }
    }
</script>
