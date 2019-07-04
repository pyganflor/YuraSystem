/* ====================== CONTROL DE SESSION abierta/cerrada ================= */
var session_count = 0;

setInterval('contador_session()', 1000);

function contador_session() {
    session_count++;
    if (session_count >= 1200) {
        $.LoadingOverlay('show');
        location.href = '/logout';
    }
}

/* ================ OTRAS FUNCIONES ======================== */

function add_pedido(id_cliente, pedido_fijo, vista, id_pedido) {
    datos = {
        id_cliente: id_cliente,
        pedido_fijo: pedido_fijo,
        vista: vista,
        id_pedido: id_pedido
    };
    get_jquery('/clientes/add_pedido', datos, function (retorno) {
        modal_view('modal_add_pedido', retorno, '<i class="fa fa-fw fa-plus"></i> Agregar pedido', true, false, '98%');
        id_cliente !== '' ? add_campos(1, id_cliente) : '';
        pedido_fijo != '' ? div_opcion_pedido_fijo(1) : '';
        setTimeout(function () {
            vista == 'pedidos' ? $("#btn_add_campos").attr('disabled', true) : '';
        }, 500);
    });
}

function add_campos(value, id_cliente, cant_especificaciones) {
    $.LoadingOverlay('show');
    datos = {
        id_cliente: id_cliente == "" ? $("#id_cliente_venta").val() : id_cliente,
    };
    $.get('/clientes/inputs_pedidos', datos, function (retorno) {
        $('#table_campo_pedido').append(retorno);
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

function store_pedido(id_cliente, pedido_fijo, csrf_token, vista, id_pedido, comprobante) {

    if ($('#form_add_pedido').valid()) {
        /*id_pedido
            ? msg = '<div class="alert alert-warning text-center">' +
            '<p>Si este pedido posee envíos prefacturados al editarlo seran borrados y se deberán crea nuevamente</p>' +
            '</div>'
            : msg = '';*/
        option_agencias_transporte = [];
        if (id_pedido && comprobante)
            texto = '<div class="alert alert-warning text-center">' +
                '<p>Luego de actualizar el pedido se debe actualizar tambien la factura de este pedido</p>' +
                '</div>';
        if (!id_pedido || (id_pedido && !comprobante))
            texto = '<div class="alert alert-info text-center"><p>Desea guardar este pedido?</p></div>';

        modal_quest('modal_edit_pedido', texto, 'Editar pedido', true, false, '40%', function () {
            if ($("#envio_automatico").is(":checked"))
                if ($("#fecha_envio").val() == "") {
                    $("#error_fecha_envio").html('<p style="color: red;">Debe seleccionar una fecha para realizar el envío</p>');
                    return false;
                } else {
                    $("#error_fecha_envio").html("");
                }

            id_variedades = '';
            variedades = '';
            arrDataDetallesPedido = [];
            arrDatosExportacion = [];
            cant_especificaciones = $('tbody#tbody_inputs_pedidos input.input_cantidad').length;
            cant_datos_exportacion = $(".th_datos_exportacion").length;
            var arr_especificaciones = [], arr_ordenado;
            $.each($("input.orden"), function (i, j) {
                if (j.value !== '')
                    arr_especificaciones.push(j.value);
            });
            arr_ordenado = arr_especificaciones.sort(menor_mayor);

            for (z = 0; z < arr_ordenado.length; z++) {
                $.each($('input.orden'), function (i, j) {
                    precio = '';
                    if ($(".cantidad_piezas_" + (i + 1)).val() != "" && $(".cantidad_piezas_" + (i + 1)).val() != 0 && arr_ordenado[z] === j.value) {
                        for (x = 1; x <= $(".input_variedad_" + (i + 1)).length; x++) {
                            id_variedades += $("#id_variedad_" + (i + 1) + "_" + x).val() + "|";
                            precio += $("#precio_" + (i + 1) + "_" + x).val() + ";" + $("#id_detalle_especificacion_empaque_" + (i + 1) + "_" + x).val() + "|"
                        }
                        variedades += id_variedades;

                        arrDataDetallesPedido.push({
                            cantidad: $("#cantidad_piezas_" + (i + 1)).val(),
                            id_cliente_pedido_especificacion: $("#id_cliente_pedido_especificacion_" + (i + 1)).val(),
                            id_agencia_carga: $("#id_agencia_carga_" + (i + 1)).val(),
                            precio: precio,
                            orden: $("#orden_" + (i + 1)).val(),
                        });
                    }
                });

                if (cant_datos_exportacion > 0) {
                    $.each($('input.orden'), function (i, j) {
                        arrDatosExportacionEspecificacion = [];
                        if ($(".cantidad_piezas_" + (i + 1)).val() != "" && $(".cantidad_piezas_" + (i + 1)).val() != 0 && arr_ordenado[z] === j.value) {
                            for (a = 1; a <= cant_datos_exportacion; a++) { //1
                                nombre_columna_dato_exportacion = $("#th_datos_exportacion_" + a).text().trim().toUpperCase();
                                console.log("#input_" + nombre_columna_dato_exportacion + "_" + (i+1));
                                arrDatosExportacionEspecificacion.push({
                                    valor: $("#input_" + nombre_columna_dato_exportacion + "_" + (i+1)).val(),
                                    id_dato_exportacion: $("#id_dato_exportacion_" + nombre_columna_dato_exportacion + "_" + (i+1)).val()
                                });
                            }
                            arrDatosExportacion.push(arrDatosExportacionEspecificacion);
                        }
                    });
                }
            }

            if (arrDataDetallesPedido.length < 1) {
                modal_view('modal_status_pedidos', '<div class="alert alert-danger text-center"><p> Debe colocar la cantidad de piezas en al menos una especificación</p> </div>', '<i class="fa fa-times" aria-hidden="true"></i> Estado pedido', true, false, '50%');
                return false;
            }

            var arrFechas = [];
            if (pedido_fijo && ($("#opcion_pedido_fijo").val() == 1) || $("#opcion_pedido_fijo").val() == 2) {
                var fechaDesde = moment($("#fecha_desde_pedido_fijo").val());
                var fechaHasta = moment($("#fecha_hasta_pedido_fijo").val());
                var diferenciaDias = fechaHasta.diff(fechaDesde, 'days');

                var fechaFormateada = $('#fecha_desde_pedido_fijo').val().replace('/-/g', '/');
                let date = new Date(fechaFormateada);
                var x = 1;

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

            datos = {
                _token: csrf_token,
                arrDataDetallesPedido: arrDataDetallesPedido,
                descripcion: $('#descripcion').val(),
                fecha_de_entrega: $('#fecha_de_entrega').length ? $('#fecha_de_entrega').val() : '',
                id_cliente: id_cliente == '' ? $("#id_cliente_venta").val() : id_cliente,
                id_pedido: $('#id_pedido').val(),
                arrFechas: arrFechas.length < 1 ? '' : arrFechas,
                pedido_fijo: $("#opcion_pedido_fijo").length > 0 ? $("#opcion_pedido_fijo").val() : '',
                opcion: $("#opcion_pedido_fijo").val(),
                variedades: variedades.split("|"),
                id_pedido: id_pedido,
                arrDatosExportacion: arrDatosExportacion,
                crear_envio: $("#envio_automatico").is(":checked"),
                fecha_envio: $("#fecha_de_entrega").val(),
            };

            post_jquery('clientes/store_pedidos', datos, function () {
                cerrar_modals();
                listar_resumen_pedidos($('#fecha_pedidos_search').val(), true);
                if (vista != 'pedidos') {
                    detalles_cliente(id_cliente == '' ? id_cliente = $("#id_cliente_venta").val() : id_cliente);
                }
            });
            $.LoadingOverlay('hide');

        });
    }
}

function peticion() {

}

function cancelar_pedidos(id_pedido, id_cliente, estado, token) {
    datos = {
        _token: token,
        id_pedido: id_pedido,
        estado: estado
    };
    modal_quest('modal_quest_cancelar_pedido', '<div class="alert alert-warning text-center">' +
        '¿Esta seguro que desea eliminar el pedido?</div>',
        '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de alerta', true, false, '35%', function () {
            $.LoadingOverlay('show');
            post_jquery('clientes/cancelar_pedido', datos, function () {
                cerrar_modals();
                console.log($("#listar_resumen_pedido").val());
                $("#listar_resumen_pedido").val() == 'true'
                    ? listar_resumen_pedidos($('#fecha_pedidos_search').val(), true)
                    : "";

            });
            $.LoadingOverlay('hide');
        });
}

function cargar_espeicificaciones_cliente(remove) {
    $.LoadingOverlay('show');
    remove ? $("#table_campo_pedido table").remove() : '';
    var cant_tr = $('tbody#tbody_inputs_pedidos tr').length;
    datos = {
        id_cliente: $("#id_cliente_venta").val()
    };
    get_jquery('pedidos/cargar_especificaciones', datos, function (response) {
        remove ? add_campos(1, '', response['agencias_carga']) : '';
        $("#btn_add_campos").attr('disabled', false);
        calcular_precio_pedido();
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

    /*cant = parseInt(cant) + parseInt(cant_new);

    if(rows_new.length != 1){
        if(cant !== null && !isNaN(cant)) {
            $("#cantidad_" + id_form + "_" + rows_new.length + " option").remove();
            for (var x = 1; x <= cant; x++) {
                cant == x ? selected = "selected='selected'" : selected = "";
                $("#cantidad_" + id_form + "_" + rows_new.length).append("<option " + selected + " value=" + x + ">" + x + "</option>");
            }
        }
    }*/
    var_cant_inputs = $("#div_inputs_envios_" + id_form + " div#rows").length;
    $("#cantidad_" + id_form + "_" + (var_cant_inputs)).attr('disabled', false);
    $.LoadingOverlay('hide');
}

function facturar_envio(id_envio, token) {
    $.LoadingOverlay('show');
    datos = {
        id_envio: id_envio
    };
    $.get('configuracion_facturacion/formulario_facturacion', datos, function (retorno) {
        modal_form('modal_view_envios_facturas', retorno, '<i class="fa fa-usd" aria-hidden="true"></i> Facturar Envío', true, false, '75%', function () {
            /*facturar_cliente(token);*/
        });
    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

/* ========== Añadir/Quitar la clase "table" a una tabla =========*/
function estrechar_tabla(id, flag) {
    if (flag)
        $('#' + id).removeClass('table');
    else
        $('#' + id).addClass('table');

}

function create_agencia_carga(id_agencia_carga, token, id_cliente) {
    $.LoadingOverlay('show');
    datos = {
        id_agencia_carga: id_agencia_carga
    };
    $.get('agrencias_carga/create_agencia', datos, function (retorno) {
        modal_form('modal_add_agencia_carga', retorno, '<i class="fa fa-fw fa-plus"></i> Añadir Agencia de carga', true, false, '50%', function () {
            store_agencia_carga(token, id_cliente);
        });
    });
    $.LoadingOverlay('hide');
}

function store_agencia_carga(token, id_cliente) {

    if ($('#form_add_agencia_carga').valid()) {
        $.LoadingOverlay('show');
        datos = {
            _token: token,//'{{csrf_token()}}'
            id_agencia_carga: $("#id_agencia_carga").val(),
            nombre: $("#nombre_agencia").val(),
            codigo: $("#codigo_agencia").val(),
            id_cliente: id_cliente,
            correo: $("#correo").val(),
            identificacion: $("#identificacion").val()
        };
        post_jquery('agrencias_carga/store_agencia', datos, function () {
            cerrar_modals();
            if (!id_cliente) {
                location.reload();
            } else {
                detalles_cliente(id_cliente);
                cargar_opcion('campos_agencia_carga', '', 'clientes/ver_agencias_carga');
            }

        });
        $.LoadingOverlay('hide');
    }
}

function cargar_opcion(div, id_cliente = '', url, add) {

    $.LoadingOverlay('show');

    if (div === 'campos_agencia_carga') {
        var cant_tr = $("tbody#campos_agencia_carga tr").length;
    } else if (div === 'campos_contactos') {
        var cant_tr = $("tbody#campos_contactos tr").length;
    }

    datos = {
        id_cliente: id_cliente,
        cant_tr: typeof cant_tr === "undefined" ? '' : cant_tr
    };

    get_jquery('/' + url, datos, function (retorno) {

        if (div === 'campos_agencia_carga') {
            $('#include_agencia_carga').removeClass('hide');
            $('#include_contactos_cliente,#div_content_opciones').addClass('hide');
            $("#div_content_opciones").html('');
            if (add === 'add') {
                $('#' + div).append(retorno);
            } else {
                $("#div_content_opciones").html(retorno);
            }
        } else if (div === 'campos_contactos') {

            $('#include_agencia_carga,#div_content_opciones').addClass('hide');
            $('#include_contactos_cliente').removeClass('hide');
            $("#div_content_opciones").html('');
            if (add === 'add') {
                $('#' + div).append(retorno);
            } else {
                $("#div_content_opciones").html(retorno);
            }

        } else if (div == 'div_content_opciones') {
            $("#div_content_opciones").removeClass('hide');
            $('#include_contactos_cliente,#include_agencia_carga').addClass('hide');

            $("#div_content_opciones").html(retorno);

        } else if (div == 'div_pedidos') {
            $('#include_contactos_cliente,#include_agencia_carga').addClass('hide');
            $("#div_content_opciones").removeClass('hide');
            $("#div_content_opciones").html(retorno);
        }

    });
    $.LoadingOverlay('hide');
}

function update_especificacion(id_especificacion, estado, token, cliente) {

    $.LoadingOverlay('show');
    datos = {
        _token: token,
        id_especificacion: id_especificacion,
        estado: estado,
    };
    post_jquery('clientes/update_especificaciones', datos, function () {
        cerrar_modals();
        if (cliente) {
            detalles_cliente($('#id_cliente').val());
            admin_especificaciones($('#id_cliente').val());
            setTimeout(function () {
                ver_especificaciones(($('#id_cliente').val()));
            }, 1000);
        } else {
            buscar_listado_especificaciones();
        }
    });
    $.LoadingOverlay('hide');
}

function buscar_listado_especificaciones() {
    //$.LoadingOverlay('show');
    datos = {
        busqueda: $('#busqueda_especifiaciones').val().trim(),
        id_cliente: $("#cliente_id").val(),
        tipo: $("#tipo").val(),
        estado: $("#estado").val()
    };
    $.get('especificacion/listado', datos, function (retorno) {
        $('#div_listado_especificaciones').html(retorno);
        //estructura_tabla('table_content_especificaciones');
    }).always(function () {
        //$.LoadingOverlay('hide');
    });
}

function add_especificacion(id_cliente, cliente) {
    datos = {
        id_cliente: id_cliente
    };
    get_jquery('clientes/add_especificacion', datos, function (retorno) {
        if (cliente) {
            $('#div_content').html(retorno);
        } else {
            modal_view('modal_admin_especificaciones', retorno, '<i class="fa fa-plus" aria-hidden="true"></i> Crear Especificaciones', true, false, '85%');
        }
    });
}

function tipo_unidad_medida(data, token) {
    datos = {
        _token: token,
        tipo_unidad_medida: 'P'//$('#'+data).val() COMENTADO SOLO PARA PYGANFLOR, SÓLO USA 'P' (PESO)
    };
    get_jquery('clientes/obtener_calsificacion_ramos', datos, function (retorno) {
        var select_clasif_x_ramo = $("#id_clasificacion_ramo_" + data.split('_')[2] + "_" + data.split('_')[3]);
        $('select#id_clasificacion_ramo_' + data.split('_')[2] + "_" + data.split('_')[3] + ' option#option_dinamic').remove();
        $.each(retorno, function (i, j) {
            select_clasif_x_ramo.append('<option id="option_dinamic" value="' + j.id_clasificacion_ramo + '"> ' + j.nombre + ' </option>');
        });
    });
}

function form_codigo_barra() {
    $.LoadingOverlay('show');
    $.get('codigo_barra/form_codigo_barra', {}, function (retorno) {
        modal_form('modal_form_codigo_barra', retorno, '<i class="fa fa-barcode"></i> Crear código de barras', true, false, '85%', function () {
            genera_codigo_barra($("#prefijo").val(), $("#codigo").val());
        });

    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

function admin_colores() {
    $.LoadingOverlay('show');
    $.get('admin_colores', {}, function (retorno) {
        modal_view('modal_view_admin_colores', retorno, '<i class="fa fa-tint"></i> Administrar colores', true, false, '85%');

    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

function genera_codigo_barra(prefijo, codigo) {
    $.LoadingOverlay('show');
    $.get('codigo_barra/generar_codigo_barra/' + codigo + "/" + prefijo, {}, function (retorno) {
        $("#img_codigo_barra").html(retorno);
    });
    $.LoadingOverlay('hide');
}

function listar_resumen_pedidos(fecha, opciones) {
    $.LoadingOverlay('show');
    datos = {
        fecha: fecha,
        opciones: opciones
    };
    $.get('despachos/listar_resumen_pedidos', datos, function (retorno) {
        $('#div_listado_blanco').html(retorno);
    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

function calcular_precio_pedido(input) {
    monto_total = 0.00;
    total_ramos = 0.00;
    total_piezas = 0.00;
    cant_rows = $(".input_cantidad").length;
    for (i = 1; i <= cant_rows; i++) {
        precio_especificacion = 0.00;
        ramos_totales_especificacion = 0.00;
        $.each($(".cantidad_" + i), function (p, q) {
            $.each($(".td_ramos_x_caja_" + i), function (a, b) {
                ramos_totales_especificacion += (q.value * b.value);
            });
            $.each($(".precio_" + i), function (y, z) {
                precio_variedad = z.value == "" ? 0 : z.value;
                ramos_x_caja = $(".input_ramos_x_caja_" + i + "_" + (y + 1)).val();
                precio_especificacion += (parseFloat(precio_variedad) * parseFloat(ramos_x_caja) * q.value);
            });
        });
        monto_total += parseFloat(precio_especificacion);
        $("#td_total_ramos_" + i).html(parseFloat(ramos_totales_especificacion));
        total_ramos += ramos_totales_especificacion;
        $("#td_precio_especificacion_" + i).html("$" + parseFloat(precio_especificacion).toFixed(2));
    }

    $.each($(".seleccion_invidual"), function (n, m) {
        if ($(".cantidad_" + (n + 1)).val() != "" && $("#seleccion_invidual_" + (n + 1)).is(":checked"))
            total_piezas += parseInt($(".cantidad_" + (n + 1)).val());
    });
    $("#total_piezas").html(total_piezas);
    $("#total_ramos").html(total_ramos);
    $(".monto_total_pedido").html("$" + monto_total.toFixed(2));
}

function barra_string(input, event, barra = true) {
    value_input = $("#" + input.id).val();
    tecla = event.which || event.keyCode;
    if (tecla !== 46 && isNaN(String.fromCharCode(tecla)))
        return false;
    if (tecla === 46)
        if (value_input.indexOf(".") > -1 && value_input.indexOf("|") == -1)
            return false;
    if (barra)
        if (tecla === 32)
            value_input += "|";

    $("#" + input.id).val(value_input.replace(" ", ""));
}

function guion_bajo_string(input, event, guion = true) {
    value_input = $("#" + input.id).val();
    tecla = event.which || event.keyCode;
    if (guion)
        if (tecla === 32)
            value_input += "_";

    $("#" + input.id).val(value_input.replace(" ", ""));
}

function duplicar_especificacion(id_especificacion, especificacion_pedido) {
    if ($("#cantidad_piezas_" + especificacion_pedido).val() == "") {
        modal_view('modal_duplicar_especificacion', '<div class="alert alert-warning text-center"><p> Debe llenar todos los datos de la especififación antes de duplicarla</p> </div>', '<i class="fa fa-times" aria-hidden="true"></i> Estado pedido', true, false, '50%');
        return false;
    }
    $.LoadingOverlay('show');
    datos = {
        id_especificacion: id_especificacion,
        id_cliente: $("#id_cliente_venta").val(),
        cant_esp: $(".input_cantidad").length
    };
    $.get('pedidos/duplicar_especificacion', datos, function (retorno) {
        $("#tbody_inputs_pedidos").append(retorno);
        //$("#cant_esp").val(parseInt($("#cant_esp").val())+1);
        calcular_precio_pedido();
        $("#btn_remove_especificacicon").removeClass('hide');
    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

function formatear_numero(numero) {
    var clean = numero.split('.');
    var num = clean[0].replace(/\./g, '');
    if (!isNaN(num)) {
        num = num.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g, '$1,');
        num = num.split('').reverse().join('').replace(/^[\.]/, '');
        numero = num;
    } else {
        alert('Solo se permiten numeros');
        numero = numero.replace(/[^\d\.]*/g, '');
    }
    if (clean.length > 1)
        return numero + '.' + clean[1];
    else
        return numero
}

function duplicar_pedido(id_pedido, id_cliente) {
    $.LoadingOverlay('show');
    datos = {
        id_pedido: id_pedido,
        id_cliente: id_cliente
    };
    $.get('pedidos/form_duplicar_pedido', datos, function (retorno) {
        modal_view('modal_duplicar_pedido', retorno, '<i class="fa fa-files-o" aria-hidden="true"></i> Duplicar pedido', true, false, '70%');
    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

function porcentaje_impuesto() {
    datos = {
        codigo_impuesto: $("#codigo_impuesto").val()
    };
    get_jquery('tipo_impuesto/get_tipo_impuesto', datos, function (retorno) {
        $("option#dinamic").remove();
        $.each(retorno, function (i, j) {
            $("#tipo_impuesto").append("<option id='dinamic' value=" + j.codigo + ">" + j.descripcion + "</option>");
        });
    });
}

function valida_identificacion() {
    if ($("#tipo_identificacion").val() == "07") {
        $("#identificacion").val("9999999999999").attr('disabled', true);
    } else {
        $("#identificacion").attr('disabled', false);
    }
    if ($("#tipo_identificacion").val() == "04") {
        $("#identificacion").attr('minlength', 13);
        $("#identificacion").attr('maxlength', 13);
    }
    if ($("#tipo_identificacion").val() == "05") {
        $("#identificacion").attr('minlength', 10);
        $("#identificacion").attr('maxlength', 10);
    }

}

function editar_pedido_tinturado(id_pedido, pos_det_ped, global = true, listar_resumen_pedido = true) {
    datos = {
        id_pedido: id_pedido,
        pos_det_ped: pos_det_ped,
        listar_resumen_pedido: listar_resumen_pedido
    };
    div_parametro = global ? 'td_opciones_' + id_pedido : false;
    get_jquery('pedidos/editar_pedido_tinturado', datos, function (retorno) {
        !global ? cerrar_modals() : '';
        modal_view('modal-view_editar_pedido_tinturado', retorno, '<i class="fa fa-fw fa-pencil"></i> Editar pedido', true, false, '98%');
    }, div_parametro);
}

function update_orden_tinturada(token) {
    if ($('#form-update_orden_semanal').valid()) {
        modal_quest('modal_quest_update_orden_tinturada', '<div class="alert alert-info text-center">' +
            '¿Está seguro de modificar los datos de este pedido? (Se perderán las distribuciones relacionadas a esta especificación)</div>',
            '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de alerta', true, false, '35%', function () {
                ids_esp_emp = $('.id_esp_emp');
                arreglo_esp_emp = [];
                for (ee = 0; ee < ids_esp_emp.length; ee++) {
                    ids_det_esp = $('.id_det_esp_' + ids_esp_emp[ee].value);
                    /* ========= PRECIOS x DETALLE ESPECIFICACION ========== */
                    arreglo_precios = [];
                    for (det = 0; det < ids_det_esp.length; det++) {
                        arreglo_precios.push({
                            id_det_esp: ids_det_esp[det].value,
                            precio: $('#precio_det_esp_' + ids_det_esp[det].value).val()
                        });
                    }
                    /* ========= MARCACIONES_COLORACIONES ========== */
                    fil = $('#marcaciones_' + ids_esp_emp[ee].value).val();
                    col = $('#coloraciones_' + ids_esp_emp[ee].value).val();
                    if ($('#cantidad_piezas').val() != $('#total_piezas_' + ids_esp_emp[ee].value).val()) {
                        alerta('<div class="alert alert-warning text-center">Las cantidades de piezas distribuidas no coinciden con las pedidas</div>');
                        $('#cantidad_piezas').addClass('error');
                        return false;
                    }
                    arreglo_marcaciones = [];
                    arreglo_coloraciones = [];
                    for (f = 0; f < fil; f++) {
                        if ($('#nombre_marcacion_' + f + '_' + ids_esp_emp[ee].value).val() != '') {
                            colores = [];
                            for (c = 0; c < col; c++) {
                                cant_x_det_esp = [];
                                if (f == 0) {
                                    /* =========== PRECIOS x COLORACION ========= */
                                    arreglo_precios_x_col = [];
                                    for (det = 0; det < ids_det_esp.length; det++) {
                                        arreglo_precios_x_col.push({
                                            id_det_esp: ids_det_esp[det].value,
                                            precio: $('#precio_color_' + c + '_' + ids_det_esp[det].value + '_' + ids_esp_emp[ee].value).val()
                                        });
                                    }
                                    arreglo_coloraciones.push({
                                        id_color: $('#color_' + c + '_' + ids_esp_emp[ee].value).val(),
                                        arreglo_precios_x_col: arreglo_precios_x_col
                                    });
                                }
                                for (det = 0; det < ids_det_esp.length; det++) {
                                    cant_x_det_esp.push({
                                        id_det_esp: ids_det_esp[det].value,
                                        cantidad: $('#ramos_marcacion_' + f + '_' + c + '_' + ids_det_esp[det].value + '_' + ids_esp_emp[ee].value).val()
                                    });
                                }
                                colores.push({
                                    cant_x_det_esp: cant_x_det_esp
                                });
                            }
                            arreglo_marcaciones.push({
                                nombre: $('#nombre_marcacion_' + f + '_' + ids_esp_emp[ee].value).val(),
                                ramos: $('#total_ramos_marcacion_' + f + '_' + ids_esp_emp[ee].value).val(),
                                piezas: $('#total_piezas_marcacion_' + f + '_' + ids_esp_emp[ee].value).val(),
                                colores: colores
                            });
                        } else {
                            alerta('<div class="alert alert-warning text-center">Faltan datos (nombre de marcación) por ingresar</div>');
                            $('#nombre_marcacion_' + f + '_' + ids_esp_emp[ee].value).addClass('error');
                            return false;
                        }
                    }

                    arreglo_esp_emp.push({
                        id_esp_emp: ids_esp_emp[ee].value,
                        arreglo_precios: arreglo_precios,
                        arreglo_marcaciones: arreglo_marcaciones,
                        arreglo_coloraciones: arreglo_coloraciones,
                    });
                }

                ids_datos_exportacion = $('.id_dato_exportacion');
                arreglo_dat_exp = [];
                for (dat = 0; dat < ids_datos_exportacion.length; dat++) {
                    id_dat_exp = ids_datos_exportacion[dat].value;
                    arreglo_dat_exp.push({
                        id_dat_exp: id_dat_exp,
                        valor: $('#dato_exportacion_' + id_dat_exp).val().toUpperCase()
                    });
                }
                datos = {
                    _token: token,
                    id_pedido: $('#id_pedido').val(),
                    id_detalle_pedido: $('#id_detalle_pedido').val(),
                    fecha_pedido: $('#fecha_pedido').val(),
                    fecha_envio: $('#fecha_envio').val(),
                    cantidad_piezas: $('#cantidad_piezas').val(),
                    id_agencia_carga: $('#id_agencia_carga').val(),
                    arreglo_esp_emp: arreglo_esp_emp,
                    arreglo_dat_exp: arreglo_dat_exp
                };

                post_jquery('pedidos/update_orden_tinturada', datos, function () {
                    cerrar_modals();
                    editar_pedido_tinturado(datos['id_pedido'], $('#pos_det_ped').val(), false);
                    listar_resumen_pedidos($('#fecha_pedidos_search').val(), true);
                });
            });
    }
}

function terminar_edicion() {
    cerrar_modals();
    $("#listar_resumen_pedido").val() == 'true'
        ? listar_resumen_pedidos($('#fecha_pedidos_search').val(), true)
        : "";
}

function eliminar_detalle_pedido(det_ped, token) {
    datos = {
        _token: token,
        id_detalle_pedido: det_ped,
    };
    modal_quest('modal-quest_eliminar_detalle_pedido',
        '<div class="alert alert-warning text-center">¿Está seguro de eliminar este detalle del pedido?</div>',
        '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de alerta', true, false, '35%', function () {
            post_jquery('pedidos/eliminar_detalle_pedido_tinturado', datos, function () {
                cerrar_modals();
                if ($('#have_next').val() == 1 || $('#have_prev').val() == 1) {
                    editar_pedido_tinturado($('#id_pedido').val(), 0);
                }
                $("#listar_resumen_pedido").val() == 'true'
                    ? listar_resumen_pedidos($('#fecha_pedidos_search').val(), true)
                    : "";
            });
        });
}

function add_marcacion(esp_emp) {
    fil = parseInt($('#marcaciones_' + esp_emp).val());
    col = parseInt($('#coloraciones_' + esp_emp).val());

    tabla = $('#tabla_marcacion_coloracion_' + esp_emp);

    tr = '<tr style="border: 2px solid #9d9d9d">' +
        '<td class="text-center" style="border-color: #9d9d9d">' +
        '<input type="text" id="nombre_marcacion_' + fil + '_' + esp_emp + '" name="nombre_marcacion_' + fil + '_' + esp_emp + '" ' +
        'placeholder="Marc ' + (fil + 1) + '" width="150px" style="border: none" class="text-center">' +
        '<input type="hidden" id="id_marcacion_' + fil + '_' + esp_emp + '" name="id_marcacion_' + fil + '_' + esp_emp + '" value="">' +
        '</td>';
    for (c = 0; c < col; c++) {
        ids_det_esp = $('.id_det_esp_' + esp_emp);
        inputs = '';
        for (det = 0; det < ids_det_esp.length; det++) {
            inputs += '<li>' +
                '<div class="input-group" style="width: 100px">' +
                '<span class="input-group-addon" style="background-color: #e9ecef">' +
                'P-' + (det + 1) +
                '</span>' +
                '<input type="number" value="0" id="ramos_marcacion_' + fil + '_' + c + '_' + ids_det_esp[det].value + '_' + esp_emp + '" ' +
                'name="ramos_marcacion_' + fil + '_' + c + '_' + ids_det_esp[det].value + '_' + esp_emp + '" ' +
                'onkeypress="return isNumber(event)" style="width: 100%;" ' +
                'class="text-center elemento_color_' + c + '_' + esp_emp + '" onchange="calcular_totales_tinturado(' + esp_emp + ')">' +
                '</div>' +
                '</li>';
        }
        tr += '<td class="text-center" style="border-color: #9d9d9d">' +
            '<ul class="list-unstyled">' +
            inputs +
            '</ul>' +
            '</td>';
    }

    if (ids_det_esp.length > 1) {    // mixta
        ids_det_esp = $('.id_det_esp_' + esp_emp);
        inputs = '';
        for (det = 0; det < ids_det_esp.length; det++) {
            inputs += '<li>' +
                '<div class="input-group" style="width: 100px">' +
                '<span class="input-group-addon" style="background-color: #e9ecef">' +
                'P-' + (det + 1) +
                '</span>' +
                '<input type="number" id="parcial_marcacion_' + fil + '_' + ids_det_esp[det].value + '_' + esp_emp + '" ' +
                'name="parcial_marcacion_' + fil + '_' + ids_det_esp[det].value + '_' + esp_emp + '" value="0" ' +
                'style="width: 100%; background-color: #357ca5; color: white" class="text-center">' +
                '</div>' +
                '</li>';
        }
        tr += '<td class="text-center" style="border-color: #9d9d9d" width="100px">' +
            '<ul class="list-unstyled">' +
            inputs +
            '</ul>' +
            '</td>';
    }

    tr += '<td class="text-center" style="border-color: #9d9d9d">' +
        '<input type="text" id="total_ramos_marcacion_' + fil + '_' + esp_emp + '" name="total_ramos_marcacion_' + fil + '_' + esp_emp + '" ' +
        'readonly class="text-center" value="0" ' +
        'style="background-color: #357ca5; color: white; width: 85px">' +
        '</td>' +
        '<td class="text-center" style="border-color: #9d9d9d">' +
        '<input type="text" id="total_piezas_marcacion_' + fil + '_' + esp_emp + '" name="total_piezas_marcacion_' + fil + '_' + esp_emp + '" ' +
        'readonly class="text-center" value="0" ' +
        'style="background-color: #357ca5; color: white; width: 85px">' +
        '</td>';

    $(tr + '</tr>').insertAfter($(tabla).find('tr')[fil]);

    for (c = 0; c < col; c++) {
        cambiar_color($('#color_' + c + '_' + esp_emp).val(), c, esp_emp);
    }

    fil++;
    $('#marcaciones_' + esp_emp).val(fil);
    $('.elemento_distribuir').hide();
}

function add_coloracion(esp_emp) {
    col = parseInt($('#coloraciones_' + esp_emp).val());

    tabla = $('#tabla_marcacion_coloracion_' + esp_emp);
    columna = col;
    num_colum_a_insertar = 0;
    $(tabla).find('tr').each(function (f, row) { // recorremos todas sus rows
        var primer_td = $(row).find('td,th')[columna]; // obtenemos  columna (por que insertaremos despues de esta)
        if (primer_td.tagName == 'TH') {
            for (var i = 0; i <= num_colum_a_insertar; i++) {
                if (f == 0) {
                    //insertamos una cabecera despues de la primera cabecera
                    $('<th class="text-center" style="border-color: #9d9d9d" width="100px">' +
                        '<select name="color_' + col + '_' + esp_emp + '" id="color_' + col + '_' + esp_emp + '"' +
                        ' onchange="cambiar_color($(this).val(), ' + col + ', ' + esp_emp + ')">' +
                        $('#select_colores').html() +
                        '</select>' +
                        '<input type="hidden" id="id_color_' + col + '_' + esp_emp + '" name="id_color_' + col + '_' + esp_emp + '" ' +
                        'value="' + $('#select_colores').val() + '">' +
                        '</th>').insertAfter(primer_td);
                } else if (f == $(tabla).find('tr').length - 2) {
                    ids_det_esp = $('.id_det_esp_' + esp_emp);
                    inputs = '';
                    for (det = 0; det < ids_det_esp.length; det++) {
                        inputs += '<li>' +
                            '<div class="input-group" style="width: 100px">' +
                            '<span class="input-group-addon" style="background-color: #e9ecef">' +
                            'P-' + (det + 1) +
                            '</span>' +
                            '<input type="number" id="parcial_color_' + col + '_' + ids_det_esp[det].value + '_' + esp_emp + '" ' +
                            'name="parcial_color_' + col + '_' + ids_det_esp[det].value + '_' + esp_emp + '" value="0" ' +
                            'style="width: 100%; background-color: #357ca5; color: white" class="text-center">' +
                            '</div>' +
                            '</li>';
                    }
                    $('<th class="text-center" style="border-color: #9d9d9d" width="100px">' +
                        '<ul class="list-unstyled">' +
                        inputs +
                        '</ul>' +
                        '</th>').insertAfter(primer_td);
                } else {
                    ids_det_esp = $('.id_det_esp_' + esp_emp);
                    inputs = '';
                    for (det = 0; det < ids_det_esp.length; det++) {
                        inputs += '<li>' +
                            '<div class="input-group" style="width: 100px">' +
                            '<span class="input-group-addon" style="background-color: #e9ecef">' +
                            'P-' + (det + 1) +
                            '</span>' +
                            '<input type="number" id="precio_color_' + col + '_' + ids_det_esp[det].value + '_' + esp_emp + '" ' +
                            'name="precio_color_' + col + '_' + ids_det_esp[det].value + '_' + esp_emp + '" ' +
                            'style="width: 100%; background-color: #e9ecef" class="text-center">' +
                            '</div>' +
                            '</li>';
                    }
                    $('<th class="text-center" style="border-color: #9d9d9d" width="100px">' +
                        '<ul class="list-unstyled">' +
                        inputs +
                        '</ul>' +
                        '</th>').insertAfter(primer_td);
                }
            }
        } else {
            for (var i = 0; i <= num_colum_a_insertar; i++) {
                ids_det_esp = $('.id_det_esp_' + esp_emp);
                inputs = '';
                for (det = 0; det < ids_det_esp.length; det++) {
                    inputs += '<li>' +
                        '<div class="input-group" style="width: 100px">' +
                        '<span class="input-group-addon" style="background-color: #e9ecef">' +
                        'P-' + (det + 1) +
                        '</span>' +
                        '<input type="number" value="0" id="ramos_marcacion_' + (f - 1) + '_' + col + '_' + ids_det_esp[det].value + '_' + esp_emp + '" ' +
                        'name="ramos_marcacion_' + (f - 1) + '_' + col + '_' + ids_det_esp[det].value + '_' + esp_emp + '" ' +
                        'onkeypress="return isNumber(event)" style="width: 100%;" ' +
                        'class="text-center elemento_color_' + col + '_' + esp_emp + '" onchange="calcular_totales_tinturado(' + esp_emp + ')">' +
                        '</div>' +
                        '</li>';
                }
                //insertamos un valor despues del primer valor de la primera columna
                $('<td class="text-center" style="border-color: #9d9d9d;" width="100px">' +
                    '<ul class="list-unstyled">' +
                    inputs +
                    '</ul>' +
                    '</td>').insertAfter(primer_td);
            }
        }
    });
    cambiar_color($('#select_colores').val(), col, esp_emp);
    col++;
    $('#coloraciones_' + esp_emp).val(col);
    $('.elemento_distribuir').hide();
}

function buscar_listado_envios() {
    $.LoadingOverlay('show');
    datos = {
        id_cliente: $('#id_cliente').val(),
        fecha: $('#fecha').val(),
        estado: $('#estado').val(),
    };
    $.get('envio/buscar', datos, function (retorno) {
        $('#div_listado_envios').html(retorno);
    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

function calcular_precio_envio() {
    cant_forms = $("div#table_envios form").length;
    for (o = 1; o <= cant_forms; o++) {
        sub_total = 0.00;
        total_ramos = 0.00;
        total_piezas = 0.00;
        cant_rows = $(".input_cantidad_" + o).length;
        for (i = 1; i <= cant_rows; i++) {
            precio_especificacion = 0.00;
            ramos_totales_especificacion = 0.00;
            $.each($(".cantidad_" + o + "_" + i), function (p, q) {
                $.each($(".td_ramos_x_caja_" + o + "_" + i), function (a, b) {
                    ramos_totales_especificacion += (q.value * b.value);
                });
                $.each($(".precio_" + o + "_" + i), function (y, z) {
                    precio_variedad = z.value == "" ? 0 : z.value;
                    ramos_x_caja = $(".input_ramos_x_caja_" + o + "_" + i + "_" + (y + 1)).val();
                    precio_especificacion += (parseFloat(precio_variedad) * parseFloat(ramos_x_caja) * q.value);
                });
            });
            sub_total += parseFloat(precio_especificacion);
            $("#td_total_ramos_" + o + "_" + i).html(parseFloat(ramos_totales_especificacion));
            total_ramos += ramos_totales_especificacion;
            $("#td_precio_especificacion_" + o + "_" + i).html("$" + parseFloat(precio_especificacion).toFixed(2));
            total_piezas += parseInt($(".cantidad_" + o + "_" + i).val());
        }
        !isNaN($("#porcentaje_impuesto_" + o).val())
            ? total = (parseFloat(sub_total) + parseFloat(((sub_total * parseFloat($("#porcentaje_impuesto_" + o).val())) / 100).toFixed(2))).toFixed(2)
            : total = sub_total;

        $("#total_piezas_" + o).html(total_piezas);
        $("#total_ramos_" + o).html(total_ramos);
        $("#sub_total_" + o).html(sub_total.toFixed(2));
        $("#total_" + o).html(parseFloat(total).toFixed(2));
    }
}

function distribuir_pedido_tinturado(det_ped, auto = false, token) {
    ids_esp_emp = $('.id_esp_emp');
    arreglo_esp_emp = [];
    for (ee = 0; ee < ids_esp_emp.length; ee++) {
        esp_emp = ids_esp_emp[ee].value;
        fil = $('#marcaciones_' + esp_emp).val();
        marcaciones = [];

        for (f = 0; f < fil; f++) {
            marcaciones.push({
                id: $('#id_marcacion_' + f + '_' + esp_emp).val(),
                distribucion: $('#distribucion_marcacion_' + f + '_' + esp_emp).val(),
            });
        }

        arreglo_esp_emp.push({
            id_esp_emp: esp_emp,
            marcaciones: marcaciones,
        });
    }

    if (auto == true) {
        modal_quest('modal-quest_auto_distribuir',
            '<div class="alert alert-info text-center">¿Desea realizar la distribución automaticamente?</div>',
            '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de confirmación', true, false, '', function () {
                datos = {
                    _token: token,
                    id_det_ped: det_ped,
                    arreglo_esp_emp: arreglo_esp_emp,
                };
                post_jquery('pedidos/auto_distribuir_pedido_tinturado', datos, function () {
                    cerrar_modals();
                    editar_pedido_tinturado($('#id_pedido').val(), 0);
                });
            });
    } else {
        datos = {
            id_det_ped: det_ped,
            arreglo_esp_emp: arreglo_esp_emp,
        };
        get_jquery('pedidos/distribuir_pedido_tinturado', datos, function (retorno) {
            $('#div_tabla_distribucion').html(retorno);
            $('#btn_guardar_distribucion').show();
            $('#btn_update_orden_tinturada').hide();
        });
    }
}

function guardar_distribucion(token) {
    ids_esp_emp = $('.id_esp_emp');
    arreglo_esp_emp = [];
    for (ee = 0; ee < ids_esp_emp.length; ee++) {
        id_esp_emp = ids_esp_emp[ee].value;
        ids_det_esp = $('.id_det_esp_' + id_esp_emp);
        marcaciones = [];
        ids_marcaciones = $('.id_marcacion_' + id_esp_emp);
        for (m = 0; m < ids_marcaciones.length; m++) {
            id_marca = ids_marcaciones[m].value;
            distribuciones = [];
            cant_distr = parseInt($('#cantidad_distribuciones_' + id_marca + '_' + id_esp_emp).val());
            for (d = 1; d <= cant_distr; d++) {
                ids_coloraciones = $('.id_coloracion_' + id_esp_emp);
                coloraciones = [];
                r = 0;
                for (c = 0; c < ids_coloraciones.length; c++) {
                    id_col = ids_coloraciones[c].value;
                    detalles_esp = [];
                    for (det = 0; det < ids_det_esp.length; det++) {
                        id_det_esp = ids_det_esp[det].value;
                        detalles_esp.push({
                            id_det_esp: id_det_esp,
                            cant: $('#distribucion_' + id_marca + '_' + id_col + '_' + id_det_esp + '_' + d + '_' + id_esp_emp).val()
                        });

                        /* ======= VALIDAR CANTIDADES ======= */
                        r += parseInt($('#distribucion_' + id_marca + '_' + id_col + '_' + id_det_esp + '_' + d + '_' + id_esp_emp).val());
                    }
                    coloraciones.push({
                        id_coloracion: id_col,
                        detalles_esp: detalles_esp
                    });
                }
                if (r != parseInt($('#input_ramos_distribucion_' + id_marca + '_' + d + '_' + id_esp_emp).val())) {
                    alerta('La distribución de los ramos no coinciden con los totales');
                    $('#celda_ramos_' + id_marca + '_' + d + '_' + id_esp_emp).addClass('error');
                    return false;
                }
                distribuciones.push({
                    ramos: $('#input_ramos_distribucion_' + id_marca + '_' + d + '_' + id_esp_emp).val(),
                    piezas: $('#select_distribuir_' + id_marca + '_' + d + '_' + id_esp_emp).val(),
                    pos_pieza: $('#n_cajas_' + id_marca + '_' + d + '_' + id_esp_emp).val(),
                    coloraciones: coloraciones
                });
            }
            marcaciones.push({
                id_marcacion: id_marca,
                distribuciones: distribuciones
            });
        }
        arreglo_esp_emp.push({
            id_esp_emp: id_esp_emp,
            marcaciones: marcaciones
        });
    }
    datos = {
        _token: token,
        arreglo_esp_emp: arreglo_esp_emp
    };
    post_jquery('pedidos/guardar_distribucion', datos, function () {

    });
}

function ver_distribucion(det_ped) {
    datos = {
        id_det_ped: det_ped
    };
    get_jquery('pedidos/ver_distribucion', datos, function (retorno) {
        $('#div_tabla_distribucion').html(retorno);
        $('#btn_guardar_distribucion').hide();
        $('#btn_update_orden_tinturada').hide();
    });
}

function menor_mayor(elem1, elem2) {
    return elem1 - elem2;
}

function quitar_distribuciones(id_ped, token) {
    datos = {
        _token: token,
        id_ped: id_ped
    };
    modal_quest('modal-quest_quitar_distribuciones', '<div class="alert alert-info text-center">' +
        '¿Está seguro de eliminar todas las distribuciones del pedido?</div>',
        '<i class="fa fa-fw fa-exclamation-triangle"></i> Mensaje de alerta', true, false, '35%', function () {
            post_jquery('pedidos/quitar_distribuciones', datos, function (retorno) {
                editar_pedido_tinturado(id_ped, 0, false);
            });
        });
}

function crear_orden_pedido(input) {
    id_campo = input.id.split("_")[2];
    orden = 0;
    $.each($('.orden'), function (i, j) {
        if (j.value !== "")
            orden++;
    });
    if ($("#orden_" + id_campo).val() == "")
        $("#orden_" + id_campo).val(orden + 1);

    if ($("#cantidad_piezas_" + id_campo).val() == "") {
        orden--;
        $("#orden_" + id_campo).val("");
    }
}

function reiniciar_orden_pedido() {
    $.each($('.orden'), function (i, j) {
        j.value = ""
    });
    $.each($('.input_cantidad'), function (i, j) {
        j.value = ""
    })
}

function facturar_pedido(id_pedido) {
    $.LoadingOverlay('show');
    datos = {
        id_pedido: id_pedido
    }
    $.get('pedidos/facturar_pedido', datos, function (retorno) {
        modal_view('modal_view_Facturar_pedido', retorno, '<i class="fa fa-cubes"></i> Pedido', true, false, '80%');
    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

function genera_comprobante_cliente(id_envio, form, action, token, id_comprobante) {
    if ($('#' + form).valid()) {
        id_form = form.split("_")[2];
        //COMENTADO PARA QUE LA FACTURACION FUNCIONE CON EL VENTURE
        // modal_quest('modal_message_facturar_envios',
        //'<div class="alert alert-info text-center">  <label>Se generará el comprobante electrónico para este envío</label></div>' +
        //'<div class="alert alert-info text-center"> <input type="checkbox" id="envio_correo" name="envio_correo"style="position: relative;top: 3px;" checked> <label for="envio_correo">¿Enviar Correo electrónico al cliente ?</label> </div>' +
        //'<div class="alert alert-info text-center"> <input type="checkbox" id="envio_correo_agencia_carga" name="envio_correo_agencia_carga"style="position: relative;top: 3px;" checked> <label for="envio_correo_agencia_carga">¿Enviar Correo electrónico a la Agencia de carga?</label> </div>',
        //'<i class="fa fa-file-code-o" aria-hidden="true"></i> Se realizaran las siguientes acciones', true, false, '40%', function () {

        arrCorreos = [];
        //COMENTADO PARA QUE LA FACTURACION FUNCIONE CON EL VENTURE
        //$.each($('input[name=correo_extra]'), function (i, j) {
        //arrCorreos.push({correo: j.value})
        //});
        datos = {
            _token: token,
            id_envio: id_envio,
            guia_madre: $("form#" + form + " #guia_madre").val(),
            guia_hija: $("form#" + form + " #guia_hija").val(),
            codigo_pais: $("form#" + form + " #codigo_pais").val(),
            dae: $("form#" + form + " #dae").val(),
            destino: $("form#" + form + " #direccion").val(),
            email: $("form#" + form + " #email").val(),
            telefono: $("form#" + form + " #telefono").val(),
            pais: $("form#" + form + " #codigo_pais option:selected").text(),
            fecha_envio: $("form#" + form + " #fecha_envio").val(),
            cant_variedades: $("form#" + form + " table tbody#tbody_inputs_pedidos tr").length,
            update: action == 'update' ? true : false,
            almacen: $("form#" + form + " #almacen").val(),
            envio_correo: $("#envio_correo").is(":checked"),
            envio_correo_agencia_carga: $("#envio_correo_agencia_carga").is(":checked"),
            arrCorreos: arrCorreos,
            fecha_pedidos_search: $("#fecha_pedidos_search").val(),
            id_comprobante: id_comprobante
        };
        cerrar_modals();
        $.LoadingOverlay("show", {
            image: "",
            progress: true,
            text: "Registrando datos de la factura",//"Generando documento electrónico...", COMENTADO PARA QUE LA FACTURACION FUNCIONE CON EL VENTURE
            textColor: "#fff",
            progressColor: "#00a65a",
            progressResizeFactor: "0.20",
            background: "rgba(0, 0, 0, 0.75)"
        });
        var count = 0;
        var tiempo = 2000;
        var interval = setInterval(function () {
            if (count >= 15 && count < 99)
            //$.LoadingOverlay("text", "Firmado documento electrónico...");
                if (count >= 100) {
                    clearInterval(interval);
                    return;
                }
            count += 100;
            $.LoadingOverlay("progress", count);
        }, tiempo);
        $.get('comprobante/generar_comprobante_factura', datos, function (retorno) {
            modal_view('modal_view_msg_factura', retorno, '<i class="fa fa-check" aria-hidden="true"></i> Estatus facturas', true, false, '50%');
            buscar_listado_envios();
        }).always(function () {
            $.LoadingOverlay("hide");
        });
        // });
    }
}

function activar(input_descuento, id_check) {
    var id = input_descuento.id.split("_")[1];
    $("#" + input_descuento.id,).removeAttr("readonly");
    $("#muestra_descuento_" + id).removeAttr("disabled");
}

function input_required(input) {
    if ($("input[type='checkbox']#" + input.id).is(':checked')) {
        $("#destino_" + input.id).attr('required', true);
    } else {
        $("#destino_" + input.id).attr('required', false);
    }
}

function buscar_codigo_dae(input, form, factura_cliente_tercero) {
    $.LoadingOverlay('show');
    datos = {
        codigo_pais: input.value,
        fecha_envio: $("form#" + form + " #fecha_envio").val()
    };
    $.get('envio/buscar_codigo_dae', datos, function (retorno) {
        if (factura_cliente_tercero) {
            $("form#" + form + " #dae_cliente_tercero").val(retorno.codigo_dae);
            $("form#" + form + " #codigo_dae_cliente_tercero").val(retorno.dae);
        } else {
            $("form#" + form + " #dae").val(retorno.codigo_dae);
            $("form#" + form + " #codigo_dae").val(retorno.dae);
        }
        //retorno.codigo_dae == "" ? $("form#"+form+" #dae").removeAttr('disabled') : $("form#"+form+" #dae").attr('disabled','disabled');

        if (retorno.codigo_empresa === datos.codigo_pais) {
            $("form#" + form + " #dae").removeAttr('required').val('');
            $("form#" + form + " #codigo_dae").removeAttr('required').val('');
            $("form#" + form + " #dae_cliente_tercero").removeAttr('required').val('');
        } else {
            $("form#" + form + " #dae").attr('required', true).val(retorno.codigo_dae);
            $("form#" + form + " #codigo_dae").attr('required', true).val(retorno.dae);
            $("form#" + form + " #dae_cliente_tercero").attr('required', true).val(retorno.codigo_dae);

        }
    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

function actualizar_envio(id_envio, form, tipo_pedido, token, id_pedido, vista) {
    if ($("#" + form).valid()) {
        $.LoadingOverlay('show');

        id_form = form.split("_")[2];
        cant_rows = $(".input_cantidad_" + id_form).length;
        arrDataPrecio = [];
        for (i = 1; i <= cant_rows; i++) {
            precio = '';
            $.each($('.precio_' + id_form + "_" + i), function (j, k) {
                precio += k.value + ";" + $(".id_detalle_esp_emp_" + id_form + "_" + i)[j].value + "|";

            });
            arrDataPrecio.push({
                precios: precio,
                piezas: $(".cantidad_" + id_form + "_" + i).val()
            });
        }

        datos = {
            _token: token,
            id_envio: id_envio,
            dae: $("form#" + form + " #dae").val(),
            guia_madre: $("form#" + form + " #guia_madre").val(),
            guia_hija: $("form#" + form + " #guia_hija").val(),
            codigo_pais: $("form#" + form + " #codigo_pais").val(),
            email: $("form#" + form + " #email").val(),
            telefono: $("form#" + form + " #telefono").val(),
            direccion: $("form#" + form + " #direccion").val(),
            fecha_envio: $("form#" + form + " #fecha_envio").val(),
            aerolinea: $("form#" + form + " #aerolinea").val(),
            precios: arrDataPrecio,
            almacen: $("form#" + form + " #almacen").val(),
            tipo_pedido: tipo_pedido,
            codigo_dae: $("#codigo_dae").val()
        };
        $.post('envio/actualizar_envio', datos, function (retorno) {
            if (retorno.success) {
                cerrar_modals();
                if (vista === "pedidos/facturar_pedido")
                    facturar_pedido(id_pedido);
                if (vista === "envio/buscar")
                    buscar_listado_envios();
                modal_view('modal_editar_envio', retorno.mensaje, '<i class="fa fa-user-plus" aria-hidden="true"></i> Editar pedido', true, false, '50%');
            } else {
                alerta(retorno.mensaje);
            }
        }, 'json').fail(function (retorno) {
            alerta(retorno.responseText);
            alerta('Ha ocurrido un problema al guardar los datos del envío');
        }).always(function () {
            $.LoadingOverlay('hide');
        });
    }
}

function factura_tercero(id_envio, token, id_pedido, vista) {
    datos = {
        id_envio: id_envio
    };
    $.get('envio/factura_cliente_tercero', datos, function (retorno) {
        modal_form('modal_factura_cliente_tercero', retorno, '<i class="fa fa-user-plus" aria-hidden="true">' +
            '</i> Datos del cliente a facturar', true, false, '75%', function () {
            store_datos_factura_cliente_tercero(id_envio, token, id_pedido, vista);
        });
    }).always(function () {
        $.LoadingOverlay("hide");
    });
}

function store_datos_factura_cliente_tercero(id_envio, token, id_pedido, vista) {
    if ($('#form_add_cliente_factura_tercero').valid()) {
        $.LoadingOverlay('show');
        datos = {
            _token: token,
            id_factura_cliente_tercero: $('#id_factura_cliente_tercero').val(),
            id_envio: id_envio,
            nombre: $('#nombre_cliente_tercero').val(),
            identificacion: $('#identificacion').val(),
            codigo_pais: $("#pais_cliente_tercero").val(),
            provincia: $("#provincia_cliente_tercero").val(),
            correo: $("#correo_cliente_tercero").val(),
            telefono: $("#telefono_cliente_tercero").val(),
            direccion: $("#direccion_cliente_tercero").val(),
            codigo_impuesto: $("#codigo_impuesto").val(),

            tipo_identificacion: $('#tipo_identificacion').val(),
            codigo_impuesto_porcentaje: $('#tipo_impuesto').val(),
            almacen: $('#almacen_cliente_tercero').val(),
            dae: $('#dae_cliente_tercero').val(),
            puerto_entrada: $('#puerto_entrada').val(),
            tipo_credito: $('#tipo_credito').val(),
            marca: $('#marca').val(),
            codigo_dae: $("#codigo_dae_cliente_tercero").val()
        };
        post_jquery('envio/store_datos_factura_cliente_tercero', datos, function () {
            cerrar_modals();
            if (vista === "pedidos/facturar_pedido")
                facturar_pedido(id_pedido);
            if (vista === "envio/buscar")
                buscar_listado_envios();
        });
        $.LoadingOverlay('hide');
    }
}

function delete_factura_tercero(id_envio, token, id_pedido, vista) {
    modal_quest('modal_message_delete_factura_tercero',
        '<div class="alert alert-warning text-center"><i class="fa fa-fw fa-exclamation-triangle"></i> ¿Esta seguro que desea eliminar los datos del cliente a facturar?</div>',
        '<i class="fa fa-file-code-o" aria-hidden="true"></i>Datos de facturación', true, false, '40%', function () {
            $.LoadingOverlay('show');
            datos = {
                _token: token,
                id_envio: id_envio,
            };
            post_jquery('envio/delete_datos_factura_cliente_tercero', datos, function () {
                cerrar_modals();
                if (vista === "pedidos/facturar_pedido")
                    facturar_pedido(id_pedido);
                if (vista === "envio/buscar")
                    buscar_listado_envios();
            });
            $.LoadingOverlay('hide');
        });
}

function agregar_correo(form) {
    cant_input = $('form#' + form + " div#correos_extras div#div_correos").length;
    datos = {
        cant_input: cant_input
    };
    $.get('envio/agregar_correo', datos, function (retorno) {
        $('form#' + form + " div#correos_extras").append(retorno);
    });
}

function eliminar_correo(form) {
    $('form#' + form + " div#correos_extras div:last-child").remove();
}

function cambiar_input_precio(idDetEmp, id_precio, posicon_variedad) {
    $('#td_precio_variedad_' + idDetEmp + '_' + id_precio).html('<input type="number" id="precio_' + id_precio + '_' + posicon_variedad + '" ' +
        'name="precio_' + idDetEmp + '" min="0" value="0" onchange="calcular_precio_pedido()" class="form-control text-center precio_' + id_precio + '" style="background-color: beige; width: 100%;text-align: left" required>');

}

function borrar_duplicado() {
    $(".tr_remove_" + $(".input_cantidad").length).remove();
    calcular_precio_pedido();
}

function busqueda_camiones_conductores(id_form) {
    datos = {
        id_transportista: $("form#" + id_form + " #id_transportista").val()
    };
    $.get('despachos/list_camiones_conductores', datos, function (retorno) {
        $.each(retorno.camiones, function (i, j) {
            $("form#" + id_form + " #id_camion").append("<option id='camion_dinamic' value='" + j.id_camion + "'>" + j.modelo + "</option>");
            if (i === 0)
                $("#n_placa").val(j.placa);
        });
        $.each(retorno.conductores, function (i, j) {
            $("form#" + id_form + " #id_chofer").append("<option id='chofer_dinamic' value='" + j.id_conductor + "'>" + j.nombre + "</option>")
        });

    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

function busqueda_placa_camion(id_form) {
    datos = {
        id_camion: $("form#" + id_form + " #id_camion").val()
    };
    $.get('despachos/list_placa_camion', datos, function (retorno) {
        $("form#" + id_form + " #n_placa").val(retorno.placa);
    }).always(function () {
        $.LoadingOverlay('hide');
    });
}

function exportar_listado_despacho(token) {
    $.LoadingOverlay('show');
    $.ajax({
        type: "POST",
        dataType: "html",
        contentType: "application/x-www-form-urlencoded",
        url: 'despachos/exportar_pedidos_despacho',
        data: {
            fecha_pedido: $("#fecha_pedidos_search").val(),
            _token: token
        },
        success: function (data) {
            var opResult = JSON.parse(data);
            var $a = $("<a>");
            $a.attr("href", opResult.data);
            $("body").append($a);
            $a.attr("download", "Despachos " + $("#fecha_pedidos_search").val() + " .xlsx");
            $a[0].click();
            $a.remove();
            cerrar_modals();
            $.LoadingOverlay('hide');
        }
    });
}
