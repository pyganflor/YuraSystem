<script>
    listar_contenedores();

    function listar_contenedores() {
        datos = {};
        $.LoadingOverlay('show');
        $.get('{{url('propag_config/listar_contenedores')}}', datos, function (retorno) {
            $('#listado_contenedores').html(retorno);
            estructura_tabla('table_contenedores', false, true);
            //$('#table_contenedores_filter').remove();
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function add_contenedor() {
        datos = {};
        $.LoadingOverlay('show');
        $.get('{{url('propag_config/add_contenedor')}}', datos, function (retorno) {
            $('#div_form_add_contenedor').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function edit_contenedor(id) {
        datos = {
            id: id
        };
        $.LoadingOverlay('show');
        $.get('{{url('propag_config/edit_contenedor')}}', datos, function (retorno) {
            $('#div_form_add_contenedor').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function eliminar_contenedor(id) {
        modal_quest('modal_quest-eliminar_contenedor',
            '<div class="alert alert-warning text-center">¿Desea activar/desactivar este contenedor?</div>',
            '<i class="fa fa-fw fa-exclamation-triangle"></i> Pregunta de alerta', true, false, '50%', function () {
                cerrar_modals();
                datos = {
                    _token: '{{csrf_token()}}',
                    id_contenedor: id
                };
                $.LoadingOverlay('show');
                $.post('{{url('propag_config/eliminar_contenedor')}}', datos, function (retorno) {
                    alerta(retorno.mensaje);
                    if (retorno.success) {
                        listar_contenedores();
                    }
                }, 'json').fail(function (retorno) {
                    console.log(retorno);
                    alerta_errores(retorno.responseText);
                }).always(function () {
                    $.LoadingOverlay('hide');
                })
            });
    }

    function store_contenedor() {
        datos = {
            _token: '{{csrf_token()}}',
            cantidad: $('#cantidad').val(),
            nombre: $('#nombre').val(),
        };
        $.LoadingOverlay('show');
        $.post('{{url('propag_config/store_contenedor')}}', datos, function (retorno) {
            alerta(retorno.mensaje);
            if (retorno.success) {
                listar_contenedores();
                add_contenedor();
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $.LoadingOverlay('hide');
        })
    }

    function update_contenedor(id) {
        datos = {
            _token: '{{csrf_token()}}',
            id_contenedor: id,
            cantidad: $('#cantidad').val(),
            nombre: $('#nombre').val(),
        };
        $.LoadingOverlay('show');
        $.post('{{url('propag_config/update_contenedor')}}', datos, function (retorno) {
            alerta(retorno.mensaje);
            if (retorno.success) {
                listar_contenedores();
                add_contenedor();
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $.LoadingOverlay('hide');
        })
    }
</script>