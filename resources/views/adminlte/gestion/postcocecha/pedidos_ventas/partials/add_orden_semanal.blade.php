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
                        onchange="buscar_agencia_carga()">
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
            <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" colspan="6">
                <table class="table-striped table-bordered table-responsive" width="100%">
                    <tr>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            Cantidad
                        </th>
                        <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="40%">
                            <input type="number" id="cantidad_cajas" name="cantidad_cajas" class="form-control" required
                                   onkeypress="return isNumber(event)">
                        </td>
                        <th class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef">
                            Pieza
                        </th>
                        <td class="text-center" style="border-color: #9d9d9d; background-color: #e9ecef" width="40%">
                            <select name="id_empaque" id="id_empaque" class="form-control" required>
                                <option value="">Seleccione...</option>
                                @foreach($empaques as $item)
                                    <option value="{{$item->id_empaque}}">
                                        {{explode('|',$item->nombre)[0]}}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                </table>
            </th>
        </tr>
        <tr>
            <td style="border-color: #9d9d9d" class="text-center" colspan="6">
                <div class="row" style="margin-top: 10px">
                    <div class="col-md-2">
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Ramos</span>
                            <input type="number" id="cantidad_ramos" name="cantidad_ramos" onkeypress="return isNumber(event)"
                                   class="form-control"
                                   style="width: 100%" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Clasificación</span>
                            <select name="id_clasificacion_ramo" id="id_clasificacion_ramo" class="form-control" required>
                                <option value="">...</option>
                                @foreach(getCalibresRamo() as $item)
                                    @if($item->unidad_medida->tipo == 'P')
                                        <option value="{{$item->id_clasificacion_ramo}}">
                                            {{$item->nombre}} {{$item->unidad_medida->siglas}}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Variedad</span>
                            <select name="id_variedad" id="id_variedad" class="form-control" required>
                                <option value="">...</option>
                                @foreach(getVariedades() as $item)
                                    <option value="{{$item->id_variedad}}">
                                        {{$item->nombre}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Envoltura</span>
                            <select name="id_empaque_e" id="id_empaque_e" class="form-control" required>
                                <option value="">...</option>
                                @foreach($envolturas as $item)
                                    <option value="{{$item->id_empaque}}">
                                        {{$item->nombre}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Presentación</span>
                            <select name="id_empaque_p" id="id_empaque_p" class="form-control" required>
                                <option value="">...</option>
                                @foreach($presentaciones as $item)
                                    <option value="{{$item->id_empaque}}">
                                        {{$item->nombre}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Longitud</span>
                            <input type="number" id="longitud_ramo" name="longitud_ramo" onkeypress="return isNumber(event)" class="form-control"
                                   style="width: 100%" min="1">
                        </div>
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">Tallos</span>
                            <input type="number" id="tallos_x_ramos" name="tallos_x_ramos" onkeypress="return isNumber(event)"
                                   class="form-control"
                                   style="width: 100%" min="1">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group input-group">
                            <span class="input-group-addon" style="background-color: #e9ecef">U. medida</span>
                            <select name="id_unidad_medida" id="id_unidad_medida" class="form-control">
                                <option value="">...</option>
                                @foreach(getUnidadesMedida() as $item)
                                    <option value="{{$item->id_unidad_medida}}">
                                        {{$item->nombre}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</form>

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
                    ' style="width: 150px" class="text-center input_ramos_marc_col_' + c + '" onchange="calcular_total_ramos(' + c + ',' + f + ')">' +
                    '</td>';
                titles_columnas += '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" id="titles_columnas_' + c + '" name="titles_columnas_' + c + '"' +
                    ' style="width: 150px" placeholder="C #' + c + '" class="text-center">' +
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
                    '<th class="text-center" style="border-color: #9d9d9d; width: 350px">Texto</th>' + colorpickers_tx +
                    '</tr>' +
                    '<tr class="row_marcaciones_colores">' +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 350px">Fondo</th>' + colorpickers_bg +
                    '</tr>' +
                    '<tr class="row_marcaciones_colores">' +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 350px">Color</th>' + titles_columnas +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 200px">Total</th>' +
                    '<th class="text-center" style="border-color: #9d9d9d; width: 200px">Piezas</th>' +
                    '</tr>');
            } else {
                $('#table_marcaciones_x_colores').append('<tr class="row_marcaciones_colores">' +
                    '<th class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" id="nombre_marcacion_' + f + '" name="nombre_marcacion_' + f + '"' +
                    ' style="width: 150px" placeholder="M #' + f + '" class="text-center">' +
                    '</th>' + html_columnas +
                    '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" class="text-center" readonly id="input_total_ramos_marcacion_' + f + '" value="0"' +
                    ' style="background-color: #357CA5; color: white">' +
                    '</td>' +
                    '<td class="text-center" style="border-color: #9d9d9d">' +
                    '<input type="text" class="text-center" readonly id="input_total_piezas_' + f + '" value="0"' +
                    ' style="background-color: #357CA5; color: white">' +
                    '</td>' +
                    '</tr>');
            }
        }

        total_ramos = '';
        for (c = 1; c <= col; c++) {
            total_ramos += '<td class="text-center" style="border-color: #9d9d9d; background-color: #357CA5">' +
                '<input type="text" class="text-center" readonly id="input_total_ramos_' + c + '" value="0"' +
                ' style="background-color: #357CA5; color: white">' +
                '</td>';
        }
        $('#table_marcaciones_x_colores').append('<tr class="row_marcaciones_colores">' +
            '<th class="text-center" style="border-color: #9d9d9d">Ramos</th>' + total_ramos +
            '<th class="text-center" style="border-color: #9d9d9d">' +
            '<input type="text" readonly id="total_ramos_' + fil + '_' + col + '" name="total_ramos_' + fil + '_' + col + '"' +
            ' value="0" style="background-color: #9d9d9d; color: white; width: 100%" class="text-center">' +
            '</th>' +
            '<th class="text-center" style="border-color: #9d9d9d">' +
            '<input type="text" readonly id="total_piezas_' + fil + '_' + col + '" name="total_piezas_' + fil + '_' + col + '"' +
            ' value="0" style="background-color: #9d9d9d; color: white; width: 100%" class="text-center">' +
            '</th>' +
            '</tr>');
    }

    function seleccionar_color(c) {
        $('.input_ramos_marc_col_' + c).css('background-color', $('#color_picker_bg_' + c).val());
        $('.input_ramos_marc_col_' + c).css('color', $('#color_picker_tx_' + c).val());
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
</script>