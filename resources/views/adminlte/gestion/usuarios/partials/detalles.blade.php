<div class="row">
    <div class="col-md-3">
        <div class="box box-primary">
            <div class="box-body box-profile">
                <a href="javascript:void(0)" onclick="$('#box-edit-image').toggleClass('hidden')">
                    <img class="profile-user-img img-responsive img-circle" onmouseover="$(this).addClass('sombra_estandar')"
                         onmouseleave="$(this).removeClass('sombra_estandar')" id="img_perfil"
                         src="{{url('storage/imagenes').'/'.$usuario->imagen_perfil}}" alt="">
                </a>
                <h3 class="profile-username text-center">{{$usuario->nombre_completo}}</h3>

                <p class="text-muted text-center">{{$usuario->rol()->nombre}}</p>
            </div>
        </div>

        <div class="box box-primary hidden" id="box-edit-image">
            <div class="box-body">
                <form action="{{url('usuarios/update_image_perfil')}}" method="post" id="form_edit_imagen_perfil">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <div class="form-group">
                            <label for="imagen_perfil">Imagen de perfil</label>
                            <input type="file" class="form-control file" id="imagen_perfil" name="imagen_perfil" accept="image/jpeg">
                        </div>
                    </div>
                    <input type="hidden" id="id_usuario" name="id_usuario" value="{{$usuario->id_usuario}}">
                    <button type="button" class="btn btn-block btn-success" onclick="update_image_perfil()">
                        <i class="fa fa-fw fa-save"></i> Guardar
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active tab-detalles">
                    <a href="#datos_usuarios" data-toggle="tab">Detalles</a>
                </li>
                <li class="tab-detalles">
                    <a href="#seguridad" data-toggle="tab">Seguridad</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="datos_usuarios">
                    @include('adminlte.gestion.usuarios.forms.edit_usuario')
                </div>
                <div class="tab-pane" id="seguridad">
                    @include('adminlte.gestion.usuarios.forms.seguridad')
                </div>
            </div>
        </div>
    </div>
</div>

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
</script>