@if($cliente != '')
    @foreach($cliente->cliente_pedido_especificaciones as $cli_ped_esp)
        @if($cli_ped_esp->especificacion->tipo == 'N')
            <div class="well sombra_estandar" id="well_{{$cli_ped_esp->id_especificacion}}">
                <table width="100%" class="table-responsive table-bordered table-striped"
                       style="font-size: 0.9em; margin-top: 10px; border: 3px solid #357ca5"
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
                        <th class="text-center" style="border-color: #9d9d9d" width="65px">
                            MARCACIONES
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d" width="65px">
                            COLORACIONES
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d">
                            <button type="button" class="btn btn-xs btn-danger" onclick="ocultar_well('{{$cli_ped_esp->id_especificacion}}')">
                                <i class="fa fa-fw fa-eye-slash"></i> Ocultar
                            </button>
                        </th>
                    </tr>
                    </thead>
                    @foreach($cli_ped_esp->especificacion->especificacionesEmpaque as $pos_esp_emp => $esp_emp)
                        @php
                            $ramos_x_caja = 0;
                        @endphp
                        @foreach($esp_emp->detalles as $pos_det_esp_emp => $det_esp_emp)
                            <tr class="esp_emp_{{$esp_emp->id_especificacion_empaque}}" onmouseover="sombrear_fila($(this).prop('class'),1)"
                                onmouseleave="sombrear_fila($(this).prop('class'),0)">
                                @if($pos_esp_emp == 0 && $pos_det_esp_emp == 0)
                                    <td class="text-center" style="border-color: #9d9d9d"
                                        rowspan="{{getCantidadDetallesByEspecificacion($cli_ped_esp->id_especificacion)}}">
                                        <input type="number" id="cant_piezas_esp_{{$cli_ped_esp->id_especificacion}}" style="width: 100%"
                                               name="cant_piezas_esp_{{$cli_ped_esp->id_especificacion}}" class="form-control"
                                               value="{{$cli_ped_esp->id_especificacion}}">
                                    </td>
                                @endif
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp_emp->variedad->nombre}}
                                </td>
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp_emp->clasificacion_ramo->nombre}}{{$det_esp_emp->clasificacion_ramo->unidad_medida->siglas}}
                                </td>
                                @if($pos_det_esp_emp == 0)
                                    <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                        {{explode('|',$esp_emp->empaque->nombre)[0]}}
                                    </td>
                                @endif
                                <td class="text-center" style="border-color: #9d9d9d">
                                    {{$det_esp_emp->cantidad}}
                                    @php
                                        $ramos_x_caja += $det_esp_emp->cantidad;
                                    @endphp
                                    <input type="hidden" id="cantidad_ramos_esp_{{$esp_emp->id_especificacion_empaque}}"
                                           value="{{$det_esp_emp->cantidad}}">
                                    {{-- DATOS PARA ESPECIFICACIONES MIXTAS --}}
                                    <input type="hidden" id="cantidad_detalles_esp_{{$esp_emp->id_especificacion_empaque}}"
                                           value="{{count($esp_emp->detalles)}}">
                                    <input type="hidden" class="id_det_esp_{{$esp_emp->id_especificacion_empaque}}"
                                           value="{{$det_esp_emp->id_detalle_especificacionempaque}}">
                                    <input type="hidden" id="id_variedad_det_esp_{{$det_esp_emp->id_detalle_especificacionempaque}}"
                                           value="{{$det_esp_emp->id_variedad}}">
                                    <input type="hidden" id="siglas_variedad_det_esp_{{$det_esp_emp->id_detalle_especificacionempaque}}"
                                           value="{{$det_esp_emp->variedad->siglas}}">
                                    <input type="hidden" id="ramos_x_caja_det_{{$det_esp_emp->id_detalle_especificacionempaque}}"
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
                                <td class="text-center" style="border-color: #9d9d9d" id="td_precio_det_esp_{{$det_esp_emp->id_especificacion}}"
                                    rowspan="{{getCantidadDetallesByEspecificacion($det_esp_emp->id_especificacion)}}">
                                    <select name="precio_esp_{{$det_esp_emp->id_especificacion}}" class="form-control"
                                            id="precio_esp_{{$det_esp_emp->id_especificacion}}"
                                            ondblclick="cambiar_input_precio('{{$det_esp_emp->id_especificacion}}')">
                                        @foreach(explode('|',$det_esp_emp->precioByCliente($cli_ped_esp->id_cliente)->cantidad) as $precio)
                                            <option value="{{$precio}}">{{$precio}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                @if($pos_det_esp_emp == 0)
                                    <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                        <input type="number" id="num_marcaciones_{{$esp_emp->id_especificacion_empaque}}"
                                               name="num_marcaciones_{{$esp_emp->id_especificacion_empaque}}" onkeypress="return isNumber(event)"
                                               placeholder="Marcaciones" min="1" class="text-center form-control">
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                        <input type="number" id="num_colores_{{$esp_emp->id_especificacion_empaque}}"
                                               name="num_colores_{{$esp_emp->id_especificacion_empaque}}" onkeypress="return isNumber(event)"
                                               placeholder="Colores"
                                               min="1" class="text-center form-control">
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                        <button type="button" class="btn btn-xs btn-primary"
                                                onclick="construir_tabla_especificacion_orden_semanal('{{$esp_emp->id_especificacion_empaque}}')"
                                                style="margin-top: 0">
                                            <i class="fa fa-fw fa-check"></i> Tabla
                                        </button>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        <input type="hidden" id="ramos_x_caja_esp_{{$esp_emp->id_especificacion_empaque}}" value="{{$ramos_x_caja}}">
                    @endforeach
                </table>

                @foreach($cli_ped_esp->especificacion->especificacionesEmpaque as $pos_esp_emp => $esp_emp)
                    <div class="sombra_pequeña" style="display: none; border: 1px dotted #0a0a0a; margin-top: 10px"
                         id="div_tabla_distribucion_pedido_{{$esp_emp->id_especificacion_empaque}}">
                        <strong style="margin-top: 0" class="mouse-hand"
                                onclick="mostrar_ocultar_distribucion('{{$esp_emp->id_especificacion_empaque}}')">
                            - Distribución para la presentación siguiente:
                            <a href="javascript:void(0)" class="pull-right">
                                <i class="fa fa-fw fa-caret-down"></i>
                            </a>
                        </strong>
                        <table class="table-striped table-bordered" width="100%" style="border: 2px solid #9d9d9d; font-size: 0.8em;">
                            <thead>
                            <tr style="background-color: #e9ecef">
                                <th class="text-center" style="border-color: #9d9d9d">
                                    Nº
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
                            </tr>
                            </thead>
                            @foreach($esp_emp->detalles as $pos_det_esp_emp => $det_esp_emp)
                                <tr class="esp_emp_{{$esp_emp->id_especificacion_empaque}}" onmouseover="sombrear_fila($(this).prop('class'),1)"
                                    onmouseleave="sombrear_fila($(this).prop('class'),0)">
                                    <td class="text-center" style="border-color: #9d9d9d"
                                        id="td_presentacion_{{$det_esp_emp->id_detalle_especificacionempaque}}">
                                        P-{{$pos_det_esp_emp+1}}
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        {{$det_esp_emp->variedad->nombre}}
                                    </td>
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        {{$det_esp_emp->clasificacion_ramo->nombre}}{{$det_esp_emp->clasificacion_ramo->unidad_medida->siglas}}
                                    </td>
                                    @if($pos_det_esp_emp == 0)
                                        <td class="text-center" style="border-color: #9d9d9d" rowspan="{{count($esp_emp->detalles)}}">
                                            {{explode('|',$esp_emp->empaque->nombre)[0]}}
                                        </td>
                                    @endif
                                    <td class="text-center" style="border-color: #9d9d9d">
                                        {{$det_esp_emp->cantidad}}
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
                                </tr>
                            @endforeach
                        </table>
                        <div style="width: 100%; overflow-x: scroll;" id="div_table_distribucion_{{$esp_emp->id_especificacion_empaque}}">
                            <table class="table-striped table-responsive table-bordered" width="100%"
                                   style="border: 1px solid #9d9d9d; margin-top: 10px"
                                   id="table_marcaciones_x_colores_{{$esp_emp->id_especificacion_empaque}}"></table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach
@endif

<script>
    function cambiar_input_precio(id_esp) {
        $('#td_precio_esp_' + id_esp).html('<input type="number" id="precio_esp_' + id_esp + '" ' +
            'name="precio_esp_' + id_esp + '" class="form-control">');
    }

    function ocultar_well(id_esp) {
        $('#well_' + id_esp).hide();
    }

    function construir_tabla_especificacion_orden_semanal(id_esp) {
        mixta = $('#cantidad_detalles_esp_' + id_esp).val();
        if (mixta > 1) {
            construir_tabla_especificacion_orden_semanal_mixta(id_esp);
        } else {
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
                        ' style="width: 60px" class="text-center input_ramos_marc_col_' + c + '_' + id_esp + '" ' +
                        'onchange="calcular_total_ramos_x_esp(' + c + ',' + f + ', ' + id_esp + ')">' +
                        '</td>';
                    titles_columnas += '<th class="text-center" style="border-color: #9d9d9d">' +
                        '<select id="titles_columnas_' + c + '_' + id_esp + '" name="titles_columnas_' + c + '_' + id_esp + '"' +
                        ' class="text-center" onchange="seleccionar_color_x_esp(' + c + ', ' + id_esp + ')" style="width: 60px">' +
                        '<option value="">C # ' + c + '</option>' + $('#select_colores').html() +
                        '</select>' +
                        '</th>';
                }
                if (f == 0) {
                    $('#table_marcaciones_x_colores_' + id_esp).append('<tr class="row_marcaciones_colores_' + id_esp + '">' +
                        '<th class="text-center" style="border-color: #9d9d9d; width: 85px">Color</th>' + titles_columnas +
                        '<th class="text-center" style="border-color: #9d9d9d; width: 60px">Total</th>' +
                        '<th class="text-center" style="border-color: #9d9d9d; width: 60px">Piezas</th>' +
                        '</tr>');
                } else {
                    $('#table_marcaciones_x_colores_' + id_esp).append('<tr class="row_marcaciones_colores_' + id_esp + '">' +
                        '<th class="text-center" style="border-color: #9d9d9d">' +
                        '<input type="text" id="nombre_marcacion_' + f + '_' + id_esp + '" name="nombre_marcacion_' + f + '_' + id_esp + '"' +
                        ' style="width: 150px" placeholder="M #' + f + '" class="text-center">' +
                        '</th>' + html_columnas +
                        '<td class="text-center" style="border-color: #9d9d9d">' +
                        '<input type="text" class="text-center" readonly id="input_total_ramos_marcacion_' + f + '_' + id_esp + '" value="0"' +
                        ' style="width: 60px; background-color: #357CA5; color: white">' +
                        '</td>' +
                        '<td class="text-center" style="border-color: #9d9d9d">' +
                        '<input type="text" class="text-center" readonly id="input_total_piezas_' + f + '_' + id_esp + '" value="0"' +
                        ' style="width: 60px; background-color: #357CA5; color: white">' +
                        '</td>' +
                        '</tr>');
                }
            }

            total_ramos = '';
            for (c = 1; c <= col; c++) {
                total_ramos += '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" class="text-center" readonly id="input_total_ramos_' + c + '_' + id_esp + '" value="0"' +
                    ' style="background-color: #357CA5; color: white; width: 60px">' +
                    '</td>';
            }
            $('#table_marcaciones_x_colores_' + id_esp).append('<tr class="row_marcaciones_colores_' + id_esp + '">' +
                '<th class="text-center" style="border-color: #9d9d9d">Ramos</th>' + total_ramos +
                '<th class="text-center" style="border-color: #9d9d9d">' +
                '<input type="text" readonly id="total_ramos_' + fil + '_' + col + '_' + id_esp + '" ' +
                'name="total_ramos_' + fil + '_' + col + '_' + id_esp + ' "' +
                ' value="0" style="background-color: #9d9d9d; color: white; width: 60px" class="text-center">' +
                '</th>' +
                '<th class="text-center" style="border-color: #9d9d9d">' +
                '<input type="text" readonly id="total_piezas_' + fil + '_' + col + '_' + id_esp + '" ' +
                'name="total_piezas_' + fil + '_' + col + '_' + id_esp + '"' +
                ' value="0" style="background-color: #9d9d9d; color: white; width: 60px" class="text-center">' +
                '</th>' +
                '</tr>'
            );
        }
    }

    function construir_tabla_especificacion_orden_semanal_mixta(id_esp) {
        mixta = $('#cantidad_detalles_esp_' + id_esp).val();
        col = $('#num_colores_' + id_esp).val();
        fil = $('#num_marcaciones_' + id_esp).val();

        $('.row_marcaciones_colores_' + id_esp).remove();
        $('#div_tabla_distribucion_pedido_' + id_esp).show();

        ids_det_esp = $('.id_det_esp_' + id_esp);
        for (f = 0; f <= fil; f++) {
            html_columnas = '';
            titles_columnas = '';
            for (c = 1; c <= col; c++) {
                inputs = '';
                for (det = 0; det < ids_det_esp.length; det++) {
                    inputs += '<li class="text-center">' +
                        '<div class="input-group" style="width: 100px">' +
                        '<span class="input-group-addon" style="background-color: #e9ecef">' +
                        $('#td_presentacion_' + ids_det_esp[det].value).html() + '</span>' +
                        '<input type="number" id="ramos_marcacion_color_' + f + '_' + c + '_' + id_esp + '_det_' + ids_det_esp[det].value + '" ' +
                        'name="ramos_marcacion_color_' + f + '_' + c + '_' + id_esp + '_det_' + ids_det_esp[det].value + '" onkeypress="return isNumber(event)"' +
                        ' style="width: 100%" class="text-center input_ramos_marc_col_' + c + '_' + id_esp + '" ' +
                        'onchange="calcular_total_ramos_x_esp(' + c + ',' + f + ', ' + id_esp + ', 1)" ' +
                        'value="">' +
                        '</div>' +
                        '</li>';
                }
                html_columnas += '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<ul class="list-unstyled text-center">' +
                    inputs +
                    '</ul>' +
                    '</td>';
                titles_columnas += '<th class="text-center" style="border-color: #9d9d9d; width: 100px">' +
                    '<select id="titles_columnas_' + c + '_' + id_esp + '" name="titles_columnas_' + c + '_' + id_esp + '"' +
                    ' class="text-center" onchange="seleccionar_color_x_esp(' + c + ', ' + id_esp + ')">' +
                    '<option value="">C # ' + c + '</option>' + $('#select_colores').html() +
                    '</select>' +
                    '</th>';
            }
            if (f == 0) {
                $('#table_marcaciones_x_colores_' + id_esp).append('<tr class="row_marcaciones_colores_' + id_esp + '">' +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 85px">Color</th>' + titles_columnas +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 100px">Parcial</th>' +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 60px">Total</th>' +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 60px">Piezas</th>' +
                    '</tr>');
            } else {
                inputs = '';
                for (det = 0; det < ids_det_esp.length; det++) {
                    inputs += '<li>' +
                        '<div class="input-group" style="width: 100px">' +
                        '<span class="input-group-addon" style="background-color: #e9ecef">' +
                        $('#td_presentacion_' + ids_det_esp[det].value).html() + '</span>' +
                        '<input type="number" id="parcial_ramos_' + f + '_' + id_esp + '_det_' + ids_det_esp[det].value + '" ' +
                        'name="parcial_ramos_' + f + '_' + id_esp + '_det_' + ids_det_esp[det].value + '" onkeypress="return isNumber(event)"' +
                        ' class="text-center" ' + 'value="0" style="background-color: #357CA5; color: white; width: 100%" readonly>' +
                        '</div>' +
                        '</li>';
                }
                tr_color = 'white';
                if (f % 2 == 0)
                    tr_color = '#ADD8E6';
                $('#table_marcaciones_x_colores_' + id_esp).append('<tr class="row_marcaciones_colores_' + id_esp + '" ' +
                    'style="background-color: ' + tr_color + '; border: 2px solid #9d9d9d">' +
                    '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" id="nombre_marcacion_' + f + '_' + id_esp + '" name="nombre_marcacion_' + f + '_' + id_esp + '"' +
                    ' style="width: 150px; background-color: ' + tr_color + '" placeholder="M #' + f + '" class="text-center">' +
                    '</th>' + html_columnas +
                    '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<ul class="list-unstyled">' +
                    inputs +
                    '</ul>' +
                    '</td>' +
                    '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" class="text-center" readonly id="input_total_ramos_marcacion_' + f + '_' + id_esp + '" value="0"' +
                    ' style="background-color: #357CA5; color: white; width: 60px">' +
                    '</td>' +
                    '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" class="text-center" readonly id="input_total_piezas_' + f + '_' + id_esp + '" value="0"' +
                    ' style="background-color: #357CA5; color: white; width: 60px">' +
                    '</td>' +
                    '</tr>');
            }
        }

        total_ramos = '';
        inputs_col = '';
        for (c = 1; c <= col; c++) {
            total_ramos += '<td class="text-center" style="border-color: #9d9d9d">' +
                '<input type="text" class="text-center" readonly id="input_total_ramos_' + c + '_' + id_esp + '" value="0"' +
                ' style="background-color: #9d9d9d; color: white; width: 100%">' +
                '</td>';

            inputs_col += '<td class="text-center" v-bind:style="border-color: #9d9d9d">' +
                '<ul class="list-unstyled">';
            for (det = 0; det < ids_det_esp.length; det++) {
                inputs_col += '<li>' +
                    '<div class="input-group" style="width: 100px">' +
                    '<span class="input-group-addon" style="background-color: #e9ecef">' +
                    $('#td_presentacion_' + ids_det_esp[det].value).html() + '</span>' +
                    '<input type="number" id="total_parcial_ramos_col_' + c + '_' + id_esp + '_det_' + ids_det_esp[det].value + '" ' +
                    'name="total_parcial_ramos_col_' + c + '_' + id_esp + '_det_' + ids_det_esp[det].value + '" onkeypress="return isNumber(event)"' +
                    ' class="text-center" ' + 'value="0" style="background-color: #357CA5; color: white; width: 100%" readonly>' +
                    '</div>' +
                    '</li>';
            }
            inputs_col += '</ul></td>';
        }
        inputs = '';
        for (det = 0; det < ids_det_esp.length; det++) {
            inputs += '<li>' +
                '<div class="input-group" style="width: 100px">' +
                '<span class="input-group-addon" style="background-color: #e9ecef">' +
                $('#td_presentacion_' + ids_det_esp[det].value).html() + '</span>' +
                '<input type="number" id="total_parcial_ramos_' + id_esp + '_det_' + ids_det_esp[det].value + '" ' +
                'name="total_parcial_ramos_' + id_esp + '_det_' + ids_det_esp[det].value + '" onkeypress="return isNumber(event)"' +
                ' class="text-center" ' + 'value="0" style="background-color: #357CA5; color: white; width: 100%" readonly>' +
                '</div>' +
                '</li>';
        }
        $('#table_marcaciones_x_colores_' + id_esp).append('<tr class="row_marcaciones_colores_' + id_esp + '">' +
            '<th class="text-center" style="border-color: #9d9d9d" rowspan="2">Totales</th>' + inputs_col +
            '<th class="text-center" style="border-color: #9d9d9d">' +
            '<ul class="list-unstyled">' +
            inputs +
            '</ul>' +
            '</th>' +
            '<th class="text-center" style="border-color: #9d9d9d">' +
            '<input type="text" readonly id="total_ramos_' + id_esp + '" ' +
            'name="total_ramos_' + id_esp + ' "' +
            ' value="0" style="background-color: #9d9d9d; color: white; width: 60px" class="text-center">' +
            '</th>' +
            '<th class="text-center" style="border-color: #9d9d9d">' +
            '<input type="text" readonly id="total_piezas_' + id_esp + '" ' +
            'name="total_piezas_' + id_esp + '"' +
            ' value="0" style="background-color: #9d9d9d; color: white; width: 60px" class="text-center">' +
            '</th>' +
            '</tr>' +
            '<tr class="row_marcaciones_colores_' + id_esp + '">' +
            total_ramos +
            '</tr>'
        );
    }

    function calcular_total_ramos_x_esp(c, f, id_esp, mixta = 0) {
        if (mixta == 0) {
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
        } else {
            calcular_total_ramos_x_esp_mixta(c, f, id_esp);
        }
    }

    function calcular_total_ramos_x_esp_mixta(c, f, id_esp) {
        fil = $('#num_marcaciones_' + id_esp).val();
        col = $('#num_colores_' + id_esp).val();

        ramos_x_caja = $('#ramos_x_caja_esp_' + id_esp).val();
        ids_det_esp = $('.id_det_esp_' + id_esp);

        total = 0;
        for (f = 1; f <= fil; f++) {
            total_fila = 0;
            for (det = 0; det < ids_det_esp.length; det++) {
                parcial = 0;
                for (c = 1; c <= col; c++) {
                    cant = $('#ramos_marcacion_color_' + f + '_' + c + '_' + id_esp + '_det_' + ids_det_esp[det].value).val();
                    if (cant != '') {
                        parcial += parseInt(cant);
                    }
                }
                $('#parcial_ramos_' + f + '_' + id_esp + '_det_' + ids_det_esp[det].value).val(parcial);
                total_fila += parcial;
            }
            $('#input_total_ramos_marcacion_' + f + '_' + id_esp).val(total_fila);
            $('#input_total_piezas_' + f + '_' + id_esp).val(Math.round((total_fila / ramos_x_caja) * 100) / 100);
            total += total_fila;
        }
        $('#total_ramos_' + id_esp).val(total);
        $('#total_piezas_' + id_esp).val(Math.round((total / ramos_x_caja) * 100) / 100);

        for (c = 1; c <= col; c++) {
            total_col = 0;
            for (det = 0; det < ids_det_esp.length; det++) {
                parcial = 0;
                for (f = 1; f <= fil; f++) {
                    cant = $('#ramos_marcacion_color_' + f + '_' + c + '_' + id_esp + '_det_' + ids_det_esp[det].value).val();
                    if (cant != '') {
                        parcial += parseInt(cant);
                    }
                }
                $('#total_parcial_ramos_col_' + c + '_' + id_esp + '_det_' + ids_det_esp[det].value).val(parcial);
                total_col += parcial;
            }
            $('#input_total_ramos_' + c + '_' + id_esp).val(total_col);
        }

        for (det = 0; det < ids_det_esp.length; det++) {
            parcial = 0;
            for (c = 1; c <= col; c++) {
                cant = $('#total_parcial_ramos_col_' + c + '_' + id_esp + '_det_' + ids_det_esp[det].value).val();
                if (cant != '') {
                    parcial += parseInt(cant);
                }
            }
            $('#total_parcial_ramos_' + id_esp + '_det_' + ids_det_esp[det].value).val(parcial);
        }
    }

    function seleccionar_color_x_esp(c, id_esp) {
        fondo = $('#fondo_color_' + $('#titles_columnas_' + c + '_' + id_esp).val()).val();
        texto = $('#texto_color_' + $('#titles_columnas_' + c + '_' + id_esp).val()).val();

        $('.input_ramos_marc_col_' + c + '_' + id_esp).css('background-color', fondo);
        $('.input_ramos_marc_col_' + c + '_' + id_esp).css('color', texto);
    }

    function sombrear_fila(clase, option) {
        if (option == 1) {
            $('.' + clase).css('background-color', '#ADD8E6');
        } else {
            $('.' + clase).css('background-color', '');
        }
    }

    function mostrar_ocultar_distribucion(id_esp) {
        if ($('#div_table_distribucion_' + id_esp).css('display') != 'none') {
            $('#div_table_distribucion_' + id_esp).css('display', 'none');
        } else {
            $('#div_table_distribucion_' + id_esp).css('display', 'block');
        }
    }
</script>