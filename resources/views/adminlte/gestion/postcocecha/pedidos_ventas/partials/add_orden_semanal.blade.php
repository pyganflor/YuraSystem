<form id="form_add_orden_semanal">
    <table class="table-striped table-responsive table-bordered" width="100%">
        <tr>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Fecha
            </th>
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <input type="date" id="fecha_pedido" name="fecha_pedido" required style="width: 100%" class="form-control">
            </td>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Cliente
            </th>
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                <select name="id_cliente_orden_semanal" id="id_cliente_orden_semanal" required style="width: 100%" class="form-control"
                        onchange="buscar_agencia_carga(); listar_especificaciones_x_cliente()">
                    <option value="">Seleccione...</option>
                    @foreach($clientes as $item)
                        <option value="{{$item->id_cliente}}">
                            {{$item->detalle()->nombre}}
                        </option>
                    @endforeach
                </select>
            </td>
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                Agencia de Carga
            </th>
            <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" id="div_agenia_carga">

            </td>
        </tr>
        <tr>
            <td colspan="6">
                <table class="table-responsive table-bordered" style="width: 100%; border: 1px solid #9d9d9d; font-size: 0.9em">
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="75px">
                            CANTIDAD
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            VARIEDAD
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="150px">
                            CALIBRE
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            CAJA
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="75px">
                            RAMOS X CAJA
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            PRESENTACIÓN
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="75px">
                            TALLOS X RAMO
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="75px">
                            LONGITUD
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            U. MEDIDA
                        </th>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="85px">
                            PRECIO
                        </th>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <input type="number" id="cantidad_cajas" name="cantidad_cajas" class="form-control" required
                                   onkeypress="return isNumber(event)">
                        </td>
                        <td class="text-center">
                            <select name="id_variedad" id="id_variedad" class="form-control" required>
                                @foreach(getVariedades() as $item)
                                    <option value="{{$item->id_variedad}}">
                                        {{$item->nombre}}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center">
                            <select name="id_clasificacion_ramo" id="id_clasificacion_ramo" class="form-control" required>
                                @foreach(getCalibresRamo() as $item)
                                    @if($item->unidad_medida->tipo == 'P')
                                        <option value="{{$item->id_clasificacion_ramo}}">
                                            {{$item->nombre}} {{$item->unidad_medida->siglas}}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center">
                            <select name="id_empaque" id="id_empaque" class="form-control" required>
                                @foreach($empaques as $item)
                                    <option value="{{$item->id_empaque}}">
                                        {{explode('|',$item->nombre)[0]}}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center">
                            <input type="number" id="cantidad_ramos" name="cantidad_ramos" onkeypress="return isNumber(event)"
                                   class="form-control" style="width: 100%" required>
                        </td>
                        <td class="text-center">
                            <select name="id_empaque_p" id="id_empaque_p" class="form-control" required>
                                @foreach($presentaciones as $item)
                                    <option value="{{$item->id_empaque}}">
                                        {{$item->nombre}}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center">
                            <input type="number" id="tallos_x_ramos" name="tallos_x_ramos" onkeypress="return isNumber(event)"
                                   class="form-control"
                                   style="width: 100%" min="1">
                        </td>
                        <td class="text-center">
                            <input type="number" id="longitud_ramo" name="longitud_ramo" onkeypress="return isNumber(event)" class="form-control"
                                   style="width: 100%" min="1">
                        </td>
                        <td class="text-center">
                            <select name="id_unidad_medida" id="id_unidad_medida" class="form-control">
                                @foreach(getUnidadesMedida() as $item)
                                    @if($item->tipo == 'L')
                                        <option value="{{$item->id_unidad_medida}}">
                                            {{$item->siglas}}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center">
                            <input type="number" id="precio" name="precio" class="form-control" required>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</form>

{{-- COLORES --}}
<select name="select_colores" id="select_colores" style="display: none">
    @foreach($colores as $c)
        <option value="{{$c->id_color}}" style="background-color: {{$c->fondo}}; color: {{$c->texto}}">{{$c->nombre}}</option>
    @endforeach
</select>

@foreach($colores as $c)
    <input type="hidden" id="fondo_color_{{$c->id_color}}" value="{{$c->fondo}}">
    <input type="hidden" id="texto_color_{{$c->id_color}}" value="{{$c->texto}}">
    <input type="hidden" id="nombre_color_{{$c->id_color}}" value="{{$c->nombre}}">
@endforeach
{{-- COLORES --}}

@include('adminlte.gestion.postcocecha.pedidos_ventas.partials._tabla_orden_semanal')

<script>
    set_min_today($('#fecha_pedido'));

    function construir_tabla() {
        col = $('#num_colores').val();
        fil = $('#num_marcaciones').val();

        $('.row_marcaciones_colores').remove();
        $('#div_tabla_distribucion_pedido').show();

        for (f = 0; f <= fil; f++) {
            html_columnas = '';
            titles_columnas = '';
            colorpickers_bg = '';
            colorpickers_tx = '';
            for (c = 1; c <= col; c++) {
                html_columnas += '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="number" id="ramos_marcacion_color_' + f + '_' + c + '" name="ramos_marcacion_color_' + f + '_' + c + '" onkeypress="return isNumber(event)"' +
                    ' style="width: 60px" class="text-center input_ramos_marc_col_' + c + '" onchange="calcular_total_ramos(' + c + ',' + f + ')">' +
                    '</td>';
                titles_columnas += '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<select id="titles_columnas_' + c + '" name="titles_columnas_' + c + '"' +
                    ' style="width: 60px" class="text-center" onchange="seleccionar_color(' + c + ')">' +
                    '<option value="">C # ' + c + '</option>' + $('#select_colores').html() +
                    '</select>' +
                    '</th>';
                colorpickers_bg += '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="color" id="color_picker_bg_' + c + '" name="color_picker_bg_' + c + '"' +
                    ' style="width: 150px" placeholder="Color" value="#ffffff" onchange="seleccionar_color(' + c + ')">' +
                    '</td>';
                colorpickers_tx += '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="color" id="color_picker_tx_' + c + '" name="color_picker_tx_' + c + '"' +
                    ' style="width: 150px" placeholder="Color" value="#000000" onchange="seleccionar_color(' + c + ')">' +
                    '</td>';
            }
            if (f == 0) {
                $('#table_marcaciones_x_colores').append('<tr class="row_marcaciones_colores">' +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 85px">Color</th>' + titles_columnas +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 60px">Total</th>' +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 60px">Piezas</th>' +
                    '</tr>');
            } else {
                $('#table_marcaciones_x_colores').append('<tr class="row_marcaciones_colores">' +
                    '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" id="nombre_marcacion_' + f + '" name="nombre_marcacion_' + f + '"' +
                    ' style="width: 150px" placeholder="M #' + f + '" class="text-center">' +
                    '</th>' + html_columnas +
                    '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" class="text-center" readonly id="input_total_ramos_marcacion_' + f + '" value="0"' +
                    ' style="background-color: #357CA5; color: white; width: 60px">' +
                    '</td>' +
                    '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" class="text-center" readonly id="input_total_piezas_' + f + '" value="0"' +
                    ' style="background-color: #357CA5; color: white; width: 60px">' +
                    '</td>' +
                    '</tr>');
            }
        }

        total_ramos = '';
        for (c = 1; c <= col; c++) {
            total_ramos += '<td class="text-center" style="border-color: #9d9d9d">' +
                '<input type="text" class="text-center" readonly id="input_total_ramos_' + c + '" value="0"' +
                ' style="background-color: #357CA5; color: white; width: 60px">' +
                '</td>';
        }
        $('#table_marcaciones_x_colores').append('<tr class="row_marcaciones_colores">' +
            '<th class="text-center" style="border-color: #9d9d9d">Ramos</th>' + total_ramos +
            '<th class="text-center" style="border-color: #9d9d9d">' +
            '<input type="text" readonly id="total_ramos_' + fil + '_' + col + '" name="total_ramos_' + fil + '_' + col + '"' +
            ' value="0" style="background-color: #9d9d9d; color: white; width: 60px" class="text-center">' +
            '</th>' +
            '<th class="text-center" style="border-color: #9d9d9d">' +
            '<input type="text" readonly id="total_piezas_' + fil + '_' + col + '" name="total_piezas_' + fil + '_' + col + '"' +
            ' value="0" style="background-color: #9d9d9d; color: white; width: 60px" class="text-center">' +
            '</th>' +
            '</tr>');
    }

    function seleccionar_color(c) {
        fondo = $('#fondo_color_' + $('#titles_columnas_' + c).val()).val();
        texto = $('#texto_color_' + $('#titles_columnas_' + c).val()).val();
        $('.input_ramos_marc_col_' + c).css('background-color', fondo);
        $('.input_ramos_marc_col_' + c).css('color', texto);
    }

    function calcular_total_ramos(c, f) {
        fil = $('#num_marcaciones').val();
        col = $('#num_colores').val();
        cantidad_ramos = parseInt($('#cantidad_ramos').val());

        total_col = 0;
        for (i = 1; i <= fil; i++) {
            if ($('#ramos_marcacion_color_' + i + '_' + c).val() != '')
                total_col += parseInt($('#ramos_marcacion_color_' + i + '_' + c).val());
        }

        $('#input_total_ramos_' + c).val(total_col);

        total_fila = 0;
        for (i = 1; i <= col; i++) {
            if ($('#ramos_marcacion_color_' + f + '_' + i).val() != '')
                total_fila += parseInt($('#ramos_marcacion_color_' + f + '_' + i).val());
        }

        $('#input_total_ramos_marcacion_' + f).val(total_fila);
        if ($('#cantidad_ramos').val() != '')
            $('#input_total_piezas_' + f).val(Math.round((total_fila / cantidad_ramos) * 100) / 100);

        total_ramos = 0;
        total_piezas = 0;
        for (i = 1; i <= fil; i++) {
            total_ramos += parseInt($('#input_total_ramos_marcacion_' + i).val());
            total_piezas += parseFloat($('#input_total_piezas_' + i).val());
        }

        $('#total_ramos_' + fil + '_' + col).val(total_ramos);
        $('#total_piezas_' + fil + '_' + col).val(total_piezas);
    }

    function buscar_agencia_carga() {
        datos = {
            id_cliente: $('#id_cliente_orden_semanal').val()
        };
        get_jquery('{{url('pedidos/buscar_agencia_carga')}}', datos, function (retorno) {
            $('#div_agenia_carga').html(retorno);
        });
    }

    function listar_especificaciones_x_cliente() {
        datos = {
            id_cliente: $('#id_cliente_orden_semanal').val()
        };
        get_jquery('{{url('pedidos/orden_semanal/listar_especificaciones_x_cliente')}}', datos, function (retorno) {
            $('#div_especificaciones_orden_semanal').html(retorno);
        });
    }
</script>