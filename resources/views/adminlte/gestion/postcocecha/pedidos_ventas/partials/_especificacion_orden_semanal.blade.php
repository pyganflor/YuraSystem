@if($cliente != '')
    @foreach($cliente->cliente_pedido_especificaciones as $cli_ped_esp)
        @if($cli_ped_esp->especificacion->tipo == 'N')
            <div class="well sombra_estandar">
                <table width="100%" class="table-responsive table-bordered table-striped"
                       style="font-size: 0.9em; margin-top: 10px; border: 3px solid #9d9d9d"
                       id="table_especificacion_orden_semanal_{{$cli_ped_esp->id_especificacion}}">
                    <thead>
                    <tr style="background-color: #e9ecef">
                        <th class="text-center" style="border-color: #9d9d9d" width="75px">
                            CANTIDAD
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            VARIEDAD
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d" width="150px">
                            CALIBRE
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            CAJA
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d" width="75px">
                            RAMOS X CAJA
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            PRESENTACIÓN
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d" width="75px">
                            TALLOS X RAMO
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d" width="75px">
                            LONGITUD
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            U. MEDIDA
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d" width="85px">
                            PRECIO
                        </th>
                    </tr>
                    </thead>
                    @foreach($cli_ped_esp->especificacion->especificacionesEmpaque as $pos_esp_emp => $esp_emp)
                        @foreach($esp_emp->detalles as $pos_det_esp_emp => $det_esp_emp)
                            <tr onmouseover="$(this).css('background-color','#ADD8E6')" onmouseleave="$(this).css('background-color','')">
                                @if($pos_esp_emp == 0 && $pos_det_esp_emp == 0)
                                    <td class="text-center" style="border-color: #9d9d9d"
                                        rowspan="{{getCantidadDetallesByEspecificacion($cli_ped_esp->id_especificacion)}}">
                                        <input type="number" id="cant_piezas_esp_{{$cli_ped_esp->id_especificacion}}" style="width: 100%"
                                               name="cant_piezas_esp_{{$cli_ped_esp->id_especificacion}}" class="form-control">
                                    </td>
                                @endif
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp_emp->variedad->nombre}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp_emp->clasificacion_ramo->nombre}}{{$det_esp_emp->clasificacion_ramo->unidad_medida->siglas}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{explode('|',$esp_emp->empaque->nombre)[0]}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp_emp->cantidad}}
                                    <input type="hidden" id="cantidad_ramos_esp_{{$cli_ped_esp->id_especificacion}}"
                                           value="{{$det_esp_emp->cantidad}}">
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp_emp->empaque_p->nombre}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp_emp->tallos_x_ramo}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp_emp->longitud_ramo}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    @if($det_esp_emp->id_unidad_medida != '')
                                        {{$det_esp_emp->unidad_medida->siglas}}
                                    @endif
                                </td>
                                @if($pos_esp_emp == 0 && $pos_det_esp_emp == 0)
                                    <td class="text-center" style="border-color: #9d9d9d" id="td_precio_esp_{{$cli_ped_esp->id_especificacion}}"
                                        rowspan="{{getCantidadDetallesByEspecificacion($cli_ped_esp->id_especificacion)}}">
                                        <select name="precio_esp_{{$cli_ped_esp->id_especificacion}}" class="form-control"
                                                id="precio_esp_{{$cli_ped_esp->id_especificacion}}"
                                                ondblclick="cambiar_input_precio('{{$cli_ped_esp->id_especificacion}}')">
                                            @foreach(explode('|',$cli_ped_esp->precio) as $precio)
                                                <option value="{{$precio}}">{{$precio}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                </table>

                <table class="table-striped table-responsive table-bordered" width="100%" style="border: 1px solid #9d9d9d">
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; padding: 0" id="th_menu">
                            <input type="number" id="num_marcaciones_{{$cli_ped_esp->id_especificacion}}"
                                   name="num_marcaciones_{{$cli_ped_esp->id_especificacion}}" onkeypress="return isNumber(event)"
                                   placeholder="Marcaciones" min="1" class="text-center">
                            <input type="number" id="num_colores_{{$cli_ped_esp->id_especificacion}}"
                                   name="num_colores_{{$cli_ped_esp->id_especificacion}}" onkeypress="return isNumber(event)"
                                   placeholder="Colores"
                                   min="1" class="text-center">
                            <button type="button" class="btn btn-xs btn-primary"
                                    onclick="construir_tabla_especificacion_orden_semanal('{{$cli_ped_esp->id_especificacion}}')"
                                    style="margin-top: 0">
                                <i class="fa fa-fw fa-check"></i> Siguiente
                            </button>
                        </th>
                    </tr>
                </table>

                <div style="width: 100%; overflow-x: scroll; display: none"
                     id="div_tabla_distribucion_pedido_{{$cli_ped_esp->id_especificacion}}">
                    <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d; margin-top: 10px">
                        <tr>
                            <td style="border-color: #9d9d9d; padding: 0;" width="100%">
                                <table class="table-striped table-responsive table-bordered" width="100%" style="border: 1px solid #9d9d9d"
                                       id="table_marcaciones_x_colores_{{$cli_ped_esp->id_especificacion}}"></table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif
    @endforeach
@endif

<script>
    function cambiar_input_precio(id_esp) {
        $('#td_precio_esp_' + id_esp).html('<input type="number" id="precio_esp_' + id_esp + '" ' +
            'name="precio_esp_' + id_esp + '" class="form-control">');
    }

    function construir_tabla_especificacion_orden_semanal(id_esp) {
        col = $('#num_colores_' + id_esp).val();
        fil = $('#num_marcaciones_' + id_esp).val();

        $('.row_marcaciones_colores_' + id_esp).remove();
        $('#div_tabla_distribucion_pedido_' + id_esp).show();

        for (f = 0; f <= fil; f++) {
            html_columnas = '';
            titles_columnas = '';
            for (c = 1; c <= col; c++) {
                html_columnas += '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="number" id="ramos_marcacion_color_' + f + '_' + c + '_' + id_esp + '" ' +
                    'name="ramos_marcacion_color_' + f + '_' + c + '_' + id_esp + '" onkeypress="return isNumber(event)"' +
                    ' style="width: 150px" class="text-center input_ramos_marc_col_' + c + '_' + id_esp + '" ' +
                    'onchange="calcular_total_ramos_x_esp(' + c + ',' + f + ', ' + id_esp + ')">' +
                    '</td>';
                titles_columnas += '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<select id="titles_columnas_' + c + '_' + id_esp + '" name="titles_columnas_' + c + '_' + id_esp + '"' +
                    ' style="width: 150px" class="text-center" onchange="seleccionar_color_x_esp(' + c + ', ' + id_esp + ')">' +
                    '<option value="">C # ' + c + '</option>' + $('#select_colores').html() +
                    '</select>' +
                    '</th>';
            }
            if (f == 0) {
                $('#table_marcaciones_x_colores_' + id_esp).append('<tr class="row_marcaciones_colores_' + id_esp + '">' +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 350px">Color</th>' + titles_columnas +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 200px">Total</th>' +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 200px">Piezas</th>' +
                    '</tr>');
            } else {
                $('#table_marcaciones_x_colores_' + id_esp).append('<tr class="row_marcaciones_colores_' + id_esp + '">' +
                    '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" id="nombre_marcacion_' + f + '_' + id_esp + '" name="nombre_marcacion_' + f + '_' + id_esp + '"' +
                    ' style="width: 150px" placeholder="M #' + f + '" class="text-center">' +
                    '</th>' + html_columnas +
                    '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" class="text-center" readonly id="input_total_ramos_marcacion_' + f + '_' + id_esp + '" value="0"' +
                    ' style="background-color: #357CA5; color: white">' +
                    '</td>' +
                    '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" class="text-center" readonly id="input_total_piezas_' + f + '_' + id_esp + '" value="0"' +
                    ' style="background-color: #357CA5; color: white">' +
                    '</td>' +
                    '</tr>');
            }
        }

        total_ramos = '';
        for (c = 1; c <= col; c++) {
            total_ramos += '<td class="text-center" style="border-color: #9d9d9d; background-color: #357CA5">' +
                '<input type="text" class="text-center" readonly id="input_total_ramos_' + c + '_' + id_esp + '" value="0"' +
                ' style="background-color: #357CA5; color: white">' +
                '</td>';
        }
        $('#table_marcaciones_x_colores_' + id_esp).append('<tr class="row_marcaciones_colores_' + id_esp + '">' +
            '<th class="text-center" style="border-color: #9d9d9d">Ramos</th>' + total_ramos +
            '<th class="text-center" style="border-color: #9d9d9d">' +
            '<input type="text" readonly id="total_ramos_' + fil + '_' + col + '_' + id_esp + '" ' +
            'name="total_ramos_' + fil + '_' + col + '_' + id_esp + ' "' +
            ' value="0" style="background-color: #9d9d9d; color: white; width: 100%" class="text-center">' +
            '</th>' +
            '<th class="text-center" style="border-color: #9d9d9d">' +
            '<input type="text" readonly id="total_piezas_' + fil + '_' + col + '_' + id_esp + '" ' +
            'name="total_piezas_' + fil + '_' + col + '_' + id_esp + '"' +
            ' value="0" style="background-color: #9d9d9d; color: white; width: 100%" class="text-center">' +
            '</th>' +
            '</tr>'
        )
        ;
    }

    function calcular_total_ramos_x_esp(c, f, id_esp) {
        fil = $('#num_marcaciones_' + id_esp).val();
        col = $('#num_colores_' + id_esp).val();
        cantidad_ramos = parseInt($('#cantidad_ramos_esp_' + id_esp).val());

        total_col = 0;
        for (i = 1; i <= fil; i++) {
            if ($('#ramos_marcacion_color_' + i + '_' + c + '_' + id_esp).val() != '')
                total_col += parseInt($('#ramos_marcacion_color_' + i + '_' + c + '_' + id_esp).val());
        }

        $('#input_total_ramos_' + c + '_' + id_esp).val(total_col);

        total_fila = 0;
        for (i = 1; i <= col; i++) {
            if ($('#ramos_marcacion_color_' + f + '_' + i + '_' + id_esp).val() != '')
                total_fila += parseInt($('#ramos_marcacion_color_' + f + '_' + i + '_' + id_esp).val());
        }

        $('#input_total_ramos_marcacion_' + f + '_' + id_esp).val(total_fila);
        if ($('#cantidad_ramos_esp_' + id_esp).val() != '')
            $('#input_total_piezas_' + f + '_' + id_esp).val(Math.round((total_fila / cantidad_ramos) * 100) / 100);

        total_ramos = 0;
        total_piezas = 0;
        for (i = 1; i <= fil; i++) {
            total_ramos += parseInt($('#input_total_ramos_marcacion_' + i + '_' + id_esp).val());
            total_piezas += parseFloat($('#input_total_piezas_' + i + '_' + id_esp).val());
        }

        $('#total_ramos_' + fil + '_' + col + '_' + id_esp).val(total_ramos);
        $('#total_piezas_' + fil + '_' + col + '_' + id_esp).val(total_piezas);
    }

    function seleccionar_color_x_esp(c, id_esp) {
        fondo = $('#fondo_color_' + $('#titles_columnas_' + c + '_' + id_esp).val()).val();
        texto = $('#texto_color_' + $('#titles_columnas_' + c + '_' + id_esp).val()).val();

        $('.input_ramos_marc_col_' + c + '_' + id_esp).css('background-color', fondo);
        $('.input_ramos_marc_col_' + c + '_' + id_esp).css('color', texto);
    }

</script>