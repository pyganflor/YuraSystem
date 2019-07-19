<script>
    function select_planta(p) {
        $.LoadingOverlay('show');
        datos = {
            id_planta: p
        };
        $.get('{{url('plantas_variedades/select_planta')}}', datos, function (retorno) {
            $('#div_content_menus').html(retorno);
            $('.row_variedad').remove();
            $('.icon_hidden_p').addClass('hidden');
            $('#icon_planta_' + p).removeClass('hidden');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
        $.LoadingOverlay('hide');
    }

    /* ===================================================== */

    function add_planta() {
        $.LoadingOverlay('show');
        $.get('{{url('plantas_variedades/add_planta')}}', {}, function (retorno) {
            modal_form('modal_add_planta', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir Planta', true, false, '{{isPC() ? '65%' : ''}}', function () {
                store_planta();
            });
        });
        $.LoadingOverlay('hide');
    }

    function store_planta() {
        if ($('#form_add_planta').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                nombre: $('#nombre').val(),
                tarifa: $("#tarifa").val(),
                nandina: $("#nandina").val(),
                siglas: $("#siglas").val(),
            };
            post_jquery('{{url('plantas_variedades/store_planta')}}', datos, function () {
                cerrar_modals();
                location.reload();
            });
            $.LoadingOverlay('hide');
        }
    }

    function add_variedad() {
        $.LoadingOverlay('show');
        $.get('{{url('plantas_variedades/add_variedad')}}', {}, function (retorno) {
            modal_form('modal_add_variedad', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir Variedad', true, false, '{{isPC() ? '75%' : ''}}', function () {
                store_variedad();
            });
        });
        $.LoadingOverlay('hide');
    }

    function store_variedad() {
        if ($('#form_add_variedad').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                nombre: $('#nombre').val(),
                id_planta: $('#id_planta').val(),
                unidad_medida: $('#unidad_medida').val(),
                minimo_apertura: $('#minimo_apertura').val(),
                maximo_apertura: $('#maximo_apertura').val(),
                estandar: $('#estandar').val(),
                siglas: $('#siglas').val(),
                color: $('#color').val(),
                tipo: $('#tipo').val(),
                tallos_x_ramo_estandar: $('#tallos_x_ramo_estandar').val(),
                tallos_x_malla: $('#tallos_x_malla').val(),
            };
            post_jquery('{{url('plantas_variedades/store_variedad')}}', datos, function () {
                cerrar_modals();
                location.reload();
            });
            $.LoadingOverlay('hide');
        }
    }

    /* ===================================================== */

    function edit_planta(p) {
        $.LoadingOverlay('show');
        datos = {
            id_planta: p,
        };
        $.get('{{url('plantas_variedades/edit_planta')}}', datos, function (retorno) {
            modal_form('modal_edit_planta', retorno, '<i class="fa fa-fw fa-plus"></i> Editar Planta', true, false, '{{isPC() ? '65%' : ''}}', function () {
                update_planta();
            });
        });
        $.LoadingOverlay('hide');
    }

    function update_planta() {
        if ($('#form_add_planta').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                nombre: $('#nombre').val(),
                id_planta: $('#id_planta').val(),
                tarifa: $("#tarifa").val(),
                nandina: $("#nandina").val(),
                siglas: $("#siglas").val(),
            };
            post_jquery('{{url('plantas_variedades/update_planta')}}', datos, function () {
                cerrar_modals();
                location.reload();
            });
            $.LoadingOverlay('hide');
        }
    }

    function cambiar_estado_planta(p, estado) {
        mensaje = {
            title: estado == 1 ? '<i class="fa fa-fw fa-trash"></i> Desactivar planta' : '<i class="fa fa-fw fa-unlock"></i> Activar planta',
            mensaje: estado == 1 ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar esta planta?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar esta planta?</div>',
        };
        modal_quest('modal_delete_planta', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_planta: p,
                estado: estado == 1 ? 0 : 1,
            };
            post_jquery('{{url('plantas_variedades/cambiar_estado_planta')}}', datos, function () {
                cerrar_modals();
                location.reload();
            });
        });
    }

    function edit_variedad(v) {
        $.LoadingOverlay('show');
        datos = {
            id_variedad: v
        };
        $.get('{{url('plantas_variedades/edit_variedad')}}', datos, function (retorno) {
            modal_form('modal_edit_variedad', retorno, '<i class="fa fa-fw fa-plus"></i> Editar Variedad', true, false, '{{isPC() ? '75%' : ''}}', function () {
                update_variedad();
            });
        });
        $.LoadingOverlay('hide');
    }

    function update_variedad() {
        if ($('#form_edit_variedad').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                nombre: $('#nombre').val(),
                id_planta: $('#id_planta').val(),
                id_variedad: $('#id_variedad').val(),
                unidad_medida: $('#unidad_medida').val(),
                minimo_apertura: $('#minimo_apertura').val(),
                maximo_apertura: $('#maximo_apertura').val(),
                estandar: $('#estandar').val(),
                siglas: $('#siglas').val(),
                color: $('#color').val(),
                tipo: $('#tipo').val(),
                tallos_x_ramo_estandar: $('#tallos_x_ramo_estandar').val(),
                tallos_x_malla: $('#tallos_x_malla').val(),
            };
            post_jquery('{{url('plantas_variedades/update_variedad')}}', datos, function () {
                cerrar_modals();
                location.reload();
            });
            $.LoadingOverlay('hide');
        }
    }

    function cambiar_estado_variedad(v, estado) {
        mensaje = {
            title: estado == 1 ? '<i class="fa fa-fw fa-trash"></i> Desactivar variedad' : '<i class="fa fa-fw fa-unlock"></i> Activar variedad',
            mensaje: estado == 1 ? '<div class="alert alert-danger text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de desactivar esta variedad?</div>' :
                '<div class="alert alert-info text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Está seguro de activar esta variedad?</div>',
        };
        modal_quest('modal_delete_variedad', mensaje['mensaje'], mensaje['title'], true, false, '{{isPC() ? '25%' : ''}}', function () {
            datos = {
                _token: '{{csrf_token()}}',
                id_variedad: v,
                estado: estado == 1 ? 0 : 1,
            };
            post_jquery('{{url('plantas_variedades/cambiar_estado_variedad')}}', datos, function () {
                cerrar_modals();
                location.reload();
            });
        });
    }

    function add_precio(id_variedad) {

        $.LoadingOverlay('show');
        datos = {
            id_variedad: id_variedad
        };

        $.get('{{url('plantas_variedades/form_precio_variedad')}}', datos, function (retorno) {
            modal_form('modal_add_precio', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir PrecioController', true, false, '{{isPC() ? '40%' : ''}}', function () {
                store_precio();
            });
            setInterval(function () {
                $("#id_variedad").val(id_variedad);
            }, 1000)
        });
        $.LoadingOverlay('hide');
    }

    function store_precio() {

        if ($('#form_add_precio').valid()) {

            var cant_tr = $("tbody#precios tr").length;
            var arrData = [];
            for (var i = 0; i < cant_tr; i++) {
                arrData.push([$("#precio_" + (parseInt(i) + parseInt(1))).val(), $("#id_clasificacion_por_ramo_" + (parseInt(i) + parseInt(1))).val(), $("#id_precio_" + (parseInt(i) + parseInt(1))).val()]);
            }
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                arrData: arrData,
                id_variedad: $("#id_variedad").val()
            };
            post_jquery('{{url('plantas_variedades/store_precio')}}', datos, function () {
                cerrar_modals();
                location.reload();
            });
            $.LoadingOverlay('hide');
        }
    }

    function add_inptus_precio() {

        $.LoadingOverlay('show');

        var cant_tr = $("tbody#precios tr").length;
        datos = {
            cant_tr: cant_tr
        };
        console.log(datos);
        $.get('{{url('plantas_variedades/add_inptus_precio_variedad')}}', datos, function (retorno) {
            $("#precios").append(retorno);

        });
        $.LoadingOverlay('hide');
    }

    function delete_inputs(cant) {
        var tr = $("tbody#precios tr#precios_" + cant);
        tr.remove(tr.cant);
    }

    function actualizar_status_precio(id_precio, estado, id_variedad) {
        $.LoadingOverlay('show');
        datos = {
            _token: '{{csrf_token()}}',
            id_precio: id_precio,
            estado: estado,
        };
        post_jquery('{{url('plantas_variedades/update_precio')}}', datos, function () {
            cerrar_modals();
            add_precio($("#id_variedad").val());
        });
        $.LoadingOverlay('hide');

    }

    function comprabar(id_input) {
        $("#id_precio_" + id_input).val('');
    }
</script>
