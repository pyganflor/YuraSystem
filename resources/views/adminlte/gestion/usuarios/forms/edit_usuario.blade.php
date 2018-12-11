<form id="form_edit_usuario">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="nombre_completo">Nombre completo</label>
                <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" required maxlength="250"
                       autocomplete="off" placeholder="Escriba el nombre completo" value="{{$usuario->nombre_completo}}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group has-feedback">
                <label for="username">Nombre de usuario</label>
                <input type="text" id="username" name="username" class="form-control" required maxlength="250" autocomplete="off"
                       placeholder="Escriba el nombre de usuario" value="{{$usuario->username}}">
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="id_rol">Rol</label>
                <select name="id_rol" id="id_rol" class="form-control" required>
                    <option value="">Seleccione un rol</option>
                    @foreach($roles as $item)
                        <option value="{{$item->id_rol}}"
                                {{$usuario->id_rol == $item->id_rol ? 'selected' : ''}}>{{$item->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group has-feedback">
                <label for="correo">Correo</label>
                <input type="email" id="correo" name="correo" class="form-control" required maxlength="250" autocomplete="off"
                       placeholder="info@yura.com" value="{{$usuario->correo}}">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-md-offset-9">
            <button type="button" class="btn btn-success btn-block" onclick="update_usuario()">
                <i class="fa fa-fw fa-save"></i> Guardar
            </button>
        </div>
    </div>
</form>

<script>
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
            $.post('{{url('usuarios/update_usuario')}}', datos, function (retorno) {
                if (retorno.success) {
                    alerta_accion(retorno.mensaje, function () {
                        for (i = 0; i < arreglo_modals_form.length; i++) {
                            arreglo_modals_form[i].close();
                        }
                        arreglo_modals_form = [];
                        buscar_listado();
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

</script>