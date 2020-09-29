<script>
    listar_camas();
    listar_ciclos();

    /* ================== CAMAS ================== */
    function listar_camas() {
        datos = {};
        $.LoadingOverlay('show');
        $.get('{{url('camas_ciclos/listar_camas')}}', datos, function (retorno) {
            $('#listado_camas').html(retorno);
            estructura_tabla('table_camas', false, true);
            //$('#table_camas_filter').remove();
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function add_cama() {
        datos = {};
        $.LoadingOverlay('show');
        $.get('{{url('camas_ciclos/add_cama')}}', datos, function (retorno) {
            $('#div_form_add_cama').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function edit_cama(id) {
        datos = {
            id: id
        };
        $.LoadingOverlay('show');
        $.get('{{url('camas_ciclos/edit_cama')}}', datos, function (retorno) {
            $('#div_form_add_cama').html(retorno);
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }

    function eliminar_cama(id) {
        modal_quest('modal_quest-eliminar_cama',
            '<div class="alert alert-warning text-center">¿Desea activar/desactivar esta cama?</div>',
            '<i class="fa fa-fw fa-exclamation-triangle"></i> Pregunta de alerta', true, false, '50%', function () {
                cerrar_modals();
                datos = {
                    _token: '{{csrf_token()}}',
                    id_cama: id
                };
                $.LoadingOverlay('show');
                $.post('{{url('camas_ciclos/eliminar_cama')}}', datos, function (retorno) {
                    alerta(retorno.mensaje);
                    if (retorno.success) {
                        listar_camas();
                    }
                }, 'json').fail(function (retorno) {
                    console.log(retorno);
                    alerta_errores(retorno.responseText);
                }).always(function () {
                    $.LoadingOverlay('hide');
                })
            });
    }

    function store_cama() {
        datos = {
            _token: '{{csrf_token()}}',
            area_trabajo: $('#area_trabajo').val(),
            nombre: $('#nombre_cama').val(),
        };
        $.LoadingOverlay('show');
        $.post('{{url('camas_ciclos/store_cama')}}', datos, function (retorno) {
            alerta(retorno.mensaje);
            if (retorno.success) {
                listar_camas();
                add_cama();
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $.LoadingOverlay('hide');
        })
    }

    function update_cama(id) {
        datos = {
            _token: '{{csrf_token()}}',
            id_cama: id,
            area_trabajo: $('#area_trabajo').val(),
            nombre: $('#nombre_cama').val(),
        };
        $.LoadingOverlay('show');
        $.post('{{url('camas_ciclos/update_cama')}}', datos, function (retorno) {
            alerta(retorno.mensaje);
            if (retorno.success) {
                listar_camas();
                add_cama();
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $.LoadingOverlay('hide');
        })
    }

    /* ================== CICLOS ================== */
    function listar_ciclos() {
        datos = {
            variedad: $('#variedad_ciclos').val(),
            activo: $('#activo_ciclos').val(),
        };
        $.LoadingOverlay('show');
        $.get('{{url('camas_ciclos/listar_ciclos')}}', datos, function (retorno) {
            $('#div_gestion_ciclos').html(retorno);
            estructura_tabla('table_ciclos', false, true);
            //$('#table_camas_filter').remove();
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }
</script>