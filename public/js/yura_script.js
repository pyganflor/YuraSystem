function add_pedido(id_cliente, pedido_fijo, vista) {
    datos = {
        id_cliente: id_cliente,
        pedido_fijo: pedido_fijo,
        vista: vista
    };
    get_jquery('/clientes/add_pedido', datos, function (retorno) {
        modal_view('modal_add_pedido', retorno, '<i class="fa fa-fw fa-plus"></i> Agregar pedido', true, false, '70%');
        id_cliente !== '' ? add_campos(1, id_cliente) : '';
        pedido_fijo != '' ? div_opcion_pedido_fijo(1) : '';
        setTimeout(function () {
            vista == 'pedidos' ? $("#btn_add_campos").attr('disabled', true) : '';
        }, 500)
    });
}

function add_campos(value, id_cliente) {
    $.LoadingOverlay('show');
    var cant_tr = $('tbody#tbody_inputs_pedidos tr').length;
    cant_tr > -1 ? $('#btn_delete_inputs').removeClass('hide') : '';
    datos = {
        cant_tr: cant_tr,
        id_cliente: id_cliente
    };
    $.get('/clientes/inputs_pedidos', datos, function (retorno) {
        $('#tbody_inputs_pedidos').append(retorno);
        if ($("#id_cliente_venta").length > 0) {
            cargar_espeicificaciones_cliente(false);
        }
    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

function delete_campos(value) {
    $.LoadingOverlay('show');
    var cant_tr = $('tbody#tbody_inputs_pedidos tr').length;

    if ($("#tr_inputs_pedido_" + cant_tr + " input#cantidad_" + cant_tr).val().length < 1) {
        var tr = $("tbody tr#tr_inputs_pedido_" + cant_tr);
        tr.remove();
        if (cant_tr == 2) {
            $('#btn_delete_inputs').addClass('hide');
        }
    } else {
        $('#btn_delete_inputs').addClass('hide')
    }
    $.LoadingOverlay('hide');
}

function div_opcion_pedido_fijo(opcion) {
    $.LoadingOverlay('show');
    datos = {
        opcion: opcion,
    };
    $.get('clientes/opcion_pedido_fijo', datos, function (retorno) {
        $('#div_opciones_pedido_fijo').html(retorno);
    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

function pushSemanas(opcion, arrSemanas) {
    if (opcion == 2 || opcion == 1) {
        $("select#intervalo option#options_dinamics").remove();
        $.each(arrSemanas, function (i, j) {
            $("select#intervalo").append('<option id="options_dinamics" value="' + (i + 1) + '">' + j + '</option>')
        });
    }
}

function verificar_intervalo_fecha() {

    if ($("#fecha_desde_pedido_fijo").val() != '' && $("#fecha_hasta_pedido_fijo").val() != '') {
        $("#intervalo").attr('disabled', false);
    }
    //if($("#fecha_desde_pedido_fijo").val().length > 1 && $("#fecha_hasta_pedido_fijo").val().length > 1){
    var fechaDesde = moment($("#fecha_desde_pedido_fijo").val());
    var fechaHasta = moment($("#fecha_hasta_pedido_fijo").val());
    var diferenciaDias = fechaHasta.diff(fechaDesde, 'days');

    var fechaFormateada = $('#fecha_desde_pedido_fijo').val().replace('/-/g', '/');
    let date = new Date(fechaFormateada);

    var p = 0;
    for (var x = 0; x < diferenciaDias + 2; x++) {
        var fechas = (date.getMonth() + 1) + "/" + date.getDate() + "/" + date.getFullYear();
        date.setDate(date.getDate() + 1);
        var d = new Date(fechas);
        if (d.getDay() === parseInt($("#dia_semana").val().trim())) {
            p++
        }
    }
    var arrSemanas = [];
    if (p > 0) {
        for (var i = 0; i < p; i++) {
            var plu = '';
            (i > 0) ? plu = 's' : plu;
            arrSemanas.push([(i + 1) + ' Semana' + plu]);
        }
    }
    pushSemanas(1, arrSemanas);
    //}
}

function add_fechas_pedido_fijo_personalizado() {

    $.LoadingOverlay('show');
    var cant_div = $('#td_fechas_pedido_fijo_personalizado div.col-md-4').length;
    if (cant_div > 0) {
        $('#btn_delete_fechas_pedido_fijo_personalizado').removeClass('hide');
    }
    datos = {
        cant_div: cant_div,
    };
    $.get('clientes/add_fechas_pedido_fijo_personalizado', datos, function (retorno) {
        $('#td_fechas_pedido_fijo_personalizado').append(retorno);
    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

function delete_fechas_pedido_fijo_personalizado() {

    $.LoadingOverlay('show');
    var cant_div = $('#td_fechas_pedido_fijo_personalizado div.col-md-4').length;
    var div = $("#div_" + cant_div);
    div.remove();

    if (cant_div == 2) {
        $('#btn_delete_fechas_pedido_fijo_personalizado').addClass('hide');
    }
    $.LoadingOverlay('hide');
}

function habilitar_campos() {
    $("#fecha_desde_pedido_fijo").attr('disabled', false);
    $("#fecha_hasta_pedido_fijo").attr('disabled', false);
}

function store_pedido(id_cliente, pedido_fijo, csrf_token, vista) {

    if ($('#form_add_pedido').valid()) {
        var result = confirm("Una vez guardado el pedido no puede ser editado");
        if (result) {
            var arrFechas = [];

            if (pedido_fijo && ($("#opcion_pedido_fijo").val() == 1) || $("#opcion_pedido_fijo").val() == 2) {
                var fechaDesde = moment($("#fecha_desde_pedido_fijo").val());
                var fechaHasta = moment($("#fecha_hasta_pedido_fijo").val());
                var diferenciaDias = fechaHasta.diff(fechaDesde, 'days');

                var fechaFormateada = $('#fecha_desde_pedido_fijo').val().replace('/-/g', '/');
                let date = new Date(fechaFormateada);
                var x = 1;

                //($("#opcion_pedido_fijo").val() == 2 || $("#opcion_pedido_fijo").val() == 1 ) ? diferenciaDias = diferenciaDias + 2 : diferenciaDias;
                for (var i = 0; i < diferenciaDias + 2; i++) {

                    var fechas = (date.getMonth() + 1) + "/" + date.getDate() + "/" + date.getFullYear();
                    date.setDate(date.getDate() + 1);
                    var d = new Date(fechas);

                    if ($("#opcion_pedido_fijo").val() == 1) {
                        if (d.getDay() === parseInt($("#dia_semana").val().trim())) {
                            if (x === parseInt($("#intervalo").val())) {
                                arrFechas.push(fechas);
                                x = 0;
                            }
                            x++;
                        }
                    } else if ($("#opcion_pedido_fijo").val() == 2) {
                        if (d.getDate() == parseInt($("#dia_mes").val())) {
                            arrFechas.push(fechas);
                        }
                    }
                }
            } else if (pedido_fijo && $("#opcion_pedido_fijo").val() == 3) {
                $cant_pedidos = $("#td_fechas_pedido_fijo_personalizado div.col-md-4").length;
                for (var i = 0; i < $cant_pedidos; i++) {
                    arrFechas.push(
                        $("input#fecha_desde_pedido_fijo_" + (i + 1)).val()
                    );
                }
            }
            $.LoadingOverlay('show');
            var cant_tr = $('tbody#tbody_inputs_pedidos tr').length;
            var arrDataDetallesPedido = [];
            for (var i = 1; i <= cant_tr; i++) {
                arrDataDetallesPedido.push([
                    $("#cantidad_" + i).val(),
                    $("#id_especificacion_" + i).val(),
                    $("#id_agencia_carga_" + i).val(),
                ]);
            }

            datos = {
                _token: csrf_token,
                arrDataDetallesPedido: arrDataDetallesPedido,
                descripcion: $('#descripcion').val(),
                fecha_de_entrega: $('#fecha_de_entrega').length ? $('#fecha_de_entrega').val() : '',
                id_cliente: id_cliente == '' ? $("#id_cliente_venta").val() : id_cliente,
                id_pedido: $('#id_pedido').val(),
                arrFechas: arrFechas.length < 1 ? '' : arrFechas,
                pedido_fijo: $("#opcion_pedido_fijo").length > 0 ? $("#opcion_pedido_fijo").val() : '',
                opcion: $("#opcion_pedido_fijo").val()
            };
            post_jquery('clientes/store_pedidos', datos, function () {
                cerrar_modals();

                if (vista != 'pedidos') {
                    detalles_cliente(id_cliente == '' ? id_cliente = $("#id_cliente_venta").val() : id_cliente);
                }
            });
            $.LoadingOverlay('hide');
        }
    }
}

function cancelar_pedidos(id_pedido, id_cliente, estado, token) {
    $.LoadingOverlay('show');
    datos = {
        _token: token,
        id_pedido: id_pedido,
        estado: estado
    };
    post_jquery('clientes/cancelar_pedido', datos, function () {
        cerrar_modals();
        detalles_cliente(id_cliente);
        buscar_listado_pedidos();
        // modal_view('modal_status_pedidos', retorno, '<i class="fa fa-fw fa-plus"></i> Estado pedido', true, false, '50%');
    });
    $.LoadingOverlay('hide');
}

function cargar_espeicificaciones_cliente(remove) {
    $.LoadingOverlay('show');
    remove ? $("#tbody_inputs_pedidos tr").remove() : '';
    var cant_tr = $('tbody#tbody_inputs_pedidos tr').length;
    datos = {
        id_cliente: $("#id_cliente_venta").val()
    };
    get_jquery('pedidos/cargar_especificaciones', datos, function (response) {
        remove ? add_campos(1, '') : '';
        setTimeout(function () {
            $.each(response['especificaciones'], function (i, j) {
                $("#id_especificacion_" + cant_tr).append('<option value="' + j.id_cliente_pedido_especificacion + '">' + j.nombre + '</option>');
            });
            $.each(response['agencias_carga'], function (i, j) {
                $("#id_agencia_carga_" + cant_tr).append('<option value="' + j.id_agencia_carga + '">' + j.nombre + '</option>');
            });
        }, 500);
        $("#btn_add_campos").attr('disabled', false);
    });
    $.LoadingOverlay('hide');
}

function detalles_cliente(id_cliente) {
    $.LoadingOverlay('show');
    datos = {
        id_cliente: id_cliente
    };
    $.get('clientes/ver_detalles_cliente', datos, function (retorno) {
        modal_view('modal_view_detalle_cliente', retorno, '<i class="fa fa-fw fa-eye"></i> Detalles de cliente', true, false, '75%');
    });
    $.LoadingOverlay('hide');
}

function add_envio(id_pedido, token) {
    $.LoadingOverlay('show');
    datos = {
        id_pedido: id_pedido
    };
    $.get('clientes/add_envio', datos, function (retorno) {
        modal_form('modal_view_envio_pedido', retorno, '<i class="fa fa-plane" ></i> Crear envío', true, false, '75%', function () {
            store_envio(token, id_pedido);
        });
    });
    $.LoadingOverlay('hide');
}

function add_form_envio(id_form, total, form) {

    var cant_total_pedidos = $("#cantidad_detalle_form_" + id_form).val();

    var cant_rows = $("form#form_envio_" + id_form + " div#rows").length;
    cant_rows < 1 ? agregar_inputs(cant_rows, cant_total_pedidos, id_form, total, form) : '';

    if (cant_rows >= 1) {
        //var campo_at = $("#id_agencia_transporte_"+id_form+"_"+cant_rows).val();
        var campo_c = $("#cantidad_" + id_form + "_" + cant_rows).val();
        //var campo_e  =  $("#envio_"+id_form+"_"+cant_rows).val();
        cant_rows == 0 ? total = total - campo_c : '';

        var totales_cantidad = 0;

        for (var i = 1; i <= cant_rows; i++) {
            totales_cantidad = totales_cantidad + parseInt($("#cantidad_" + id_form + "_" + i).val());
        }
        total2 = total - totales_cantidad;

        if (campo_c == undefined || campo_c == null) {
            $('#msg_' + id_form).html('<b>Complete todos los campos del Envío N# ' + cant_rows + '</b>');
        } else {
            agregar_inputs(cant_rows, cant_total_pedidos, id_form, total2, form);
            $('#msg_' + id_form).html('');
        }
    }
}

function agregar_inputs(cant_rows, cant_total_pedidos, id_form, total, form) {

    //$.LoadingOverlay('show');
    if (total > 0) {
        datos = {
            rows: cant_rows + 1,
            cant_pedidos: cant_total_pedidos,
            id_form: id_form
        };
        $.get('clientes/add_form_envio', datos, function (retorno) {

            $("#div_inputs_envios_" + id_form).append(retorno);

            var_cant_inputs = $("#div_inputs_envios_" + id_form + " div#rows").length;
            $("#cantidad_" + id_form + "_" + (var_cant_inputs - 1)).attr('disabled', true);

            for (var i = 1; i <= total; i++) {
                $("#cantidad_" + id_form + "_" + (cant_rows + 1)).append('<option value="' + i + '">' + i + '</option>');
            }
            $('#msg_' + id_form).html('');
        });
    } else {
        setTimeout(function () {
            $('#msg_' + id_form).html('No se pueden realizar mas envíos en este detalle');
        }, 500);
    }
    setTimeout(function () {
        var cant_forms = $('div.well').length;
        var options = [];

        for (var j = 1; j <= cant_forms; j++) {
            var cant_rows_x_form = $("#div_inputs_envios_" + j + " div#rows").length;

            for (var z = 1; z <= cant_rows_x_form; z++) {
                options.push("<option  value=" + j + "_" + z + " id=dinamic_" + j + "> Detalle N# " + j + " Envio N# " + z + " </option>");
            }
            for (var l = 1; l <= cant_forms; l++) {
                var cant_rows_x_form = $("#div_inputs_envios_" + l + " div#rows").length;
                for (var p = 1; p <= cant_rows_x_form; p++) {
                    add_option(options, id_form, l, p, form);
                    $("select#envio_" + l + "_" + p + " option#dinamic_" + l).remove();
                }
            }
        }
    }, 1000);
    $.LoadingOverlay('hide');
}

function add_option(arr, id_form, form, input, selected) {
    $("#envio_" + form + "_" + input + " option:not(#seleccione)").remove();
    for (var p = 0; p < arr.length; p++) {
        $("#envio_" + form + "_" + input).append(arr[p]);
    }
    setTimeout(function () {
        if (selected != undefined) {
            var s = selected.split("|");
            $("#div_inputs_envios_" + s[0] + " select#envio_" + s[0] + "_" + s[1] + " option[value=" + s[2] + "_" + s[3] + "]").attr('selected', true);
        }
    });

}

function change_agencia_transporte(input) {

    var id_form = input.id.split("_")[1];
    var id_input = input.id.split("_")[2];
    var val_form = input.value.split("_")[0];
    var val_input = input.value.split("_")[1];

    $("select#id_agencia_transporte_" + id_form + "_" + id_input + " option").attr('selected', false);

    var val_form_selected = $("#id_agencia_transporte_" + val_form + "_" + val_input).val();

    $("select#id_agencia_transporte_" + id_form + "_" + id_input + " option[value=" + val_form_selected + "]").attr('selected', true);

    if ($("select#" + input.id + " option:selected").text().trim() != ("Mismo envío").trim()) {
        $("#id_agencia_transporte_" + id_form + "_" + id_input).attr("disabled", true)
    } else {
        $("#id_agencia_transporte_" + id_form + "_" + id_input).attr("disabled", false)
    }
}

function store_envio(token, id_pedido, vista) {

    var cant_forms = $('div.well').length;
    var data = [];
    var suma_cant_input = 0;
    var suma_cant_forms = 0;
    for (var j = 1; j <= cant_forms; j++) {

        if ($("#fecha_envio_" + j).val() == '') {
            var msg = '<div class="alert alert-warning text-center"><p> El campo fecha del Detalle N# ' + j + ' es obligatorio </p></div>';
            modal_view('modal_view_error_fechas', msg, '<i class="fa fa-fw fa-eye"></i> Error al realizar el envío', true, false, '40%');
            return false;
        }

        var cant_rows_x_form = $("#div_inputs_envios_" + j + " div#rows").length;
        for (var z = 1; z <= cant_rows_x_form; z++) {

            var envio = 1;
            var fecha = "";
            var form = '';

            if ($("select#envio_" + j + "_" + z).text().trim() === ("Mismo envío").trim()) {
                //envio = $("#envio_"+j+"_"+z).val();
                fecha = $("#fecha_envio_" + j).val();
                form = 0;
            } else {
                var arrEnvio = $("#envio_" + j + "_" + z).val().split("_");

                //envio = arrEnvio[0];
                envio = $("select[name=envio_" + j + "]")[0].name.split("_")[1];
                fecha = fecha = $("#fecha_envio_" + arrEnvio[0]).val();
                form = j + "|" + z + "|" + arrEnvio[0] + "|" + arrEnvio[1];
            }
            suma_cant_input += Number($("#cantidad_" + j + "_" + z).val());

            data.push([
                $("#id_especificacion_" + j).val(),
                $("#id_agencia_transporte_" + j + "_" + z).val(),
                $("#cantidad_" + j + "_" + z).val(),
                envio,
                fecha,
                form,
                //$("#id_detalle_envio_"+j+"_"+z).val()
            ]);
        }
        suma_cant_forms += Number($("#cantidad_detalle_form_" + j).val());
    }
    if (suma_cant_input < suma_cant_forms) {
        var msg = '<div class="alert alert-warning text-center"><p> Aún faltan especificaiones por ordenar en este pedido para su envío </p></div>';
        modal_view('modal_view_error_cantidad', msg, '<i class="fa fa-fw fa-eye"></i> Error al realizar el envío', true, false, '40%');
        return false;
    } else if (suma_cant_input > suma_cant_forms) {
        var msg = '<div class="alert alert-warning text-center"><p> La suma de las cantidades de los envios no puede ser mayor a la suma de las cantidades de las especificaciones</p></div>';
        modal_view('modal_view_error_cantidad', msg, '<i class="fa fa-fw fa-eye"></i> Error al realizar el envío', true, false, '40%');
        return false;
    }

    $.LoadingOverlay('show');
    datos = {
        _token: token,
        arrData: data,
        id_pedido: id_pedido
    };
    post_jquery('clientes/store_envio', datos, function () {
        cerrar_modals();
        vista === 'envios' ? buscar_listado_envios() : buscar_listado_pedidos();
    });
    $.LoadingOverlay('hide');
}

function editar_envio(id_envio, id_detalle_envio, id_pedido, token) {

    $.LoadingOverlay('show');
    datos = {
        //_token           : token,
        id_pedido: id_pedido,
        id_detalle_envio: id_detalle_envio,
        id_envio: id_envio
    };
    $.get('envio/editar_envio', datos, function (retorno) {
        modal_form('modal_view_edtiar_envio_pedido', retorno, '<i class="fa fa-plane" ></i> Editar envío', true, false, '75%', function () {
            store_envio(token, id_pedido, 'envios');
        });
    });
    $.LoadingOverlay('hide');

}

function ver_envio(id_pedido) {
    //clientes/ver_envio
}

function delete_input(id_form) {
    $.LoadingOverlay('show');
    var div = $('div#div_inputs_envios_' + id_form + ' div#rows:last-child');
    var rows = $("#div_inputs_envios_" + id_form + " #rows");
    var cant = $("#cantidad_" + id_form + "_" + rows.length).val();

    div.remove();

    var rows_new = $("#div_inputs_envios_" + id_form + " #rows");
    var cant_new = $("#cantidad_" + id_form + "_" + rows_new.length).val();

    cant = parseInt(cant) + parseInt(cant_new);

    $("#cantidad_" + id_form + "_" + rows_new.length + " option").remove();
    for (var x = 1; x <= cant; x++) {
        cant == x ? selected = "selected='selected'" : selected = "";
        $("#cantidad_" + id_form + "_" + rows_new.length).append("<option " + selected + " value=" + x + ">" + x + "</option>");
    }
    var_cant_inputs = $("#div_inputs_envios_" + id_form + " div#rows").length;
    $("#cantidad_" + id_form + "_" + (var_cant_inputs)).attr('disabled', false);
    $.LoadingOverlay('hide');
}

/* ========== Añadir/Quitar la clase "table" a una tabla =========*/
function estrechar_tabla(id, flag) {
    if (flag)
        $('#' + id).removeClass('table');
    else
        $('#' + id).addClass('table');
}

