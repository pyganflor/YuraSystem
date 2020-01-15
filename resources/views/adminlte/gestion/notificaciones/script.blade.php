<script>
    estructura_tabla('table_listado_notificaciones');

    var new_not_count = 0;

    function add_notificacion() {
        new_not_count++;
        $('#table_listado_notificaciones').append('<tr id="row_new_' + new_not_count + '">' +
            '<td class="text-center" style="border-color: #9d9d9d">' +
            '<input type="text" class="text-center" id="new_nombre_not_' + new_not_count + '" style="width: 100%">' +
            '</td>' +
            '<td class="text-center" style="border-color: #9d9d9d">' +
            '<select id="new_tipo_not_' + new_not_count + '" style="width: 100%">' +
            '<option value="S">Sistema</option>' +
            '<option value="M">Mensaje</option>' +
            '</select>' +
            '</td>' +
            '<td class="text-center" style="border-color: #9d9d9d">' +
            '<select id="new_icon_not_' + new_not_count + '" style="width: 100%">' +
            $('#tipo_not').html() +
            '</select>' +
            '</td>' +
            '<td class="text-center" style="border-color: #9d9d9d">' +
            '<button class="btn btn-xs btn-success" onclick="store_notificacion(new_not_count)" title="Guardar notificación" ' +
            'id="btn_store_' + new_not_count + '">' +
            '<i class="fa fa-fw fa-save"></i>' +
            '</button>' +
            '</td>' +
            '</tr>');
    }

    function store_notificacion(pos) {
        datos = {
            _token: '{{csrf_token()}}',
            nombre: $('#new_nombre_not_' + pos).val(),
            tipo: $('#new_tipo_not_' + pos).val(),
            icon: $('#new_icon_not_' + pos).val(),
        };
        post_jquery('{{url('notificaciones/store_notificacion')}}', datos, function () {
            cerrar_modals();
            $('#btn_store_' + pos).hide();
        });
    }

    function update_notificacion(not) {
        datos = {
            _token: '{{csrf_token()}}',
            id: not,
            nombre: $('#nombre_not_' + not).val(),
            tipo: $('#tipo_not_' + not).val(),
            icon: $('#icon_not_' + not).val(),
        };
        post_jquery('{{url('notificaciones/update_notificacion')}}', datos, function () {
            cerrar_modals();
        });
    }

    function cambiar_estado(not) {
        datos = {
            _token: '{{csrf_token()}}',
            id: not,
        };
        post_jquery('{{url('notificaciones/cambiar_estado')}}', datos, function () {
            cerrar_modals();
            location.reload();
        });
    }

    function save_notificacion_usuario(user, not) {
        datos = {
            _token: '{{csrf_token()}}',
            not: not,
            user: user,
        };
        post_jquery('{{url('notificaciones/save_notificacion_usuario')}}', datos, function () {
        });
    }

    function admin_usuarios(not) {
        datos = {
            id: not
        };
        get_jquery('{{url('notificaciones/admin_usuarios')}}', datos, function (retorno) {
            modal_view('modal-view_admin_usuarios', retorno, '<i class="fa fa-fw fa-users"></i> Usuarios', true, false, '{{isPC() ? '65%' : ''}}');
        });
    }
</script>