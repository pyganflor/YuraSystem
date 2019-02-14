<script>
    function select_rol(id) {
        $.LoadingOverlay('show');
        datos = {
            id_rol: id
        };
        $.get('{{url('permisos/select_rol/submenus')}}', datos, function (retorno) {
            $('#div_content_submenus').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
        $.LoadingOverlay('show');
        $.get('{{url('permisos/select_rol/usuarios')}}', datos, function (retorno) {
            $('#div_content_usuarios').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
        $.LoadingOverlay('hide');

        $('.icon_hidden_r').addClass('hidden');
        $('#icon_rol_' + id).removeClass('hidden');

        $('#rol_selected').val(id);
        $('#texto_seleccionar_rol').hide();
    }

    function listar_menus_x_grupo(g) {
        $.LoadingOverlay('show');
        datos = {
            id_grupo_menu: g,
        };
        $.get('{{url('permisos/listar_menus_x_grupo')}}', datos, function (retorno) {
            $('#div_input_menus').html(retorno);

            $('.options_submenu').remove();
        }).always(function () {
            $.LoadingOverlay('hide');
        });
        $.LoadingOverlay('hide');
    }

    function listar_submenus_x_menu(id) {
        $.LoadingOverlay('show');
        datos = {
            id_menu: id,
            id_rol: $('#rol_selected').val(),
        };
        $.get('{{url('permisos/listar_submenus_x_menu')}}', datos, function (retorno) {
            $('#div_input_submenus').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
        $.LoadingOverlay('hide');
    }


    /* ====================================================== */
    function add_rol() {
        $.LoadingOverlay('show');
        $.get('{{url('permisos/add_rol')}}', {}, function (retorno) {
            modal_form('modal_add_rol', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir Rol', true, false, '{{isPC() ? '60%' : ''}}', function () {
                store_rol();
            });
        });
        $.LoadingOverlay('hide');
    }

    function store_rol() {
        if ($('#form_add_rol').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                nombre: $('#nombre').val(),
            };
            post_jquery('{{url('permisos/store_rol')}}', datos, function () {
                for (i = 0; i < arreglo_modals_form.length; i++) {
                    arreglo_modals_form[i].close();
                }
                arreglo_modals_form = [];
                location.reload();
            });
            $.LoadingOverlay('hide');
        }
    }

    function add_submenu() {
        $.LoadingOverlay('show');
        $.get('{{url('permisos/add_submenu')}}', {}, function (retorno) {
            modal_form('modal_add_submenu', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir submenú', true, false, '{{isPC() ? '60%' : ''}}', function () {
                store_submenu();
            });
        });
        $.LoadingOverlay('hide');
    }

    function store_submenu() {
        if ($('#form_add_submenu').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                id_submenu: $('#id_submenu').val(),
                id_rol: $('#rol_selected').val(),
            };
            post_jquery('{{url('permisos/store_submenu')}}', datos, function () {
                for (i = 0; i < arreglo_modals_form.length; i++) {
                    arreglo_modals_form[i].close();
                }
                arreglo_modals_form = [];
                location.reload();
            });
            $.LoadingOverlay('hide');
        }
    }

    function add_usuario() {
        $.LoadingOverlay('show');
        datos = {
            id_rol: $('#rol_selected').val()
        };
        $.get('{{url('permisos/add_usuario')}}', datos, function (retorno) {
            modal_form('modal_add_usuario', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir usuario', true, false, '{{isPC() ? '60%' : ''}}', function () {
                store_usuario();
            });
        });
        $.LoadingOverlay('hide');
    }

    function store_usuario() {
        if ($('#form_add_usuario').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                id_usuario: $('#id_usuario').val(),
                id_rol: $('#rol_selected').val(),
            };
            post_jquery('{{url('permisos/store_usuario')}}', datos, function () {
                for (i = 0; i < arreglo_modals_form.length; i++) {
                    arreglo_modals_form[i].close();
                }
                arreglo_modals_form = [];
                location.reload();
            });
            $.LoadingOverlay('hide');
        }
    }

    /* ====================================================== */

    function cambiar_estado_rol(r, estado) {
        mensaje = {
            title: estado == 'A' ? '<i class="fa fa-fw fa-trash"></i> Desactivar rol' : '<i class="fa fa-fw fa-unlock"></i> Activar rol',
            mensaje: estado == 'A' ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar este rol?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar este rol?</div>',
        };
        modal_quest('modal_delete_rol', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_rol: r,
                estado: estado == 'A' ? 'I' : 'A',
            };
            post_jquery('{{url('permisos/cambiar_estado_rol')}}', datos, function () {
                for (i = 0; i < arreglo_modals_form.length; i++) {
                    arreglo_modals_form[i].close();
                }
                arreglo_modals_form = [];
                location.reload();
            });
        });
    }

    function cambiar_estado_rol_submenu(rs, estado) {
        mensaje = {
            title: estado == 'A' ? '<i class="fa fa-fw fa-trash"></i> Desactivar submenú' : '<i class="fa fa-fw fa-unlock"></i> Activar submenú',
            mensaje: estado == 'A' ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar este submenú?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar este submenú?</div>',
        };
        modal_quest('modal_delete_submenu', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_rol_submenu: rs,
                estado: estado == 'A' ? 'I' : 'A',
            };
            post_jquery('{{url('permisos/cambiar_estado_rol_submenu')}}', datos, function () {
                for (i = 0; i < arreglo_modals_form.length; i++) {
                    arreglo_modals_form[i].close();
                }
                arreglo_modals_form = [];
                location.reload();
            });
        });
    }

</script>