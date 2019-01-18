<script>
    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            busqueda: $('#busqueda_comprobantes').val().trim(),
            estado  : $("#estado").val()
        };
        $.get('{{url('tipo_comprobante/buscar')}}', datos, function (retorno) {
            $('#div_listado_comprobantes').html(retorno);
            estructura_tabla('table_content_comprobantes');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function add_tipo_comprobante(id_comprobante){
        $.LoadingOverlay('show');
        datos = {
            id_comprobante : id_comprobante
        };
        $.get('{{url('tipo_comprobante/add_tipo_comprobantes')}}', datos, function (retorno) {
            modal_form('modal_add_comprobante', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir comprobante', true, false, '{{isPC() ? '60%' : ''}}', function () {
                tipo_comprobantes_store();
            });
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function tipo_comprobantes_store() {
        if ($('#form_add_comprobante').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token         : '{{csrf_token()}}',
                nombre         : $('#nombre').val(),
                codigo         : $('#codigo').val(),
                id_comprobante : $("#id_comprobante").val()
            };
            post_jquery('{{url('tipo_comprobante/store_tipo_comprobantes')}}', datos, function () {
                cerrar_modals();
                buscar_listado();
            });
            $.LoadingOverlay('hide');
        }
    }

    function actualizar_estado_tipo_comprobante(id_comprobante,estado_comprobante) {
        mensaje = {
            title: estado_comprobante == 1 ? '<i class="fa fa-fw fa-trash"></i> Desactivar comprobante' : '<i class="fa fa-fw fa-unlock"></i> Activar comprobante',
            mensaje: estado_comprobante == 1 ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar este comprobante?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar este comprobante?</div>',
        };
        modal_quest('modal_actualizar_estado_comprobante', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_comprobante: id_comprobante
            };
            $.LoadingOverlay('show');
            $.post('{{url('tipo_comprobante/actualizar_estado_tipo_comprobantes')}}', datos, function (retorno) {
                if (retorno.success) {
                    cerrar_modals();
                    buscar_listado();
                } else {
                    alerta(retorno.mensaje);
                }
            }, 'json').fail(function (retorno) {
                alerta(retorno.responseText);
                alerta('Ha ocurrido un problema al cambiar el estado de la agencia de carga');
            }).always(function () {
                $.LoadingOverlay('hide');
            })
        });
    }
</script>
