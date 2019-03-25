<script>
    function select_grupo_menu(g) {
        $.LoadingOverlay('show');
        datos = {
            id_grupo_menu: g
        };
        $.get('{{url('menu_sistema/select_grupo_menu')}}', datos, function (retorno) {
            $('#div_content_menus').html(retorno);
            $('.row_submenu').remove();
            $('.icon_hidden_g').addClass('hidden');
            $('#icon_grupo_menu_' + g).removeClass('hidden');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
        $.LoadingOverlay('hide');
    }

    function select_menu(m) {
        $.LoadingOverlay('show');
        datos = {
            id_menu: m
        };
        $.get('{{url('menu_sistema/select_menu')}}', datos, function (retorno) {
            $('#div_content_submenus').html(retorno);
            $('.icon_hidden_m').addClass('hidden');
            $('#icon_menu_' + m).removeClass('hidden');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
        $.LoadingOverlay('hide');
    }

    function listar_menus_x_grupo(g) {
        $.LoadingOverlay('show');
        datos = {
            id_grupo_menu: g,
        };
        $.get('{{url('menu_sistema/listar_menus_x_grupo')}}', datos, function (retorno) {
            $('#div_input_menus').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
        $.LoadingOverlay('hide');
    }

    /* ===================================================== */

    function add_grupo_menu() {
        $.LoadingOverlay('show');
        $.get('{{url('menu_sistema/add_grupo_menu')}}', {}, function (retorno) {
            modal_form('modal_add_grupo_menu', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir Grupo de Menú', true, false, '{{isPC() ? '35%' : ''}}', function () {
                store_grupo_menu();
            });
        });
        $.LoadingOverlay('hide');
    }

    function store_grupo_menu() {
        if ($('#form_add_grupo_menu').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                nombre: $('#nombre').val().toUpperCase(),
            };
            post_jquery('{{url('menu_sistema/store_grupo_menu')}}', datos, function () {
                for (i = 0; i < arreglo_modals_form.length; i++) {
                    arreglo_modals_form[i].close();
                }
                arreglo_modals_form = [];
                location.reload();
            });
            $.LoadingOverlay('hide');
        }
    }

    function add_menu() {
        $.LoadingOverlay('show');
        $.get('{{url('menu_sistema/add_menu')}}', {}, function (retorno) {
            modal_form('modal_add_menu', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir Menú', true, false, '{{isPC() ? '75%' : ''}}', function () {
                store_menu();
            });
        });
        $.LoadingOverlay('hide');
    }

    function store_menu() {
        if ($('#form_add_menu').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                nombre: $('#nombre').val(),
                id_grupo_menu: $('#id_grupo_menu').val(),
                id_icono: $('#id_icono').val(),
            };
            post_jquery('{{url('menu_sistema/store_menu')}}', datos, function () {
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
        $.get('{{url('menu_sistema/add_submenu')}}', {}, function (retorno) {
            modal_form('modal_add_submenu', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir Submenú', true, false, '{{isPC() ? '55%' : ''}}', function () {
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
                nombre: $('#nombre').val(),
                id_menu: $('#id_menu').val(),
                url: $('#url').val(),
                tipo: $('#tipo').val(),
            };
            post_jquery('{{url('menu_sistema/store_submenu')}}', datos, function () {
                cerrar_modals();
                location.reload();
            });
            $.LoadingOverlay('hide');
        }
    }

    /* ===================================================== */

    function edit_grupo_menu(g) {
        $.LoadingOverlay('show');
        datos = {
            id_grupo_menu: g
        };
        $.get('{{url('menu_sistema/edit_grupo_menu')}}', datos, function (retorno) {
            modal_form('modal_edit_grupo_menu', retorno, '<i class="fa fa-fw fa-plus"></i> Editar Grupo de Menú', true, false, '{{isPC() ? '35%' : ''}}', function () {
                update_grupo_menu();
            });
        });
        $.LoadingOverlay('hide');
    }

    function update_grupo_menu() {
        if ($('#form_edit_grupo_menu').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                nombre: $('#nombre').val().toUpperCase(),
                id_grupo_menu: $('#id_grupo_menu').val(),
            };
            post_jquery('{{url('menu_sistema/update_grupo_menu')}}', datos, function () {
                for (i = 0; i < arreglo_modals_form.length; i++) {
                    arreglo_modals_form[i].close();
                }
                arreglo_modals_form = [];
                location.reload();
            });
            $.LoadingOverlay('hide');
        }
    }

    function cambiar_estado_grupo_menu(g, estado) {
        mensaje = {
            title: estado == 'A' ? '<i class="fa fa-fw fa-trash"></i> Desactivar grupo' : '<i class="fa fa-fw fa-unlock"></i> Activar grupo',
            mensaje: estado == 'A' ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar este grupo?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar este grupo?</div>',
        };
        modal_quest('modal_delete_grupo_menu', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_grupo_menu: g,
                estado: estado == 'A' ? 'I' : 'A',
            };
            post_jquery('{{url('menu_sistema/cambiar_estado_grupo_menu')}}', datos, function () {
                for (i = 0; i < arreglo_modals_form.length; i++) {
                    arreglo_modals_form[i].close();
                }
                arreglo_modals_form = [];
                location.reload();
            });
        });
    }

    function edit_menu(m) {
        $.LoadingOverlay('show');
        datos = {
            id_menu: m
        };
        $.get('{{url('menu_sistema/edit_menu')}}', datos, function (retorno) {
            modal_form('modal_edit_menu', retorno, '<i class="fa fa-fw fa-plus"></i> Editar menú', true, false, '{{isPC() ? '75%' : ''}}', function () {
                update_menu();
            });
        });
        $.LoadingOverlay('hide');
    }

    function update_menu() {
        if ($('#form_edit_menu').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                nombre: $('#nombre').val(),
                id_grupo_menu: $('#id_grupo_menu').val(),
                id_menu: $('#id_menu').val(),
                id_icono: $('#id_icono').val(),
            };
            post_jquery('{{url('menu_sistema/update_menu')}}', datos, function () {
                for (i = 0; i < arreglo_modals_form.length; i++) {
                    arreglo_modals_form[i].close();
                }
                arreglo_modals_form = [];
                location.reload();
            });
            $.LoadingOverlay('hide');
        }
    }

    function cambiar_estado_menu(m, estado) {
        mensaje = {
            title: estado == 'A' ? '<i class="fa fa-fw fa-trash"></i> Desactivar menú' : '<i class="fa fa-fw fa-unlock"></i> Activar menú',
            mensaje: estado == 'A' ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar este menú?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar este menú?</div>',
        };
        modal_quest('modal_delete_menu', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_menu: m,
                estado: estado == 'A' ? 'I' : 'A',
            };
            post_jquery('{{url('menu_sistema/cambiar_estado_menu')}}', datos, function () {
                for (i = 0; i < arreglo_modals_form.length; i++) {
                    arreglo_modals_form[i].close();
                }
                arreglo_modals_form = [];
                location.reload();
            });
        });
    }

    function edit_submenu(s) {
        $.LoadingOverlay('show');
        datos = {
            id_submenu: s
        };
        $.get('{{url('menu_sistema/edit_submenu')}}', datos, function (retorno) {
            modal_form('modal_edit_submenu', retorno, '<i class="fa fa-fw fa-plus"></i> Editar submenú', true, false, '{{isPC() ? '55%' : ''}}', function () {
                update_submenu();
            });
        });
        $.LoadingOverlay('hide');
    }

    function update_submenu() {
        if ($('#form_edit_submenu').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                nombre: $('#nombre').val(),
                url: $('#url').val(),
                tipo: $('#tipo').val(),
                id_submenu: $('#id_submenu').val(),
                id_menu: $('#id_menu').val(),
            };
            post_jquery('{{url('menu_sistema/update_submenu')}}', datos, function () {
                for (i = 0; i < arreglo_modals_form.length; i++) {
                    arreglo_modals_form[i].close();
                }
                arreglo_modals_form = [];
                location.reload();
            });
            $.LoadingOverlay('hide');
        }
    }

    function cambiar_estado_submenu(s, estado) {
        mensaje = {
            title: estado == 'A' ? '<i class="fa fa-fw fa-trash"></i> Desactivar submenú' : '<i class="fa fa-fw fa-unlock"></i> Activar submenú',
            mensaje: estado == 'A' ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar este submenú?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar este submenú?</div>',
        };
        modal_quest('modal_delete_submenu', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_submenu: s,
                estado: estado == 'A' ? 'I' : 'A',
            };
            post_jquery('{{url('menu_sistema/cambiar_estado_submenu')}}', datos, function () {
                for (i = 0; i < arreglo_modals_form.length; i++) {
                    arreglo_modals_form[i].close();
                }
                arreglo_modals_form = [];
                location.reload();
            });
        });
    }

    function seleccionar_icono(id_icono) {
        $('#id_icono').val(id_icono);
    }
</script>