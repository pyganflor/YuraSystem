<script>
    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            busqueda: $('#busqueda_tipo_iva').val().trim(),
            estado  : $("#estado").val()
        };
        $.get('{{url('tipo_iva/buscar')}}', datos, function (retorno) {
            $('#div_listado_tipo_iva').html(retorno);
            estructura_tabla('table_content_tipo_iva');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function add_tipo_iva(id_tipo_iva){
        $.LoadingOverlay('show');
        datos = {
            id_tipo_iva : id_tipo_iva
        };
        $.get('{{url('tipo_iva/add_tipo_iva')}}', datos, function (retorno) {
            modal_form('modal_add_tipo_iva', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir tipo de iva', true, false, '{{isPC() ? '60%' : ''}}', function () {
                tipo_iva_store();
            });
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function tipo_iva_store() {
        if ($('#form_add_tipo_iva').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token      : '{{csrf_token()}}',
                codigo      : $('#codigo').val(),
                porcentaje  : $('#porcentaje').val(),
                id_tipo_iva : $("#id_tipo_iva").val()
            };
            post_jquery('{{url('tipo_iva/store_tipo_iva')}}', datos, function () {
                cerrar_modals();
                buscar_listado();
            });
            $.LoadingOverlay('hide');
        }
    }

    function actualizar_estado_tipo_iva(id_tipo_iva,estado_tipo_iva) {
        mensaje = {
            title: estado_tipo_iva == 1 ? '<i class="fa fa-fw fa-trash"></i> Desactivar tipo de iva' : '<i class="fa fa-fw fa-unlock"></i> Activar tipo de iva',
            mensaje: estado_tipo_iva == 1 ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar este tipo de iva?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar este tipo de iva?</div>',
        };
        modal_quest('modal_actualizar_tipo_iva', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_tipo_iva: id_tipo_iva
            };
            $.LoadingOverlay('show');
            $.post('{{url('tipo_iva/actualizar_estado_tipo_iva')}}', datos, function (retorno) {
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
