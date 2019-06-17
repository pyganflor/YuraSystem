<script>

    function add_inptus(id_tbody, id_input) {
        if (id_tbody === 'empaques') {
            $("#add_empaques").attr('disabled', 'disabled');
            var cant = $("tbody#" + id_tbody + " tr td div div input").length;
            datos = {
                cant_tr: cant
            };
            $.get('{{url('configuracion/campos_empaques')}}', datos, function (retorno) {
                $("tbody#" + id_tbody).append(retorno);
                $("#add_empaques").attr('disabled', false);
            });

        } else {
            $("#add_campo_detalle").attr('disabled', 'disabled');
            var cant_input = $("tbody#" + id_tbody + " tr td div input").length;
            var cant = cant_input + 1;
            var pattern, placeholder, name_span;
            id_tbody == 'campos_clasifc_x_ramo' ? placeholder = "placeholder='Solo números'" : '';
            id_tbody == 'campos_clasifc_x_ramo' ? name_span = "Cantidad" : '';
            id_tbody === 'campos_clasifc_unitaria' ? placeholder = "placeholder='30|7'" : '';
            id_tbody === 'campos_clasifc_unitaria' ? name_span = "Cantidad" : '';
            id_tbody === 'empaques' ? name_span = "Nombre" : '';
            //id_tbody === 'campos_clasifc_x_ramo' ? pattern = "pattern='^([0-9])*$'" : '';

            if ($.isArray(id_input)) {
                var datos = {
                    cant_tr: $("tbody#" + id_tbody + " tr").length
                };
                $.get('{{route('view.inputs_detalle_empaque')}}', datos, function (retorno) {
                    $("tbody#" + id_tbody).append(retorno);
                    $("#add_campo_detalle").attr('disabled', false);
                });
            } else {
                $("tbody#" + id_tbody).append(
                    '<tr id="' + id_tbody + '_' + cant + '">' +
                    '<td >' +
                    '<div class="input-group"">' +
                    '<span class="input-group-addon" style="background-color: #e9ecef">' + name_span + '</span>' +
                    '<input type="text" id="' + id_input + '_' + cant + '" name="' + id_input + '_' + cant + '" ' + placeholder + ' required="" ' + pattern + ' class="form-control" minlength="1" maxlength="10" >' +
                    '</div>' +
                    '</td>' +
                    '<td class="text-center">' +
                    '<input type="hidden" id="id_' + id_input + '_' + cant + '" value="">' +
                    '<button type="button" class="btn btn-xs btn-danger" title="Eliminar campo" onclick="delete_inputs(' + id_tbody + ',' + cant + ')">' +
                    '<i class="fa fa-trash" aria-hidden="true"></i>' +
                    '</button>' +
                    '</td>' +
                    '</tr>'
                );
            }
        }
    }

    function delete_inputs(id_tbody, cant) {

        if (id_tbody === 'tr_detalles_empaque' || id_tbody === 'empaques') {
            var tr = $("tbody tr#" + id_tbody + "_" + cant);
        } else {
            var tr = $("tbody tr#" + id_tbody.id + "_" + cant);
        }
        tr.remove(tr.cant);
    }

    function actualizarClasificacion(id_clasifi, est_clasifi, clase) {
        $.LoadingOverlay('show');
        datos = {
            _token: '{{csrf_token()}}',
            id_clasificacion: id_clasifi,
            estado: est_clasifi,
            clase: clase
        };
        post_jquery('{{url('configuracion/actualizar_esatdo_clasificacion')}}', datos, function () {
            cerrar_modals();
            location.reload();
        });
        $.LoadingOverlay('hide');
    }

    function store_data_config() {
        var id_config;
        $("#id_config").val().length == 1 ? id_config = $("#id_config").val() : id_config = '';
        var cant_inputs_clasif_unit = $("tbody#campos_clasifc_unitaria tr").length;
        var cant_inputs_clasif_x_ramos = $("tbody#campos_clasifc_x_ramo tr").length;
        var cant_inputs_empaques = $("tbody#empaques tr").length;

        var arrIdClasifiUnit = [];
        var arrClasifiUnit = [];
        for (var i = 0; i < cant_inputs_clasif_unit; i++) {
            arrClasifiUnit.push($("#clasificacion_unitaria_" + (parseInt(i) + parseInt(1))).val());
            arrIdClasifiUnit.push($("#id_clasificacion_unitaria_" + (parseInt(i) + parseInt(1))).val());
        }

        var arrIdClasifiXRamos = [];
        var arrClasifiXRamos = [];
        for (var j = 0; j < cant_inputs_clasif_x_ramos; j++) {
            arrClasifiXRamos.push($("#clasificacion_por_ramo_" + (parseInt(j) + parseInt(1))).val());
            arrIdClasifiXRamos.push($("#id_clasificacion_por_ramo_" + (parseInt(j) + parseInt(1))).val());
        }

        var arrIdClasifiEmpaque = [];
        var arrClasifiEmpaque = [];
        for (var x = 0; x < cant_inputs_empaques; x++) {
            arrClasifiEmpaque.push([$("#campo_empaque_" + (parseInt(x) + parseInt(1))).val(), $("#tipo_empaque_" + (parseInt(x) + parseInt(1))).val()]);
            arrIdClasifiEmpaque.push($("#id_campo_empaque_" + (parseInt(x) + parseInt(1))).val());
        }


        if ($('#form_config').valid()) {
            $.LoadingOverlay('show');
            datos = {
                _token: '{{csrf_token()}}',
                nombre: $("#nombre_empresa").val(),
                razon_social : $("#razon_social").val(),
                matriz : $("#matriz").val(),
                establecimiento : $("#establecimiento").val(),
                cantidad_usuarios: $("#cant_usuarios").val(),
                cant_hectarea: $("#hectarea").val(),
                propagacion: $("#propagacion_proceso1").val() + "|" + $("#propagacion_proceso2").val() + "|" + $("#propagacion_proceso3").val(),
                campo: $("#campo_proceso1").val() + "|" + $("#campo_proceso2").val(),
                postcocecha: $("#postcocecha_proceso1").val() + "|" + $("#postcocecha_proceso2").val() + "|" + $("#postcocecha_proceso3").val() + "|" + $("#postcocecha_proceso4").val() + "|" + $("#postcocecha_proceso5").val(),
                clasifi_unit_tipos: arrClasifiUnit,
                clasifi_x_ramos_tipos: arrClasifiXRamos,
                empaque_nombres: arrClasifiEmpaque,
                arrIdClasifiUnit: arrIdClasifiUnit,
                arrIdClasifiXRamos: arrIdClasifiXRamos,
                arrIdClasifiEmpaque: arrIdClasifiEmpaque,
                tallos_x_ramo: $("#tallos_por_ramo").val(),
                unidad_medida: $("#unidad_medida").val(),
                moneda: $('#moneda').val(),
                id_config: id_config,
                codigo_pais : $("#codigo_pais").val(),
                telefono : $("#telefono").val(),
                correo : $("#correo").val(),
                fax :$("#fax").val(),
                permiso_agrocalidad : $("#permiso_agrocalidad").val(),
            };
            post_jquery('{{route('configuracion.store')}}', datos, function () {
                cerrar_modals();
                location.reload();
            });
            $.LoadingOverlay('hide');
        }
    }

    function modal_detalle_empaque(id_empaque, nombre) {
        $.LoadingOverlay('show');
        datos = {
            id_empaque: id_empaque,
            nombre: nombre
        };
        $.get('{{route('configuracion.create')}}', datos, function (retorno) {
            modal_form('modal_add_detalle_empaque', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir detalles del empaque', true, false, '{{isPC() ? '60%' : ''}}', function () {
                add_detalle_empaque();
            });
        });
        $.LoadingOverlay('hide');
    }

    function add_detalle_empaque() {
        if ($('#form_add_detalle_empaque').valid()) {
            $.LoadingOverlay('show');
            var cant_inputs_detalles_empaques = $("tbody#detalles_empaques tr").length;

            var arrDetallesEmpaque = [];

            for (var i = 0; i < cant_inputs_detalles_empaques; i++) {
                arrDetallesEmpaque.push([
                    $('#empaque_id_variedad_' + (parseInt(i) + parseInt(1))).val(),
                    $('#empaque_id_clasificacion_por_ramo_' + (parseInt(i) + parseInt(1))).val(),
                    $('#cantidad_empaque_' + (parseInt(i) + parseInt(1))).val(), $("#id_empque").val(),
                    $('#id_detalle_empaque_' + (parseInt(i) + parseInt(1))).val()]);
            }

            datos = {
                _token: '{{csrf_token()}}',
                data_detalles_empaque: arrDetallesEmpaque,
            };
            post_jquery('{{route('store.detalle_empaque')}}', datos, function () {
                cerrar_modals();
            });
            $.LoadingOverlay('hide');
        }

    }

    function actualizarEstadoDetallePaquete(id_detalle_empaque, est_detalle_empaque) {
        $.LoadingOverlay('show');
        datos = {
            _token: '{{csrf_token()}}',
            id_detalle_empaque: id_detalle_empaque,
            estado: est_detalle_empaque,
        };
        post_jquery('{{route('update.detalle_empaque')}}', datos, function () {
            cerrar_modals();
            modal_detalle_empaque($("#id_empque").val());
        });
        $.LoadingOverlay('hide');
    }

    function icono_moneda() {

        $("#icono_moneda").html('<i class="fa fa-' + $('#moneda').val() + '" aria-hidden="true"></i>');

    }

    function admin_clasificacion_unitaria() {
        datos = {};
        get_jquery('{{url('configuracion/admin_clasificacion_unitaria')}}', datos, function (retorno) {
            modal_view('modal_view_clasificaciones', retorno, '<i class="fa fa-fw fa-table"></i> Clasificación unitaria', true, false,
                '{{isPC() ? '85%' : ''}}');
        });
    }

    function admin_clasificacion_ramo() {
        datos = {};
        get_jquery('{{url('configuracion/admin_clasificacion_ramo')}}', datos, function (retorno) {
            modal_view('modal_view_clasificaciones', retorno, '<i class="fa fa-fw fa-table"></i> Clasificaciones de ramos', true, false,
                '{{isPC() ? '50%' : ''}}');
        });
    }

    function seleccionar_unidad_medida(id_clasificacion_unitaria, campo) {
        datos = {
            id_unidad_medida: $('#id_unidad_medida_' + id_clasificacion_unitaria).val(),
            campo: campo,
            id_clasificacion_unitaria: id_clasificacion_unitaria
        };
        get_jquery('{{url('configuracion/seleccionar_unidad_medida')}}', datos, function (retorno) {
            $('#td_clasificacion_' + campo + '_' + id_clasificacion_unitaria).html(retorno);
        });
    }

    function admin_grosor_ramo() {
        get_jquery('{{url('configuracion/admin_grosor_ramo')}}', {}, function (retorno) {
            modal_view('modal_view_admin_grosor_ramo', retorno, '<i class="fa fa-fw fa-leaf"></i> Grosor de ramos', true, false,
                '{{isPC() ? '35%' : ''}}');
        });
    }
</script>
