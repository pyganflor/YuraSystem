<script>
    inicializa('file');
    $('.fileinput-cancel').hide();

    function update_image_perfil() {
        $.LoadingOverlay('show');
        formulario = $('#form_edit_imagen_perfil');
        var formData = new FormData(formulario[0]);
        //hacemos la petición ajax
        $.ajax({
            url: formulario.attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            //necesario para subir archivos via ajax
            cache: false,
            contentType: false,
            processData: false,

            success: function (retorno2) {
                if (retorno2.success) {
                    alerta_accion(retorno2.mensaje, function () {
                        $.LoadingOverlay('show');
                        $.post('{{url('usuarios/get_usuario_json')}}', {
                            _token: '{{csrf_token()}}',
                            id_usuario: '{{$usuario->id_usuario}}'
                        }, function (retorno) {
                            $('#img_perfil').prop('src', '{{url('storage/imagenes')}}/' + retorno.user['imagen_perfil']);
                            $('#img_perfil_menu_izquierda').prop('src', '{{url('storage/imagenes')}}/' + retorno.user['imagen_perfil']);
                            $('#img_perfil_menu_superior').prop('src', '{{url('storage/imagenes')}}/' + retorno.user['imagen_perfil']);
                            $('#img_perfil_menu_superior_2').prop('src', '{{url('storage/imagenes')}}/' + retorno.user['imagen_perfil']);
                            $('#box-edit-image').toggleClass('hidden');
                        }, 'json').fail(function (retorno) {
                            console.log(retorno);
                            alerta_errores(retorno.responseText);
                            alert('Ha ocurrido un problema al obtener el cambio');
                        }).always(function () {
                            $.LoadingOverlay('hide');
                        });
                    });
                } else {
                    alerta(retorno2.mensaje);
                }
                $.LoadingOverlay('hide');
            },
            //si ha ocurrido un error
            error: function (retorno2) {
                console.log(retorno2);
                alerta(retorno2.responseText);
                alert('Hubo un problema en la envío de la información');
                $.LoadingOverlay('hide');
            }
        });
        $.LoadingOverlay('hide');
    }

    $('.tab-detalles').on('mouseover', function () {
        $(this).addClass('sombra_estandar');
    });
    $('.tab-detalles').on('mouseleave', function () {
        $(this).removeClass('sombra_estandar');
    });

    $('#form_edit_usuario').validate({
        messages: {
            nombre_completo: {
                pattern: 'Formato incorrecto para un nombre'
            },
        }
    });

    function update_usuario() {
        if ($('#form_edit_usuario').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                id_usuario: '{{$usuario->id_usuario}}',
                nombre_completo: $('#nombre_completo').val(),
                username: $('#username').val(),
                correo: $('#correo').val(),
                id_rol: $('#id_rol').val(),
            };
            $.post('{{url('perfil/update_usuario')}}', datos, function (retorno) {
                if (retorno.success) {
                    alerta_accion(retorno.mensaje, function () {
                        for (i = 0; i < arreglo_modals_form.length; i++) {
                            arreglo_modals_form[i].close();
                        }
                        arreglo_modals_form = [];
                        location.reload();
                    });
                } else {
                    alerta(retorno.mensaje);
                }
            }, 'json').fail(function (retorno) {
                console.log(retorno);
                alerta_errores(retorno.responseText);
                alert('Ha ocurrido un problema al enviar la información');
            }).always(function () {
                $.LoadingOverlay('hide');
            });
        }
    }

    $('#form_edit_password').validate({
        messages: {
            password_current: {
                pattern: 'Al menos un número, una mayúscula y una minúscula. Más de 6 caracteres'
            },
            password: {
                pattern: 'Al menos un número, una mayúscula y una minúscula. Más de 6 caracteres'
            }
        }
    });

    function update_password() {
        if ($('#form_edit_password').valid()) {
            passw = {
                password: $('#password').val(),
                password_b: $('#password_b').val(),
            };
            if (passw['password'] == passw['password_b']) {
                var passw_current = $('#password_current');
                var password = $('#password');
                var password_b = $('#password_b');
                var h_clave1 = $('#h_clave1');
                var h_clave = $('#h_clave');

                var publickey = "{{$key}}";
                var rsakey = new RSAKey();
                rsakey.setPublic(publickey, "10001");

                h_clave1.val(rsakey.encrypt(passw_current.val()));
                h_clave.val(rsakey.encrypt(password.val()));
                password.val('');
                password_b.val('');
                passw_current.val('');

                datos = {
                    _token: '{{csrf_token()}}',
                    id_usuario: '{{$usuario->id_usuario}}',
                    passw: $('#h_clave').val(),
                    passw_current: $('#h_clave1').val(),
                };
                $.LoadingOverlay('show');
                post_jquery('{{url('perfil/update_password')}}', datos, function () {
                    for (i = 0; i < arreglo_modals_form.length; i++) {
                        arreglo_modals_form[i].close();
                    }
                    arreglo_modals_form = [];
                });
                $.LoadingOverlay('hide');
            }
            else {
                alerta('<p class="text-center">Las contraseñas deben ser iguales</p>');
            }
        }
    }

    function mostrar_ocultar_passw(id) {
        if ($('#' + id).prop('type') == 'password') {
            $('#' + id).prop('type', 'text');
        } else {
            $('#' + id).prop('type', 'password');
        }
    }

    function seleccionar_submenu(submenu) {
        datos = {
            _token: '{{csrf_token()}}',
            submenu: submenu,
            icono: $('#id_icon_' + submenu).val(),
            check: $('#check_' + submenu).prop('checked') == true ? 1 : 0
        };
        $('#tr_submenu_' + submenu).LoadingOverlay('show');
        $.post('{{url('perfil/seleccionar_submenu')}}', datos, function (retorno) {
            if (!retorno.success)
                alerta(retorno.mensaje);
            else {
                $('.acceso_directo').remove();
                cargar_accesos_directos();
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $('#tr_submenu_' + submenu).LoadingOverlay('hide');
        });
    }

    function select_icono(icono, nombre, submenu) {
        $('#id_icon_' + submenu).val(icono);
        $('#icon_selected_' + submenu).html('<i class="fa fa-fw fa-' + nombre + '"></i>');
    }
</script>