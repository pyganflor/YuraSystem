<form id="form_add_usuario" action="{{url('usuarios/store')}}" method="post">
    {!! csrf_field() !!}
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="nombre_completo">Nombre completo</label>
                <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" required
                       maxlength="250"
                       autocomplete="off" placeholder="Escriba el nombre completo">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="username">Nombre de usuario</label>
                <input type="text" id="username" name="username" class="form-control" required maxlength="250"
                       autocomplete="off"
                       placeholder="Escriba el nombre de usuario">
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" class="form-control" required maxlength="250"
                       autocomplete="off"
                       placeholder="info@yura.com">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <label for="password">Contraseña</label>
            <div class="form-group input-group has-feedback" style="width: 100%">
                    <span class="input-group-btn">
                        <button type="button" class="btn text-black" title="Mostrar/ocultar"
                                id="btn_mostrar_ocultar_reg"
                                onclick="mostrar_ocultar_passw()">
                            <i class="fa fa-fw fa-eye"></i>
                        </button>
                    </span>
                <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña"
                       required
                       maxlength="250" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
        </div>
        <div class="col-md-6">
            <label for="password_b">Repite la contraseña</label>
            <div class="form-group has-feedback">
                <input type="password" name="password_b" id="password_b" class="form-control"
                       placeholder="Repite la contraseña"
                       required maxlength="250">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="id_rol">Rol</label>
                <select name="id_rol" id="id_rol" class="form-control" required>
                    <option value="">Seleccione un rol</option>
                    @foreach($roles as $item)
                        <option value="{{$item->id_rol}}">{{$item->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="imagen_perfil">Imagen de perfil</label>
                <input type="file" class="form-control file" id="imagen_perfil" name="imagen_perfil"
                       accept="image/jpeg">
            </div>
        </div>
    </div>
    <input type="hidden" name="h_clave" id="h_clave" value="">

</form>

<script>
    inicializa('file');
    $('.fileinput-cancel').hide();

    $('#form_add_usuario').validate({
        messages: {
            nombre_completo: {
                pattern: 'Formato incorrecto para un nombre'
            },
            password: {
                pattern: 'Al menos un número, una mayúscula y una minúscula. Más de 6 caracteres'
            }
        }
    });

    function store_usuario() {
        if ($('#form_add_usuario').valid()) {
            $.LoadingOverlay('show');
            passw = {
                password: $('#password').val(),
                password_b: $('#password_b').val(),
            };
            if (passw['password'] == passw['password_b']) {
                var password = $('#password');
                var password_b = $('#password_b');
                var h_clave = $('#h_clave');

                var publickey = "{{$key}}";
                var rsakey = new RSAKey();
                rsakey.setPublic(publickey, "10001");

                h_clave.val(rsakey.encrypt(password.val()));
                password.val('');
                password_b.val('');

                formulario = $('#form_add_usuario');
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
                                cerrar_modals();
                                location.reload();
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
            } else {
                alerta('<p class="text-center">Las contraseñas deben ser iguales</p>');
            }
            $.LoadingOverlay('hide');
        }
    }

</script>