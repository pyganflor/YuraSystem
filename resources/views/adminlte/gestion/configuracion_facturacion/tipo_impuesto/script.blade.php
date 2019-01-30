<script>
    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            busqueda: $('#busqueda_tipo_impuesto').val().trim(),
            estado  : $("#estado").val(),
            codigo_impuesto : $("#codigo_impuesto").val()
        };
        $.get('{{url('tipo_impuesto/buscar')}}', datos, function (retorno) {
            $('#div_listado_tipo_impuesto').html(retorno);
            estructura_tabla('table_content_tipo_impuesto');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function add_tipo_impuesto(id_tipo_impuesto){
        $.LoadingOverlay('show');
        datos = {
            id_tipo_impuesto : id_tipo_impuesto
        };
        $.get('{{url('tipo_impuesto/add_tipo_impuesto')}}', datos, function (retorno) {
            modal_form('modal_add_tipo_impuesto', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir tipo de impuesto', true, false, '{{isPC() ? '60%' : ''}}', function () {
                tipo_impuesto_store();
            });
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function tipo_impuesto_store() {
        if ($('#form_add_tipo_impuesto').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token      : '{{csrf_token()}}',
                codigo      : $('#codigo').val(),
                porcentaje  : $('#porcentaje').val(),
                impuesto    : $('#impuesto').val(),
                descripcion : $('#descripcion').val(),
                id_tipo_impuesto : $("#id_tipo_impuesto").val()
            };
            post_jquery('{{url('tipo_impuesto/store_tipo_impuesto')}}', datos, function () {
                cerrar_modals();
                buscar_listado();
            });
            $.LoadingOverlay('hide');
        }
    }

    function actualizar_estado_tipo_impuesto(id_tipo_impuesto,estado_tipo_impuesto) {
        mensaje = {
            title: estado_tipo_impuesto == 1 ? '<i class="fa fa-fw fa-trash"></i> Desactivar tipo de impuesto' : '<i class="fa fa-fw fa-unlock"></i> Activar tipo de impuesto',
            mensaje: estado_tipo_impuesto == 1 ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar este tipo de impuesto?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar este tipo de impuesto?</div>',
        };
        modal_quest('modal_actualizar_tipo_impuesto', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_tipo_impuesto: id_tipo_impuesto
            };
            $.LoadingOverlay('show');
            $.post('{{url('tipo_impuesto/actualizar_estado_tipo_impuesto')}}', datos, function (retorno) {
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
