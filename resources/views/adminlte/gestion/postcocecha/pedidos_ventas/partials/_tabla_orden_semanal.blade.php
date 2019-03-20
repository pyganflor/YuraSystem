<table class="table-striped table-responsive table-bordered" width="100%" style="border: 1px solid #9d9d9d">
    <tr>
        <th class="text-center" style="border-color: #9d9d9d; padding: 0" id="th_menu">
            <input type="number" id="num_marcaciones" name="num_marcaciones" onkeypress="return isNumber(event)" placeholder="Marcaciones"
                   min="1" class="text-center">
            <input type="number" id="num_colores" name="num_colores" onkeypress="return isNumber(event)" placeholder="Colores"
                   min="1" class="text-center">
            <button type="button" class="btn btn-xs btn-primary" onclick="construir_tabla()" style="margin-top: 0">
                <i class="fa fa-fw fa-check"></i> Siguiente
            </button>
        </th>
    </tr>
</table>

<div style="width: 100%; overflow-x: scroll; display: none" id="div_tabla_distribucion_pedido">
    <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d; margin-top: 10px">
        <tr>
            <td style="border-color: #9d9d9d; padding: 0;" width="100%">
                <table class="table-striped table-responsive table-bordered" width="100%" style="border: 1px solid #9d9d9d"
                       id="table_marcaciones_x_colores"></table>
            </td>
        </tr>
    </table>
</div>

<div id="div_especificaciones_orden_semanal" style="margin-top: 10px"></div>

<div class="text-center" style="margin-top: 10px">
    <button type="button" class="btn btn-xs btn-success" onclick="store_orden_semanal()">
        <i class="fa fa-fw fa-check"></i> Continuar
    </button>
</div>

<script>
    function store_orden_semanal() {
        if ($('#form_add_orden_semanal').valid()) {
            col = $('#num_colores').val();
            fil = $('#num_marcaciones').val();

            if (col > 0 && fil > 0) {
                if ($('#total_ramos_' + fil + '_' + col).val() == parseInt($('#cantidad_ramos').val()) * parseInt($('#cantidad_cajas').val()) &&
                    $('#total_piezas_' + fil + '_' + col).val() == $('#cantidad_cajas').val()) {
                    marcaciones = [];
                    colores = [];
                    matrix = [];

                    for (f = 1; f <= fil; f++) {
                        if ($('#nombre_marcacion_' + c).val() != '') {
                            columnas = [];
                            for (c = 1; c <= col; c++) {
                                if ($('#titles_columnas_' + c).val() != '') {
                                    columnas.push($('#ramos_marcacion_color_' + f + '_' + c).val());
                                } else {
                                    alert('Faltan datos (nombre de colores) por ingresar en la tabla.');
                                    return false;
                                }
                            }
                            columnas.push($('#input_total_ramos_marcacion_' + f).val());
                            columnas.push($('#input_total_piezas_' + f).val());
                            matrix.push(columnas);
                            marcaciones.push($('#nombre_marcacion_' + f).val());
                        } else {
                            alert('Faltan datos (nombre de marcaciones) por ingresar en la tabla.');
                            return false;
                        }
                    }

                    for (i = 1; i <= col; i++) {
                        id_color = $('#titles_columnas_' + i).val();
                        colores.push({
                            nombre: $('#nombre_color_' + id_color).val(),
                            fondo: $('#fondo_color_' + id_color).val(),
                            texto: $('#texto_color_' + id_color).val(),
                        });
                    }

                    datos = {
                        _token: '{{csrf_token()}}',
                        fecha_pedido: $('#fecha_pedido').val(),
                        id_cliente: $('#id_cliente_orden_semanal').val(),
                        id_agencia_carga: $('#id_agencia_carga').val(),
                        cantidad_cajas: $('#cantidad_cajas').val(),
                        id_empaque: $('#id_empaque').val(),
                        cantidad_ramos: $('#cantidad_ramos').val(),
                        id_clasificacion_ramo: $('#id_clasificacion_ramo').val(),
                        id_variedad: $('#id_variedad').val(),
                        /*id_empaque_e: $('#id_empaque_e').val(),*/
                        id_empaque_p: $('#id_empaque_p').val(),
                        longitud_ramo: $('#longitud_ramo').val(),
                        tallos_x_ramos: $('#tallos_x_ramos').val(),
                        id_unidad_medida: $('#id_unidad_medida').val(),
                        matrix: matrix,
                        marcaciones: marcaciones,
                        colores: colores,
                    };

                    $.LoadingOverlay('show');
                    $.post('{{url('pedidos/store_orden_semanal')}}', datos, function (retorno) {
                        if (retorno.success) {
                            alerta_accion(retorno.mensaje, function () {
                                alert(5555);
                                cerrar_modals();
                                distribuir_orden_semanal(retorno.id_pedido);
                                listar_resumen_pedidos($("#fecha_pedidos_search").val(), true);
                            });
                        } else {
                            alerta(retorno.mensaje);
                        }

                    }, 'json').fail(function (retorno) {
                        console.log(retorno);
                        alerta_errores(retorno.responseText);
                        alerta('Ha ocurrido un problema');
                    }).always(function () {
                        $.LoadingOverlay('hide');
                    });
                } else {
                    alerta('<div class="alert alert-info text-center">' +
                        'La cantidad de ramos totales no coinciden con la cantidad de ramos especificados en el pedido</div>');
                }
            } else {
                alerta('<div class="alert alert-info text-center">Faltan los datos de las marcaciones/colores</div>');
            }
        }
    }
</script>