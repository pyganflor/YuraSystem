<script>
    buscar_listado();

    function buscar_listado() {
        $.LoadingOverlay('show');
        datos = {
            busqueda: $('#busqueda_usuarios').val().trim(),
        };
        $.get('{{url('usuarios/buscar')}}', datos, function (retorno) {
            $('#div_listado_usuarios').html(retorno);
            estructura_tabla('table_content_usuarios');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function exportar_usuarios() {
        $.LoadingOverlay('show');
        window.open('{{url('usuarios/exportar')}}' + '?busqueda=' + $('#busqueda_usuarios').val().trim(), '_blank');
        $.LoadingOverlay('hide');
    }

    function eliminar_usuario(id, estado) {
        mensaje = {
            title: estado == 'A' ? '<i class="fa fa-fw fa-trash"></i> Desactivar usuario' : '<i class="fa fa-fw fa-unlock"></i> Activar usuario',
            mensaje: estado == 'A' ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar este usuario?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar este usuario?</div>',
        };
        modal_quest('modal_delete_usuario', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_usuario: id
            };
            $.LoadingOverlay('show');
            $.post('{{url('usuarios/eliminar')}}', datos, function (retorno) {
                if (retorno.success) {
                    if (retorno.estado) {
                        $('#row_usuarios_' + id).removeClass('error');
                        $('#btn_usuarios_' + id).removeClass('btn-danger');
                        $('#btn_usuarios_' + id).addClass('btn-success');
                        $('#btn_usuarios_' + id).prop('title', 'Desactivar');
                        $('#icon_usuarios_' + id).removeClass('fa-unlock');
                        $('#icon_usuarios_' + id).addClass('fa-trash');
                    } else {
                        $('#row_usuarios_' + id).addClass('error');
                        $('#btn_usuarios_' + id).removeClass('btn-success');
                        $('#btn_usuarios_' + id).addClass('btn-danger');
                        $('#btn_usuarios_' + id).prop('title', 'Activar');
                        $('#icon_usuarios_' + id).removeClass('fa-trash');
                        $('#icon_usuarios_' + id).addClass('fa-unlock');
                    }
                    cerrar_modals();
                    location.reload();
                } else {
                    alerta(retorno.mensaje);
                }
            }, 'json').fail(function (retorno) {
                console.log(retorno);
                alerta(retorno.responseText);
                alerta('Ha ocurrido un problema al cambiar el estado del usuario');
            }).always(function () {
                $.LoadingOverlay('hide');
            })
        });
    }

    function add_usuario() {
        $.LoadingOverlay('show');
        $.get('{{url('usuarios/add')}}', {}, function (retorno) {
            modal_form('modal_add_usuario', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir usuario', true, false, '{{isPC() ? '60%' : ''}}', function () {
                store_usuario();
                $.LoadingOverlay('hide');
            });
        });
        $.LoadingOverlay('hide');
    }

    function mostrar_ocultar_passw() {
        if ($('#password').prop('type') == 'password') {
            $('#password').prop('type', 'text');
        } else {
            $('#password').prop('type', 'password');
        }
    }

    function ver_usuario(id_usuario) {
        $.LoadingOverlay('show');
        datos = {
            id_usuario: id_usuario
        };
        $.get('{{url('usuarios/ver_usuario')}}', datos, function (retorno) {
            modal_view('modal_view_usuario', retorno, '<i class="fa fa-fw fa-eye"></i> Detalles', true, false, '{{isPC() ? '75%' : ''}}');
            $.LoadingOverlay('hide');
        });
        $.LoadingOverlay('hide');
    }

</script>