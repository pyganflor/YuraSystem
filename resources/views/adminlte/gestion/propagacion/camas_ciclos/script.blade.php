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

    function crear_ciclo(cama) {
        datos = {
            cama: cama,
            variedad: $('#variedad_' + cama).val(),
            fecha_inicio: $('#fecha_inicio_' + cama).val(),
            fecha_fin: $('#fecha_fin_' + cama).val(),
            esq_planta: $('#esq_planta_' + cama).val(),
        };
        if (datos['fecha_fin'] >= datos['fecha_inicio']) {
            $.LoadingOverlay('show');
            $.get('{{url('camas_ciclos/crear_ciclo')}}', datos, function (retorno) {
                modal_view('modal-view_crear_ciclo', retorno, '<i class="fa fa-fw fa-plsu"></i> Crear ciclo', true, false, '65%');
            }).always(function () {
                $.LoadingOverlay('hide');
            })
        } else {
            alerta('<div class="alert alert-warning text-center">La fecha fin debe ser mayor o igual que la fecha de inicio</div>')
        }
    }

    function calcular_totales_ciclo() {
        ids_contenedor = $('.ids_contenedor');
        total_contenedores = 0;
        total_plantas = 0;
        for (i = 0; i < ids_contenedor.length; i++) {
            id = ids_contenedor[i].value;
            cantidad = parseInt($('#cantidad_' + id).val());
            cantidad_plantas = cantidad * $('#cantidad_x_unidad_' + id).val();
            if (cantidad > 0) {
                total_contenedores += cantidad;
                total_plantas += cantidad_plantas;
                $('#td_total_' + id).html(cantidad_plantas);
            }
        }
        $('#th_total_contenedores').html(total_contenedores);
        $('#th_total_plantas').html(total_plantas);
    }

    function store_ciclo() {
        ids_contenedor = $('.ids_contenedor');
        contenedores = [];
        for (i = 0; i < ids_contenedor.length; i++) {
            id = ids_contenedor[i].value;
            cantidad = parseInt($('#cantidad_' + id).val());
            if (cantidad > 0)
                contenedores.push({
                    id: id,
                    cantidad: cantidad,
                });
        }
        datos = {
            _token: '{{csrf_token()}}',
            cama: $('#id_cama').val(),
            variedad: $('#id_variedad').val(),
            fecha_inicio: $('#fecha_inicio').val(),
            fecha_fin: $('#fecha_fin').val(),
            esq_planta: $('#esq_planta').val(),
            contenedores: contenedores,
        };
        $.LoadingOverlay('show');
        $.post('{{url('camas_ciclos/store_ciclo')}}', datos, function (retorno) {
            alerta_accion(retorno.mensaje, function () {
                cerrar_modals();
            });
            if (retorno.success) {
                //$('#activo_ciclos').val(1)
                listar_ciclos();
            }
        }, 'json').fail(function (retorno) {
            console.log(retorno);
            alerta_errores(retorno.responseText);
        }).always(function () {
            $.LoadingOverlay('hide');
        })
    }
</script>