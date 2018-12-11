<form id="form_edit_password">
    <div class="row">
        <div class="col-md-6">
            <label for="password">Contraseña</label>
            <div class="form-group input-group has-feedback" style="width: 100%">
                    <span class="input-group-btn">
                        <button type="button" class="btn text-black" title="Mostrar/ocultar" id="btn_mostrar_ocultar_reg"
                                onclick="mostrar_ocultar_passw()">
                            <i class="fa fa-fw fa-eye"></i>
                        </button>
                    </span>
                <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required
                       maxlength="250" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
        </div>
        <div class="col-md-6">
            <label for="password_b">Repite la contraseña</label>
            <div class="form-group has-feedback">
                <input type="password" name="password_b" id="password_b" class="form-control" placeholder="Repite la contraseña"
                       required maxlength="250">
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
        </div>
    </div>
    <input type="hidden" name="h_clave" id="h_clave" value="">
    <div class="row">
        <div class="col-md-3 col-md-offset-9">
            <button type="button" class="btn btn-success btn-block" onclick="update_password()">
                <i class="fa fa-fw fa-save"></i> Guardar
            </button>
        </div>
    </div>
</form>

<script>
    $('#form_edit_password').validate({
        messages: {
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
                var password = $('#password');
                var password_b = $('#password_b');
                var h_clave = $('#h_clave');

                var publickey = "{{$key}}";
                var rsakey = new RSAKey();
                rsakey.setPublic(publickey, "10001");

                h_clave.val(rsakey.encrypt(password.val()));
                password.val('');
                password_b.val('');

                datos = {
                    _token: '{{csrf_token()}}',
                    id_usuario: '{{$usuario->id_usuario}}',
                    passw: $('#h_clave').val()
                };
                $.LoadingOverlay('show');
                post_jquery('{{url('usuarios/update_password')}}', datos, function () {
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
</script>