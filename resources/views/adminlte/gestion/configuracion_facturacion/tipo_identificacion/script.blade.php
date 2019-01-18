<script>
    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            busqueda: $('#busqueda_tipo_identificacion').val().trim(),
            estado  : $("#estado").val()
        };
        $.get('{{url('tipo_identificacion/buscar')}}', datos, function (retorno) {
            $('#div_listado_tipo_identificacion').html(retorno);
            estructura_tabla('table_content_tipo_identificacion');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function add_tipo_identificacion(id_tipo_identificacion){
        $.LoadingOverlay('show');
        datos = {
            id_tipo_identificacion : id_tipo_identificacion
        };
        $.get('{{url('tipo_identificacion/add_tipo_identificacion')}}', datos, function (retorno) {
            modal_form('modal_add_tipo_identificacion', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir tipo de identificacion', true, false, '{{isPC() ? '60%' : ''}}', function () {
                tipo_identificacion_store();
            });
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function tipo_identificacion_store() {
        if ($('#form_add_tipo_identificacion').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token                 : '{{csrf_token()}}',
                nombre                 : $('#nombre').val(),
                codigo                 : $('#codigo').val(),
                id_tipo_identificacion : $("#id_tipo_identificacion").val()
            };
            post_jquery('{{url('tipo_identificacion/store_tipo_identificacion')}}', datos, function () {
                cerrar_modals();
                buscar_listado();
            });
            $.LoadingOverlay('hide');
        }
    }

    function actualizar_estado_tipo_identificacion(id_tipo_identificacion,estado_tipo_identificacion) {
        mensaje = {
            title: estado_tipo_identificacion == 1 ? '<i class="fa fa-fw fa-trash"></i> Desactivar tipo de identificación' : '<i class="fa fa-fw fa-unlock"></i> Activar tipo de identificación',
            mensaje: estado_tipo_identificacion == 1 ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar este tipo de identificación?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar este tipo de identificación?</div>',
        };
        modal_quest('modal_actualizar_tipo_identificacion', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_tipo_identificacion: id_tipo_identificacion
            };
            $.LoadingOverlay('show');
            $.post('{{url('tipo_identificacion/actualizar_estado_tipo_identificacion')}}', datos, function (retorno) {
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
